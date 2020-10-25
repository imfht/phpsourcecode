<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

/**
 *	微信公众号管理后台控制器
 *  @version 1.0
 */
namespace app\mpbase\controller;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminTreeListBuilder;
use app\admin\controller\Admin;
use app\common\model\Channel;
use com\TPWechat;


class Mpbase extends Admin
{

    protected $weObj;          //管理后台自动注入的wechat SDK实例,用于管理公众号，自定义微信会员卡、优惠券、运营人员与微会员互动等场景

    //TP5 的架构方法绑定（属性注入）的对象
    public function __construct(TPWechat $weObj)
    {
        $this->weObj = $weObj;

        parent::__construct();
    }

    public function config()
    {

        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $admin_config->title('管理基本设置')
            ->keyBool('NEED_VERIFY', '公众号是否需要审核','默认无需审核')
            ->buttonSubmit('', '保存')->data($data);
        return $admin_config->fetch();
    }

    public function autoreply($page=1,$r=20)
    {
        //读取数据
        $model = model('Mpbase/Autoreply');
        $map['status'] = array('EGT', 0);
        $map['uid'] = UID;
        $map['mp_id'] = get_mpid();

        $list = $model->where($map)->page($page, $r)->order('id asc')->select();

        foreach ($list as &$val) {
            $val['type'] = $model->getArType($val['type']);
        }
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('自动回复设置')
            ->buttonNew(url('Mpbase/aredit'))
            ->setStatusUrl(url('setStatus'))->buttonEnable()->buttonDisable()->button('删除',array('class' => 'btn ajax-post tox-confirm', 'data-confirm' => '您确实要删除自动回复吗？', 'url' => url('ardel'), 'target-form' => 'ids'))
            ->keyId()->keyText('type','自动回复类型')->keyText('name', '名称')->keyStatus()->keyDoActionEdit('aredit?id=###')->keyDoAction('ardel?ids=###', '删除')
            ->data($list)
            ->pagination($totalCount, $r);
        return $builder->fetch();
    }

    public function ardel($ids = null){
        if (!$ids) {
            $this->error('请选择自动回复');
        }
        $model =  D('Mpbase/Autoreply');
        $res = $model->delete($ids);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function aredit($id = null)
    {
        $model = D('Mpbase/Autoreply');
        if (IS_POST) {   //提交表单
            $data['uid'] = I('post.uid', '', 'op_t');
            $data['mp_id'] = I('post.mp_id', '', 'op_t');
            $data['type'] = I('post.type', '', 'intval');
            $data['keyword_id'] = I('post.keyword_id', '', 'intval');
            $data['name'] = I('post.name', '', 'op_t');
            $data['content'] = I('post.content', '');
            $data['status'] = I('post.status', 1, 'intval');

            if ($id != 0) {
                $data['id'] = $id;
                $res = $model->editAr($data);
            } else {
                $res = $model->addAr($data);
            }
            $this->success(($id == 0 ? '添加' : '编辑') . '成功', $id == 0 ? U('', array('id' => $res)) : '');

        }else{   //显示表单
            //读取公众号信息
            if ($id != 0) {  //编辑
                $ar = $model->where(array('id' => $id))->find();

            } else {  //新增
                $ar = array('uid' => UID,'status' => 1,'mp_id' => get_mpid() );
            }

            //显示页面
            $builder = new AdminConfigBuilder();

                $builder->title($id != 0 ? '编辑自动回复' : '添加自动回复')
                    ->keyId()->keyHidden('uid', '', '')->keyHidden('mp_id', '', '')
                    ->keySelect('type', '类型', '请选择自动回复类型', $model->getArType(null))
                    ->keyText('keyword_id', '关键词ID', '关键词ID')
                    ->keyText('name', '名称', '名称')
                    ->keyTextArea('content', '自定义回复内容', '自定义回复内容')
                    ->keyStatus()
                    ->data($ar)
                    ->buttonSubmit(U('aredit?id=###'))->buttonBack()
                    ->display();
        }

    }

    public function index($page=1,$r=20)
    {
        //读取数据
        $model = db('MemberPublic');            //用后台builder的话，就得用db不能用model，model要重构的代码有点多...
        $map['status'] = array('EGT', 0);
        if ( !is_administrator() ) {  //管理员可以管理全部公众号，非管理员只能管理自己添加的公众号
            $map['uid'] = UID;
        }
        $list = $model->where($map)->page($page, $r)->order('id asc')->select();
        foreach ($list as &$val) {
            $val['u_name'] = model('Member')->where('uid=' . $val['uid'])->value('nickname');
            $val['type'] = model('MemberPublic')->getMpType($val['mp_type']);      //要用model里面的方法就只能这样咯
        }
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('公众号列表')
            ->buttonNew(url('mpbase/Mpbase/edit'))
            ->setStatusUrl(url('setStatus'))->buttonEnable()->buttonDisable()->button('删除',array('class' => 'btn ajax-post tox-confirm', 'data-confirm' => '您确实要删除公众号吗？（删除后对应的公众号配置将会清空，不可恢复，请谨慎删除！）', 'url' => url('del'), 'target-form' => 'ids'))
            ->keyId()->keyText('public_name', '名称')->keyText('wechat', '微信号')->keyText('public_id', '原始ID')->keyText('type','公众号类型')->keyStatus()->keyDoActionEdit('edit?id=###')->keyDoAction('del?ids=###', '删除')->keyDoAction('change?id=###', '切换为当前公众号')
            ->keyDoAction('mpbase/Mpbase/help?id=###', '接口配置')
            ->data($list)
            ->pagination($totalCount, $r);
        return $builder ->fetch();

    }

    /**
     * 删除公众号
     * @param null $id
     * @author patrick<contact@uctoo.com>
     */
    public function del($ids = null){
        if (!$ids) {
            $this->error('请选择公众号');
        }
        $model =  D('Mpbase/MemberPublic');
        $res = $model->delete($ids);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function edit($id = null)
    {

        if (request()->isPost()) {   //提交表单
            $model = model('MemberPublic');                       //提交的时候用了model，model那些特性还是要用哒
            $data['uid'] = input('post.uid', '', 'op_t');
            $data['public_name'] = input('post.public_name', '', 'op_t');
            $data['wechat'] = input('post.wechat', 1, 'op_t');
            $data['public_id'] = input('post.public_id', 1, 'op_t');
            $data['mp_type'] = input('post.mp_type', '', 'intval');
            $data['appid'] = input('post.appid', '', 'op_t');
            $data['secret'] = input('post.secret', '', 'op_t');
            $data['encodingaeskey'] = input('post.encodingaeskey', '', 'op_t');
            $data['mchid'] = input('post.mchid', '', 'op_t');
            $data['mchkey'] = input('post.mchkey', '', 'op_t');
            $data['notify_url'] = input('post.notify_url', '', 'op_t');
            $data['status'] = input('post.status', 1, 'intval');

            $data['mp_id'] = mpid_md5($data['appid']);

            if ($id != 0) {
                $data['id'] = $id;
                $res = $model->isUpdate(true)->save($data);
            } else {
                $res = $model->isUpdate(false)->save($data);
            }
            $this->success(($id == 0 ? '添加' : '编辑') . '成功', $id == 0 ? url('', array('id' => $res)) : '');

        }else{   //显示表单
            //读取公众号信息
            $model = db('MemberPublic');  //显示的时候用了db，TODO::统一后台builder支持db和model，自动判断类型？
            if ($id != 0) {  //编辑
                $mp = $model->where(array('id' => $id))->find();
                if($mp['uid']){  //公众号有归属帐号
                }else{
                    !is_administrator()?  :$mp['uid'] = UID  ;
                } ;
            } else {  //新增
                $mp = array('uid' => UID,'status' => 1);
            }
            $mpModel = model('MemberPublic');

            //显示页面
            $builder = new AdminConfigBuilder();
            if(is_administrator()){   //管理员可以设定公众号uid
                $builder->title($id != 0 ? '编辑公众号' : '添加公众号')
                    ->keyId()->keyUid('uid', '用户', '公众号管理员')->keyReadOnly('mp_id', '公众号索引ID', '公众号索引ID')->keyText('public_name', '名称', '公众号名称')->keyText('wechat', '微信号', '微信号')->keyText('public_id', '原始ID', '公众号原始ID')
                    ->keySelect('mp_type', '类型', '请选择公众号类型', $mpModel->getMpType(null))->keyText('appid', 'AppID', '应用ID')->keyText('secret', 'AppSecret', '应用密钥，需公众号管理员才能在mp.weixin.qq.com后台完整显示')
                    ->keyText('encodingaeskey', '消息加解密密钥', '安全模式下必填') ->keyText('mchid', '微信支付商户号', '微信支付必须配置') ->keyText('mchkey', '微信支付商户支付密钥', '微信支付必须配置')
                    ->keyText('notify_url', '接收微信支付异步通知回调地址', '微信支付必须配置，带http://的完整URL')
                    ->keyStatus()
                    ->data($mp)
                    ->buttonSubmit(url('edit'))->buttonBack();
                  return  $builder->fetch();
            }else{    //非管理员以登录uid作为公众号uid
                $builder->title($id != 0 ? '编辑公众号' : '添加公众号')
                    ->keyId()->keyReadOnly('uid', '用户', '公众号管理员')->keyReadOnly('mp_id', '公众号索引ID', '公众号索引ID')->keyText('public_name', '名称', '公众号名称')->keyText('wechat', '微信号', '微信号')->keyText('public_id', '原始ID', '公众号原始ID')
                    ->keySelect('mp_type', '类型', '请选择公众号类型', $mpModel->getMpType(null))->keyText('appid', 'AppID', '应用ID')->keyText('secret', 'AppSecret', '应用密钥，需公众号管理员才能在mp.weixin.qq.com后台完整显示')
                    ->keyText('encodingaeskey', '消息加解密密钥', '安全模式下必填') ->keyText('mchid', '微信支付商户号', '微信支付必须配置') ->keyText('mchkey', '微信支付商户支付密钥', '微信支付必须配置')
                    ->keyText('notify_url', '接收微信支付异步通知回调地址', '微信支付必须配置，带http://的完整URL')
                    ->keyStatus()
                    ->data($mp)
                    ->buttonSubmit(url('edit'))->buttonBack();
                     return  $builder->fetch();
            }

        }

    }

    public function change() {
        $map ['id'] = input ( 'id', 0, 'intval' );
        $info = model ( 'MemberPublic' )->where ( $map )->find ();
        set_mpid($info ['mp_id']);                                               //设置当前上下文mp_id

        unset ( $map );
        $map ['uid'] = UID;
        $res =  model ( 'Member' )->where( $map )->update ([ 'mp_token'=> $info['public_id']] );
            $user = session('user_auth');
            $user['mp_token'] = $info['public_id'];
            $user['mp_id'] = $info ['mp_id'];//修复mp_id问题，不知道有没其他影响
            $user['public_name'] = $info['public_name'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('切换公众号成功！');


        redirect ( url ( 'index' ) );
    }

    /*
     * 自动回复消息；
     *  - 关注回复
     *  - 关键词回复
     *  - 未匹配关键字回复
     * */
    public function replay_messages($r=10,$page=1){
        $autor = db('Mpbase/Autoreply');
        $messages = db('Mpbase/Messages');
        I('mtype')? $where['mtype'] = I('mtype'):null;
        $where['mp_id']=get_mpid();
        $list = $messages->get_replay_all($where);
        $message_type=$autor ->getMessagesType();
         $reply_type=$autor ->replyMessagesType();

        $builder = new AdminListBuilder();

        $mtype['mtype'] = I('mtype');

        $builder
            ->title('自动消息回复')
            ->buttonNew(U('edit_text_messages',$mtype),'新增文本消息')
            ->buttonNew(U('edit_picture_messages',$mtype),'新增图文消息')
            ->setSelectPostUrl(U(''))
            ->select('动作类型','mtype','select','','','',$message_type)
            ->keyId()
            ->keyText('mtype','动作类型')
            ->keyText('type','回复类型')
            ->keyText('title','规则名称')
            ->keyTime('time','创建时间')
            //转出想要的操作key 为do；
//            ->keyTruncText('detile','详情',15)
//            ->keyDoAction('editaction','操作')
            ->keyHtml('statu','操作')
            ->keyHtml('editaction','操作')
            ->data($list['data'])
            ->pagination($list['count'],$r)
            ->display();


    }

    public function edit_text_messages(){
        $auto = D('Mpbase/Autoreply');
        $model = D('Replay_messages');
        $id = I('id');
        $list['mtype'] = I('mtype');
        $mtype = $auto ->getArType();
        if(IS_POST){

            $data = I('post.');
            $data['type']= 'text';
            $data['mp_id']= get_mpid();
            $data['time']= time();
//            开始事务
            $model->startTrans();

            $res_mes = $auto ->post_messages($data);

            $data['ms_id']=$res_mes;

            if(!$id){
                $res =$model->field('id,title,statu,time,type,mp_id,mtype,ms_id,keywork')->add($data);

            }else{
                $res =$model->field('id,title,statu,time,type,mp_id,mtype,ms_id,keywork')->save($data);
            }

            if(!$res||!$res_mes){
                $model->rollback();
                $this->error($res_mes.'错误-'.$res);
            }else{
                $model->commit();
                $this->success('成功',"javascript:history.back(-1);");
            }
        }else{
        if($id) {
            $list = $model->find($id);
            $list = $auto->get_mes_data($list);
        }

        $builder = new AdminConfigBuilder();
        $builder
            ->title('文本')
            ->keyId()
            ->keyText('title','规则名称')
            ->keyRadio('mtype','动作类型','',$mtype)
            ->keyText('keywork','关键字','匹配多个关键字请以 ，号隔开')
            ->keyTextArea('detile','内容')
            ->data($list)
            ->buttonSubmit(U(''))
            ->buttonBack()
            ->display();
        }
    }


    public function edit_picture_messages(){

        $auto = D('Mpbase/Autoreply');
        $model = D('Replay_messages');
        $id = I('id');
        $list['mtype'] = I('mtype');

        $mtype = $auto ->getArType(null,1);

        if(IS_POST){
            $data = I('post.');

            $data['type']= 'picture';
            $data['mp_id']= get_mpid();
            $data['time']= time();
//            开始事务

            $model->startTrans();

            $res_mes = $auto ->post_messages($data);

            $data['ms_id']=$res_mes;


            if(!$id){
                $res =$model->where("id=$id")->field('title,statu,time,type,mp_id,mtype,ms_id,keywork')->add($data);

            }else{
                $res =$model->field('id,title,statu,time,type,mp_id,mtype,ms_id,keywork')->save($data);
            }
//            dump($model->getlastsql());
//            dump($res_mes);
//            dump($res);
//            dump($data);
//            die;


            if(!$res||!$res_mes){
                $model->rollback();
                $this->error($res_mes.'错误-'.$res);
            }else{
                $model->commit();
                $this->success('成功',"javascript:history.back(-1);");
            }

        }else{
            if($id) {
                $list = $model->find($id);

                $list = $auto->get_mes_data($list);
            }

//            dump($list);die;
    //        $auto->builder_picture_messages();
            $builder = new AdminConfigBuilder();
            $builder
                ->title('图文[只填写每一条则回复一条图文信息，多条则回复图文且第一条为大图]')
                ->keyId()
                ->keyText('title','规则名称')
                ->keyRadio('mtype','动作类型','',$mtype)
                ->keyText('keywork','关键字','匹配多个关键字请以 ，号隔开')
                ->keyText('title0','标题','第一条')
                ->keyText('detile0','内容')
                ->keyText('url0','url0')
                ->keyText('title1','标题','第二条')
                ->keyText('detile1','内容')
                ->keyText('url1','url1')
                ->keyText('title2','标题','第三条')
                ->keyText('detile2','内容')
                ->keyText('url2','url2')
                ->keyText('title3','标题','第四条')
                ->keyText('detile3','内容')
                ->keyText('url3','url3')
                ->keyText('title4','标题','第五条')
                ->keyText('detile4','内容')
                ->keyText('url4','url4')
                ->keyMultiImage('pic','图片','按顺序添加图片')
                ->data($list)
                ->buttonSubmit(U('edit_picture_messages',$mtype))
                ->buttonBack()
                ->display();

        }
    }

    public function open_mes_uq(){
        $id = I('get.id');
        $model = D('Mpbase/Messages');
        $res = $model->set_unqiue($id);

        if($res){
            redirect($_SERVER['HTTP_REFERER']);
        }
//        dump($res);
//        die;
        $this->error('错误');

    }

    public function open_mes_kw(){
        $id = I('get.id');
        $model = D('Mpbase/Messages');
        $res = $model->set_open($id);

        if($res){
            redirect($_SERVER['HTTP_REFERER']);
        }
        //dump($res);
        //die;
        $this->error('错误');

    }
    public function close_mes(){

        $id = I('get.id');
        $model = D('Mpbase/Messages');
        $res = $model->close_open($id);

        if($res){
            redirect($_SERVER['HTTP_REFERER']);
        }
        //dump($res);
        //die;
        $this->error('错误');

    }


    public function putfile(){

        $file = A('Core/File');
        $file->downloadFile(1);

//        $builder = new AdminConfigBuilder();
//        $builder
//            ->keySingleFile(111,11)
//            ->buttonSubmit()
//            ->display();


    }

    public function help() {
        if (empty ( input ('id') )) {
            $this->error ( '公众号参数非法' );
        }
        $map['uid'] = is_login();
        $map['id'] = input('id');
        $mp = db('MemberPublic')->where($map)->find();
        $this->assign('mp_id', $mp['mp_id']);

        $channel = new Channel;
        $navtree = $channel ->lists(true,true); //获取导航栏树
        $sub_menu['left']= array(array('tab' => 'home', 'title' => lang('_SQUARE_'), 'href' =>  url('index'))//,array('tab'=>'rank','title'=>'排行','href'=>url('rank'))
        );

        $this->assign('navtree', $navtree);
        $this->assign('sub_menu', $sub_menu);

        return $this->fetch( );
    }

}
