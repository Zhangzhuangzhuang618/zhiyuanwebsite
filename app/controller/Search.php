<?php
namespace app\controller;

use app\model\CmsProduct;
use app\model\CmsArticle;

class Search extends BaseController
{
    /**
     * 全站搜索
     */
    public function index(string $model = '')
    {
        $keyword = $this->get('keyword', '');
        $results = [];

        if (!empty($keyword)) {
            if (empty($model) || $model === 'products') {
                $productModel = new CmsProduct();
                $results['products'] = $productModel->select(
                    [],
                    'id, title, sketch, image, "product" as type',
                    'id DESC',
                    20
                );
            }

            if (empty($model) || $model === 'news') {
                $articleModel = new CmsArticle();
                $results['news'] = $articleModel->select(
                    [],
                    'id, title, sketch, image, "news" as type',
                    'id DESC',
                    20
                );
            }
        }

        $this->render('search/index', [
            'keyword'    => $keyword,
            'results'    => $results,
            'page_title' => '搜索' . ($keyword ? ': ' . $keyword : ''),
        ]);
    }

    /**
     * 产品搜索（AJAX）
     */
    public function productSearch()
    {
        $keyword = $this->post('keyword', '');
        if (empty($keyword)) {
            $this->json(['code' => 0, 'msg' => '请输入关键词']);
        }

        $db = \app\model\Database::getInstance();
        $sql = "SELECT id, title, sketch, image, 'product' as type FROM "
             . \app\model\Database::table('cms_product')
             . " WHERE states = 1 AND delete_time IS NULL AND (title LIKE ? OR sketch LIKE ?)"
             . " ORDER BY id DESC LIMIT 20";

        $stmt = $db->prepare($sql);
        $stmt->execute(["%{$keyword}%", "%{$keyword}%"]);

        $this->json([
            'code' => 1,
            'data' => $stmt->fetchAll(),
        ]);
    }
}
