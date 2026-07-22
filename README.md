# 志远搬家官网

**广州志远搬家服务有限公司官方网站**

---

## 技术栈

- **语言**：PHP 7.3+
- **数据库**：MySQL 5.7
- **前端**：jQuery + Swiper + LayUI + 原生 CSS
- **架构**：MVC（自主框架，轻量级）

## 目录结构

```
zhiyuan_guanwang/
├── public/                  # Web根目录
│   ├── index.php            # 应用入口
│   ├── .htaccess            # Apache重写规则
│   └── static/home/         # 静态资源(CSS/JS/images)
├── app/
│   ├── config/site.php      # 站点配置
│   ├── controller/          # 控制器
│   │   ├── BaseController   # 基类控制器
│   │   ├── Index.php        # 首页/关于/联系
│   │   ├── Products.php     # 产品/服务
│   │   ├── News.php         # 新闻/文章
│   │   ├── Cases.php        # 案例
│   │   ├── Message.php      # 留言/评估
│   │   ├── Page.php         # 单页
│   │   ├── Photo.php        # 相册
│   │   ├── Search.php       # 搜索
│   │   └── Verify.php       # 验证码
│   ├── model/               # 模型层
│   │   ├── Database.php     # 数据库连接
│   │   ├── BaseModel.php    # 基础模型
│   │   ├── CmsProduct.php   # 产品模型
│   │   ├── CmsArticle.php   # 文章模型
│   │   ├── CmsCases.php     # 案例模型
│   │   ├── CmsBanner.php    # 轮播图模型
│   │   ├── CmsNav.php       # 导航菜单模型
│   │   ├── CmsMessage.php   # 留言模型
│   │   ├── CmsPhoto.php     # 相册模型
│   │   ├── CmsExpand.php    # 扩展字段模型
│   │   ├── CmsLink.php      # 友情链接模型
│   │   ├── CmsCountry.php   # 国家地区模型
│   │   └── SystemConfig.php # 系统配置模型
│   ├── core/App.php         # 路由核心
│   └── view/                # 视图模板
├── runtime/                 # 运行时(缓存/日志)
├── bootstrap.php            # 应用引导
├── composer.json            # Composer配置
├── nginx.conf               # Nginx配置示例
└── README.md
```

## 部署步骤

### 1. 环境要求

- PHP >= 7.3（需要PDO、JSON、MBString扩展）
- MySQL >= 5.7
- Apache/Nginx
- Composer

### 2. 安装依赖

```bash
cd zhiyuan_guanwang
composer install
```

### 3. 配置数据库

编辑 `app/config/site.php`，修改数据库连接信息：

```php
'database' => [
    'hostname' => '127.0.0.1',
    'database' => 'zhiyuan_com',
    'username' => 'your_username',
    'password' => 'your_password',
    'hostport' => '3306',
    'prefix'   => 'zw_',
],
```

### 4. 导入数据库

使用 `zhiyuan_com.sql` 导入原有数据库结构和数据。

### 5. 配置Web服务器

**Nginx**: 使用项目根目录下的 `nginx.conf`

**Apache**: 已内置 `.htaccess` 重写规则

Web根目录指向 `public/`

### 6. 权限设置

```bash
chmod -R 777 runtime/
chmod -R 777 public/upload/
```

## URL 结构

| 页面 | URL格式 |
|------|---------|
| 首页 | `/` `/index.html` |
| 关于我们 | `/about/13.html` |
| 产品服务 | `/products/2.html` |
| 新闻资讯 | `/news/7.html` |
| 服务案例 | `/cases/6.html` |
| 联系我们 | `/contact/8.html` |
| 相册展示 | `/photo/:id.html` |
| 产品详情 | `/detail/products:数字.html` |
| 新闻详情 | `/detail/news:数字.html` |
| 案例详情 | `/detail/case:数字.html` |
| 搜索 | `/search.html?keyword=xxx` |
| 验证码 | `/verify.html` |

## 多城市子域名

支持通过子域名访问不同城市站点：
- `tianhe.zhiyuanbj.cn` → 天河
- `haizhu.zhiyuanbj.cn` → 海珠
- `baiyun.zhiyuanbj.cn` → 白云
- ...

子域名访问时会自动切换城市标识，SEO标题中的 `$city` 变量会被替换。

## 注意事项

1. **验证码功能** 需要PHP GD库支持
2. **邮件功能** 需要在 `app/config/site.php` 中配置SMTP信息
3. **短信功能** 为占位实现，需自行接入第三方短信平台
4. **上传目录** 为 `public/upload/`，确保Web服务器有写入权限

## GEO Content OS 自动发布接口

官网提供专用的服务端新闻发布 API，用于接收通过 GEO Content OS 机器质量门禁的官网文章。该接口使用 Bearer Token、内容版本幂等和事务写入，不模拟后台登录。

部署前必须：

1. 备份当前 SQLite 数据库和 `app/config/site.php`；
2. 执行 `php scripts/migrate-geo-publish.php /绝对路径/官网.sqlite`；
3. 生成高熵原始令牌，并只把其 SHA-256 配置为 `GEO_PUBLISH_TOKEN_SHA256`；
4. 确认 `GEO_PUBLISH_TARGET_NAV_ID` 指向启用中的新闻栏目；
5. 完成本地或预发布验证后再设置 `GEO_PUBLISH_API_ENABLED=1`。

接口契约、配置、测试和回滚方法见 [docs/GEO_PUBLISH_API.md](docs/GEO_PUBLISH_API.md)。

## 与原版对比

| 对比项 | 原版 | 此版本 |
|--------|------|--------|
| 代码可读性 | ✗ 17个核心文件加密 | ✓ 全部可读 |
| 维护性 | ✗ 无法修改业务逻辑 | ✓ 标准MVC结构 |
| 安全性 | ✗ 加密代码不可审计 | ✓ 所有代码透明 |
| 数据库兼容 | N/A | ✓ 完全兼容原有表结构 |
| URL兼容 | N/A | ✓ 保持原有URL格式 |
| 后端管理 | EasyAdmin | 可对接原始admin模块 |

## License

MIT
