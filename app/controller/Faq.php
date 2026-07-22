<?php
namespace app\controller;

class Faq extends BaseController
{
    public function index(): void
    {
        $faqs = require CONFIG_PATH . 'faq.php';
        $schemaItems = [];
        foreach ($faqs as $faq) {
            $schemaItems[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ];
        }

        $this->render('faq/index', [
            'faqs' => $faqs,
            'p_active' => 6,
            'canonical_url' => $this->siteUrl('/faq.html'),
            'page_title' => '广州搬家常见问题｜费用、流程、打包与搬运指南 - 志远搬家',
            'page_description' => '广州搬家常见问题解答，涵盖费用构成、预约、打包、家具拆装、同城和跨市搬家、办公室及设备搬迁等实用信息。',
            'page_keywords' => '广州搬家常见问题,广州搬家费用,搬家流程,搬家打包,搬家公司选择',
            'structured_data' => array_merge([[
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $schemaItems,
            ]], $this->breadcrumbSchema([
                ['name' => '首页', 'url' => '/'],
                ['name' => '搬家常见问题', 'url' => '/faq.html'],
            ])),
        ]);
    }
}
