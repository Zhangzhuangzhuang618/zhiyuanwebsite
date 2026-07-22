<?php
namespace app\controller;

use app\model\CmsCases;
use app\model\CmsNav;
use app\model\CmsProduct;

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

        $page = (int)($this->get('page', 1));
        // The original /cases/6.html entry defaults to the cross-city cases child.
        $queryNavId = $id === 6 ? 10 : $id;
        $data = $casesModel->getListByNav($queryNavId, $page, 8);

        $siblings = [];
        if ($currentNav) {
            $pid = $currentNav['pid'] ?: $currentNav['id'];
            $siblings = $navModel->select(['pid' => $pid, 'status' => 1], '*', 'sort ASC');
        }

        $this->render('cases/index', array_merge($this->getNewsSidebar(), [
            'currentNav' => $currentNav ?? [],
            'classify'   => $siblings,
            'list'       => $data['list'],
            'total'      => $data['total'],
            'page'       => $data['page'],
            'totalPage'  => $data['total_page'],
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
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

        $navModel = new CmsNav();
        $breadcrumb = $navModel->getBreadcrumb($detail['nav_id'] ?? 0);
        $currentNav = $navModel->find($detail['nav_id'] ?? 0) ?: [];
        $classify = $navModel->select(['pid' => ($currentNav['pid'] ?: $currentNav['id'] ?? 0), 'status' => 1], '*', 'sort ASC');
        $prevNext = $casesModel->getPrevNext($id, $detail['nav_id'] ?? 0);

        $this->render('cases/detail', array_merge($this->getNewsSidebar(), [
            'detail'     => $detail,
            'classify'   => $classify,
            'prev'       => $prevNext['prev'],
            'next'       => $prevNext['next'],
            'about_news' => [],
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 5,
            'canonical_url' => $this->siteUrl('/detail_cases' . $id . '.html'),
            'page_title'      => $detail['seo_title'] ?: $this->seoTitle($detail['title']),
            'page_keywords'   => $detail['seo_keyword'] ?? '',
            'page_description'=> $detail['seo_content'] ?? '',
            'page_image'      => $detail['image'] ?? '',
        ]));
    }
}
