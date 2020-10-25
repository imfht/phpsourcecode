<?php

/**
 * 支付设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;


class PayConfAdmin extends \app\system\admin\SystemAdmin {

    protected $_model = 'PayConfig';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '支付设置',
                'description' => '设置系统支付接口信息',
            ),
        );
    }

    /**
     * 站点设置
     */
    public function index() {
        $this->assign('list', target('PayConfig')->typeList());
        $this->systemDisplay();
    }

    /**
     * 配置
     */
    public function setting() {
        if (!isPost()) {
            $type = request('get', 'type');
            if (empty($type)) {
                $this->error('参数不能为空！');
            }
            $typeList = target('PayConfig')->typeList();
            $typeInfo = $typeList[$type];
            $where = array();
            $where['type'] = $type;
            $info = target($this->_model)->getWhereInfo($where);
            $this->assign('info', $info);
            $this->assign('settingInfo', unserialize($info['setting']));
            $this->assign('typeInfo', $typeInfo);
            $this->assign('ruleList', $typeInfo['configRule']);
            $this->assign('type', $type);
            $this->systemDisplay();
        } else {
            $post = request('post');
            $data = array();
            $data['status'] = $post['status'];
            $data['type'] = $post['type'];
            $data['setting'] = serialize($post);
            if ($post['config_id']) {
                $data['config_id'] = $post['config_id'];
                $type = 'edit';
            } else {
                $type = 'add';
            }
            if (target($this->_model)->saveData($type, $data)) {
                //编辑后处理
                $this->success('保存成功！', url('index'));
            } else {
                $this->error(target($this->_model)->getError());
            }
        }
    }

}