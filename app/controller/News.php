<?php
namespace app\controller;

use app\model\CmsArticle;
use app\model\CmsNav;
use app\model\CmsProduct;

class News extends BaseController
{
    public function index(?int $id = null)
    {
        $navModel = new CmsNav();
        $articleModel = new CmsArticle();

        $isAggregate = $id === null;
        $rootNav = $navModel->findWhere(['url_model' => 'news', 'pid' => 0])
            ?: $navModel->findWhere(['url_model' => 'news']);
        $currentNav = $isAggregate ? $rootNav : $navModel->find($id);
        if (!$currentNav) {
            $currentNav = $rootNav;
            $isAggregate = true;
        }

        $page = (int)($this->get('page', 1));
        $articleFields = 'id, title, subtitle, sketch, image, link, target, nav_id, create_time, browse, seo_title, seo_keyword, seo_content';
        $data = $isAggregate
            ? $articleModel->paginate(['status' => 1], $page, 8, $articleFields)
            : $articleModel->getListByNav((int)$currentNav['id'], $page, 8);

        $siblings = [];
        if ($rootNav) {
            $pid = $rootNav['id'];
            $siblings = $navModel->select(['pid' => $pid, 'status' => 1], '*', 'sort ASC');
        }

        $this->render('news/index', array_merge($this->getNewsSidebar(), [
            'currentNav' => $currentNav ?? [],
            'classify'   => $siblings,
            'list'       => $data['list'],
            'total'      => $data['total'],
            'page'       => $data['page'],
            'totalPage'  => $data['total_page'],
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 6,
            'canonical_url' => $isAggregate
                ? $this->siteUrl('/news.html')
                : $this->siteUrl('/news/' . ($currentNav['id'] ?? 7) . '.html'),
            'page_title' => $currentNav['seo_title'] ?: $this->seoTitle($currentNav['title'] ?? '新闻资讯'),
            'page_keywords'   => $currentNav['seo_keyword'] ?? '',
            'page_description'=> $currentNav['seo_content'] ?? '',
        ]));
    }

    public function detail(int $id)
    {
        $articleModel = new CmsArticle();
        $detail = $articleModel->find($id);

        if (!$detail) {
            $this->redirect('/news');
            return;
        }

        $articleModel->incrementBrowse($id);
        $prevNext = $articleModel->getPrevNext($id, $detail['nav_id'] ?? 0);

        $navModel = new CmsNav();
        $breadcrumb = $navModel->getBreadcrumb($detail['nav_id'] ?? 0);

        $currentNav = $navModel->find($detail['nav_id'] ?? 0) ?: [];
        $classify = $navModel->select(['pid' => ($currentNav['pid'] ?: $currentNav['id'] ?? 0), 'status' => 1], '*', 'sort ASC');

        $this->render('news/detail', array_merge($this->getNewsSidebar(), [
            'detail'     => $detail,
            'prev'       => $prevNext['prev'],
            'next'       => $prevNext['next'],
            'classify'   => $classify,
            'about_news' => $articleModel->getRelated($detail['nav_id'] ?? 0, $id, 8),
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 6,
            'canonical_url' => $this->siteUrl('/detail/news' . $id . '.html'),
            'page_title'      => $detail['seo_title'] ?: $this->seoTitle($detail['title']),
            'page_keywords'   => $detail['seo_keyword'] ?? '',
            'page_description'=> $detail['seo_content'] ?? '',
            'page_type'       => 'article',
            'page_image'      => $detail['image'] ?? '',
            'structured_data' => array_merge([
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'mainEntityOfPage' => $this->currentUrl(),
                    'headline' => $detail['title'],
                    'datePublished' => date(DATE_ATOM, (int)$detail['create_time']),
                    'dateModified' => date(DATE_ATOM, (int)$detail['create_time']),
                    'author' => ['@type' => 'Organization', 'name' => $this->siteConfig['company_name'] ?? '广州志远搬家服务有限公司'],
                    'publisher' => ['@id' => $this->siteUrl('/#organization')],
                    'image' => $this->absoluteUrl($detail['image'] ?? ''),
                    'description' => $detail['seo_content'] ?: ($detail['sketch'] ?: mb_substr(trim(strip_tags(html_entity_decode($detail['content'] ?? ''))), 0, 160)),
                    'inLanguage' => 'zh-CN',
                ],
            ], $this->breadcrumbSchema([
                ['name' => '首页', 'url' => '/'],
                ['name' => '新闻资讯', 'url' => '/news.html'],
                ['name' => $detail['title'], 'url' => $this->currentUrl()],
            ])),
        ]));
    }

    /**
     * 常见问题
     */
    public function problem(int $id = 0)
    {
        return $this->index($id ?: 7);
    }
}
