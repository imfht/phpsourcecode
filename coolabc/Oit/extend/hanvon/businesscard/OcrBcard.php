<?php
namespace hanvon\businesscard;

use think\Config;

/**
 * 用于汉王阿里名片识别
 */
class OcrBcard {
    /**
     * @param null $image_file
     * @return mixed
     */
    public static function hanvonapi($image_file = null) {
        $config = Config::get('hanvon_ocr_bcard');

        $url = "http://api.hanvon.com/rt/ws/v1/ocr/bcard/recg?key=" . $config['key'] . "&code=cf22e3bb-d41c-47e0-aa44-a92984f5829d";
        //模拟发送POST请求（CURL四步走）
        //第一步：初始化curl
        $ch = curl_init();
        //第二步：设置相关参数
        //设置请求的url地址
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //禁止SSL证书的校检功能
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //模拟发送POST请求
        curl_setopt($ch, CURLOPT_POST, 1);
        $image = base64_encode(file_get_contents($image_file));
        $arr = [
            'uid' => $config['server_ip'],
            'lang' => 'auto',
            'color' => 'color',
            'image' => $image,
        ];
        $data = json_encode($arr);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //第三步：执行curl
        $output = curl_exec($ch);
        $inc = 0;
        while ($output == false) {
            $inc += 1;
            if ($inc == $config['max_num']) {
                break;
            }
            sleep(3);
        }
        // 判断输出结果是否异常
        if ($output === false) {
            $output = curl_error($ch);
            $result = [
                'state' => 'error',
                'info' => lang('识别失败'),
                'card_info' => $output,
            ];
        } else {
            $result = self::process_info($output);
        }
        // 第四步：关闭curl
        curl_close($ch);
        return $result;
    }

    /**
     * 读取名片数据
     * @param null $image_file
     * @return mixed
     */
    public static function aliapi($image_file = null) {
        $config = Config::get('hanvon_ocr_bcard');

        $host = "http://businesscard.aliapi.hanvon.com";
        $path = "/rt/ws/v1/ocr/bcard/recg";
        $method = "POST";
        $app_code = $config['code'];

        $headers = [];
        array_push($headers, "Authorization:APPCODE " . $app_code);
        array_push($headers, "Content-Type" . ":" . "application/octet-stream");
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
        $query = "code=cf22e3bb-d41c-47e0-aa44-a92984f5829d";
        $body = "{\"uid\": \"" . $config['server_ip'] . "\", \"lang\": \"auto\", \"color\": \"color\",\"image\": \"" . base64_encode(file_get_contents($image_file)) . "\"}";
        $url = $host . $path . "?" . $query;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($curl);
        //第四步：关闭curl
        curl_close($curl);
        return $result;
    }

    /**
     * 读取名片
     * @param null $image_file
     */
    public static function read($image_file = null) {
        $config = Config::get('hanvon_ocr_bcard');
        $read_method = $config['method'];
        $result = self::$read_method($image_file);

        return $result;
    }

    /**
     * 处理汉王成功时返回的数据
     * @param $card_info
     * @return array
     */
    public static function process_info($card_info) {
        $card_info = json_decode($card_info, true);
        $info = [];
        // 转成与 eba 的字段
        foreach ($card_info as $key => $val) {
            if (is_array($val)) {
                if (!empty($val)) {
                    switch ($key) {
                        case 'name':
                            $info['linkman'] = $val[0];
                            break;
                        case 'mobile':
                            $info['mobile_no'] = $val[0];
                            break;
                        case 'tel':
                            $info['office_no'] = $val[0];
                            break;
                        case 'email':
                            $info['e_mail'] = $val[0];
                            break;
                        case 'comp':
                            $info['eba_name'] = $val[0];
                            break;
                        case 'addr':
                            $info['address'] = $val[0];
                            break;
                    }
                }
            }
        }
        $result = [
            'state' => 'success',
            'card_info' => $info,
            'info' => lang('识别成功'),
        ];
        return $result;
    }

}