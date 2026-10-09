const fs = require('fs');
const { chromium } = require('C:/Users/shameer/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');

const coreFiles = [
	'gravitas-plugins/gravitas-core/includes/class-gravitas-core-settings.php',
	'gravitas-coaching/inc/plugins/gravitas-core/includes/class-gravitas-core-settings.php',
];
const template = fs.readFileSync('gravitas-coaching/single.php', 'utf8');

function generatedSidebarCss(source) {
	const match = source.match(/(body\.gravitas-sidebar-none:not\(\.single-post\)[\s\S]*?)(?=\.gravitas-post-title-center)/);
	if (!match) throw new Error('Could not find the generated sidebar CSS.');
	return match[1];
}

(async () => {
	const generatedCss = coreFiles.map(file => generatedSidebarCss(fs.readFileSync(file, 'utf8')));
	if (generatedCss[0] !== generatedCss[1]) throw new Error('Bundled and standalone Gravitas Core sidebar CSS differ.');
	if (!template.includes("elevora_get_setting( 'single_sidebar'")) throw new Error('Single template does not read the Theme Options sidebar setting.');
	if (!template.includes("if ( 'none' !== $sidebar_pos )")) throw new Error('Single template does not conditionally render the sidebar.');

	const css = fs.readFileSync('gravitas-coaching/assets/css/main.css', 'utf8') + generatedCss[0];
	const browser = await chromium.launch({ headless: true, channel: 'chrome' });
	const page = await browser.newPage({ viewport: { width: 1200, height: 800 } });
	await page.setContent(`<!doctype html><style>${css}</style><main class="single-post-main soledad-single-style"><article><div class="container article-layout"><div class="single-post-content">Article</div><aside class="site-sidebar"><section class="widget widget_block"><div class="wp-block-group__inner-container"><h2 class="wp-block-heading">Recent Comments</h2><p>A comment</p></div></section></aside></div></article></main>`);

	async function measure(mode, bodyClass) {
		return page.evaluate(({ mode, bodyClass }) => {
			document.body.className = bodyClass;
			document.querySelector('main').className = mode === 'archive' ? '' : `single-post-main soledad-single-style gravitas-single-sidebar-${mode}`;
			document.querySelector('article > div').className = mode === 'archive' ? 'content-with-sidebar' : 'container article-layout' + (mode === 'left' ? ' article-layout--sidebar-left' : mode === 'none' ? ' article-layout--no-sidebar' : '');
			const content = document.querySelector('.single-post-content').getBoundingClientRect();
			const sidebar = document.querySelector('.site-sidebar');
			const box = sidebar.getBoundingClientRect();
			return {
				visible: getComputedStyle(sidebar).display !== 'none' && box.width > 0,
				leftOfContent: box.right <= content.left + 1,
				rightOfContent: box.left >= content.right - 1,
			};
		}, { mode, bodyClass });
	}

	const right = await measure('right', 'single single-post gravitas-sidebar-none gravitas-single-sidebar-right');
	const left = await measure('left', 'single single-post gravitas-sidebar-none gravitas-single-sidebar-left');
	const none = await measure('none', 'single single-post gravitas-sidebar-none gravitas-single-sidebar-none');
	const staleBodyClass = await measure('right', 'single single-post gravitas-sidebar-none gravitas-single-sidebar-none elevora-no-sidebar');
	const archiveNone = await measure('archive', 'blog gravitas-sidebar-none');

	if (!right.visible || !right.rightOfContent) throw new Error('Right single-post sidebar is hidden or misplaced: ' + JSON.stringify(right));
	if (!left.visible || !left.leftOfContent) throw new Error('Left single-post sidebar is hidden or misplaced: ' + JSON.stringify(left));
	if (none.visible) throw new Error('Single-post sidebar remains visible when disabled: ' + JSON.stringify(none));
	if (!staleBodyClass.visible || !staleBodyClass.rightOfContent) throw new Error('A stale body class hides the template-enabled sidebar: ' + JSON.stringify(staleBodyClass));
	if (archiveNone.visible) throw new Error('Archive sidebar remains visible when disabled: ' + JSON.stringify(archiveNone));
	await measure('right', 'single single-post gravitas-single-sidebar-right');
	const commentHeading = await page.locator('.site-sidebar .wp-block-heading').evaluate(element => ({fontSize: parseFloat(getComputedStyle(element).fontSize), width: element.getBoundingClientRect().width, parentWidth: element.parentElement.getBoundingClientRect().width}));
	if (commentHeading.fontSize > 16 || commentHeading.width > commentHeading.parentWidth + 1) throw new Error('Recent Comments heading is oversized or clipped: ' + JSON.stringify(commentHeading));

	await browser.close();
	console.log('Single-post sidebar Theme Options regression test passed.');
})().catch(error => {
	console.error(error);
	process.exit(1);
});
