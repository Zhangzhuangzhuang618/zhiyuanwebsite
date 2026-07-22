<?php
$dbFile = __DIR__ . '/data/demo.sqlite';
@mkdir(__DIR__ . '/data', 0755, true);
@unlink($dbFile);

$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE zw_system_config (id INTEGER PRIMARY KEY, name TEXT, value TEXT, origin TEXT, `group` TEXT)");
$db->exec("CREATE TABLE zw_cms_nav (id INTEGER PRIMARY KEY, title TEXT, url_model TEXT, pid INTEGER, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0, is_show INTEGER DEFAULT 1, link TEXT, seo_title TEXT, seo_keyword TEXT, seo_content TEXT, image TEXT, content TEXT)");
$db->exec("CREATE TABLE zw_cms_product (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, subtitle TEXT, sketch TEXT, content TEXT, image TEXT, nav_id INTEGER, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0, is_hot INTEGER DEFAULT 0, browse INTEGER DEFAULT 0, create_time TEXT, link TEXT, target TEXT, seo_title TEXT, seo_keyword TEXT, seo_content TEXT)");
$db->exec("CREATE TABLE zw_cms_article (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, subtitle TEXT, sketch TEXT, content TEXT, image TEXT, nav_id INTEGER, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0, browse INTEGER DEFAULT 0, create_time TEXT, seo_title TEXT, seo_keyword TEXT, seo_content TEXT, nav_pid INTEGER, delete_time TEXT)");
$db->exec("CREATE TABLE zw_cms_cases (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, sketch TEXT, content TEXT, image TEXT, nav_id INTEGER, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0, create_time TEXT, seo_title TEXT, seo_keyword TEXT, seo_content TEXT)");
$db->exec("CREATE TABLE zw_cms_banner (id INTEGER PRIMARY KEY, title TEXT, image TEXT, link TEXT, position TEXT DEFAULT 'home', states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0)");
$db->exec("CREATE TABLE zw_cms_message (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, phone TEXT, email TEXT, content TEXT, type TEXT, from_url TEXT, create_time TEXT, ip TEXT)");
$db->exec("CREATE TABLE zw_cms_photo (id INTEGER PRIMARY KEY, title TEXT, image TEXT, sketch TEXT, nav_id INTEGER, states INTEGER DEFAULT 1, create_time TEXT)");
$db->exec("CREATE TABLE zw_cms_expand (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, sketch TEXT, minimg TEXT, parent_id INTEGER, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0)");
$db->exec("CREATE TABLE zw_cms_link (id INTEGER PRIMARY KEY, title TEXT, link TEXT, image TEXT, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0)");
$db->exec("CREATE TABLE zw_cms_country (id INTEGER PRIMARY KEY, title TEXT, en_name TEXT, states INTEGER DEFAULT 1, sort INTEGER DEFAULT 0)");

// 系统配置
$configs = [
    ['company_name', '广州志远搬家服务有限公司', 'web', 'site'],
    ['web_phone', '020-85627757', 'web', 'site'],
    ['web_call', '18924177677', 'web', 'site'],
    ['web_email', 'service@zhiyuanbj.cn', 'web', 'site'],
    ['web_address', '广州市天河区中山大道中38号', 'web', 'site'],
    ['site_copyright', 'Copyright © 2025 广州志远搬家服务有限公司 版权所有', 'web', 'site'],
    ['site_beian', '粤ICP备2024288329号-2', 'web', 'site'],
    ['seo_title', '广州天河搬家公司_工厂搬家_仓库搬家_日式搬家-广州志远搬家', 'web', 'seo'],
    ['seo_keyword', '广州天河搬家公司,广州工厂搬家,广州机器设备吊装,广州仓库搬迁,广州学校搬迁,广州日式搬家', 'web', 'seo'],
    ['seo_content', '广州志远搬家服务公司【18924177677】主营业务:广州搬家公司,广州工厂搬家,单位搬家,办公室搬家,大型企业投标服务.', 'web', 'seo'],
    ['hot_keyword', '广州搬家，天河搬家，工厂搬迁，办公室搬迁', 'web', 'other'],
    ['detail_route', '{"news":"detail/news","products":"/detail/product","cases":"detail/case"}', 'web', 'route'],
    ['web_domain', 'https://www.zhiyuanbj.cn', 'web', 'site'],
    ['default_image', '', 'web', 'site'],
    ['web_online', '', 'web', 'site'],
];
$stmt = $db->prepare("INSERT INTO zw_system_config (name, value, origin, `group`) VALUES (?,?,?,?)");
foreach ($configs as $c) $stmt->execute($c);

// 导航
$navs = [
    [1,'首页','index',0,1],
    [2,'同城搬家','products',0,2],
    [3,'跨市搬家','products',0,3],
    [4,'出国搬家','products',0,4],
    [5,'关于志远','about',0,5],
    [6,'服务案例','cases',0,6],
    [7,'新闻资讯','news',0,7],
    [8,'联系志远','contact',0,8],
    [9,'同城服务','cases',6,1],
    [10,'跨市服务','cases',6,2],
    [11,'搬家百科','news',7,1],
    [12,'搬家心得','news',7,2],
    [13,'公司简介','about',5,1],
    [14,'企业文化','about',5,2],
    [15,'员工风采','about',5,3],
    [16,'媒体报道','about',5,4],
    [18,'联系志远','about',5,5],
    [19,'收费标准','about',5,6],
];
$stmt = $db->prepare("INSERT INTO zw_cms_nav (id,title,url_model,pid,sort) VALUES (?,?,?,?,?)");
foreach ($navs as $n) $stmt->execute($n);

// 产品
$services = [
    ['同城搬家','提供快速、专业的搬运服务，确保客户物品安全、高效地送达新居',2],
    ['跨市搬家','实现城市间物品的安全、高效转移，满足客户长途搬迁的需求',3],
    ['出国搬家','涵盖全程，从物品打包、运输到清关配送，专业、安全、便捷',4],
    ['日式搬家','以细致入微的打包、搬运及还原整理为核心，提供一站式无忧搬家体验',2],
    ['钢琴搬运','以专业团队和精细流程为核心，确保钢琴安全、无损地从一地搬运至另一地',2],
    ['大型工厂搬迁','凭借专业团队与高效方案，实现工厂设备、物资的全面、安全、快速迁移',2],
    ['收纳整理','通过专业规划与整理技巧，帮您打造整洁有序的生活或工作环境',2],
    ['家居拆装','以专业团队为核心，提供从拆卸到组装的全程无忧解决方案',2],
];
$stmt = $db->prepare("INSERT INTO zw_cms_product (title,sketch,nav_id,image,create_time) VALUES (?,?,?,'/upload/20250120/8634eee3b02c916365e99d9592d4422e.png','2025-01-20')");
foreach ($services as $s) $stmt->execute($s);

// 新闻
$news = [
    ['广州高层大件家具吊装搬运，无电梯搬家专项服务','为居住在高层且无电梯的居民提供专业的大件家具吊装搬运服务','/upload/20260710/c882b1c5f464dc644ec97e34bdaca33e.png',7],
    ['广州24小时同城搬家服务，就近派车当日上门搬运','提供广州范围内快速响应的搬家服务','',7],
    ['广州全屋搬家打包搬运一站式服务','跨区跨省均可承接，从打包到搬运一站式专业服务','/upload/20260709/fe6d6542e3d9a815a26bec8ace3ab8ae.png',7],
    ['广州正规搬家公司怎么选？明码标价无中途加价','选择正规搬家公司需要注意的事项','',7],
];
$stmt = $db->prepare("INSERT INTO zw_cms_article (title,sketch,image,nav_id,create_time) VALUES (?,?,?,?,'2026-07-10')");
foreach ($news as $n) $stmt->execute($n);

// 案例
$cases = [
    ['大亚湾核电站','大型工业搬迁项目','/upload/20241222/dmg6fh.jpg',9],
    ['阳江十八子集团','企业整体搬迁','/upload/20241222/8jztni.jpg',9],
    ['文本体育馆','场馆设备搬迁','/upload/20241222/k9mur6.jpg',9],
    ['河源碧桂园','大型住宅搬迁','/upload/20241222/8g35yr.jpg',9],
    ['佛山法院','机关单位搬迁','/upload/20241222/wf7xqh.jpg',10],
    ['东莞虎门地标广场','商业综合体搬迁','/upload/20241222/s7n5b2.jpg',10],
    ['珠海市市政府大楼','政府机关搬迁','/upload/20241222/8bjt29.jpg',10],
    ['岭南明珠体育馆','体育场馆搬迁','/upload/20241222/9tegj8.jpg',10],
];
$stmt = $db->prepare("INSERT INTO zw_cms_cases (title,sketch,image,nav_id,create_time) VALUES (?,?,?,?,'2024-12-22')");
foreach ($cases as $c) $stmt->execute($c);

// 选择理由
$reasons = [
    ['专业服务','拥有一支经验丰富、训练有素的搬家团队','/upload/20250112/5da7feb3611931aab4769513c3207992.png'],
    ['先进设备','配备了先进的搬家设备和工具，如搬家车、升降机等','/upload/20250113/61a2e356be620c01651733a23ef774e7.png'],
    ['丰富经验','每位至少有5年的搬迁经验，长期积累了整套搬运经验','/upload/20250113/b8655e050808c20dae806398035e8766.png'],
    ['一站式服务','完善的一站式搬迁配套服务','/upload/20250113/6fad1ce60bb2424e000af85a5f851064.png'],
    ['搬迁标准','搬家搬迁行业标准化，精细搬运，粗细归置','/upload/20250113/3ccb17a9b2ad332f104a74adb2ea315b.png'],
    ['服务保障','收费标准明码标价，不乱收费，不乱加价','/upload/20250113/7fb442ecf24de88fffc877f65fc8247d.png'],
];
$stmt = $db->prepare("INSERT INTO zw_cms_expand (title,sketch,minimg,parent_id) VALUES (?,?,?,3)");
foreach ($reasons as $r) $stmt->execute($r);
$db->exec("INSERT INTO zw_cms_expand (title,sketch,parent_id) VALUES ('服务热线','020-85627757',2)");

// Banner
$stmt = $db->prepare("INSERT INTO zw_cms_banner (title,image,position,states,sort) VALUES (?,?,'home',1,?)");
$stmt->execute(['banner1','/upload/20260601/5687dee1d2a3aa94c741847c9d0f887e.jpg',1]);
$stmt->execute(['banner2','/upload/20250216/091bcd172c24c0bf1d66320fcc2b3c80.jpg',2]);

echo "OK: " . $db->query("SELECT COUNT(*) FROM zw_cms_product")->fetchColumn() . " products, ";
echo $db->query("SELECT COUNT(*) FROM zw_cms_article")->fetchColumn() . " articles, ";
echo $db->query("SELECT COUNT(*) FROM zw_cms_cases")->fetchColumn() . " cases\n";
