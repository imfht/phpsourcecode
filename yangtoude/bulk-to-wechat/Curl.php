<?php
/**
 * Curl类
 */
class Curl {

    function __construct ( ) {
        # code...
    }

    /**
     * 取得微信返回的JSON数据
     *
     * @param string $url
     * @return string json_decode
     */
    public function curl_get_json( $url ) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $raw = curl_exec($ch);
        curl_close($ch);

        return json_decode($raw, true);
    }

    /**
     * 传递不同的url和参数向微信订阅号接口发送不同post请求
     *
     * @param string $url 要请求的url地址
     * @param string $params json格式的post参数
     * @param string $fp 本地文件路径
     * @return string $raw|array $res
     */
    public function curl_post_wx($url, $params=false, $fp=false) {
        $c  = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_SAFE_UPLOAD, true);

        if ($fp === false) {
            // 上传图文/删除thumb_media_id/发送消息
            curl_setopt($c, CURLOPT_POSTFIELDS, $params);
            $raw = curl_exec($c);
            curl_close($c);
            return $raw;
        } elseif (file_exists($fp) && filesize($fp) > 0) {
            // 上传图片
            $cf = curl_file_create($fp, 'image/jpeg', basename($fp));
            curl_setopt($c, CURLOPT_POSTFIELDS, ['type'=>'image', 'media'=>$cf]);
            $raw = curl_exec($c);
            curl_close($c);
            $res = json_decode($raw, true);

            unlink($fp);
            unset($cf);

            if (isset($res['errcode']) && $res['errcode'] != 0) {
                //报告错误
                error_log('Error: 图片上传至微信订阅号错误 - ' . $fp . ' - ' . $raw);
				switch ($res['errcode']) {
					case 45001:
						exit('文章中有图片大小超过了2M');
					case 40001:
						exit('access_token无效，请联系网站管理员处理');
					default:
						exit($raw);
				}
            }
            return $res;
        } else {
            // 本地图片不存在/有问题
            error_log('Error: 图片' . $fp . '不存在或有问题');
            exit;
        }
    }

    /**
     * 将文章中的图片下载到本地
     *
     * @param string $url 图片地址
     * @return string $fp 图片保存路径
     */
    public function curl_get_img($url) {
        // 本地下载路径
        $wud = wp_upload_dir();
        $fp = $wud['path'] . '/' . basename($url);
        if (file_exists($fp)) {
            unlink($fp);
        }
        $im = fopen($fp,'wb');

        $c = curl_init($url);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        // 抓取时如果发生301，302跳转，则进行跳转抓取
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        // 最多跳转20次
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        // 发起连接前最长等待时间
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_FILE, $im);
        $r = curl_exec($c);
        curl_close($c);
        fclose($im);

        unset($wud);
        if (file_exists($fp) && filesize($fp) > 0) {
            return $fp;
        } else {
            error_log('Error: 图片' . $url . '下载错误');
            exit;
        }
    }

}
