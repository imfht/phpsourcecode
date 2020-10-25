<?php

/**
 * 地址管理
 */

namespace app\order\middle;

class AddressMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'order/OrderAddress';

    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '收货地址',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $where = [];
        $where['user_id'] = $userId;
        $model = target($this->_model);
        $list = $model->loadList($where, 0, '`default` desc, add_id desc');
        return $this->run([
            'pageList' => $list,
        ]);
    }

    protected function info() {
        $id = intval($this->params['id']);
        $userId = intval($this->params['user_id']);
        $info = target($this->_model)->getWhereInfo([
            'add_id' => $id,
            'user_id' => $userId
        ]);
        if(empty($info)) {
            return $this->stop('地址不存在!', 404);
        }
        return $this->run([
            'info' => $info
        ]);
    }

    protected function getAddress() {
        $id = intval($this->params['id']);
        $userId = intval($this->params['user_id']);
        $where = [
            'user_id' => $userId
        ];
        if($id) {
            $where['add_id'] = $id;
        }else {
            $where['default'] = 1;
        }
        $info = target($this->_model)->getWhereInfo($where);
        return $this->run($info);
    }

    protected function add() {
        $data = $this->params;
        $count = target($this->_model)->countList(['user_id' => $data['user_id']]);
        if($count >= 10) {
            return $this->stop('收货地址已上限,清先删除或修改其他地址!');
        }
        if(!$count) {
            $data['default'] = 1;
        }
        if(empty($data['name']) || empty($data['tel']) || empty($data['province']) || empty($data['city']) || empty($data['region']) || empty($data['address'])) {
            return $this->stop('收货信息填写不完整!');
        }

        target($this->_model)->beginTransaction();
        if($data['default']) {
            $status = target($this->_model)->where([
                'user_id' => $this->params['user_id']
            ])->data([
                'default' => 0
            ])->update();
            if(!$status) {
                return $this->stop(target($this->_model)->getError());
            }
        }
        $id = target($this->_model)->add($data);
        if(!$id) {
            target($this->_model)->rollBack();
            return $this->stop(target($this->_model)->getError());
        }
        target($this->_model)->commit();
        return $this->run([
            'id' => $id
        ], '收货地址添加成功!');
    }

    protected function edit() {
        $data = $this->params;
        if(empty($data['add_id'])) {
            return $this->stop('地址数据获取错误!');
        }
        if(empty($data['name']) || empty($data['tel']) || empty($data['province']) || empty($data['city']) || empty($data['region']) || empty($data['address'])) {
            return $this->stop('收货信息填写不完整!');
        }
        $info = target($this->_model)->getWhereInfo([
            'add_id' => $data['add_id'],
            'user_id' => $data['user_id']
        ]);
        if(empty($info)) {
            return $this->stop('该地址不存在或已删除!');
        }
        target($this->_model)->beginTransaction();
        if($data['default']) {
            $status = target($this->_model)->where([
                'user_id' => $this->params['user_id']
            ])->data([
                'default' => 0
            ])->update();
            if(!$status) {
                return $this->stop(target($this->_model)->getError());
            }
        }
        if(!target($this->_model)->edit($data)) {
            target($this->_model)->rollBack();
            return $this->stop(target($this->_model)->getError());
        }
        target($this->_model)->commit();
        return $this->run([], '收货地址修改成功!');
    }

    protected function del() {
        $this->params['id'] = intval($this->params['id']);
        $this->params['user_id'] = intval($this->params['user_id']);
        if(empty($this->params['id']) || empty($this->params['user_id'])) {
            return $this->stop('参数不正确!');
        }
        $info = target('order/OrderAddress')->getWhereInfo([
            'add_id' => $this->params['id'],
            'user_id' => $this->params['user_id']
        ]);
        if(empty($info)) {
            return $this->stop('该地址不存在或已删除!');
        }
        if($info['default']) {
            $newInfo = target($this->_model)->order('add_id desc')->getWhereInfo([
                '_sql' => 'add_id not in ('.$this->params['id'].')',
                'user_id' => $this->params['user_id']
            ]);
            if(!empty($newInfo)) {
                $status = target($this->_model)->where([
                    'add_id' => $newInfo['add_id']
                ])->data([
                    'default' => 1
                ])->update();
                if(!$status) {
                    return $this->stop(target($this->_model)->getError());
                }
            }
        }
        if(!target($this->_model)->del($this->params['id'])) {
            return $this->stop(target($this->_model)->getError());
        }
        return $this->run([], '收货地址删除成功!');
    }

    protected function default() {
        $this->params['id'] = intval($this->params['id']);
        $this->params['user_id'] = intval($this->params['user_id']);
        if(empty($this->params['id']) || empty($this->params['user_id'])) {
            return $this->stop('参数不正确!');
        }
        $status = target($this->_model)->where([
            'user_id' => $this->params['user_id']
        ])->data([
            'default' => 0
        ])->update();
        if(!$status) {
            return $this->stop(target($this->_model)->getError());
        }
        $status = target($this->_model)->where([
            'add_id' => $this->params['id'],
            'user_id' => $this->params['user_id']
        ])->data([
            'default' => 1
        ])->update();
        if(!$status) {
            return $this->stop(target($this->_model)->getError());
        }
        return $this->run([], '设为默认地址成功!');
    }

}