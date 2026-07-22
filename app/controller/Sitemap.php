<?php
namespace app\controller;

use app\model\CmsArticle;
use app\model\CmsCases;
use app\model\CmsNav;
use app\model\CmsProduct;

class Sitemap extends BaseController
{
    public function index(): void
    {
        $urls = [
            ['loc' => $this->siteUrl('/'), 'lastmod' => date('c')],
            ['loc' => $this->siteUrl('/faq.html')],
        ];

        foreach ((new CmsNav())->select(['status' => 1], 'id, url_model', 'sort ASC, id ASC') as $nav) {
            $path = $this->navPath($nav);
            if ($path) $urls[] = ['loc' => $this->siteUrl($path)];
        }

        foreach ((new CmsProduct())->select(['status' => 1], 'id, update_time, create_time', 'id DESC') as $item) {
            $urls[] = ['loc' => $this->siteUrl('/detail/products' . $item['id'] . '.html'), 'lastmod' => $this->lastModified($item)];
        }
        foreach ((new CmsArticle())->select(['status' => 1], 'id, update_time, create_time', 'id DESC') as $item) {
            $urls[] = ['loc' => $this->siteUrl('/detail/news' . $item['id'] . '.html'), 'lastmod' => $this->lastModified($item)];
        }
        foreach ((new CmsCases())->select(['status' => 1], 'id, update_time, create_time', 'id DESC') as $item) {
            $urls[] = ['loc' => $this->siteUrl('/detail_cases' . $item['id'] . '.html'), 'lastmod' => $this->lastModified($item)];
        }

        header('Content-Type: application/xml; charset=utf-8');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url><loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            if (!empty($url['lastmod'])) echo '<lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</lastmod>';
            echo '<changefreq>weekly</changefreq></url>';
        }
        echo '</urlset>';
    }

    private function lastModified(array $item): string
    {
        $time = (int)($item['update_time'] ?: $item['create_time'] ?: time());
        return date('c', $time);
    }

    private function navPath(array $nav): string
    {
        $model = $nav['url_model'] ?? '';
        $id = (int)($nav['id'] ?? 0);
        if ($model === 'index') return '';
        if (in_array($model, ['products', 'news', 'cases', 'about', 'contact'], true) && $id > 0) {
            return '/' . $model . '/' . $id . '.html';
        }
        return '';
    }
}
