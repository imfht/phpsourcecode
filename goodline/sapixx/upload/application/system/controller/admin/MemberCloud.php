<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 站点设置
 */
namespace app\system\controller\admin;
use app\common\controller\Admin;
use app\common\model\SystemMemberCloudProduct;
use app\common\model\SystemMiniapp;

class MemberCloud extends Admin{

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = SystemMemberCloudProduct::order('id')->select();
        $view['pathMaps']  = [['name' => '市场列表','url' => url("system/admin.memberCloud/index")]];
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'product_id' => $this->request->param('product_id/d'),
                'miniapp_id' => $this->request->param('miniapp_id/d'),
            ];
            $validate = $this->validate($data,'Config.cloud');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  SystemMemberCloudProduct::create(['product_id' => $data['product_id'],'miniapp_id' => $data['miniapp_id']]);
            if($result){
                return enjson(200,'操作成功',['url' => url('system/admin.memberCloud/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $this->view->engine->layout('admin/main');
            $view['lists'] = SystemMiniapp::order('id desc')->select();
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'         => $this->request->param('id/d'),
                'product_id' => $this->request->param('product_id/d'),
                'miniapp_id' => $this->request->param('miniapp_id/d'),
            ];
            $validate = $this->validate($data,'Config.cloud');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  SystemMemberCloudProduct::where(['id' => $data['id']])->update(['product_id' => $data['product_id'],'miniapp_id' => $data['miniapp_id']]);
            if($result){
                return enjson(200,'操作成功',['url' => url('system/admin.memberCloud/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id  = input('get.id/d');
            $view['info'] = SystemMemberCloudProduct::where(['id' => $id])->find();
            $view['lists'] = SystemMiniapp::order('id desc')->select();
            $this->view->engine->layout('admin/main');
            return view()->assign($view);
        }
    }

    /**
     * 删除
     */
    public function delete(int $id){
        $result = SystemMemberCloudProduct::destroy(['id' => $id]);
        if($result){
            return enjson(200,'操作成功');
        }
        return enjson(403,'操作失败');
    }
}