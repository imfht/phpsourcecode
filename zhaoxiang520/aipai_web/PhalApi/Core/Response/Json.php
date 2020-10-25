<?php
namespace PhalApi\Core\Response;
use PhalApi\Core\Exception\PAException;
use PhalApi\Core\Response;

/**
 * @since   2016-09-03
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
class Json extends Response {

    protected $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];

    /**
     * 处理数据
     * @param $data
     * @throws PAException
     */
    protected function create( $data ) {
        $data = json_encode($data, $this->options['json_encode_param']);
        if ($data === false) {
            throw new PAException(json_last_error_msg());
        }
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS');
        if ( !headers_sent() ) {
            http_response_code($this->code);
            header('Content-Type:application/json; charset=utf-8');
        }
        echo $data;
    }
}