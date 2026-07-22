<?php
namespace app\controller;

use app\model\CmsProduct;
use app\model\CmsNav;
use app\model\CmsExpand;
use app\model\CmsCases;

/**
 * 产品/服务控制器
 */
class Products extends BaseController
{
    /**
     * 产品列表页
     */
    public function index(int $id = 2)
    {
        $navModel = new CmsNav();
        $productModel = new CmsProduct();

        $currentNav = $navModel->find($id);
        if (!$currentNav) {
            $currentNav = $navModel->findWhere(['url_model' => 'products']);
        }

        $this->render('products/index', array_merge($this->getProductTemplateData($id), [
            'currentNav' => $currentNav ?? [],
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 1,
            'canonical_url' => $this->siteUrl('/products/' . ($currentNav['id'] ?? $id) . '.html'),
            'page_title' => $currentNav['seo_title'] ?: $this->seoTitle($currentNav['title'] ?? '搬家服务'),
            'page_keywords'   => $currentNav['seo_keyword'] ?? '',
            'page_description'=> $currentNav['seo_content'] ?? '',
        ]));
    }

    /**
     * 产品详情页
     */
    public function detail(int $id)
    {
        $productModel = new CmsProduct();
        $detail = $productModel->find($id);

        if (!$detail) {
            header('HTTP/1.1 404 Not Found');
            $this->render('error/404', ['page_title' => '404']);
            return;
        }

        $navModel = new CmsNav();
        $currentNav = $navModel->find($detail['nav_id'] ?? 0) ?: [];

        $this->render('products/detail', array_merge($this->getProductTemplateData($detail['nav_id'] ?? 0), [
            'detail'     => $detail,
            'banner'     => $currentNav['image'] ?? '/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg',
            'p_active'   => 1,
            'canonical_url' => $this->siteUrl('/detail/products' . $id . '.html'),
            'page_title'      => $detail['seo_title'] ?: $this->seoTitle($detail['title']),
            'page_keywords'   => $detail['seo_keyword'] ?? '',
            'page_description'=> $detail['seo_content'] ?? '',
            'page_image'      => $detail['image'] ?? '',
        ]));
    }

    /**
     * 商业服务（同产品逻辑）
     */
    public function business(int $id = 0)
    {
        return $this->index($id);
    }

    /**
     * 产品搜索（AJAX）
     */
    public function productSearch()
    {
        $keyword = $this->post('keyword', '');
        $navId   = (int)$this->post('nav_id', 0);

        $productModel = new CmsProduct();
        $sql = "SELECT id, title, sketch, image FROM " . $productModel->getTable()
             . " WHERE status = 1 AND delete_time IS NULL";

        $params = [];
        if ($keyword) {
            $sql .= " AND (title LIKE ? OR sketch LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }
        if ($navId) {
            $sql .= " AND nav_id = ?";
            $params[] = $navId;
        }

        $sql .= " ORDER BY sort ASC, id DESC LIMIT 20";
        $stmt = $productModel->getConnection()->prepare($sql);
        $stmt->execute($params);

        $this->json([
            'code' => 1,
            'data' => $stmt->fetchAll(),
        ]);
    }

    private function getProductTemplateData(int $navId): array
    {
        $productModel = new CmsProduct();
        return [
            'all'  => $productModel->select(
                ['nav_id' => $navId, 'status' => 1],
                'id, title, subtitle, sketch, image, link, target',
                'sort ASC, id DESC'
            ),
            'HFF'  => $this->getExpandSection(70),
            'CX'   => $this->getExpandSection(37),
            'BDTS' => $this->getExpandSection(43),
            'CJWT' => $this->getExpandSection(52),
            'why'  => $this->getExpandSection(3),
            'BZCL' => $this->getExpandSection(59),
        ];
    }
}
