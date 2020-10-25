<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用管理
 */
namespace app\system\controller\admin;
use app\common\event\Admin;
use app\common\model\SystemMember;
use app\common\model\SystemMiniapp;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberMiniappOrder;
use app\common\model\SystemUser;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;
use app\system\event\AppConfig;
use app\system\event\Install;
use think\facade\Config;
use think\facade\Request;
use think\Validate;
use think\Db;
use Exception;

class Miniapp extends Common{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'应用管理','url'=>url("system/admin.miniapp/index")]]);
    }

    /**
     * 应用列表
     * @access public
     */
    public function index(){
        $view['list'] = SystemMiniapp::order('id desc')->paginate(10);
        $install_app = array_diff(Install::getDir(PATH_APP,['system','common','install']),SystemMiniapp::column('miniapp_dir')); //返回差集
        $i = 0;
        $app = [];
        foreach ($install_app as $value) {
            $app[$i] = AppConfig::version($value);
            $app[$i]['miniapp_dir'] = $value;
            $app[$i]['is_diyapp']   = $app[$i]['is_diyapp'] ?? 0;
            $i++;
        }
        $view['diff'] = $app;
        return view()->assign($view);
    }

    /**
     * @param $dir
     * @return \think\response\Json
     * 安装程序
     */
    public function install(){
        try {
            $dir = $this->request->param('dir');
            if(empty($dir)){
                return json(['code' => 0, 'msg' => '未找到应用']);
            }
            $param = AppConfig::version($dir);
            if(empty($param)){
                return json(['code' => 0, 'msg' => '未找到应用配置']);
            }
            $app = SystemMiniapp::column('miniapp_dir');
            if(in_array($dir,$app)){
                return json(['code' => 0, 'msg' => '应用已安装,禁止重复安装']);
            }
            //插入一条数据
            $data = [
                'types'         => $param['types'],
                'title'         => $param['name'],
                'version'       => $param['version'],
                'is_manage'     => $param['is_manage'],
                'is_wechat_pay' => $param['is_wechat_pay'],
                'is_alipay_pay' => $param['is_alipay_pay'],
                'is_openapp'    => $param['is_openapp'],
                'is_diyapp'     => 0,
                'describe'      => $param['describe'],
                'view_pic'      => Request::root(true)."/static/{$dir}/logo.png",
                'style_pic'     => [Request::root(true)."/static/{$dir}/logo.png"],
                'content'       => $param['describe'],
                'expire_day'    => 0,
                'sell_price'    => 0,
                'market_price'  => 0,
                'miniapp_dir'   => $dir,
                'template_id'   => 0,
                'qrcode'        => ''
            ];
            $validate = $this->validate($data,'miniapp.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $file = PATH_APP.$dir.'/install.sql';
            if (file_exists($file)) {
                $array = Install::get_sql_array($file,Config::get('database.prefix'));
                foreach ($array as $sql) {
                    Db::query($sql);
                }
                SystemMiniapp::edit($data);
                return json(['code' => 200, 'msg' => '操作成功']);
            }else{
                return json(['code' => 0, 'msg' => '未找到数据库脚本']);
            }
        } catch (Exception $e) {
            return json(['code' => 0,'msg' =>$e->getMessage()]);
        }
    }

        /**
     * 用户列表
     */
    public function select(){
        $keyword = $this->request->param('keyword');
        if(!empty($keyword)){
            $condition['phone_id'] = $keyword;     
        }else{
            $condition = [];
        }
        $view['keyword'] = $this->request->param('keyword');
        $view['input']   = $this->request->param('input');
        $view['list']    = SystemMiniapp::where($condition)->order('id desc')->paginate(10);
        return view()->assign($view);   
    } 
    
    /**
     * 切换应用管理
     * */
    public function manage(){
        $info = SystemMiniapp::where(['id' => $this->request->param('id/d')])->find();
        if(!$info){
            return json(['code'=>0,'msg'=>'操作失败']);
        }
        if($info['is_manage'] == 0){
            return json(['code'=>0,'msg'=>'当前应用没有独立管理中心']);
        }
        Admin::clearMiniapp();
        Admin::setMiniapp($info->id);
        return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.index/index')]);
    }

     /**
     * 列表
     * @access public
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'types'         => $this->request->param('types/s'),
                'title'         => $this->request->param('title/s'),
                'view_pic'      => $this->request->param('view_pic/s'),
                'style_pic'     => $this->request->param('imgs/a'),
                'version'       => $this->request->param('version/s'),
                'expire_day'    => $this->request->param('expire_day/d'),
                'sell_price'    => $this->request->param('sell_price/f',0),
                'market_price'  => $this->request->param('market_price/f',0),
                'is_manage'     => $this->request->param('is_manage/d',0),
                'is_diyapp'     => $this->request->param('is_diyapp/d',0),
                'is_wechat_pay' => $this->request->param('is_wechat_pay/d'),
                'is_alipay_pay' => $this->request->param('is_alipay_pay/d'),
                'miniapp_dir'   => $this->request->param('miniapp_dir/s'),
                'is_openapp'    => $this->request->param('is_openapp/d'),
                'template_id'   => $this->request->param('template_id/d'),
                'describe'      => $this->request->param('describe/s'),
                'qrcode'        => $this->request->param('qrcode/s'),
                'content'       => $this->request->param('content/s')
            ];
            $validate = $this->validate($data,'miniapp.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemMiniapp::edit($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.miniapp/index')]);
            }
        }else{
            return view();   
        }
    }   

    /**
     * 编辑用户
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'            => $this->request->param('id/s'),
                'types'         => $this->request->param('types/s'),
                'title'         => $this->request->param('title/s'),
                'view_pic'      => $this->request->param('view_pic/s'),
                'style_pic'     => $this->request->param('imgs/a'),
                'version'       => $this->request->param('version/s'),
                'expire_day'    => $this->request->param('expire_day/d'),
                'sell_price'    => $this->request->param('sell_price/f',0),
                'market_price'  => $this->request->param('market_price/f',0),
                'is_manage'     => $this->request->param('is_manage/d',0),
                'is_diyapp'     => $this->request->param('is_diyapp/d',0),
                'is_wechat_pay' => $this->request->param('is_wechat_pay/d'),
                'is_alipay_pay' => $this->request->param('is_alipay_pay/d'),
                'miniapp_dir'   => $this->request->param('miniapp_dir/s'),
                'is_openapp'    => $this->request->param('is_openapp/d'),
                'template_id'   => $this->request->param('template_id/d'),
                'describe'      => $this->request->param('describe/s'),
                'qrcode'        => $this->request->param('qrcode/s'),
                'content'       => $this->request->param('content/s')
            ];
            $validate = $this->validate($data,'miniapp.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemMiniapp::edit($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.miniapp/index')]);
            }
        }else{
            $id   = $this->request->param('id/d');
            $info = SystemMiniapp::where(['id' => $id])->find();
            if(!$info){
                return $this->error("404 NOT FOUND");
            }
            $info['style_pic'] = empty($info['style_pic']) ? json_decode('[]',true):json_decode($info['style_pic'],true);
            $view['info']      = $info;
            return view()->assign($view);
        }
    }

    
    /**
     * 锁定
     * @param integer $id 用户ID
     */
    public function islock(int $id){
        $result = SystemMiniapp::lock($id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }

    /**
     * [删除]
     * @access public
     * @return bool
     */
    public function delete(int $id){
        $member = SystemMemberMiniapp::where(['miniapp_id' => $id])->count();
        if($member){
            return json(['code' => 0,'msg'=>'用户已开通当前应用,建议禁用']);
        }
        $result  = SystemMiniapp::destroy($id);
        if(!$result){
            return json(['code' => 0,'msg'=>'操作失败']);
        }else{
            return json(['code' =>200,'msg'=>'操作成功']);
        }
    }

     /**
     * 授权管理
     * @access public
     * @return bool
     */
    public function authorizar(int $types = 0,int $miniapp_id = 0){
        $types = $types ? 1 : 0;
        $condition   = [];
        $condition[] = ['is_lock','=',$types];
        $keyword = $this->request->param('keyword');
        if(!empty($keyword)){
            $data['keyword'] = $keyword;
            $is_mobile = Validate::make()->rule('keyword','mobile')->check($data);
            if($is_mobile){
                $member = SystemMember::where(['phone_id' => $keyword])->field('id')->find();
                $condition[] = ['member_id','=',$member ? $member->id : 0];
            }else{
                $condition[] = ['appname','like','%'.$keyword.'%'];
            }
        }
        if($miniapp_id){
            $condition[] = ['miniapp_id','=',$miniapp_id];  
        }
        $view['keyword'] = $this->request->param('keyword');
        $view['list']    = SystemMemberMiniapp::where($condition)->order('id desc')->paginate(10,false,['query' =>['types' => $types]]);
        $apps = [];
        foreach ($view['list'] as $key => $value) {
            $apps[$key] = $value;
            switch ($value->miniapp->types) {
                case 'mp':
                    $head_img = $value->mp_head_img;
                    break;
                case 'program':
                    $head_img = $value->miniapp_head_img;
                    break;
                case 'app':
                    $head_img = $value->head_img;
                    break;
                default:
                    $head_img = empty($value->mp_head_img) ? $value->miniapp_head_img : $value->mp_head_img;
                    break;
            }
            $apps[$key]['logo'] = empty($head_img) ? "/static/{$value->miniapp->miniapp_dir}/logo.png" : $head_img;
        }
        $view['apps']               = $apps;
        $view['miniapp_num']        = SystemMiniapp ::where(['is_lock' => 0])->count();
        $view['member_miniapp_num'] = SystemMemberMiniapp ::where(['is_lock' => 0])->count();
        $view['consume']            = SystemMemberBankBill::where(['state'   => 1])->sum('money');
        $view['money']              = SystemMemberBank    ::sum('money');
        $view['consume']            = SystemMemberBankBill::where(['state'   => 1])->sum('money');
        $view['types']              = $types;
        $view['miniapp_id']         = $miniapp_id;
        return view()->assign($view);
    }   

     /**
     * 添加授权
     * @access public
     */
    public function addAuthorizar(){
        if(request()->isAjax()){
            $data = [
                'member_id'  => $this->request->param('member_id/d'),
                'miniapp_id' => $this->request->param('miniapp_id/d'),
                'appname'    => $this->request->param('appname/s'),
            ];
            $validate = $this->validate($data,'miniapp.addAuthorizar');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //新增购买列表
            $miniapp  = SystemMiniapp::where(['id' => $data['miniapp_id']])->find();
            $order['member_id']  = $data['member_id'];
            $order['miniapp_id'] = $data['miniapp_id'];
            $order['start_time'] = time();
            $order['end_time']   = time()+31536000;
            $order['is_lock']    = 1;
            $data['miniapp_order_id'] = SystemMemberMiniappOrder::insertGetId($order);
            if($data['miniapp_order_id']){
                $result  = SystemMemberMiniapp::edit($data);
                if($result){
                    return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.miniapp/Authorizar')]);
                }
            } 
            return json(['code'=>0,'msg'=>'操作失败']);      
        }else{
            return view();   
        }
    }   

    /**
     * 编辑授权
     */
    public function editAuthorizar(){
        if(request()->isAjax()){
            $data = [
                'id'           => $this->request->param('id/d'),
                'uid'          => $this->request->param('uid/d'),
                'navbar_color' => $this->request->param('navbar_color/s','','filter\Filter::filter_escape'),
                'navbar_style' => $this->request->param('navbar_style/s','','filter\Filter::filter_escape'),
            ];
            $validate = $this->validate($data,'miniapp.editAuthorizar');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemMemberMiniapp::where(['id'=>$data['id']])->update($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.miniapp/editAuthorizar',['id' => $data['id']])]);
            }
        }else{
            $id   = $this->request->param('id/d');
            $info = SystemMemberMiniapp::where(['id' => $id])->find();
            if(!$info){
                return $this->error("404 NOT FOUND");
            }
            $view['info']    = $info;
            $view['user']    = SystemUser::where(['id' => $info->uid])->find();
            $view['miniapp'] = SystemMiniapp::where(['id' => $info->miniapp_id])->find();
            return view()->assign($view);
        }
    }

    
    /**
     * 锁定授权
     * @param integer $id 用户ID
     */
    public function islockAuthorizar(int $id){
        $result = SystemMemberMiniapp::lock($id);
        if($result){
            return enjson(200);
        }
        return enjson(0,'解锁失败,请先解除用户锁定。');
    }

    /**
     * 读取应用所属微信(属于前台管理员)
     * @return void
     */
    public function selectWechatUser(){
        $keyword = $this->request->param('keyword');
        $view['keyword'] = $this->request->param('keyword');
        $view['input']   = $this->request->param('input');
        $view['id']      = $this->request->param('id');
        $view['list']    = [];
        if(!empty($keyword)){
            $view['list'] = SystemUser::where(['member_miniapp_id' => $view['id']])->whereLike('nickname','%'.$keyword.'%')->order('id desc')->limit(10)->select();
        }        
        return view()->assign($view);
    }
}