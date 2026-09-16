<link rel="stylesheet" href="/static/admin/case-editor.css?v=1">
<section class="case-editor" data-upload-url="<?= $this->e($uploadUrl) ?>">
    <strong>案例正文</strong>
    <p class="muted">输入或粘贴文章，在需要配图的位置点击正文，再选择“插入图片”。图片说明可直接在图下修改；封面单独设置。</p>
    <div class="case-toolbar" role="toolbar" aria-label="正文格式">
        <button type="button" data-command="formatBlock" data-value="p">段落</button>
        <button type="button" data-command="formatBlock" data-value="h2">小标题</button>
        <button type="button" data-command="formatBlock" data-value="h3">次级标题</button>
        <button type="button" data-command="bold">加粗</button>
        <button type="button" data-command="insertUnorderedList">项目列表</button>
        <button type="button" data-command="insertOrderedList">编号列表</button>
        <button type="button" data-action="image">插入图片</button>
        <button type="button" data-command="undo">撤销</button>
        <button type="button" data-command="redo">重做</button>
        <button type="button" data-action="source" aria-pressed="false">HTML 编辑</button>
        <button type="button" data-action="preview" aria-pressed="false">预览</button>
    </div>
    <textarea name="content" class="case-source" rows="18" aria-label="案例正文 HTML"><?= $this->e($item['content'] ?? '') ?></textarea>
    <iframe class="case-canvas" title="案例正文可视化编辑" sandbox="allow-same-origin" hidden></iframe>
    <div class="case-image-panel" hidden>
        <label>正文图片<input type="file" class="case-image-file" accept="image/jpeg,image/png,image/gif,image/webp"></label>
        <label>图片说明<input type="text" class="case-image-caption" placeholder="例如：搬运人员配合将家具通过门框"></label>
        <button type="button" data-action="upload">上传并插入</button>
        <button type="button" data-action="cancel-image">取消</button>
    </div>
    <p class="case-editor-status" role="status" aria-live="polite"></p>
</section>
<script src="/static/admin/case-editor.js?v=1" defer></script>
