<?php

/**
 * 推广二维码
 */

namespace app\sale\middle;

class QrcodeMiddle extends \app\base\middle\BaseMiddle {


    protected function meta() {
        return parent::meta('我的二维码', '我的二维码');
    }

    private function saleInfo() {
        $userId = intval($this->params['user_id']);

        return target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId,
            'agent' => 1
        ]);
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $userInfo = $this->params['user_info'];

        $config = target('sale/SaleConfig')->getConfig();

        $saleInfo = $this->saleInfo();
        if (empty($saleInfo)) {
            return $this->stop('请先成为推广商!', 302, url('sale/Apply/index'));
        }
        $code = $saleInfo['code'];

        $path = 'upload/qrcode/' . $userId . '/';
        $dir = ROOT_PATH . $path;
        if (!is_dir($dir)) {
            if (!\mkdir($dir, 0777, true)) {
                return $this->stop('upload目录没有写入权限!');
            }
        }

        $shareFile = $dir . 'share.jpg';
        $shareUrl = url(VIEW_LAYER_NAME . '/index/Index/index', ['sale_code' => $code], true, true, false);

        if(strstr($config['qrcode_image'], 'http') === false) {
            $sharePath = ROOT_PATH . $config['qrcode_image'];
        }else {
            $sharePath = $config['qrcode_image'];
        }

        $main = imagecreatefrompng($sharePath);
        $width = imagesx($main);
        $dstShare = @imagecreatefromstring(file_get_contents($sharePath));
        $font = ROOT_PATH . 'public/fonts/wqy-microhei.ttc';

        if($config['qrcode_status']) {
            $qrcodePath = $dir . "qrcode.png";

            $qrCode = new \Endroid\QrCode\QrCode($shareUrl);
            $qrCode->setSize(300);
            $qrCode->setWriterByName('png');
            $qrCode->setMargin(11);
            $qrCode->setEncoding('UTF-8');
            $qrCode->writeFile($qrcodePath);

            //缩放二维码
            $dstCode = imagecreatefromstring(file_get_contents($qrcodePath));
            $qrcodeInfo = getimagesize($qrcodePath);
            $thumb = imagecreatetruecolor($config['qrcode_width'], $config['qrcode_height']);
            imagecopyresampled($thumb, $dstCode, 0, 0, 0, 0, $config['qrcode_width'], $config['qrcode_height'], $qrcodeInfo[0], $qrcodeInfo[1]);

            //写入二维码
            imagecopy($dstShare, $thumb, $config['qrcode_x'], $config['qrcode_y'], 0, 0, $config['qrcode_width'], $config['qrcode_height']);
            imagedestroy($dstCode);
            imagedestroy($thumb);

        }

        //写入头像
        if (!empty($userInfo['avatar']) && $config['qrcode_avatar_status']) {
            $dstAvatar = imagecreatefromstring(file_get_contents($userInfo['avatar'] . '?type=3'));
            $avatarInfo = getimagesize($userInfo['avatar'] . '?type=3');
            $thumbAvatar = imagecreatetruecolor($config['qrcode_avatar_width'], $config['qrcode_avatar_height']);
            imagecopyresampled($thumbAvatar, $dstAvatar, 0, 0, 0, 0, $config['qrcode_avatar_width'], $config['qrcode_avatar_height'], $avatarInfo[0], $avatarInfo[1]);
            imagecopy($dstShare, $thumbAvatar, $config['qrcode_avatar_x'], $config['qrcode_avatar_y'], 0, 0, $config['qrcode_avatar_width'], $config['qrcode_avatar_height']);
            imagedestroy($dstAvatar);
        }

        //写入用户名
        if($config['qrcode_username_status']) {
            $color = $this->hex2rgb($config['qrcode_username_color']);
            $fontSize = $config['qrcode_username_size'];
            $fontColor = imagecolorallocate($dstShare, $color['r'], $color['g'], $color['b']);
            $username = $userInfo['show_name'];
            if($config['qrcode_username_align']) {
                $fontBox = imagettfbbox($fontSize, 0, $font, $username);
                imagettftext($dstShare, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), $config['qrcode_username_y'], $fontColor, $font, $username);
            }else {
                imagettftext($dstShare, $fontSize, 0, $config['qrcode_username_x'], $config['qrcode_username_y'], $fontColor, $font, $username);
            }
        }
        //写入广告文字
        for ($i = 1; $i <=3; $i++) {
            if($config['qrcode_ad'.$i.'_status']) {
                $color = $this->hex2rgb($config['qrcode_ad'.$i.'_color']);
                $fontSize = intval($config['qrcode_ad'.$i.'_size']);
                $str = $config['qrcode_ad'.$i.'_text'];
                $fontColor = imagecolorallocate($dstShare, $color['r'], $color['g'], $color['b']);
                $config['qrcode_ad'.$i.'_y'] = intval($config['qrcode_ad'.$i.'_y']);
                $config['qrcode_ad'.$i.'_x'] = intval($config['qrcode_ad'.$i.'_x']);
                if($config['qrcode_ad'.$i.'_align']) {
                    $fontBox = imagettfbbox($fontSize, 0, $font, $str);
                    imagettftext($dstShare, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), $config['qrcode_ad'.$i.'_y'], $fontColor, $font, $str);
                }else {
                    imagettftext($dstShare, $fontSize, 0, $config['qrcode_ad'.$i.'_x'], $config['qrcode_ad'.$i.'_y'], $fontColor, $font, $str);
                }
            }
        }

        //写入推荐码
        if($config['qrcode_code_status']) {
            $color = $this->hex2rgb($config['qrcode_code_color']);
            $fontSize = intval($config['qrcode_code_size']);
            $config['qrcode_code_y'] = intval($config['qrcode_code_y']);
            $config['qrcode_code_x'] = intval($config['qrcode_code_x']);
            $str = $code;
            $fontColor = imagecolorallocate($dstShare, $color['r'], $color['g'], $color['b']);
            if($config['qrcode_code_align']) {
                $fontBox = imagettfbbox($fontSize, 0, $font, $str);
                imagettftext($dstShare, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), $config['qrcode_code_y'], $fontColor, $font, $str);
            }else {
                imagettftext($dstShare, $fontSize, 0, $config['qrcode_code_x'], $config['qrcode_code_y'], $fontColor, $font, $str);
            }
        }


        ob_start();//启用输出缓存，暂时将要输出的内容缓存起来
        imagejpeg($dstShare, null, 100);//输出
        $poster = ob_get_contents();//获取刚才获取的缓存
        ob_end_clean();//清空缓存
        imagedestroy($dstShare);

        file_put_contents($shareFile, $poster);
        $shareImg = DOMAIN . ROOT_URL . '/' . $path . 'share.jpg?t=' . time();

        return $this->run([
            'share_img' => $shareImg,
            'share_url' => $shareUrl,
            'sale_code' => $code
        ]);
    }



    private function hex2rgb($colour) {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = [$colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]];
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = [$colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]];
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }


}