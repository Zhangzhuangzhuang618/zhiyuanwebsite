CREATE UNIQUE INDEX IF NOT EXISTS zw_cms_article_id_uq ON zw_cms_article (id);

CREATE TABLE IF NOT EXISTS zw_geo_publish_receipts (
    idempotency_key TEXT PRIMARY KEY,
    content_version_id TEXT NOT NULL UNIQUE,
    payload_hash TEXT NOT NULL,
    article_id INTEGER NOT NULL UNIQUE,
    article_url TEXT NOT NULL,
    published_at INTEGER NOT NULL,
    response_json TEXT NOT NULL,
    created_at INTEGER NOT NULL,
    CHECK (length(idempotency_key) BETWEEN 1 AND 128),
    CHECK (length(content_version_id) = 36),
    CHECK (length(payload_hash) = 64),
    CHECK (article_id > 0)
);
