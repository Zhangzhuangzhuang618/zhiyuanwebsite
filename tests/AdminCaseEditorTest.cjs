// PLAYWRIGHT_MODULE=/path/to/playwright node tests/AdminCaseEditorTest.cjs
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const { spawn } = require('node:child_process');
const { mkdtempSync, rmSync } = require('node:fs');
const { tmpdir } = require('node:os');
const path = require('node:path');
const assert = require('node:assert/strict');
const net = require('node:net');
(async () => {
  const directory = mkdtempSync(path.join(tmpdir(), 'case-editor-test-'));
  const port = await new Promise(resolve => { const s = net.createServer(); s.listen(0, '127.0.0.1', () => { const p = s.address().port; s.close(() => resolve(p)); }); });
  const root = path.resolve(__dirname, '..');
  const server = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', 'public', 'tests/AdminCaseEditorRouter.php'], { cwd: root, env: {...process.env, CASE_EDITOR_TEST_DIR: directory}, stdio:'ignore' });
  let browser;
  try {
    const base = `http://127.0.0.1:${port}`;
    let ready = false;
    for (let i=0;i<50;i++) { try { await fetch(base+'/test-admin/login'); ready=true; break; } catch { await new Promise(r=>setTimeout(r,100)); } }
    assert(ready, 'test server starts');
    browser = await chromium.launch({channel:'chrome', headless:true});
    const context = await browser.newContext({viewport:{width:1280,height:1000}});
    const page = await context.newPage();
    const errors = []; page.on('pageerror',e=>errors.push(e.message));
    let response = await context.request.post(base+'/test-admin/caseImageUpload');
    assert.equal(response.status(),401);
    await page.goto(base+'/test-admin/login');
    await page.locator('[name=username]').fill('test');
    await page.locator('[name=password]').fill('test-password');
    await page.locator('button[type=submit]').click();
    await page.goto(base+'/test-admin/caseEdit?id=1');
    const canvas = page.frameLocator('.case-canvas');
    await canvas.locator('body[contenteditable=true]').waitFor();
    const legacy = await page.locator('[name=content]').inputValue();
    await page.getByRole('button',{name:'保存服务案例',exact:true}).click();
    await page.goto(base+'/test-admin/caseEdit?id=1');
    await canvas.locator('body[contenteditable=true]').waitFor();
    assert.equal(await page.locator('[name=content]').inputValue(),legacy,'legacy HTML unchanged');
    const csrf = await page.locator('form.edit-form [name=_csrf]').inputValue();
    response = await context.request.post(base+'/test-admin/caseImageUpload',{multipart:{_csrf:'bad'}});
    assert.equal(response.status(),403);
    response = await context.request.post(base+'/test-admin/caseImageUpload',{multipart:{_csrf:csrf,image:{name:'fake.png',mimeType:'image/png',buffer:Buffer.from('not an image')}}});
    assert.equal(response.status(),422);
    await page.locator('[data-action=source]').click();
    await page.locator('[name=content]').fill('<h2>家具防护</h2><p>图片之前的段落</p><p>图片之后的段落</p>');
    await page.locator('[data-action=source]').click();
    await canvas.locator('p').first().evaluate(el=>{
      const d=el.ownerDocument,r=d.createRange();r.selectNodeContents(el);r.collapse(false);const s=d.getSelection();s.removeAllRanges();s.addRange(r);el.focus();
    });
    await page.locator('[data-action=image]').click();
    // Failed upload keeps the text and permits retry without a page reload.
    await page.locator('.case-image-file').setInputFiles({name:'bad.png',mimeType:'image/png',buffer:Buffer.from('invalid')});
    await page.locator('[data-action=upload]').click();
    await page.locator('.case-editor-status.error').waitFor();
    assert((await page.locator('[name=content]').inputValue()).includes('图片之后的段落'));
    await page.locator('.case-image-file').setInputFiles(path.join(root,'public/static/home/images/icon_line.jpg'));
    await page.locator('.case-image-caption').fill('门框搬运 & 家具防护');
    await page.locator('[data-action=upload]').click();
    await canvas.locator('figure img').waitFor();
    const html = await page.locator('[name=content]').inputValue();
    assert(html.indexOf('图片之前')<html.indexOf('<figure'));
    assert(html.indexOf('<figure')<html.indexOf('图片之后'),'image stays at cursor');
    assert.equal(await canvas.locator('figcaption').innerText(),'门框搬运 & 家具防护');
    assert.equal(await canvas.locator('figure img').getAttribute('alt'),'门框搬运 & 家具防护');
    await canvas.locator('figcaption').fill('修改后的图注 <现场> & 说明');
    await page.locator('[data-action=preview]').click();
    assert.equal(await canvas.locator('body').getAttribute('contenteditable'),'false');
    assert((await page.locator('[name=content]').inputValue()).includes('修改后的图注 &lt;现场&gt; &amp; 说明'));
    await page.locator('[data-action=preview]').click();
    await page.getByRole('button',{name:'保存服务案例',exact:true}).click();
    await page.goto(base+'/test-admin/caseEdit?id=1');
    await canvas.locator('figure img').waitFor();
    assert.equal(await canvas.locator('figcaption').innerText(),'修改后的图注 <现场> & 说明');
    const imageUrl=await canvas.locator('figure img').getAttribute('src');
    assert.equal((await context.request.get(base+imageUrl)).status(),200);
    await page.setViewportSize({width:390,height:844});
    assert(await canvas.locator('body').evaluate(el=>el.scrollWidth<=el.clientWidth),'mobile article fits');
    if(process.env.CASE_EDITOR_SCREENSHOT) { await page.locator('.case-editor').scrollIntoViewIfNeeded(); await page.screenshot({path:process.env.CASE_EDITOR_SCREENSHOT,fullPage:true}); }
    const publicPage = await context.newPage();
    await publicPage.goto(base+'/test-detail');
    assert.equal(await publicPage.locator('.case-article-body figcaption').innerText(),'修改后的图注 <现场> & 说明');
    assert.equal(await publicPage.locator('.case-article-body figure img').count(),1);
    await publicPage.close();
    // Source-mode changes must also survive preview and save; scripts cannot run in the editor.
    await page.locator('[data-action=source]').click();
    await page.locator('[name=content]').fill('<p>源代码修改</p><img src="x" onerror="parent.document.title=\'UNSAFE\'">');
    await page.locator('[data-action=preview]').click();
    assert.equal(await canvas.locator('p').innerText(),'源代码修改');
    assert.notEqual(await page.title(),'UNSAFE');
    assert.equal(errors.length,0,errors.join('\n'));
    console.log('PASS: login/CSRF, invalid image rejection, failed-upload recovery, cursor insertion, captions, preview, persistence, legacy HTML, mobile layout, sandbox');
  } finally {
    if(browser) await browser.close();
    server.kill();
    await new Promise(resolve=>server.once('exit',resolve));
    rmSync(directory,{recursive:true,force:true});
  }
})().catch(e=>{console.error(e);process.exitCode=1;});
