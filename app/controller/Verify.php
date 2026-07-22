<?php
namespace app\controller;

/**
 * 验证码控制器
 */
class Verify extends BaseController
{
    /**
     * 生成图形验证码
     */
    public function index()
    {
        $width  = $this->config['captcha']['width'] ?? 150;
        $height = $this->config['captcha']['height'] ?? 50;
        $length = $this->config['captcha']['length'] ?? 4;
        $fontSize = $this->config['captcha']['font_size'] ?? 20;

        // 生成随机码
        $code = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // 存入session
        session_start();
        $_SESSION['captcha'] = $code;

        // 创建画布
        $image = imagecreatetruecolor($width, $height);

        // 背景色
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        // 添加干扰线
        for ($i = 0; $i < 6; $i++) {
            $lineColor = imagecolorallocate($image,
                random_int(100, 200),
                random_int(100, 200),
                random_int(100, 200)
            );
            imageline($image,
                random_int(0, $width), random_int(0, $height),
                random_int(0, $width), random_int(0, $height),
                $lineColor
            );
        }

        // 添加干扰点
        for ($i = 0; $i < 100; $i++) {
            $pixelColor = imagecolorallocate($image,
                random_int(100, 200),
                random_int(100, 200),
                random_int(100, 200)
            );
            imagesetpixel($image,
                random_int(0, $width),
                random_int(0, $height),
                $pixelColor
            );
        }

        // 写入文字
        $fontPath = PUBLIC_PATH . 'static/home/fonts/arial.ttf';
        $x = 10;
        $y = (int) round($height / 2 + $fontSize / 2 - 5);
        for ($i = 0; $i < $length; $i++) {
            $textColor = imagecolorallocate($image,
                random_int(0, 100),
                random_int(0, 100),
                random_int(0, 100)
            );
            if (file_exists($fontPath)) {
                imagettftext($image, $fontSize, random_int(-15, 15), (int) round($x), $y, $textColor, $fontPath, $code[$i]);
            } else {
                imagestring($image, 5, (int) round($x), $y - (int) round($fontSize / 2), $code[$i], $textColor);
            }
            $x += $width / $length;
        }

        // 输出
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, max-age=0');
        imagepng($image);
        imagedestroy($image);
        exit;
    }
}
