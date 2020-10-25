<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;

class Invite extends Admin
{
    protected $inviteModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->inviteModel=model('ucenter/Invite');
    }

    /**
     * 邀请注册基本配置
     */
    public function config()
    {
        $builder = new AdminConfigBuilder;
        $data = $builder->handleConfig();
        !isset($data['REGISTER_TYPE'])&&$data['REGISTER_TYPE']='normal';

        $register_options=array(
            'normal'=>lang('_ORDINARY_REGISTRATION_'),
            'invite'=>lang('_INVITED_TO_REGISTER_')
        );
        $builder
            ->title(lang('_INVITE_REGISTERED_INFORMATION_CONFIGURATION_'))
            ->keyCheckBox('REGISTER_TYPE', lang('_REGISTERED_TYPE_'), lang('_CHECK_TO_OPEN_'),$register_options)
            ->data($data)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }

    /**
     * 邀请码类型列表
     */
    public function index()
    {
        $data_list=Db::name('InviteType')->select();

        $builder=new AdminListBuilder();
        $builder->title(lang('_INVITE_CODE_TYPE_LIST_'))
            ->buttonNew(Url('Invite/edit'))
            ->button(lang('_DELETE_'),array('class' => 'btn ajax-post confirm', 'url' => Url('Invite/setDel', array('status' => -1)), 'target-form' => 'ids', 'confirm-info' => lang('_DELETE_CONFIRM_')))
            ->keyId()
            ->keyTitle()
            ->keyText('length',lang('_INVITE_CODE_LENGTH_'))
            ->keyText('time_show',lang('_LONG_'))
            ->keyText('cycle_num',lang('_PERIOD_CAN_BUY_A_FEW_'))
            ->keyText('cycle_time_show',lang('_PERIOD_IS_LONG_'))
            ->keyText('roles_show',lang('_BINDING_IDENTITY_'))
            ->keyText('auth_groups_show',lang('_ALLOWS_USERS_TO_BUY_'))
            ->keyText('pay',lang('_EACH_AMOUNT_'))
            ->keyText('income',lang('_AFTER_EVERY_SUCCESS_'))
            ->keyBool('is_follow',lang('_SUCCESS_IS_CONCERNED_WITH_EACH_OTHER_'))
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Invite/edit?id=###')
            ->data($data_list)
            ->display();
    }

    /**
     * 编辑邀请码类型
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function edit()
    {
        $aId=input('id',0,'intval');
        $is_edit=$aId?1:0;
        $title=$is_edit?lang('_EDIT_'):lang('_NEW_');
        if(request()->isPost()){
            $data['title']=input('post.title','','op_t');
            $data['length']=input('post.length',0,'intval');
            $data['time_num']=input('post.time_num',0,'intval');
            $data['time_unit']=input('post.time_unit','second','op_t');
            $data['cycle_num']=input('post.cycle_num',0,'intval');
            $data['cycle_time_num']=input('post.cycle_time_num',0,'intval');
            $data['cycle_time_unit']=input('post.cycle_time_unit','second','op_t');
            $data['roles']=input('post.roles',array());
            $data['auth_groups']=input('post.auth_groups',array());
            $data['pay_score_type']=input('post.pay_score_type',1,'intval');
            $data['pay_score']=input('post.pay_score',0,'intval');
            $data['income_score_type']=input('post.income_score_type',1,'intval');
            $data['income_score']=input('post.income_score',0,'intval');
            $data['is_follow']=input('post.is_follow',0,'intval');
            if($is_edit){
                $data['id']=$aId;
                $result=$this->inviteTypeModel->saveData($data);
            }else{
                $result=$this->inviteTypeModel->addData($data);
            }
            if($result){
                $this->success($title.lang('_INVITATION_CODE_TYPE_SUCCESS_'),Url('Invite/index'));
            }else{
                $this->error($title.lang('_INVITATION_CODE_TYPE_FAILED_').$this->inviteTypeModel->getError());
            }
        }else{

            $data= [];
            if($is_edit){
                $map['id']=$aId;
                $data=$this->inviteTypeModel->getData($map);

                $data['time']=explode(' ',$data['time']);
                $data['time_num']=$data['time'][0];
                $data['time_unit']=$data['time'][1];

                $data['cycle_time']=explode(' ',$data['cycle_time']);
                $data['cycle_time_num']=$data['cycle_time'][0];
                $data['cycle_time_unit']=$data['cycle_time'][1];
            }
            if(isset($data['length'])){
                $data['length']=$data['length'];
            }else{
                $data['length']=11;
            }
            

            $score_option=$this->_getMemberScoreType();
            $role_option=$this->_getRoleOption();
            $auth_group_option=$this->_getAuthGroupOption();
            $is_follow_option=array(
                0=>lang('_NO_'),
                1=>lang('_YES_')
            );

            $builder=new AdminConfigBuilder();

            $builder->title($title.lang('_INVITATION_CODE_TYPE_'));
            $builder
                ->keyId()
                ->keyTitle()
                ->keyText('length',lang('_INVITE_CODE_LENGTH_'))

                ->keyMultiInput('time_num|time_unit',lang('_LONG_'),lang('_TIME_UNIT_'),[
                    ['type'=>'text','style'=>'width:295px;margin-right:5px'],
                    ['type'=>'select','opt'=>get_time_unit(),'style'=>'width:100px']
                ])

                ->keyInteger('cycle_num',lang('_PERIOD_CAN_BUY_A_FEW_'))

                ->keyMultiInput('cycle_time_num|cycle_time_unit',lang('_PERIOD_IS_LONG_'),lang('_TIME_UNIT_'),[
                    array('type'=>'text','style'=>'width:295px;margin-right:5px'),
                    array('type'=>'select','opt'=>get_time_unit(),'style'=>'width:100px')
                ])

                ->keyChosen('roles',lang('_BINDING_IDENTITY_'),'',$role_option)

                ->keyChosen('auth_groups',lang('_ALLOWS_USERS_TO_BUY_'),'',$auth_group_option)

                ->keyMultiInput('pay_score_type|pay_score',lang('_EVERY_INVITATION_AMOUNT_'),lang('_SCORE_NUMBER_'),[
                    ['type'=>'select','opt'=>$score_option,'style'=>'width:100px;margin-right:5px'],
                    ['type'=>'text','style'=>'width:295px']
                ])

                ->keyMultiInput('income_score_type|income_score',lang('_EACH_INVITATION_WAS_SUCCESSFUL_'),lang('_SCORE_NUMBER_'),[
                    array('type'=>'select','opt'=>$score_option,'style'=>'width:100px;margin-right:5px'),
                    array('type'=>'text','style'=>'width:295px')
                ])

                ->keyRadio('is_follow',lang('_SUCCESS_IS_CONCERNED_WITH_EACH_OTHER_'),'',$is_follow_option)
                ->buttonSubmit()
                ->buttonBack()
                ->data($data)
                ->display();
        }
    }

    /**
     * 真删除邀请码类型
     * @param mixed|string $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setDel($ids,$status=-1)
    {
        $ids=is_array($ids)?$ids:explode(',',$ids);
        //删除邀请码类型，真删除
        if($status==-1){
            $this->inviteTypeModel->deleteIds($ids);
            $this->success(lang('_OPERATION_SUCCESS_'));
        }else{
            $this->error(lang('_UNKNOWN_OPERATION_'));
        }

    }

    /**
     * 邀请码列表页
     */
    public function invite()
    {
        $aBuyer=input('buyer',0,'intval');
        if($aBuyer==1){
            $map['uid']=['gt',0];
        }else{
            $map['uid']=['lt',0];
        }
        $aStatus=input('status',1,'intval');
        $status=$aStatus;
        if($aStatus==3){
            $status=1;
            $map['end_time']=['lt',time()];
        }else if($aStatus==1){
            $map['end_time']=['egt',time()];
        }
        $map['status']=$status;

        $aType=input('type',0,'intval');
        if($aType!=0){
            $map['invite_type']=$aType;
        }

        $list = Db::name('invite')->where($map)->paginate(20);
        $page = $list->render();

        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge(array(array('id'=>0,'value'=>lang('_ALL_'))),$typeOptions);
        if($aStatus==1){
            $this->assign('page',$page);
            $this->assign('invite_list',$list);
            $this->assign('buyer',$aBuyer);
            $this->assign('type_list',$typeOptions);
            $this->assign('now_type',$aType);
            return $this->fetch();

        }else{
            $builder=new AdminListBuilder();

            $builder
                ->title(lang('_INVITE_CODE_LIST_PAGE_'))
                ->setSelectPostUrl(Url('Invite/invite'))
                /*->buttonDelete(Url('Invite/delete'))*/
                ->buttonModalPopup(Url('Invite/createCode'),array(),lang('_GENERATE_AN_INVITATION_CODE_'),array('data-title'=>lang('_GENERATE_AN_INVITATION_CODE_')))
                ->buttonDelete(Url('Invite/deleteTrue'),lang('_DELETE_INV_CODE_WEAK_'))
                ->select('邀请码类型：','type','select','','','',$typeOptions)
                ->select('','status','select','','','',array(array('id'=>'1','value'=>lang('_REGISTERED_')),array('id'=>'3','value'=>lang('_EXPIRED_')),array('id'=>'2','value'=>lang('_HAS_BEEN_RETURNED_')),array('id'=>'0','value'=>lang('_RUN_OUT_')),array('id'=>'-1','value'=>lang('_ADMIN_DELETE_'))))
                ->select('','buyer','select','','','',array(array('id'=>'-1','value'=>lang('_ADMINISTRATOR_GENERATION_')),array('id'=>'1','value'=>lang('_USER_PURCHASE_'))))
                ->keyId()
                ->keyText('code',lang('_INVITATION_CODE_'))
                ->keyText('code_url',lang('_INVITE_CODE_LINK_'))
                ->keyText('invite',lang('_INVITATION_CODE_TYPE_'))
                ->keyText('buyer',lang('_BUYERS_'))
                ->keyText('can_num',lang('_CAN_BE_REGISTERED_A_FEW_'))
                ->keyText('already_num',lang('_ALREADY_REGISTERED_A_FEW_'))
                ->keyTime('end_time',lang('_PERIOD_OF_VALIDITY_'))
                ->keyCreateTime()
                ->data($list)
                ->page($page)
                ->display();
        }

    }

    /**
     * 生成邀请码
     */
    public function createCode()
    {
        if(request()->isPost()){
            $data['invite_type']=input('post.invite',0,'intval');
            $aCodeNum=input('post.code_num',0,'intval');
            $aCanNum=$data['can_num']=input('post.can_num',0,'intval');
            if($aCanNum<=0||$aCodeNum<=0){
                $result['status']=0;
                $result['info']=lang('_GENERATE_A_NUMBER_AND_CAN_BE_REGISTERED_A_NUMBER_CAN_NOT_BE_LESS_THAN_1_');
            }else{
                $result=$this->inviteModel->createCodeAdmin($data,$aCodeNum);
            }
            $this->ajaxReturn($result);
        }else{
            $type_list=$this->_getTypeList();
            $this->assign('type_list',$type_list);
            return $this->fetch('create');
        }
    }

    /**
     * 伪删除邀请码
     * @param string $ids
     */
    public function del($ids)
    {
        $ids=is_array($ids)?$ids:explode(',',$ids);
        $result=$this->inviteModel->where(array('id'=>array('in',$ids)))->setField('status','-1');
        if($result){
            $this->success(lang('_OPERATION_SUCCESS_'));
        }else{
            $this->error(lang('_OPERATION_FAILED_').$this->inviteModel->getError());
        }
    }

    /**
     * 删除无用的邀请码
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function delTrue()
    {
        $map['status']=array('neq',1);
        $map['end_time']=array('lt',time());
        $map['_logic']='OR';
        $result=$this->inviteModel->where($map)->delete();
        if($result){
            $this->success(lang('_OPERATION_SUCCESS_'));
        }else{
            $this->error(lang('_OPERATION_FAILED_').$this->inviteModel->getError());
        }
    }

    /**
     * 用户兑换名额记录
     */
    public function buyLog()
    {
        $aInviteType=input('invite_type',0,'intval');
        $aOrder=input('order',0,'intval');

        $map = [];
        if($aInviteType){
            $map['invite_type']=$aInviteType;
        }
        if($aOrder==0){
            $order='create_time desc';
        }elseif($aOrder==1){
            $order='create_time asc';
        }elseif($aOrder==2){
            $order='uid asc,invite_type asc,create_time desc';
        }

        $list=Db::name('inviteBuyLog')->where($map)->paginate(20);
        $page = $list->render();

        $orderOptions=array(
            array('id'=>0,'value'=>lang('_LATEST_CREATION_')),
            array('id'=>1,'value'=>lang('_FIRST_CREATED_')),
            array('id'=>2,'value'=>lang('_USER_'))
        );
        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge([['id'=>0,'value'=>lang('_ALL_')]],$typeOptions);

        $builder=new AdminListBuilder();
        $builder
            ->title(lang('_USER_EXCHANGE_QUOTA_RECORD_'))
            ->setSelectPostUrl(Url('Invite/buyLog'))
            ->select(lang('_INVITATION_CODE_TYPE_').lang('_COLON_'),'invite_type','select','','','',$typeOptions)
            ->select(lang('_SORT_TYPE_').lang('_COLON_'),'order','select','','','',$orderOptions)
            ->keyId()
            ->keyText('user',lang('_BUYERS_'))
            ->keyText('invite_type_title',lang('_INVITATION_CODE_TYPE_'))
            ->keyText('num',lang('_EXCHANGE_COUNT_'))
            ->keyText('content',lang('_INFORMATION_'))
            ->keyCreateTime()
            ->page($page)
            ->data($list)
            ->display();
    }

    /**
     * 用户邀请信息列表
     * @param int $page
     * @param int $r
     */
    public function userInfo()
    {
        $aInviteType=input('invite_type',0,'intval');

        $map = [];
        if($aInviteType){
            $map['invite_type']=$aInviteType;
        }
        $list = Db::name('inviteUserInfo')->where($map)->paginate(20);
        // 获取分页显示
        $page = $list->render();

        $typeOptions=$this->_getTypeList();
        foreach($typeOptions as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $typeOptions=array_merge([['id'=>0,'value'=>lang('_ALL_')]],$typeOptions);

        $builder = new AdminListBuilder();
        $builder
            ->title(lang('_USER_INFORMATION_'))
            ->setSelectPostUrl(Url('Invite/userInfo'))
            ->select(lang('_INVITATION_CODE_TYPE_').lang('_COLON_'),'invite_type','select','','','',$typeOptions)
            ->keyId()
            ->keyText('user',lang('_USER_'))
            ->keyText('invite_type_title',lang('_INVITATION_CODE_TYPE_'))
            ->keyText('num',lang('_AVAILABLE_'))
            ->keyText('already_num',lang('_ALREADY_INVITED_'))
            ->keyText('success_num',lang('_SUCCESSFUL_INVITATION_'))
            ->keyDoActionEdit('Invite/editUserInfo?id=###')
            ->page($page)
            ->data($list)
            ->display();
    }

    /**
     * 编辑用户邀请信息
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function editUserInfo()
    {
        $aId=input('id',0,'intval');
        if($aId<=0){
            $this->error(lang('_PARAMETER_ERROR_'));
        }
        if(request()->isPost()){
            $data['num']=input('num',0,'intval');
            $data['already_num']=input('already_num',0,'intval');
            $data['success_num']=input('success_num',0,'intval');
            if($data['num']<0||$data['already_num']<0||$data['success_num']<0){
                $this->error(lang('_PLEASE_FILL_IN_THE_CORRECT_DATA_'));
            }
            $result=$this->inviteUserInfoModel->saveData($data,$aId);
            if($result){
                $this->success(lang('_EDITOR_SUCCESS_'),Url('Admin/Invite/userInfo'));
            }else{
                $this->error(lang('_EDIT_FAILED_'));
            }
        }else{
            $map['id']=$aId;
            $data=$this->inviteUserInfoModel->getInfo($map);

            $builder=new AdminConfigBuilder();
            $builder->title(lang('_EDIT_USER_INVITATION_INFORMATION_'))
                ->keyId()
                ->keyReadOnly('uid',lang('_USER_ID_'))
                ->keyReadOnly('invite_type',lang('_INVITATION_CODE_TYPE_ID_'))
                ->keyInteger('num',lang('_AVAILABLE_'))
                ->keyInteger('already_num',lang('_INVITED_PLACES_'))
                ->keyInteger('success_num',lang('_SUCCESSFUL_INVITATION_'))
                ->data($data)
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }

    /**
     * 邀请日志
     * @param int $page
     * @param int $r
     */
    public function inviteLog()
    {
        $list = Db::name('InviteLog')->paginate(20);
        $page = $list->render();

        $builder=new AdminListBuilder();
        $builder->title(lang('_INVITE_REGISTRATION_RECORDS_'))
            ->keyId()
            ->keyText('user','注册者')
            ->keyText('inviter',lang('_INVITED_'))
            ->keyText('invite_type_title','邀请码类型')
            ->keyText('content',lang('_INFORMATION_'))
            ->keyCreateTime('create_time',lang('_REGISTRATION_TIME_'))
            ->page($page)
            ->data($list)
            ->display();
    }

    /**
     * 导出cvs
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function cvs()
    {
        $aIds=input('ids',array());

        if(count($aIds)){
            $map['id']=array('in',$aIds);
        }else{
            $map['status']=array('in',array(1,0,-1));
            $dataListBack=$this->inviteModel->getListAll(array('status'=>2));
        }
        $dataList=$this->inviteModel->getListAll($map,'status desc,end_time desc');
        if(!count($dataList)&&!count($dataListBack)){
            $this->error(lang('_NO_DATA_'));
        }
        if(count($dataListBack)){
            if(count($dataList)){
                $dataList=array_merge($dataList,$dataListBack);
            }else{
                $dataList=$dataListBack;
            }
        }
        $data=lang('_DATA_MANY_')."\n";
        foreach ($dataList as $val) {
            if($val['status']==-1){
                $val['status']=lang('_ADMIN_DELETE_');
            }elseif($val['status']==0){
                $val['status']=lang('_RUN_OUT_');
            }elseif($val['status']==1){
                if($val['end_time']<=time()){
                    $val['status']=lang('_EXPIRED_');
                }else{
                    $val['status']=lang('_REGISTERED_');
                }
            }elseif($val['status']==2){
                $val['status']=lang('_HAS_BEEN_RETURNED_');
            }
            $val['end_time']=time_format($val['end_time']);
            $val['create_time']=time_format($val['create_time']);
            $data.=$val['id'].",[".$val['invite_type']."]".$val['invite'].",".$val['code'].",".$val['code_url'].",[".$val['uid']."]".$val['buyer'].",".$val['can_num'].",".$val['already_num'].",".$val['end_time'].",".$val['status'].",".$val['create_time']."\n";
        }
        $data=iconv('utf-8','gb2312',$data);
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$data); //导出
    }

    private function export_csv($filename,$data) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        echo $data;
    }

    //私有函数 start

    /**
     * 获取身份列表
     * @return mixed
     */
    private function _getRoleOption()
    {
        $role_option=Db::name('Role')->where(['status'=>1])->order('sort asc')->field('id,title')->select();
        return $role_option;
    }

    /**
     * 获取权限用户组列表
     * @return mixed
     */
    private function _getAuthGroupOption()
    {
        $role_option=Db::name('AuthGroup')->where(['status'=>1])->field('id,title')->select();
        return $role_option;
    }

    /**
     * 获取积分类型列表
     * @return array
     */
    private function _getMemberScoreType()
    {
        $score_option=Db::name('UcenterScoreType')->where(array('status'=>1))->field('id,title')->select();
        $score_option=array_combine(array_column($score_option,'id'),array_column($score_option,'title'));
        return $score_option;
    }

    private function _getTypeList(){
        $map['status']=1;
        $type_list=model('ucenter/InviteType')->getSimpleList($map);
        return $type_list;
    }

    //私有函数 end
} 