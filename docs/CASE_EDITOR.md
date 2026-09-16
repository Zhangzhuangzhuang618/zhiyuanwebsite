# 服务案例图文编辑

后台「服务案例 → 新增／编辑」支持可视化正文编辑。数据库继续使用原有 `content` 字段，无需迁移。

## 使用

1. 填写标题、分类和摘要；封面仍在「上传封面图片」中单独选择。
2. 在正文区域输入或粘贴文章。粘贴按纯文本处理，避免带入其他网站或 Word 的杂乱样式。
3. 将光标放进需要设置格式的段落，使用「小标题」「次级标题」「加粗」「项目列表」「编号列表」。
4. 将光标放在需要配图的位置，点击「插入图片」，选择本地图片并填写图片说明，再点击「上传并插入」。
5. 图注可以直接在图片下修改；可用撤销、重做，或通过 HTML 编辑调整结构和图片位置。
6. 点击「预览」查看正文排版；再次点击返回编辑。预览使用前台案例正文样式，不包含网站页头和侧栏。
7. 选择草稿或发布，然后保存。上传图片本身不等于保存案例；上传中会阻止提交，失败后可重试，正文不会刷新丢失。

支持 JPG、PNG、GIF、WEBP，大小沿用后台上传配置。新上传的正文图片使用 `figure / img / figcaption` 保存，前台按内容宽度缩放，保留图片比例。

保留 HTML 编辑入口以及无 JavaScript 时的文本框。旧案例仅打开并保存不会被编辑器自动改写。前台兼容旧的全实体编码正文，并避免对新正文二次解码而破坏图注中的特殊字符。

## 验证

PHP 语法检查及已有测试：

```sh
php -l app/controller/Admin.php
php -l app/core/App.php
php -l app/view/admin/case_editor.php
php -l app/view/cases/detail.php
node --check public/static/admin/case-editor.js
php tests/BaiduUrlPushTest.php
php tests/GeoNewsPublisherTest.php
php tests/GeoPublishHttpTest.php
```

浏览器回归测试需要 PHP SQLite、Node、Playwright 和 Google Chrome。Playwright 仅作为测试工具，不是网站运行依赖：

```sh
PLAYWRIGHT_MODULE=/absolute/path/to/playwright node tests/AdminCaseEditorTest.cjs
```

测试自动创建临时 SQLite、上传目录及本地 PHP 服务，结束后清理；不读取生产配置，不连接实际业务数据库，也不推送测试内容到搜索平台。覆盖登录与 CSRF、伪造图片拒绝、上传失败重试、光标插图、图注编辑、预览、保存回读、旧 HTML 保留、前台特殊字符及窄屏正文布局。

## 部署

同步本次 PHP、JS、CSS 文件即可，无数据库变更。后台上传权限、登录会话和 CSRF 校验沿用原系统；正文图片上传错误以 JSON 返回。可视化编辑和预览在禁用脚本的沙箱中运行。
