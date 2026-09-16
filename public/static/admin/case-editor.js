(function () {
    'use strict';
    document.querySelectorAll('.case-editor').forEach(function (root) {
        var source = root.querySelector('.case-source');
        var frame = root.querySelector('.case-canvas');
        var form = root.closest('form');
        var panel = root.querySelector('.case-image-panel');
        var fileInput = root.querySelector('.case-image-file');
        var captionInput = root.querySelector('.case-image-caption');
        var status = root.querySelector('.case-editor-status');
        var doc, range, mode = 'visual', uploading = false, changed = false;
        var escape = function (s) {
            return s.replace(/[&<>"']/g, function (c) { return {'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]; });
        };
        function message(text, error) {
            status.textContent = text;
            status.classList.toggle('error', !!error);
        }
        function remember() {
            var selection = doc.getSelection();
            if (selection.rangeCount && doc.body.contains(selection.anchorNode)) range = selection.getRangeAt(0).cloneRange();
        }
        function restore() {
            frame.contentWindow.focus();
            if (!range || !doc.body.contains(range.startContainer)) {
                range = doc.createRange();
                range.selectNodeContents(doc.body);
                range.collapse(false);
            }
            var selection = doc.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }
        function sync() {
            // Do not rewrite legacy HTML merely by opening and saving a case.
            if (changed && mode !== 'source') source.value = doc.body.innerHTML;
        }
        function updateControls() {
            root.querySelectorAll('[data-command], [data-action="image"]').forEach(function (button) {
                button.disabled = mode !== 'visual' || uploading;
            });
            root.querySelector('[data-action="source"]').setAttribute('aria-pressed', String(mode === 'source'));
            root.querySelector('[data-action="preview"]').setAttribute('aria-pressed', String(mode === 'preview'));
            root.querySelector('[data-action="source"]').disabled = uploading;
            root.querySelector('[data-action="preview"]').disabled = uploading;
        }
        function switchMode(next) {
            sync();
            if (mode === 'source') {
                doc.body.innerHTML = source.value;
                range = null;
                changed = false;
            }
            mode = next;
            panel.hidden = true;
            source.hidden = mode !== 'source';
            frame.hidden = mode === 'source';
            doc.body.contentEditable = mode === 'visual' ? 'true' : 'false';
            updateControls();
        }
        // The sandbox disables scripts/event handlers in legacy HTML and preview.
        frame.addEventListener('load', function () {
            doc = frame.contentDocument;
            doc.body.innerHTML = source.value;
            doc.body.contentEditable = 'true';
            doc.body.setAttribute('aria-label', '案例正文');
            doc.body.addEventListener('input', function () { changed = true; sync(); remember(); });
            doc.addEventListener('selectionchange', remember);
            doc.addEventListener('click', function (event) {
                if (event.target.closest('a')) event.preventDefault();
            });
            doc.body.addEventListener('paste', function (event) {
                // Paste text only: no remote images, scripts or Word formatting.
                event.preventDefault();
                var text = event.clipboardData.getData('text/plain');
                doc.execCommand('insertHTML', false, text.split(/\r?\n/).map(function (line) {
                    return '<p>' + (escape(line) || '<br>') + '</p>';
                }).join(''));
                changed = true; sync(); remember();
            });
            source.hidden = true;
            frame.hidden = false;
            updateControls();
        }, {once: true});
        frame.srcdoc = '<!doctype html><html lang="zh-CN"><head><meta charset="utf-8">'
            + '<meta http-equiv="Content-Security-Policy" content="default-src \'none\'; img-src \'self\' https: http: data:; style-src \'self\' \'unsafe-inline\'; form-action \'none\'">'
            + '<link rel="stylesheet" href="/static/home/css/case-article.css">'
            + '<style>body{font:16px/1.9 sans-serif;margin:0;padding:20px;min-height:480px;outline:none;box-sizing:border-box}</style>'
            + '</head><body class="case-article-body"></body></html>';
        root.querySelector('.case-toolbar').addEventListener('mousedown', function (event) {
            if (event.target.closest('button')) event.preventDefault();
        });
        root.addEventListener('click', async function (event) {
            var button = event.target.closest('button');
            if (!button || !doc) return;
            var command = button.dataset.command;
            var action = button.dataset.action;
            if (command) {
                restore();
                doc.execCommand(command, false, button.dataset.value || null);
                changed = true; sync(); remember();
            } else if (action === 'source') switchMode(mode === 'source' ? 'visual' : 'source');
            else if (action === 'preview') switchMode(mode === 'preview' ? 'visual' : 'preview');
            else if (action === 'image') { remember(); panel.hidden = false; fileInput.focus(); }
            else if (action === 'cancel-image') panel.hidden = true;
            else if (action === 'upload') {
                var file = fileInput.files[0];
                if (!file) { message('请先选择图片。', true); return; }
                var data = new FormData();
                data.append('image', file);
                data.append('_csrf', form.querySelector('[name="_csrf"]').value);
                uploading = true;
                button.disabled = true;
                doc.body.contentEditable = 'false';
                updateControls();
                message('图片上传中，请稍候…');
                try {
                    var response = await fetch(root.dataset.uploadUrl, {method:'POST', body:data, credentials:'same-origin'});
                    var result = await response.json();
                    if (!response.ok || !result.url) throw new Error(result.error || '图片上传失败，请重试。');
                    if (!/^\/upload\/[a-zA-Z0-9/_.-]+$/.test(result.url)) throw new Error('图片地址无效。');
                    var caption = captionInput.value.trim();
                    doc.body.contentEditable = 'true';
                    restore();
                    doc.execCommand('insertHTML', false, '<figure><img src="' + escape(result.url) + '" alt="' + escape(caption) + '">'
                        + '<figcaption>' + (escape(caption) || '在此填写图片说明') + '</figcaption></figure><p><br></p>');
                    changed = true; sync(); remember();
                    fileInput.value = ''; captionInput.value = ''; panel.hidden = true;
                    message('图片已插入。请保存案例，图片位置和说明才会生效。');
                } catch (error) { message(error.message || '上传失败，请检查网络后重试。正文仍保留。', true); }
                finally { uploading = false; button.disabled = false; doc.body.contentEditable = 'true'; updateControls(); }
            }
        });
        form.addEventListener('submit', function (event) {
            if (uploading) { event.preventDefault(); message('图片仍在上传，请完成后保存。', true); return; }
            if (doc) sync();
        });
    });
}());
