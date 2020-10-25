<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;

/**
 * 后台行为限制控制器
 */
class ActionLimit extends Admin
{
    public function _initialize()
    {

        parent::_initialize();
    }

    public function limitList()
    {
        $action_name = input('get.action','','text') ;
        !empty($action_name) && $map['action_list'] = ['like', '%[' . $action_name . ']%','','or'];
        //读取规则列表
        $map['status'] = ['EGT', 0];
        
        $list = model('common/ActionLimit')->getListByPage($map);

        $timeUnit = $this->getTimeUnit();
        foreach($list as &$val){
            $val['time'] = $val['time_number']. $timeUnit[$val['time_unit']];
            $val['action_list'] = model('Action')->getActionName($val['action_list']);
            empty( $val['action_list']) &&  $val['action_list'] = lang('_ALL_ACTS_');

            $val['punish'] = model('ActionLimit')->getPunishName($val['punish']);
        }
        unset($val);

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title(lang('_ACTION_LIST_'))
            ->buttonNew(url('editLimit'))
            ->setStatusUrl(url('setLimitStatus'))
            ->buttonEnable()
            ->buttonDisable()
            ->buttonDelete()
            ->keyId()
            ->keyTitle()
            ->keyText('name', lang('_NAME_'))
            ->keyText('frequency', lang('_FREQUENCY_'))
            ->keyText('time', lang('_TIME_UNIT_'))
            ->keyText('punish', lang('_PUNISHMENT_'))
            ->keyBool('if_message', lang('_SEND_REMINDER_'))
            ->keyText('message_content', lang('_MESSAGE_PROMPT_CONTENT_'))
            ->keyText('action_list', lang('_ACT_'))
            ->keyStatus()
            ->keyDoActionEdit('editLimit?id=###')
            ->data($list)
            ->display();
    }

    /**
     * [editLimit description]
     * @return [type] [description]
     */
    public function editLimit()
    {
        $aId = input('id', 0, 'intval');
        $model = model('ActionLimit');
        if (request()->isPost()) {

            $data['title'] = input('post.title', '', 'text');
            $data['name'] = input('post.name', '', 'text');
            $data['frequency'] = input('post.frequency', 1, 'intval');
            $data['time_number'] = input('post.time_number', 1, 'intval');
            $data['time_unit'] = input('post.time_unit', '', 'text');
            $data['punish'] = input('post.punish/a', array());
            $data['if_message'] = input('post.if_message', '', 'text');
            $data['message_content'] = input('post.message_content', '', 'text');
            $data['action_list'] = input('post.action_list/a');
            $data['status'] = input('post.status', 1, 'intval');
            $data['module'] = input('post.module', '', 'text');
            $data['id'] = $aId;
            
            $data['punish'] = implode(',', $data['punish']);
            if($data['action_list']){
                foreach($data['action_list'] as &$v){
                    $v = '['.$v.']';
                }
                unset($v);
                $data['action_list'] = implode(',', $data['action_list']);
            }

            $res = model('ActionLimit')->editData($data);
            
            if($res){
                $this->success(($aId == 0 ? lang('_ADD_') : lang('_EDIT_')) . lang('_SUCCESS_'), url('limitList'));
            }else{
                $this->error($aId == 0 ? lang('_THE_OPERATION_FAILED_') : lang('_THE_OPERATION_FAILED_VICE_'));
            }
        } else {

            $builder = new AdminConfigBuilder();
            //获取所有模块
            $modules = model('Module')->getAll();
            $module['all'] = lang('_TOTAL_STATION_');
            foreach($modules as $k=>$v){
                $module[$v['name']] = $v['alias'];
            }

            if ($aId != 0) {
                $limit = model('common/ActionLimit')->where(['id' => $aId])->find();
                $limit['punish'] = explode(',', $limit['punish']);
                $limit['action_list'] = str_replace('[','',$limit['action_list']);
                $limit['action_list'] = str_replace(']','',$limit['action_list']);
                $limit['action_list'] = explode(',', $limit['action_list']);

            } else {
                $limit = [
                    'status' => 1,
                    'time_number'=>1,
                    'time_unit'=>[],
                    ];
            }

            $opt_punish = $this->getPunish();

            $opt = model('common/Action')->getActionOpt();
            
            $builder->title(($aId == 0 ? lang('_NEW_') : lang('_EDIT_')) . lang('_ACT_RESTRICTION_'))->keyId()
                ->keyTitle()
                ->keyText('name', lang('_NAME_'))
                ->keySelect('module', lang('_MODULE_'),'',$module)
                ->keyText('frequency', lang('_FREQUENCY_'))
                ->keyMultiInput(
                    'time_number|time_unit',
                    lang('_TIME_UNIT_'),
                    lang('_TIME_UNIT_'),
                    [
                        ['type'=>'text','placeholder'=>'时间单位','style'=>'width:295px;margin-right:5px'],
                        ['type'=>'select','opt'=>get_time_unit(),'style'=>'width:100px']
                    ]
                )

                ->keyChosen('punish', lang('_PUNISHMENT_'), lang('_MULTI_SELECT_'), $opt_punish)
                ->keyBool('if_message', lang('_SEND_REMINDER_'))
                ->keyTextArea('message_content', lang('_MESSAGE_PROMPT_CONTENT_'))
                ->keyChosen('action_list', lang('_ACT_'), lang('_MULTI_SELECT_DEFAULT_'), $opt)
                ->keyStatus()
                ->data($limit)
                ->buttonSubmit(Url('editLimit'))
                ->buttonBack()
                ->display();
        }
    }


    public function setLimitStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('action_limit', $ids, $status);
    }

    private function getTimeUnit()
    {
        return get_time_unit();
    }


    private function getPunish()
    {
        $obj = model('ActionLimit');
        return $obj->punish;

    }
}
