const fs = require('fs');
const { chromium } = require('C:/Users/shameer/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');

function luminance(color) {
  const values = color.match(/[\d.]+/g).slice(0, 3).map(Number).map(value => {
    const channel = value / 255;
    return channel <= 0.03928 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4);
  });
  return 0.2126 * values[0] + 0.7152 * values[1] + 0.0722 * values[2];
}

function contrast(foreground, background) {
  const light = Math.max(luminance(foreground), luminance(background));
  const dark = Math.min(luminance(foreground), luminance(background));
  return (light + 0.05) / (dark + 0.05);
}

(async () => {
  const css = [
    fs.readFileSync('gravitas-coaching/assets/css/main.css', 'utf8'),
    fs.readFileSync('gravitas-coaching/assets/css/content-hub.css', 'utf8'),
    fs.readFileSync('gravitas-plugins/gravitas-video/assets/front-layout.css', 'utf8')
  ].join('\n');
  const script = fs.readFileSync('gravitas-coaching/assets/js/theme.js', 'utf8');
  const browser = await chromium.launch({ headless: true, channel: 'chrome' });
  const page = await browser.newPage({ colorScheme: 'light' });
  await page.setContent(`<!doctype html><html><head><style>${css}</style></head><body>
    <button data-theme-toggle>Theme</button>
    <a class="site-brand--dual-logo has-dark-logo"><span class="custom-logo site-logo site-logo--light">Light logo</span><span class="custom-logo site-logo site-logo--dark">Dark logo</span></a>
    <section class="page-hero blog-hero"><h1>Journal title</h1></section>
    <article class="post-card"><h3><a href="#">Blog title</a></h3></article>
    <section class="gravitas-video-archive__hero"><h1>Videos</h1></section>
  </body></html>`);
  await page.addScriptTag({ content: script });
  await page.evaluate(() => { delete document.documentElement.dataset.theme; });

  const read = () => page.evaluate(() => {
    const sample = selector => {
      const element = document.querySelector(selector);
      const style = getComputedStyle(element);
      const container = selector.includes(' h1') || selector.includes(' a') ? element.closest('section, article') : element;
      const containerStyle = getComputedStyle(container);
      return { color: style.color, background: containerStyle.backgroundColor, backgroundImage: containerStyle.backgroundImage };
    };
    return {
      theme: document.documentElement.dataset.theme || 'light',
      rootInk: getComputedStyle(document.documentElement).getPropertyValue('--color-ink').trim(),
      lightLogo: getComputedStyle(document.querySelector('.site-logo--light')).display,
      darkLogo: getComputedStyle(document.querySelector('.site-logo--dark')).display,
      journal: sample('.blog-hero h1'),
      blog: sample('.post-card h3 a'),
      videos: sample('.gravitas-video-archive__hero h1')
    };
  });

  const light = await read();
  await page.locator('[data-theme-toggle]').click();
  await page.waitForTimeout(500);
  const dark = await read();
  if (light.theme !== 'light' || dark.theme !== 'dark') throw new Error('Theme toggle did not switch from light to dark.');
  if (light.lightLogo === 'none' || light.darkLogo !== 'none' || dark.lightLogo !== 'none' || dark.darkLogo === 'none') throw new Error('Header logos did not switch with the theme.');
  for (const [mode, values] of Object.entries({ light, dark })) {
    for (const key of ['journal', 'blog']) {
      const ratio = contrast(values[key].color, values[key].background);
      if (ratio < 4.5) throw new Error(`${mode} ${key} contrast is ${ratio.toFixed(2)}:1 (${values[key].color} on ${values[key].background}; --color-ink ${values.rootInk})`);
    }
  }
  if (light.videos.backgroundImage === dark.videos.backgroundImage) throw new Error('Video hero background did not change with the theme.');
  if (contrast(light.videos.color, 'rgb(237, 244, 241)') < 4.5 || contrast(dark.videos.color, 'rgb(9, 19, 33)') < 4.5) {
    throw new Error('Video title contrast failed against the configured hero gradients.');
  }
  console.log('Journal, blog-card, and video-title contrast passed in light and dark modes.');
  await browser.close();
})().catch(error => { console.error(error); process.exit(1); });
