// Static design preview using the theme's templates and assets; no WordPress runtime.
const fs = require('fs');
const path = require('path');
const root = 'gravitas-coaching/';
fs.mkdirSync('design-preview', { recursive: true });
function render(source) {
  return source.replace(/<\?php[\s\S]*?\?>/g, php => {
    if (php.includes('elevora_icon(')) {
      const name = php.match(/elevora_icon\( '([^']+)'/)[1];
      const icons = fs.readFileSync(root + 'inc/template-functions.php', 'utf8');
      const match = icons.match(new RegExp("'" + name + "'\\s*=>\\s*'([^']+)'"));
      return `<svg class="elevora-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${match ? match[1] : ''}</svg>`;
    }
    if (php.includes('elevora_brand();')) return '<a class="site-brand" href="#"><span class="site-brand-mark"><i></i><i></i></span><span class="site-brand-name">Gravitas Coaching</span></a>';
    if (php.includes("theme_location' => 'primary")) return '<ul class="primary-menu">' + ['Home','Services','About','Process','Blog'].map((x,i) => `<li${i === 0 ? ' class="current-menu-item"' : ''}><a href="#${x.toLowerCase()}">${x}</a></li>`).join('') + '</ul>';
    if (php.includes('esc_url( $hero_image )')) return 'file:///' + path.resolve(root + 'assets/images/hero-coaching.jpg').replaceAll('\\','/');
    if (php.includes('esc_url(')) return '#services';
    const translated = php.match(/(?:__|esc_html_e|esc_attr_e)\( '([^']*)', 'elevora' \)/);
    return translated ? translated[1] : '';
  });
}
const homepage = fs.readFileSync(root + 'inc/homepage.php', 'utf8');
const front = fs.readFileSync(root + 'front-page.php', 'utf8');
const widget = fs.readFileSync(root + 'inc/plugins/gravitas-core/includes/class-gravitas-core-elementor.php', 'utf8');
for (const marker of ['class="hero"', 'class="focus-services container"', 'class="consulting-intro section"', 'class="finder-strip"', 'services-section', 'trainers-section', 'testimonials-section', 'journal-section']) {
  if (!homepage.includes(marker)) throw new Error('Shared homepage renderer is missing: ' + marker);
}
if (!front.includes('elevora_render_homepage();')) throw new Error('Native homepage does not use the shared renderer.');
if (!widget.includes("get_name() { return 'gravitas-homepage'; }") || !widget.includes('elevora_render_homepage( $this->get_settings_for_display() )')) {
  throw new Error('Elementor homepage widget does not use the shared renderer.');
}
if (!fs.existsSync('design-preview/index.html')) throw new Error('The original-design preview fixture is missing.');
(async () => {
  const {chromium} = require('C:/Users/shameer/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
  const browser = await chromium.launch({headless: true, channel: 'chrome'});
  const page = await browser.newPage();
  const errors = [];
  page.on('pageerror', e => errors.push(e.message));
  await page.goto('file:///' + path.resolve('design-preview/index.html').replaceAll('\\','/'));
  for (const width of [1440, 1024, 768, 390, 320]) {
    await page.setViewportSize({width, height: 1100});
    await page.screenshot({path: `design-preview/desktop-${width}.png`, fullPage: true});
    const result = await page.evaluate(() => ({width: innerWidth, scrollWidth: document.documentElement.scrollWidth, cards: document.querySelectorAll('.focus-card').length}));
    if (result.scrollWidth > width) throw new Error('Horizontal overflow: ' + JSON.stringify(result));
    console.log(JSON.stringify(result));
  }
  await page.setViewportSize({width:390,height:844});
  await page.locator('[data-menu-toggle]').click();
  if (await page.locator('[data-menu-toggle]').getAttribute('aria-expanded') !== 'true') throw new Error('Mobile menu did not open');
  await page.waitForTimeout(300);
  const menuBounds = await page.locator('[data-primary-nav]').boundingBox();
  if (!menuBounds || menuBounds.y >= 844 || menuBounds.x >= 390) throw new Error('Mobile menu is outside the viewport');
  await page.keyboard.press('Escape');
  if (await page.locator('[data-menu-toggle]').getAttribute('aria-expanded') !== 'false') throw new Error('Mobile menu did not close');
  await page.setViewportSize({width:1440,height:1100});
  await page.locator('[data-theme-toggle]').click();
  await page.screenshot({path:'design-preview/dark.png', fullPage:true});
  if (errors.length) throw new Error(errors.join('\n'));
  console.log('Responsive layout, menu open/Escape, dark-mode interaction, and JS error checks passed.');
  await page.locator('[data-theme-toggle]').click();
  await page.setViewportSize({width:1200,height:900});
  await page.screenshot({path:root + 'screenshot.png'});
  await browser.close();
})();
