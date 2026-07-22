<?php
namespace app\controller;

use app\model\CmsNav;
use app\model\CmsArticle;

class Page extends BaseController
{
    /**
     * 单页展示
     */
    public function index(int $id = 0)
    {
        $navModel = new CmsNav();
        $page = $navModel->find($id);

        if (!$page) {
            $this->redirect('/');
            return;
        }

        // 如果页面类型是内容页（关联文章）
        $content = '';
        if (!empty($page['content'])) {
            $content = $page['content'];
        } else {
            // 可能关联了文章
            $articleModel = new CmsArticle();
            $article = $articleModel->findWhere(['nav_id' => $id, 'states' => 1]);
            $content = $article['content'] ?? '';
        }

        $this->render('page/index', [
            'page'      => $page,
            'content'   => $content,
            'page_title'      => $page['seo_title'] ?? $page['title'],
            'page_keywords'   => $page['seo_keyword'] ?? '',
            'page_description'=> $page['seo_content'] ?? '',
        ]);
    }
}
