<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 公众号管理
 */
namespace app\system\controller\passport;
use app\common\model\SystemMemberOfficialMenu;
use app\common\facade\WechatMp;
use Exception;

class Official extends Common{

    protected $app;

    public function initialize() {
        parent::initialize();
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if($this->user->parent_id){
            $this->error('无权限访问,只有创始人身份才允许使用.');
        }
        if(!$this->member_miniapp_id){
            $this->error('未找到所属应用,请先开通应用.');
        }
        if ($this->member_miniapp->miniapp->types == 'program' || $this->member_miniapp->miniapp->types == 'app'){
            $this->error('非公众号应用',url('system/passport.setting/index'));
        }
        if($this->member_miniapp->miniapp->is_openapp == 0){
            if(empty(WechatMp::isTypes($this->member_miniapp_id))){
               // $this->redirect('system/passport.setting/pushAuth',['id' => $this->member_miniapp_id,'types'=>'mp']);

                $this->error('请先授权公众号',url('passport.setting/index'),'去授权应用');
                exit;
            }
        }
        $this->assign('pathMaps',[['name'=>$this->member_miniapp->appname,'url'=>'javascript:;'],['name'=>'应用管理','url'=>url("system/passport.setting/index")],['name'=>'公众号管理','url'=>url("system/passport.official/index")]]);
    }

    /**
     * 导航菜单 
     */
    public function index(int $parent_id = 0){
        $view['lists']     = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $parent_id])->order('sort asc,id desc')->select(); 
        $view['menu']      = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort asc,id desc')->select(); 
        $view['parent_id'] = $parent_id;
        $view['action_btn'] = count($view['lists']) >= ($parent_id ? 5 : 3) ? false : true;
        return view('menu')->assign($view);;
    }

    /**
     * 公众号管理添加菜单
     * @access public
     */
    public function menuAdd(int $parent_id = 0){
        if(request()->isAjax()){
            $data = [
                'parent_id'         => $this->request->param('parent_id/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
                'types'             => $this->request->param('types/s'),
                'name'              => $this->request->param('name/s'),
                'key'               => $this->request->param('key/s'),
                'url'               => $this->request->param('url/s'),
                'pagepath'          => $this->request->param('pagepath/s'),
                'update_time'       => time(),
                'sort'              => 0,
            ];
            $validate = $this->validate($data,'Official.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $count = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' =>$data['parent_id']])->count();
            if($data['parent_id']){
                if($count >= 5) return json(['code'=>0,'msg'=>'二级菜单禁止超过5个']);
            }else{
                if($count >= 3) return json(['code'=>0,'msg'=>'一级菜单禁止超过3个']);
            }
            $result =  SystemMemberOfficialMenu::insert($data);
            if($result){
                return json(['code'=>200,'url'=>url('passport.official/index',['parent_id'=>$data['parent_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['menu']      = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort asc,id desc')->select(); 
            $view['parent_id'] = $parent_id;
            return view()->assign($view);
        } 
    }

    /**
     * 公众号管理编辑
     * @access public
     */
    public function menuEdit(int $id){
        if(request()->isAjax()){
            $data = [
                'id'          => $this->request->param('menu_id/d'),
                'parent_id'   => $this->request->param('parent_id/d'),
                'types'       => $this->request->param('types/s'),
                'name'        => $this->request->param('name/s'),
                'key'         => $this->request->param('key/s'),
                'url'         => $this->request->param('url/s'),
                'pagepath'    => $this->request->param('pagepath/s'),
                'update_time' => time(),
            ];
            $validate = $this->validate($data,'Official.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  SystemMemberOfficialMenu::update($data);
            if($result){
                return json(['code'=>200,'url'=>url('passport.official/index',['parent_id' => $data['parent_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['info'] = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            if(empty($view['info'])){
                $this->error("404 NOT FOUND");
            } 
            $view['menu'] = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort asc,id desc')->select(); 
            return view()->assign($view);
        }    
    }

  
    /**
     * 公众号菜单删除
     * @access public
     */
    public function menuDel(int $id){
        $info = SystemMemberOfficialMenu::where(['parent_id' => $id])->find();
        if($info){
            return json(['code'=>403,'msg'=>'删除失败,请查看是否包含子菜单']);
        }
        $result = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','url' => url('system/passport.official/index')]);
        }else{
            return json(['code'=>403,'msg'=>'删除失败']);
        } 
    }  

    /**
     * 公众号菜单排序
     * @access public
     */
    public function menuSort(){
        if(request()->isAjax()){
            $data = [
                'sort' => $this->request->param('sort/d'),
                'id'   => $this->request->param('id/d'),
            ];
            $validate = $this->validate($data,'Official.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = SystemMemberOfficialMenu::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $data['id']])->update(['sort' => $data['sort']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }      
 
    /**
     * 公众号菜单同步
     * @access public
     */
    public function menuSync(){
        if(request()->isAjax()){
            try {
                $menu = SystemMemberOfficialMenu::official_menu($this->member_miniapp_id);
                if(empty($menu)){
                    return json(['code'=>204,'msg'=>'微信菜单没有创建']);
                }
                $official  = WechatMp::isTypes($this->member_miniapp_id);
                if(!$official){
                    return json(['code'=>0,'msg'=>'【'.$this->member_miniapp->appname.'】微信认证失败,请确认公众号是否已经授权']); 
                }
                $rel = $official->menu->create($menu);
                if($rel['errcode'] == 0){
                    return json(['code'=>200,'msg'=>'【'.$this->member_miniapp->appname.'】同步成功']); 
                }else{
                    return json(['code'=>0,'msg'=>'【'.$this->member_miniapp->appname.'】同步失败,'.$rel['errcode']]); 
                }
            } catch (\Exception $e) {
                return enjson(0,$e->getMessage());
            }
        }
    }
}