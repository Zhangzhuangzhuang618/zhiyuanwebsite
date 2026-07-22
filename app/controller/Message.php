<?php
namespace app\controller;

use app\model\CmsMessage;
use PHPMailer\PHPMailer\PHPMailer;

class Message extends BaseController
{
    /**
     * 留言页面
     */
    public function index(int $id = 0)
    {
        $page_title = '在线留言 - 志远搬家';
        if ($id > 0) {
            $navModel = new \app\model\CmsNav();
            $nav = $navModel->find($id);
            $page_title = $nav['title'] ?? $page_title;
        }

        $this->render('message/index', [
            'page_title' => $page_title,
        ]);
    }

    /**
     * 提交留言
     */
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['code' => 0, 'msg' => '请求方式错误']);
        }

        // 验证验证码
        $code = $this->post('captcha', '');
        session_start();
        if (strtolower($code) !== strtolower($_SESSION['captcha'] ?? '')) {
            $this->json(['code' => 0, 'msg' => '验证码错误']);
        }
        $_SESSION['captcha'] = ''; // 清除验证码

        // 收集数据
        $data = [
            'title'   => $this->filterInput($this->post('name', '')),
            'phone'   => $this->filterInput($this->post('phone', '')),
            'email'   => $this->filterInput($this->post('email', '')),
            'content' => $this->filterInput($this->post('content', '')),
            'nuit'    => $this->filterInput($this->post('type', '留言咨询')),
            'status'  => 0,
        ];

        if (empty($data['title']) || empty($data['phone'])) {
            $this->json(['code' => 0, 'msg' => '请填写姓名和电话']);
        }

        // 存入数据库
        try {
            $messageModel = new CmsMessage();
            $messageModel->add($data);
            $this->json(['code' => 200, 'msg' => '提交成功，我们将尽快与您联系！']);
        } catch (\Exception $e) {
            $this->json(['code' => 0, 'msg' => '提交失败，请稍后再试']);
        }
    }

    /**
     * 在线评估提交
     */
    public function assess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['code' => 0, 'msg' => '请求方式错误']);
        }

        $postData = $_POST['form'] ?? [];
        if (empty($postData)) {
            $this->json(['code' => 0, 'msg' => '请填写评估信息']);
        }

        $values = array_values(array_filter(array_map('trim', $postData), static function ($value) {
            return $value !== '';
        }));
        $phone = '';
        foreach ($values as $value) {
            if (preg_match('/^1[3-9]\d{9}$/', $value)) {
                $phone = $value;
                break;
            }
        }
        if ($phone === '') {
            $this->json(['code' => 0, 'msg' => '请填写正确的手机号码']);
        }

        $data = [
            'title'   => '在线报价咨询',
            'phone'   => $phone,
            'content' => implode("\n", $values),
            'nuit'    => '在线评估',
            'status'  => 0,
        ];

        try {
            $messageModel = new CmsMessage();
            $messageModel->add($data);
            $this->json(['code' => 200, 'msg' => '评估提交成功，我们将尽快与您联系！']);
        } catch (\Exception $e) {
            $this->json(['code' => 0, 'msg' => '提交失败，请稍后再试']);
        }
    }

    /**
     * 发送短信验证码（接口占位）
     */
    public function sendSms()
    {
        $phone = $this->post('phone', $this->post('form_email', ''));
        if (empty($phone)) {
            $this->json(['code' => 0, 'msg' => '手机号不能为空']);
        }

        // 当前网站未配置短信服务商，不能伪造“已发送”。
        $this->json(['code' => 0, 'msg' => '短信验证码服务尚未配置']);
    }
}
