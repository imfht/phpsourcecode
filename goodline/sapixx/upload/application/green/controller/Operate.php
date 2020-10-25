<?php
namespace app\green\controller;
use app\common\model\SystemMember;
use app\common\model\SystemUser;
use app\green\model\GreenMember;
use app\green\model\GreenOperate;
use think\facade\Request;

class Operate extends Common{

    public function initialize(){
        parent::initialize();
        if(!$this->founder){
            $this->error('您无权限操作');
        }
        $this->mini_program = ['member_miniapp_id' => $this->member_miniapp_id];
        $this->assign('pathMaps',[['name'=>'运营商','url'=>url("green/operate/index")]]);
    }

    /**
     * 列表
     */
    public function index(int $lock = 0){
        $where = [];
        if($lock){
            if($lock == 1){
                $where[] =  ['is_lock' ,'=', 0];
            }else{
                $where[] =  ['is_lock' ,'=', 1];
            }
        }
        $view['lists']       = GreenOperate::where($this->mini_program)->where($where)->order('id desc')->paginate(20);
        $view['operate_num'] = GreenOperate::where($this->mini_program)->count();
        $view['lock']        = $lock;
        return view()->assign($view);
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => Request::param('id/s'),
                'member_miniapp_id' => $this->member_miniapp_id,
                'uid'               => Request::param('uid/d', 0),
                'company'           => Request::param('company/s'),
                'address'           => Request::param('address/s'),
                'tel'               => Request::param('tel/s'),
                'operate_name'      => Request::param('operate_name/s'),
                'longitude'         => Request::param('longitude/s'),
                'latitude'          => Request::param('latitude/s'),
            ];
            $validate = $this->validate($data,'GreenOperate.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            if(empty($data['id'])){
                $data['create_time'] = time();
                $result = GreenOperate::create($data);
            }else{
                $result = GreenOperate::where(['id' => $data['id']])->update($data);
            }
            if($result){
                return enjson(200,'操作成功',['url'=>url('operate/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['info'] = GreenOperate::where($this->mini_program)->where(['id' => Request::param('id/d')])->find();
            return view()->assign($view);
        }
    }

    /**
     * 员工管理
     */
    public function user(int $id){
        $pathMaps[]         = ['name' => '管理员', 'url' => 'javascript:;'];
        $view['list']       = GreenMember::where($this->mini_program)->where(['operate_id' => $id])->order('id desc')->paginate(20);
        $view['pathMaps']   = $pathMaps;
        $view['operate_id'] = $id;
        return view()->assign($view);
    }

    /**
     * 选择城市管理员
     */
    public function winUser(int $operate_id){
        if(request()->isAjax()){
            $ids = Request::param('ids/s');
            if(empty($ids)){
                return json(['code'=>0,'msg'=>'请选择要关联的用户']);
            }
            $ida = ids($ids,true);
            $data = [];
            foreach ($ida as $key => $value) {
                $data[$key]['member_miniapp_id'] = $this->member_miniapp_id;
                $data[$key]['member_id']         = $value;
                $data[$key]['operate_id']        = $operate_id;
            }
            $result = GreenMember::insertAll($data);
            if($result){
                return enjson(200,'绑定管理员成功');
            }else{
                return enjson(0,'绑定管理员失败');
            }
        }else{
            $result = GreenMember::where($this->mini_program)->field('member_id')->select()->toArray();
            $uid  = [];
            if(!empty($result)){
                $uid = array_column($result,'member_id');
            }
            $view['list'] = SystemMember::whereNotIn('id',$uid)->where(['bind_member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $this->user->id,'is_lock' => 0])->order('id desc')->paginate(20);
            $view['operate_id'] = $operate_id;
            return view()->assign($view);
        }
    }


    /**
     * 选择运营商管理用户
     */
    public function selectUser(){
        $keyword = trim(Request::param('keyword','','htmlspecialchars'));
        $view['list']    = SystemUser::where($this->mini_program)->whereLike('nickname','%'.$keyword.'%')->order('id desc')->limit(10)->select();
        $view['keyword'] = $keyword;
        $view['input']   = trim(Request::param('input','','htmlspecialchars'));
        $view['id']      = $this->member_miniapp_id;
        return view()->assign($view);
    }


    //删除
    public function userDelete(int $id){
        $result = GreenMember::where($this->mini_program)->where(['id' => $id])->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'删除失败');
        }
    }

    /**
     * 是否锁定
     * @param integer $id 用户ID
     */
    public function isLock(int $id){
        $result = GreenOperate::where($this->mini_program)->where(['id' => $id])->field('is_lock')->find();
        if(!$result){
            return enjson(0,'删除失败');
        }else{
            $result->is_lock = $result->is_lock ? 0 : 1;
            $result->save();
            return enjson(200,'操作成功');
        }
    }
}