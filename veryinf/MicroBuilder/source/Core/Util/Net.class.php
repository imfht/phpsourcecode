<?php
namespace Core\Util;

class Net {
    /**
     * @param string $url     URL
     * @param string $post    提交POST的数据
     * @param array $headers  附加的请求头
     * @param string $forceIp 强制使用特定IP
     * @param int $timeout    访问超时
     * @param array $options  其他选项
     * @return array|error 
     */
    public static function httpRequest($url, $post = '', $headers = array(), $forceIp = '', $timeout = 60, $options = array()) {
        $urls = parse_url($url);
        if(empty($urls['path'])) {
            $urls['path'] = '/';
        }
        if(!empty($urls['query'])) {
            $urls['query'] = "?{$urls['query']}";
        }
        if(empty($urls['port'])) {
            $urls['port'] = $urls['scheme'] == 'https' ? '443' : '80';
        }
        if(!empty($forceIp)) {
            $headers['Host'] = $urls['host'];
            $headers['Expect'] = '';
            $urls['host'] = $forceIp;
        }
        
        $url = "{$urls['scheme']}://{$urls['host']}:{$urls['port']}{$urls['path']}{$urls['query']}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if(is_array($post)) {
                $post = http_build_query($post);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($options) && is_array($options)) {
            foreach($options as $key => $val) {
                if(is_int($key)) {
                    curl_setopt($ch, $key, $val);
                } else {
                    curl_setopt($ch, constant($key), $val);
                }
            }
        }
        
        $ret = array();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) use(&$ret) {
            if(!empty($header)) {
                $pieces = explode(':', $header, 2);
                if(count($pieces) == 2) {
                    $key = strtolower($pieces[0]);
                    $var = trim($pieces[1]);
                    $ret['headers'][$key] = $var;
                }
            }
            return strlen($header);
        });
        $ret['content'] = curl_exec($ch);
        $ret['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if(!empty($errno)) {
            return error(1, $error);
        } else {
            return $ret;
        }
    }
    
    public static function httpGet($url, $forceIp = '') {
        $resp = self::httpRequest($url, '', array(), $forceIp);
        if(!is_error($resp)) {
            return $resp['content'];
        }
        return '';
    }
    
    public static function httpPost($url, $data, $forceIp = '') {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $resp = self::httpRequest($url, $data, $headers, $forceIp);
        if(!is_error($resp)) {
            return $resp['content'];
        }
        return '';
    }
}