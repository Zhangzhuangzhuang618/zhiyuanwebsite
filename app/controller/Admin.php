<?php
namespace app\controller;

use app\model\Database;

/**
 * 最小内容后台：基于当前 SQLite 表结构管理留言、新闻、案例、站点配置和上传文件。
 * 后台入口由 app.config.admin_path 决定，所有写操作使用 Session + CSRF 校验。
 */
class Admin extends BaseController
{
    private $pdo;
    private $path;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->pdo = Database::getInstance();
        $this->path = '/' . trim((string)($config['app']['admin_path'] ?? 'webadmini'), '/');
        $this->startSession();
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirectTo('/dashboard');
        }

        $error = '';
        if ($this->isPost()) {
            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $admin = $this->config['admin'] ?? [];
            $validUser = isset($admin['username']) && hash_equals((string)$admin['username'], $username);
            $validPassword = !empty($admin['password_hash']) && password_verify($password, $admin['password_hash']);
            if ($validUser && $validPassword) {
                session_regenerate_id(true);
                $_SESSION['zhiyuan_admin'] = true;
                $_SESSION['zhiyuan_admin_name'] = $username;
                $this->redirectTo('/dashboard');
            }
            $error = '账号或密码错误。';
        }

        $notice = $this->flash();
        $body = '<div class="login-card"><h1>志远搬家后台</h1><p>内容与线索管理</p>'
            . ($notice ? '<div class="notice success">' . $this->e($notice) . '</div>' : '')
            . ($error ? '<div class="notice error">' . $this->e($error) . '</div>' : '')
            . '<form method="post" action="' . $this->url('/login') . '">'
            . '<label>账号<input name="username" autocomplete="username" required autofocus></label>'
            . '<label>密码<input type="password" name="password" autocomplete="current-password" required></label>'
            . '<button class="primary" type="submit">登录后台</button></form></div>';
        $this->page('后台登录', $body, false);
    }

    public function logout()
    {
        $this->requireLogin();
        $this->requirePost();
        $this->verifyCsrf();
        $_SESSION = [];
        session_destroy();
        $this->startSession();
        $this->setFlash('已安全退出。');
        $this->redirectTo('/login');
    }

    public function dashboard()
    {
        $this->requireLogin();
        $stats = [
            '待处理留言' => (int)$this->pdo->query('SELECT COUNT(*) FROM ' . $this->table('cms_message') . ' WHERE status = 0')->fetchColumn(),
            '新闻资讯' => (int)$this->pdo->query('SELECT COUNT(*) FROM ' . $this->table('cms_article') . ' WHERE status = 1 AND delete_time IS NULL')->fetchColumn(),
            '服务案例' => (int)$this->pdo->query('SELECT COUNT(*) FROM ' . $this->table('cms_cases') . ' WHERE status = 1 AND delete_time IS NULL')->fetchColumn(),
            '上传目录' => is_dir(UPLOAD_PATH) ? '可写' : '不存在',
        ];
        $cards = '';
        foreach ($stats as $label => $value) {
            $cards .= '<div class="stat"><strong>' . $this->e((string)$value) . '</strong><span>' . $this->e($label) . '</span></div>';
        }
        $messages = $this->pdo->query('SELECT id, title, phone, create_time FROM ' . $this->table('cms_message') . ' WHERE status = 0 ORDER BY id DESC LIMIT 8')->fetchAll();
        $rows = '';
        foreach ($messages as $message) {
            $rows .= '<tr><td>#' . (int)$message['id'] . '</td><td>' . $this->e($message['title']) . '</td><td>' . $this->e($message['phone']) . '</td><td>' . $this->e($this->formatTime($message['create_time'])) . '</td></tr>';
        }
        if ($rows === '') $rows = '<tr><td colspan="4" class="muted">暂无待处理留言。</td></tr>';
        $body = '<h1>工作台</h1><div class="stats">' . $cards . '</div><section class="panel"><div class="panel-head"><h2>待处理留言</h2><a href="' . $this->url('/messages') . '">查看全部</a></div><table><thead><tr><th>ID</th><th>主题</th><th>电话</th><th>提交时间</th></tr></thead><tbody>' . $rows . '</tbody></table></section>';
        $this->page('工作台', $body);
    }

    public function messages()
    {
        $this->requireLogin();
        $rows = $this->pdo->query('SELECT id, title, phone, email, content, ip, status, create_time FROM ' . $this->table('cms_message') . ' WHERE delete_time IS NULL ORDER BY id DESC LIMIT 100')->fetchAll();
        $html = '';
        foreach ($rows as $row) {
            $state = (int)$row['status'] === 1 ? '<span class="badge done">已处理</span>' : '<span class="badge pending">待处理</span>';
            $html .= '<tr><td>#' . (int)$row['id'] . '</td><td><strong>' . $this->e($row['title']) . '</strong><br><small>' . $this->e($row['phone']) . ($row['email'] ? ' · ' . $this->e($row['email']) : '') . '</small></td><td class="pre">' . $this->e($row['content']) . '</td><td>' . $state . '<br><small>' . $this->e($this->formatTime($row['create_time'])) . '<br>' . $this->e($row['ip']) . '</small></td><td>'
                . $this->postButton('/messageStatus', '标为' . ((int)$row['status'] === 1 ? '待处理' : '已处理'), ['id' => $row['id'], 'status' => (int)$row['status'] === 1 ? 0 : 1], 'secondary')
                . $this->postButton('/messageDelete', '删除', ['id' => $row['id']], 'danger', '确认删除这条留言？') . '</td></tr>';
        }
        if ($html === '') $html = '<tr><td colspan="5" class="muted">暂无留言。</td></tr>';
        $body = '<div class="title-row"><h1>留言与报价线索</h1><span class="muted">最多显示最近 100 条</span></div><section class="panel"><table><thead><tr><th>ID</th><th>客户</th><th>内容</th><th>状态</th><th>操作</th></tr></thead><tbody>' . $html . '</tbody></table></section>';
        $this->page('留言管理', $body);
    }

    public function messageStatus()
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $stmt = $this->pdo->prepare('UPDATE ' . $this->table('cms_message') . ' SET status = ?, update_time = ? WHERE id = ?');
        $stmt->execute([(int)($_POST['status'] ?? 0), time(), (int)($_POST['id'] ?? 0)]);
        $this->setFlash('留言状态已更新。'); $this->redirectTo('/messages');
    }

    public function messageDelete()
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $stmt = $this->pdo->prepare('UPDATE ' . $this->table('cms_message') . ' SET delete_time = ?, status = 0 WHERE id = ?');
        $stmt->execute([time(), (int)($_POST['id'] ?? 0)]);
        $this->setFlash('留言已删除。'); $this->redirectTo('/messages');
    }

    public function articles() { $this->contentList('article'); }
    public function articleEdit() { $this->contentEdit('article'); }
    public function articleSave() { $this->contentSave('article'); }
    public function articleDelete() { $this->contentDelete('article'); }
    public function casesManage() { $this->contentList('case'); }
    public function caseEdit() { $this->contentEdit('case'); }
    public function caseSave() { $this->contentSave('case'); }
    public function caseDelete() { $this->contentDelete('case'); }

    public function settings()
    {
        $this->requireLogin();
        $rows = $this->pdo->query("SELECT name, value, remark FROM " . $this->table('system_config') . " WHERE origin = 'web' ORDER BY sort ASC, id ASC")->fetchAll();
        $fields = '';
        foreach ($rows as $row) {
            $fields .= '<label><span>' . $this->e($row['remark'] ?: $row['name']) . '<small>' . $this->e($row['name']) . '</small></span><textarea name="config[' . $this->e($row['name']) . ']" rows="2">' . $this->e($row['value']) . '</textarea></label>';
        }
        $body = '<div class="title-row"><h1>站点基础配置</h1><span class="muted">保存后自动刷新前台配置缓存</span></div><form method="post" action="' . $this->url('/settingsSave') . '" class="panel edit-form">' . $this->csrfInput() . $fields . '<div class="form-actions"><button class="primary">保存配置</button></div></form>';
        $this->page('站点配置', $body);
    }

    public function settingsSave()
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $allowed = $this->pdo->query("SELECT name FROM " . $this->table('system_config') . " WHERE origin = 'web'")->fetchAll(\PDO::FETCH_COLUMN);
        $input = $_POST['config'] ?? [];
        $stmt = $this->pdo->prepare('UPDATE ' . $this->table('system_config') . ' SET value = ?, update_time = ? WHERE name = ? AND origin = ?');
        foreach ($allowed as $name) {
            if (array_key_exists($name, $input)) $stmt->execute([(string)$input[$name], time(), $name, 'web']);
        }
        $this->clearConfigCache(); $this->setFlash('站点配置已保存。'); $this->redirectTo('/settings');
    }

    public function upload()
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $returnTo = (string)($_POST['return_to'] ?? '/articles');
        $path = $this->storeImageUpload('image', $returnTo);
        if (!$path) $this->fail('请选择一个有效文件。', $returnTo);
        $this->setFlash('上传完成，图片地址：' . $path); $this->redirectTo($returnTo);
    }

    private function contentList($kind)
    {
        $this->requireLogin();
        $table = $kind === 'article' ? 'cms_article' : 'cms_cases';
        $label = $kind === 'article' ? '新闻资讯' : '服务案例';
        $edit = $kind === 'article' ? '/articleEdit' : '/caseEdit';
        $delete = $kind === 'article' ? '/articleDelete' : '/caseDelete';
        $rows = $this->pdo->query('SELECT id, title, nav_id, image, status, sort, create_time, update_time FROM ' . $this->table($table) . ' WHERE delete_time IS NULL ORDER BY id DESC LIMIT 100')->fetchAll();
        $html = '';
        foreach ($rows as $row) {
            $image = $row['image'] ? '<img class="thumb" src="' . $this->e($row['image']) . '" alt="">' : '<span class="muted">无图</span>';
            $status = (int)$row['status'] === 1 ? '<span class="badge done">已发布</span>' : '<span class="badge pending">未发布</span>';
            $html .= '<tr><td>#' . (int)$row['id'] . '</td><td>' . $image . '</td><td><strong>' . $this->e($row['title']) . '</strong><br><small>栏目 ' . (int)$row['nav_id'] . ' · 排序 ' . (int)$row['sort'] . '</small></td><td>' . $status . '<br><small>' . $this->e($this->formatTime($row['update_time'] ?: $row['create_time'])) . '</small></td><td><a class="button secondary" href="' . $this->url($edit . '?id=' . (int)$row['id']) . '">编辑</a>' . $this->postButton($delete, '删除', ['id' => $row['id']], 'danger', '确认删除？') . '</td></tr>';
        }
        if ($html === '') $html = '<tr><td colspan="5" class="muted">暂无内容。</td></tr>';
        $body = '<div class="title-row"><h1>' . $label . '</h1><a class="button primary" href="' . $this->url($edit) . '">新增' . $label . '</a></div><section class="panel"><table><thead><tr><th>ID</th><th>封面</th><th>标题</th><th>状态</th><th>操作</th></tr></thead><tbody>' . $html . '</tbody></table></section>';
        $this->page($label, $body);
    }

    private function contentEdit($kind)
    {
        $this->requireLogin();
        $table = $kind === 'article' ? 'cms_article' : 'cms_cases';
        $label = $kind === 'article' ? '新闻资讯' : '服务案例';
        $id = (int)($_GET['id'] ?? 0);
        $item = $id ? $this->find($table, $id) : [];
        if ($id && !$item) { $this->setFlash('内容不存在。'); $this->redirectTo($kind === 'article' ? '/articles' : '/casesManage'); }
        $navs = $this->pdo->query('SELECT id, title FROM ' . $this->table('cms_nav') . ' ORDER BY sort ASC, id ASC')->fetchAll();
        $options = '<option value="0">未分类</option>';
        foreach ($navs as $nav) $options .= '<option value="' . (int)$nav['id'] . '"' . ((int)($item['nav_id'] ?? 0) === (int)$nav['id'] ? ' selected' : '') . '>' . $this->e($nav['title']) . '（' . (int)$nav['id'] . '）</option>';
        $save = $kind === 'article' ? '/articleSave' : '/caseSave';
        $back = $kind === 'article' ? '/articles' : '/casesManage';
        $image = (string)($item['image'] ?? '');
        $body = '<div class="title-row"><h1>' . ($id ? '编辑' : '新增') . $label . '</h1><a class="button secondary" href="' . $this->url($back) . '">返回列表</a></div>'
            . '<form method="post" enctype="multipart/form-data" action="' . $this->url($save) . '" class="panel edit-form">' . $this->csrfInput() . '<input type="hidden" name="id" value="' . $id . '">'
            . '<label>标题<input name="title" required value="' . $this->e($item['title'] ?? '') . '"></label><label>所属栏目<select name="nav_id">' . $options . '</select></label><label>摘要<textarea name="sketch" rows="3">' . $this->e($item['sketch'] ?? '') . '</textarea></label><label>封面图片地址<input name="image" value="' . $this->e($image) . '" placeholder="/upload/日期/文件名.jpg"></label><label>上传封面图片<input type="file" name="cover_image" accept="image/jpeg,image/png,image/gif,image/webp"><small>选择图片后保存，系统会自动上传并关联到当前' . $label . '；留空则保留上方图片地址。</small></label>'
            . ($image ? '<p><img class="preview" src="' . $this->e($image) . '" alt="当前封面"></p>' : '')
            . '<label>正文（可填写 HTML）<textarea name="content" rows="16">' . $this->e($item['content'] ?? '') . '</textarea></label><div class="two-cols"><label>SEO 标题<input name="seo_title" value="' . $this->e($item['seo_title'] ?? '') . '"></label><label>SEO 关键词<input name="seo_keyword" value="' . $this->e($item['seo_keyword'] ?? '') . '"></label></div><label>SEO 描述<textarea name="seo_content" rows="3">' . $this->e($item['seo_content'] ?? '') . '</textarea></label><div class="two-cols"><label>排序<input type="number" name="sort" value="' . (int)($item['sort'] ?? 0) . '"></label><label>发布状态<select name="status"><option value="1"' . ((int)($item['status'] ?? 1) === 1 ? ' selected' : '') . '>发布</option><option value="0"' . ((int)($item['status'] ?? 1) === 0 ? ' selected' : '') . '>草稿</option></select></label></div><div class="form-actions"><button class="primary">保存' . $label . '</button></div></form>';
        $this->page($label . '编辑', $body);
    }

    private function contentSave($kind)
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $table = $kind === 'article' ? 'cms_article' : 'cms_cases';
        $back = $kind === 'article' ? '/articles' : '/casesManage';
        $title = trim((string)($_POST['title'] ?? ''));
        if ($title === '') $this->fail('标题不能为空。', $back);
        $id = (int)($_POST['id'] ?? 0); $now = time();
        $uploadedImage = $this->storeImageUpload('cover_image', $back);
        $data = [
            'title' => $title, 'nav_id' => (int)($_POST['nav_id'] ?? 0), 'sketch' => trim((string)($_POST['sketch'] ?? '')),
            'image' => $uploadedImage ?: trim((string)($_POST['image'] ?? '')), 'content' => (string)($_POST['content'] ?? ''),
            'seo_title' => trim((string)($_POST['seo_title'] ?? '')), 'seo_keyword' => trim((string)($_POST['seo_keyword'] ?? '')),
            'seo_content' => trim((string)($_POST['seo_content'] ?? '')), 'sort' => (int)($_POST['sort'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1), 'state' => 1, 'states' => 1, 'update_time' => $now,
        ];
        if ($id) {
            $this->update($table, $id, $data);
        } else {
            // 旧库的 id 不是自增主键，新增内容必须显式分配 ID，否则前台无法生成详情链接。
            $data['id'] = (int)$this->pdo->query('SELECT COALESCE(MAX(id), 0) + 1 FROM ' . $this->table($table))->fetchColumn();
            $data['create_time'] = $now; $data['delete_time'] = null; $data['browse'] = 0; $data['lang'] = 'zh-cn';
            $this->insert($table, $data);
        }
        $this->clearContentCache(); $this->setFlash('内容已保存。'); $this->redirectTo($back);
    }

    private function contentDelete($kind)
    {
        $this->requireLogin(); $this->requirePost(); $this->verifyCsrf();
        $table = $kind === 'article' ? 'cms_article' : 'cms_cases';
        $back = $kind === 'article' ? '/articles' : '/casesManage';
        $this->update($table, (int)($_POST['id'] ?? 0), ['status' => 0, 'delete_time' => time(), 'update_time' => time()]);
        $this->clearContentCache(); $this->setFlash('内容已删除。'); $this->redirectTo($back);
    }

    private function page($title, $body, $withNav = true)
    {
        $flash = $this->flash();
        $nav = '';
        if ($withNav) {
            $nav = '<aside><a class="brand" href="' . $this->url('/dashboard') . '">志远搬家后台</a><a href="' . $this->url('/dashboard') . '">工作台</a><a href="' . $this->url('/messages') . '">留言线索</a><a href="' . $this->url('/articles') . '">新闻资讯</a><a href="' . $this->url('/casesManage') . '">服务案例</a><a href="' . $this->url('/settings') . '">站点配置</a><form method="post" action="' . $this->url('/logout') . '">' . $this->csrfInput() . '<button class="logout">退出登录</button></form></aside>';
        }
        $notice = $flash ? '<div class="notice success">' . $this->e($flash) . '</div>' : '';
        echo '<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $this->e($title) . ' - 志远搬家后台</title><style>' . $this->styles() . '</style></head><body class="' . ($withNav ? '' : 'login') . '">' . $nav . '<main>' . $notice . $body . '</main></body></html>';
    }

    private function styles()
    {
        return '*,*:before,*:after{box-sizing:border-box}body{margin:0;background:#f5f7fa;color:#263238;font:14px/1.55 "Microsoft YaHei",Arial,sans-serif}body:not(.login){display:flex}aside{position:fixed;width:220px;min-height:100vh;background:#18232f;padding:18px 12px;color:#d9e1e8}aside a{display:block;color:#d9e1e8;text-decoration:none;padding:10px 12px;border-radius:6px;margin:3px 0}aside a:hover{background:#25394d}.brand{font-size:17px;font-weight:700;color:#fff!important;margin-bottom:16px!important}main{width:calc(100% - 220px);margin-left:220px;max-width:1500px;padding:32px}h1{margin:0 0 22px;font-size:26px}h2{font-size:17px;margin:0}.title-row,.panel-head{display:flex;justify-content:space-between;align-items:center;gap:12px}.panel{background:#fff;border:1px solid #e4e8ed;border-radius:10px;padding:20px;box-shadow:0 1px 3px #18232f0d}.panel-head{margin-bottom:14px}.stats{display:grid;grid-template-columns:repeat(4,minmax(120px,1fr));gap:14px;margin-bottom:20px}.stat{background:#fff;border-radius:10px;border:1px solid #e4e8ed;padding:18px}.stat strong{font-size:28px;color:#e5382d;display:block}.stat span,.muted,small{color:#73808c}table{width:100%;border-collapse:collapse;background:#fff}th,td{padding:10px;border:1px solid #e4e8ed;text-align:left;vertical-align:top}th{background:#eef4f8;color:#36556e}.pre{white-space:pre-wrap;max-width:420px}.thumb{width:72px;height:52px;object-fit:cover;border-radius:4px}.preview{max-width:240px;max-height:160px;border:1px solid #e4e8ed}.badge{display:inline-block;padding:2px 7px;border-radius:10px;font-size:12px}.done{background:#e6f6ed;color:#16803b}.pending{background:#fff4dd;color:#a56300}.button,button{display:inline-block;border:0;border-radius:5px;padding:8px 12px;cursor:pointer;font:inherit;text-decoration:none;margin:2px}.primary{background:#e5382d;color:#fff}.secondary{background:#edf2f5;color:#335}.danger{background:#fff0f0;color:#c9362b}.inline{display:inline}.edit-form label{display:block;margin-bottom:14px;font-weight:600}.edit-form label>span{display:block;margin-bottom:5px}.edit-form label small{display:block;font-weight:400}.edit-form input,.edit-form textarea,.edit-form select,.login-card input{display:block;width:100%;margin-top:5px;padding:9px;border:1px solid #cfd8df;border-radius:5px;font:inherit}.edit-form textarea{resize:vertical}.two-cols{display:grid;grid-template-columns:1fr 1fr;gap:16px}.form-actions{margin-top:20px;border-top:1px solid #e4e8ed;padding-top:16px}.upload{margin-top:18px}.notice{padding:10px 12px;border-radius:6px;margin-bottom:16px}.success{background:#e9f8ef;color:#19713b}.error{background:#fff0f0;color:#b52b24}.logout{background:transparent;color:#b9c7d3;padding:10px 12px}.login{display:grid;min-height:100vh;place-items:center;background:linear-gradient(135deg,#172431,#2d5a7a)}.login-card{width:min(400px,calc(100% - 32px));padding:32px;background:#fff;border-radius:12px;box-shadow:0 16px 42px #0004}.login-card h1{margin-bottom:2px}.login-card p{margin:0 0 20px;color:#73808c}.login-card label{display:block;margin:14px 0;font-weight:600}.login-card button{width:100%;margin:8px 0 0}@media(max-width:800px){aside{position:static;width:100%;min-height:0}body:not(.login){display:block}main{width:100%;margin:0;padding:16px}.stats{grid-template-columns:1fr 1fr}.two-cols{grid-template-columns:1fr}.panel{overflow:auto}table{min-width:680px}}';
    }

    private function storeImageUpload($field, $returnTo)
    {
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) $this->fail('图片上传失败。', $returnTo);
        $file = $_FILES[$field];
        $maxSize = (int)($this->config['upload']['max_size'] ?? 10485760);
        if ($file['size'] > $maxSize) $this->fail('图片超过允许大小。', $returnTo);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']); finfo_close($finfo);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) $this->fail('仅支持 JPG、PNG、GIF、WEBP 图片。', $returnTo);
        $day = date('Ymd'); $directory = rtrim(UPLOAD_PATH, '/\\') . DIRECTORY_SEPARATOR . $day;
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) $this->fail('上传目录无法创建。', $returnTo);
        $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . DIRECTORY_SEPARATOR . $name)) $this->fail('图片保存失败。', $returnTo);
        return '/upload/' . $day . '/' . $name;
    }

    private function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('zhiyuan_admin_session');
            session_start();
        }
        if (empty($_SESSION['zhiyuan_csrf'])) $_SESSION['zhiyuan_csrf'] = bin2hex(random_bytes(32));
    }
    private function isLoggedIn() { return !empty($_SESSION['zhiyuan_admin']); }
    private function requireLogin() { if (!$this->isLoggedIn()) { $this->setFlash('请先登录后台。'); $this->redirectTo('/login'); } }
    private function requirePost() { if (!$this->isPost()) { http_response_code(405); exit('仅支持 POST 请求'); } }
    private function isPost() { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
    private function verifyCsrf() { if (!hash_equals((string)($_SESSION['zhiyuan_csrf'] ?? ''), (string)($_POST['_csrf'] ?? ''))) { http_response_code(403); exit('CSRF 校验失败'); } }
    private function csrfInput() { return '<input type="hidden" name="_csrf" value="' . $this->e($_SESSION['zhiyuan_csrf']) . '">'; }
    private function url($suffix = '') { return $this->path . $suffix; }
    private function redirectTo($suffix) { header('Location: ' . $this->url($suffix)); exit; }
    private function setFlash($message) { $_SESSION['zhiyuan_admin_flash'] = $message; }
    private function flash() { $message = $_SESSION['zhiyuan_admin_flash'] ?? ''; unset($_SESSION['zhiyuan_admin_flash']); return $message; }
    private function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
    private function table($name) { return Database::table($name); }
    private function find($table, $id) { $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table($table) . ' WHERE id = ? AND delete_time IS NULL'); $stmt->execute([$id]); return $stmt->fetch(); }
    private function update($table, $id, array $data) { $sets = []; foreach ($data as $field => $value) $sets[] = '`' . $field . '` = :' . $field; $data['id'] = $id; $stmt = $this->pdo->prepare('UPDATE ' . $this->table($table) . ' SET ' . implode(', ', $sets) . ' WHERE id = :id'); $stmt->execute($data); }
    private function insert($table, array $data) { $fields = array_keys($data); $sql = 'INSERT INTO ' . $this->table($table) . ' (`' . implode('`,`', $fields) . '`) VALUES (:' . implode(',:', $fields) . ')'; $this->pdo->prepare($sql)->execute($data); }
    private function formatTime($value) { if (!$value) return '-'; return is_numeric($value) ? date('Y-m-d H:i', (int)$value) : (string)$value; }
    private function clearConfigCache() { $file = RUNTIME_PATH . 'cache/config_' . __LANG__ . '.php'; if (is_file($file)) unlink($file); }
    private function clearContentCache() { foreach (glob(RUNTIME_PATH . 'cache/*.php') ?: [] as $file) { if (strpos(basename($file), 'config_') !== 0) @unlink($file); } }
    private function fail($message, $returnTo) { $this->setFlash($message); $this->redirectTo($returnTo); }
    private function postButton($action, $label, array $fields, $class, $confirm = '') { $html = '<form class="inline" method="post" action="' . $this->url($action) . '"' . ($confirm ? ' onsubmit="return confirm(\'' . $this->e($confirm) . '\')"' : '') . '>' . $this->csrfInput(); foreach ($fields as $name => $value) $html .= '<input type="hidden" name="' . $this->e($name) . '" value="' . $this->e($value) . '">'; return $html . '<button class="' . $this->e($class) . '">' . $this->e($label) . '</button></form>'; }
}
