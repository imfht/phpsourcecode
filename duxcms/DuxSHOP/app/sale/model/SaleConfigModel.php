<?php

/**
 * 推广设置
 */
namespace app\sale\model;

use app\system\model\SystemModel;

class SaleConfigModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'into' => '',
        'out' => '',
    ];

    public function getConfig() {
        $list = $this->loadList();
        $data = array();
        foreach($list as $vo) {
            $data[$vo['name']] = $vo['content'];
        }
        return $data;
    }

    public function saveInfo() {
        $post = request('post');
        $config = $this->getConfig();
        foreach ($post as $key => $value) {
            $where = array();
            $where['name'] = $key;
            $data = array();
            if(is_array($value)) {
                $data['content'] = serialize($value);
            }else{
                $data['content'] = html_in($value);
            }
            if(isset($config[$key])) {
                $status = $this->data($data)->where($where)->update();
            }else {
                $data['name'] = $key;
                $status = $this->data($data)->insert();
            }
            if(!$status){
                return false;
            }
        }
        return true;
    }

    public function levelWhereText() {
        return [
            1 => '推广订单总额',
            2 => '推广订单总数',
            3 => '直属推广订单金额',
            4 => '直属推广订单总数',
            5 => '自购订单金额',
            6 => '自购订单数量',
            7 => '直属下线人数',
            8 => '下线总人数',

        ];
    }


}