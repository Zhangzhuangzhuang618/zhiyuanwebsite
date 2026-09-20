// Start: php -S 127.0.0.1:8771 -t public public/router.php
// Run: node tests/SiteSyncTest.cjs [base URL]
const assert = require('node:assert/strict');
const fs = require('node:fs');
const base = process.argv[2] || 'http://127.0.0.1:8771';
async function get(path) {
  const response = await fetch(base + path);
  assert.equal(response.status, 200, path);
  const html = await response.text();
  // Existing clearly labelled external friendship link is intentionally preserved.
  const own = html.replace(/<p class="beian">友情链接：[\s\S]*?<\/p>/g, '');
  assert(!/众人|zhongren|zrbanjia|18148943200|400-?837-?2383|Fatal error|Warning:/.test(own), 'brand leakage: ' + path);
  const schemas = [...html.matchAll(/<script type="application\/ld\+json">(.*?)<\/script>/gs)].map(m => JSON.parse(m[1]));
  if (html.includes('<html')) {
    assert(html.includes('www.zhiyuanbj.cn'), 'canonical domain: ' + path);
    assert(html.includes('02085627757'), 'telephone: ' + path);
    assert(schemas.some(s => s['@type'] === 'Organization' && s.name === '广州志远搬家服务有限公司'));
  }
  return {html, schemas};
}
(async () => {
  for (const path of ['/', '/about/13.html', '/pricing.html', '/about/19.html', '/faq.html', '/detail/products15.html', '/products/2.html', '/detail/products2.html', '/contact/8.html', '/cases/6.html', '/cases/9.html', '/cases/10.html', '/llms.txt', '/sitemap.xml']) await get(path);
  const {html: cases} = await get('/cases/6.html');
  assert(cases.includes('href="/cases/6.html" aria-current="page">全部案例'));
  const links = [...cases.matchAll(/href="(\/detail_cases\d+\.html)"/g)].map(m=>m[1]);
  assert(links.length > 0, 'case links');
  const {schemas: caseSchemas} = await get(links[0]);
  assert(caseSchemas.some(s=>s['@type']==='Article' && s.headline.startsWith('志远搬家案例：')));
  const {html: faq, schemas: faqSchemas} = await get('/faq.html');
  const questions = faqSchemas.find(s=>s['@type']==='FAQPage').mainEntity;
  assert.equal(questions.length, 38);
  for (const question of questions) assert(faq.includes(question.name) && faq.includes(question.acceptedAnswer.text), 'FAQ schema matches visible answer');
  const {html: japanese, schemas: services} = await get('/detail/products15.html');
  assert(japanese.includes('280元/立方米，5立方米起') && japanese.includes('320元/立方米，10立方米起'));
  assert(!japanese.includes('打包收纳与整理实拍'), 'do not attribute another company photos');
  assert(services.some(s=>s['@type']==='Service'));
  const {html: sitemap} = await get('/sitemap.xml');
  for(const path of ['/pricing.html', '/faq.html', '/detail/products15.html', '/cases/6.html']) assert(sitemap.includes('https://www.zhiyuanbj.cn'+path));
  const llms = fs.readFileSync('public/llms.txt');
  new TextDecoder('utf-8', {fatal:true}).decode(llms);
  assert(!llms.toString().includes('\ufffd'), 'no replacement characters');
  console.log('PASS: 14 routes, brand/phone/canonical isolation, cases, Article/Service/FAQ schemas, sitemap and UTF-8 llms');
})().catch(error=>{console.error(error);process.exitCode=1;});
