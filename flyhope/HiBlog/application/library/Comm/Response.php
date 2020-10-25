<?php
/**
 * 输出结构
 * 
 * @author chengxuan <i@chengxuan.li>
 */
namespace Comm;
abstract class Response {
    //响应体类型（JSON）
    const TYPE_JSON = 'json';

    //响应体类型（JS）
    const TYPE_JS = 'js';

    //响应体类型（HTML）
    const TYPE_HTML = 'html';

    /**
     * 输出响应类型
     * 
     * @param type $type
     */
    static public function contentType($type) {
        if (headers_sent()) {
            return false;
        }
        switch ($type) {
            case 'json' :
                header('Content-type: application/json; charset=utf-8');
                break;
            case 'html' :
                header('Content-type: text/html; charset=utf-8');
                break;
            case 'js' :
                header('Content-type: text/javascript; charset=utf-8');
                break;
            case 'jpg' :
                header('Content-Type: image/jpeg');
                break;
        }
        return true;
    }

    /**
     * 输出一段JSONP
     * @param type $code
     * @param type $msg
     * @param type $data
     * @param type $return
     * @return boolean
     */
    static public function jsonp($code, $msg, $data = null, $return = false) {
        self::contentType(self::TYPE_JS);
        $result = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data), JSON_UNESCAPED_UNICODE);
        $callback = \Comm\Arg::get('callback');
        
        if($callback && preg_match('/^[a-zA-Z0-9_]+$/', $callback)) {
            $result = "{$callback}({$result});";
        }
        
        if ($return) {
            return $result;
        } else {
            echo $result;
            return true;
        }
    }
    

    /**
     * 输出一段JSON
     * @param type $code
     * @param type $msg
     * @param type $data
     * @param type $return
     * @return boolean
     */
    static public function json($code, $msg, $data = null, $return = true) {
        $result = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data), JSON_UNESCAPED_UNICODE);
        if ($return) {
            return $result;
        } else {
            echo $result;
            return true;
        }
    }

    /**
     * 直接输出一段Header头和JSON
     *
     * @param mixed   $data          要编码的数据
     * @param boolean $output_header 是否输出JSONHEADER（默认是）
     *
     * @return void
     */
    static public function outputJson($data, $output_header = true) {
        $output_header && self::contentType(self::TYPE_JSON);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
}
