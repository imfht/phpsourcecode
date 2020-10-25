<?php
/**
 * Created by Wenlong li
 * User: wenlong
 * Date: 2018/9/19
 * Time: 下午12:15
 */

namespace Kernel\Core\Mime;


use Kernel\AgileCore;

class Response
{
//响应体类型（JSON）
    const TYPE_JSON = 'json';

    //响应体类型（JS）
    const TYPE_JS = 'js';

    //响应体类型（HTML）
    const TYPE_HTML = 'html';

    /**
     * Swoole响应对象
     *
     * @var \Swoole\Http\Response
     */
    protected $_response;

    /**
     * 构造方法
     *
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Response $response) {
        $this->_response = $response;
    }

    /**
     * 输出响应类型
     *
     * @param string $type
     * @param boolean $set
     * @return string
     */
    public function contentType($type, $set = false) {
        $content_type = self::showContentType($type);

        if ($content_type && $set) {
            $this->_response->header('Content-Type', $content_type);
        }
        return $content_type;
    }

    /**
     * 根据扩展名获取Content-Type
     *
     * @param string $type
     *
     * @return string
     */
    public static function showContentType($type) {
        $result = '';
        try {
            $config = AgileCore::getInstance()->getConfig('statics');
            if (!empty($config[$type]['content_type'])) {
                $result = $config[$type]['content_type'];
            }
        }catch (\Exception $exception) {
            $result = 'text/html';
        }finally{
            if($result == '') {
                $result = 'text/html';
            }
        }
        return $result;
    }

    /**
     * 跳转至一个页面
     *
     * @param \Swoole\Http\Response $response
     * @param string                $url
     * @param int                   $code
     *
     * @return void
     */
    public static function location($response, $url, $code = 302) {
        $response->status($code);
        $response->header('Location', $url);
        $response->end();
    }

    /**
     * 输出一段JSON
     *
     * @param mixed $code
     * @param string $msg
     * @param mixed $data
     */
    public function json($code, $msg = '', $data = null) {
        $result = json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
        $this->contentType(self::TYPE_JSON);
        $this->_response->end($result);
    }
}