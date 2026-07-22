<?php
namespace app\controller;

use app\model\CmsPhoto;
use app\model\CmsNav;

class Photo extends BaseController
{
    public function index(int $id = 0)
    {
        $navModel = new CmsNav();
        $photoModel = new CmsPhoto();

        $currentNav = $navModel->find($id);
        if (!$currentNav) {
            $currentNav = $navModel->findWhere(['url_model' => 'photo']);
        }

        $page = (int)($this->get('page', 1));
        $data = $photoModel->getListByNav($id, $page, 20);

        $this->render('photo/index', [
            'currentNav' => $currentNav ?? [],
            'list'       => $data['list'],
            'total'      => $data['total'],
            'page'       => $data['page'],
            'totalPage'  => $data['total_page'],
            'page_title' => $currentNav['seo_title'] ?? ($currentNav['title'] ?? '相册展示'),
            'page_keywords'   => $currentNav['seo_keyword'] ?? '',
            'page_description'=> $currentNav['seo_content'] ?? '',
        ]);
    }
}
