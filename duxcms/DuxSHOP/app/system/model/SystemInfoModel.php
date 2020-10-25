<?php

/**
 * 系统配置
 */
namespace app\system\model;

use app\system\model\SystemModel;

class SystemInfoModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'info_id',
        'validate' => [
            'name' => [
                'required' => ['', '标题必须填写', 'must', 'all'],
            ],
            'key' => [
                'regex' => ['/^[a-zA-Z][a-zA-Z0-9_]*$/', '键名只能为英文数字或下划线', 'must', 'all'],
            ]
        ],
        'format' => [
            'reserve' => [
                    'string' => [0, 'all'],
                ],
        ],
        'into' => '',
        'out' => '',
    ];

    public function saveInfo() {
        $post = request('post');
        foreach ($post as $key => $value) {
            $data = array();
            $data['value'] = $value;
            $where = array();
            $where['key'] = $key;
            if(!$this->data($data)->where($where)->update()){
                return false;
            }
        }
        return true;
    }

    public function getConfig() {
        $list = $this->loadList();
        $data = array();
        foreach($list as $vo) {
            $data[$vo['key']] = $vo['value'];
        }
        return $data;
    }

    public function _delBefore($id) {
        $info = $this->getInfo($id);
        if(empty($info)){
            return false;
        }
        if($info['reserve'] == 1) {
            return false;
        }
        return true;
    }


}