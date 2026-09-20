<?php
namespace app\controller;

use app\model\CmsCases;
use app\model\CmsNav;
use app\model\CmsProduct;
use app\service\CasePresentation;

class Cases extends BaseController
{
    public function index(int $id = 6)
    {
        $navModel = new CmsNav();
        $casesModel = new CmsCases();

        $currentNav = $navModel->find($id);
        if (!$currentNav) {
            $currentNav = $navModel->findWhere(['url_model' => 'cases']);
        }

        $page = max(1, (int)($this->get('page', 1)));
        // The main cases entry includes every published case, without a category filter.
        $queryNavId = $id === 6 ? 0 : $id;
        $data = $casesModel->getListByNav($queryNavId, $page, 8);
        $data['list'] = array_map([CasePresentation::class, 'prepare'], $data['list']);

        $siblings = [];
        if ($currentNav) {
            $pid = $currentNav['pid'] ?: $currentNav['id'];
            $siblings = $navModel->select(['pid' => $pid, 'status' => 1], '*', 'sort ASC');
        }

        $this->render('cases/index', array_merge($this->getNewsSidebar(), [
            'currentNav' => $currentNav ?? [],
            'classify'   => $siblings,
            'selectedCaseNavId' => $queryNavId,
            'list'       => $data['list'],
            'total'      => $data['total'],
            'page'       => $data['page'],
            'totalPage'  => $data['total_page'],
            'banner'     => ($currentNav['image'] ?? '') ?: '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 5,
            'canonical_url' => $this->siteUrl('/cases/' . ($currentNav['id'] ?? $id) . '.html'),
            'page_title' => $currentNav['seo_title'] ?: $this->seoTitle($currentNav['title'] ?? '服务案例'),
            'page_keywords'   => $currentNav['seo_keyword'] ?? '',
            'page_description'=> $currentNav['seo_content'] ?? '',
        ]));
    }

    public function detail(int $id)
    {
        $casesModel = new CmsCases();
        $detail = $casesModel->find($id);

        if (!$detail) {
            $this->redirect('/cases');
            return;
        }

        $detail = CasePresentation::prepare($detail);
        $navModel = new CmsNav();
        $breadcrumb = $navModel->getBreadcrumb($detail['nav_id'] ?? 0);
        $currentNav = $navModel->find($detail['nav_id'] ?? 0) ?: [];
        $classify = $navModel->select(['pid' => ($currentNav['pid'] ?: $currentNav['id'] ?? 0), 'status' => 1], '*', 'sort ASC');
        $prevNext = $casesModel->getPrevNext($id, $detail['nav_id'] ?? 0);

        $caseUrl = $this->siteUrl('/detail_cases' . $id . '.html');
        $article = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => $caseUrl . '#article',
            'url' => $caseUrl,
            'mainEntityOfPage' => $caseUrl,
            'headline' => $detail['headline'],
            'description' => $detail['summary'],
            'inLanguage' => 'zh-CN',
            'author' => ['@id' => $this->siteUrl('/') . '#organization'],
            'publisher' => ['@id' => $this->siteUrl('/') . '#organization'],
            'articleBody' => $detail['plain_body'],
        ];
        if (!empty($detail['create_time'])) $article['datePublished'] = date(DATE_ATOM, (int) $detail['create_time']);
        if ($detail['display_time']) $article['dateModified'] = date(DATE_ATOM, $detail['display_time']);
        if (!empty($detail['image'])) {
            $article['image'] = $this->absoluteUrl($detail['image']);
        }
        if ($article['articleBody'] === '') {
            unset($article['articleBody']);
        }
        if (!empty($currentNav['title'])) {
            $article['articleSection'] = $currentNav['title'];
        }

        $this->render('cases/detail', array_merge($this->getNewsSidebar(), [
            'structured_data' => [$article],
            'currentNav' => $currentNav,
            'detail'     => $detail,
            'classify'   => $classify,
            'prev'       => $prevNext['prev'],
            'next'       => $prevNext['next'],
            'about_news' => [],
            'banner'     => ($currentNav['image'] ?? '') ?: '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 5,
            'canonical_url' => $this->siteUrl('/detail_cases' . $id . '.html'),
            'page_title'      => $detail['headline'],
            'page_keywords'   => $detail['seo_keyword'] ?? '',
            'page_description'=> $detail['summary'],
            'page_image'      => $detail['image'] ?? '',
        ]));
    }
}
