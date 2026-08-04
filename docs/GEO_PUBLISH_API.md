# GEO 官网新闻发布 API

## 作用范围

该 API 接收 GEO Content OS 上传的合格文章图片，并新增、立即发布“新闻资讯”文章。它不模拟后台登录，不读取 GEO Content OS 数据库，也不反向同步人工删除。调用方必须先完成内容生成、机器质量检查和自动重写。

Base URL：`/api/geo/v1`。GEO Content OS 平台账号应填写包含该路径前缀的完整 HTTPS 地址，例如 `https://www.zhiyuanbj.cn/api/geo/v1/`。

## 配置

```text
GEO_PUBLISH_API_ENABLED=0
GEO_PUBLISH_TOKEN_SHA256=<64位小写SHA-256>
GEO_PUBLISH_TARGET_NAV_ID=11
```

生成一次性高熵原始令牌并计算摘要：

```bash
TOKEN="$(openssl rand -base64 48 | tr -d '\n')"
printf '%s' "$TOKEN" | shasum -a 256
```

- 原始 `TOKEN` 只写入 GEO Content OS 的官网平台账号加密凭证；不得写入本仓库、日志或截图。
- 官网只配置摘要 `GEO_PUBLISH_TOKEN_SHA256`。
- 正式启用前确认 `target_nav_id` 对应 `status=1` 且 `url_model=news` 的栏目。
- API 必须只通过 HTTPS 暴露，并由反向代理限制请求体和访问频率。
- PHP 运行用户必须对 `public/upload/` 有写权限；自动配图保存在 `public/upload/geo/`。

## 数据库迁移

先备份数据库，再执行：

```bash
php scripts/migrate-geo-publish.php /absolute/path/to/site.sqlite
```

迁移新增：

- `zw_geo_publish_receipts`：记录幂等键、GEO 内容版本、载荷哈希、官网文章 ID、URL、发布时间和固定响应；
- `zw_cms_article.id` 唯一索引：保证并发分配文章 ID 时不会重复。

迁移可重复执行。若旧文章存在重复 ID，迁移会停止且不修改数据。

## 鉴权与公共约定

所有端点要求：

```http
Authorization: Bearer <原始令牌>
Accept: application/json
```

发布请求还必须包含：

```http
Content-Type: application/json
Idempotency-Key: official-site:<variant_uuid>:<content_version_uuid>
X-Request-Id: <调用链请求ID>
```

鉴权比较使用 SHA-256 和常量时间比较。`Idempotency-Key` 最长 128 字符。

## 端点

### GET `/capabilities`

成功响应：

```json
{"publish":true,"get_status":true,"media_upload":true,"metrics":false}
```

### POST `/media`

发布文章前，调用方把本地 MinIO 中的 JPEG 图片作为原始请求体上传。该端点与文章发布使用同一个
Bearer Token，单张图片最大 10 MB。

```http
Content-Type: image/jpeg
Idempotency-Key: official-site-media:<asset_uuid>
X-Content-Sha256: <图片二进制的64位小写SHA-256>
X-Content-Version-Id: <content_version_uuid>
X-Media-Asset-Id: <media_asset_uuid>
X-Media-Role: cover|body
```

首次保存返回 HTTP 201；相同内容重放返回 HTTP 200。文件按内容哈希保存，相同图片不会重复写入：

```json
{
  "asset_id": "aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa",
  "content_hash": "64位小写SHA-256",
  "content_type": "image/jpeg",
  "size_bytes": 123456,
  "url": "https://www.zhiyuanbj.cn/upload/geo/ab/完整哈希.jpg"
}
```

服务端重新计算 SHA-256、MIME 和图片结构；伪造类型、损坏文件或哈希不一致会被拒绝。上传成功后，
图片由官网服务器持久保存，不再依赖 GEO Content OS 所在 Windows 服务器在线。

### POST `/publish`

请求体遵循 `zhiyuan-news-request@1`：

```json
{
  "content_version_id": "11111111-1111-4111-8111-111111111111",
  "payload_hash": "64位小写SHA-256",
  "payload": {
    "schema_version": "zhiyuan-news-payload@1",
    "platform_code": "official_site",
    "title": "20至60个Unicode字符的官网新闻标题示例文本",
    "summary": "文章摘要，最多240个字符。",
    "body_html": "<h2>小标题</h2><p>正文</p>",
    "seo_keywords": ["广州搬家", "企业搬迁"],
    "meta_description": "页面搜索摘要，最多240个字符。"
  }
}
```

`payload_hash` 是对 `payload` 递归按对象键排序、数组保持原顺序、UTF-8 且不转义 Unicode/斜杠后的 JSON 计算 SHA-256。

首次成功返回 HTTP 201；同一有效载荷的幂等重放返回 HTTP 200：

```json
{
  "external_id": "123",
  "status": "published",
  "url": "https://www.zhiyuanbj.cn/detail/news123.html",
  "published_at": "2026-07-23T03:00:00+00:00"
}
```

同一幂等键或同一 `content_version_id` 携带不同载荷时返回 HTTP 409 `IDEMPOTENCY_CONFLICT`，不会新增文章。

### GET `/status/{external_id}`

返回已存储的固定发布响应。不存在时返回 HTTP 404 `RESOURCE_NOT_FOUND`。

## HTML 安全

仅保留 `p,h2,h3,ul,ol,li,blockquote,strong,em,a,section,aside,figure,figcaption,img`。链接只保留有效 HTTP/HTTPS `href` 并补充 `rel="noopener noreferrer"`；图片只允许引用当前官网 `/upload/geo/` 下的内容寻址 JPEG，并仅保留 `src`、`alt` 及固定的延迟加载属性。脚本、事件处理器、JavaScript URL、第三方图片、iframe 和对象嵌入均拒绝。正文第一张合格图片同时写入文章封面字段。

## 错误响应

```json
{
  "error": {"code": "REQUEST_INVALID", "message": "..."},
  "request_id": "..."
}
```

主要错误：`AUTH_REQUIRED`、`AUTH_INVALID`、`REQUEST_TOO_LARGE`、`REQUEST_INVALID`、`CONTENT_HASH_MISMATCH`、`MEDIA_INVALID`、`MEDIA_WRITE_FAILED`、`PAYLOAD_HASH_MISMATCH`、`IDEMPOTENCY_CONFLICT`、`CATEGORY_INVALID`、`ARTICLE_WRITE_FAILED`、`RESOURCE_NOT_FOUND`。

调用方只应重试网络错误和 HTTP 5xx，并始终复用原幂等键。4xx 是确定性拒绝，不应自动重试。

## 验证

```bash
composer install
composer test:geo-publish
php -l app/controller/GeoPublishApi.php
php -l app/service/GeoNewsPublisher.php
php -l scripts/migrate-geo-publish.php
```

测试命令同时覆盖服务层和真实本地 HTTP 路由。联调必须使用数据库副本、临时令牌和 loopback URL，不得调用生产接口；非本机地址必须使用 HTTPS。

## 回滚

1. 设置 `GEO_PUBLISH_API_ENABLED=0` 并重载 PHP 服务，立即停止新调用；
2. 保留 `zw_geo_publish_receipts` 以维持已发布请求的幂等事实；
3. 代码回滚不应删除已发布文章；
4. 只有在确认不再运行任何版本的发布 API 后，才可在备份完成的维护窗口删除收据表或索引。

人工在官网后台删除文章不会回写 GEO Content OS，这是第一期明确限制。
