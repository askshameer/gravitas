const fs = require('fs');
const { chromium } = require('C:/Users/shameer/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');

(async () => {
  const css = fs.readFileSync('gravitas-coaching/assets/css/main.css', 'utf8');
  const script = fs.readFileSync('gravitas-coaching/assets/js/theme.js', 'utf8');
	const nextPage = '<div class="blog-results" data-blog-results><div class="blog-loop"><article class="post-card">Second post</article><article class="post-card">Third post</article></div><nav class="pagination"></nav></div>';
  const browser = await chromium.launch({ headless: true, channel: 'chrome' });
  const page = await browser.newPage({ viewport: { width: 1200, height: 900 } });
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
	await page.route('https://example.test/**', route => route.fulfill({ status: 200, contentType: 'text/html', body: nextPage }));
	await page.goto('https://example.test/start');
  await page.setContent(`<!doctype html><style>${css}</style><section class="section"><div class="container blog-shell"><div class="blog-results" data-blog-results data-pagination-mode="load_more"><div class="blog-loop blog-loop--grid"><article class="post-card">First post</article></div><nav class="pagination"><a class="next" href="https://example.test/page2">Next</a></nav><p class="blog-load-status"></p></div><aside class="site-sidebar">Sidebar</aside></div></section>`);
	const probe = await page.evaluate(async () => ({ url: location.href, text: await (await fetch('https://example.test/page2')).text() }));
	if (!probe.text.includes('Second post')) throw new Error('Test route failed: ' + JSON.stringify(probe));
	const parsed = await page.evaluate(html => new DOMParser().parseFromString(html, 'text/html').querySelectorAll('[data-blog-results] .blog-loop > article').length, probe.text);
	if (parsed !== 2) throw new Error('Test response parsing failed: ' + parsed);
  await page.addScriptTag({ content: script });
  const layout = await page.evaluate(() => {
    const results = document.querySelector('.blog-results').getBoundingClientRect();
    const sidebar = document.querySelector('.site-sidebar').getBoundingClientRect();
	const pagination = document.querySelector('.blog-load-more').getBoundingClientRect();
    const card = document.querySelector('.post-card').getBoundingClientRect();
    return { resultsRight: results.right, sidebarLeft: sidebar.left, paginationTop: pagination.top, cardBottom: card.bottom };
  });
  if (layout.resultsRight > layout.sidebarLeft || layout.paginationTop < layout.cardBottom) throw new Error('Pagination/sidebar layout regression: ' + JSON.stringify(layout));
  await page.locator('.blog-load-more').click();
	await page.waitForTimeout(500);
  const state = await page.evaluate(() => ({ cards: document.querySelectorAll('.blog-loop > article').length, status: document.querySelector('.blog-load-status').textContent, buttonHidden: document.querySelector('.blog-load-more').hidden }));
  if (state.cards !== 3 || !state.buttonHidden || state.status !== 'All posts loaded.') throw new Error('Load-more regression: ' + JSON.stringify(state));
  if (errors.length) throw new Error(errors.join('\n'));
  console.log('Blog pagination stays below content and dynamic loading passed.');
  await browser.close();
})().catch(error => { console.error(error); process.exit(1); });
