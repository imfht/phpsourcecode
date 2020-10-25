<?php

/**
 * 商品详情
 */

namespace app\mall\api;

use \app\base\api\BaseApi;

class InfoApi extends BaseApi {

    protected $_middle = 'mall/Info';

    /**
     * 详情
     */
    public function index() {

        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['id'],
            'pro_id' => $this->data['pro_id'],
            'user_id' => $_SERVER['HTTP_AUTHUID'],
            'layer' => 'api'
        ])->classInfo()->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        }); 
    }
	
	
	/**
     * 修改购物车商品
     */
	public function spec() {
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['id'],
            'pro_id' => $this->data['pro_id'],
        ])->spec()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }

}