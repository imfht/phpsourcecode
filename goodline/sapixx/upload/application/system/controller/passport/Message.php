<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 站内通知
 */
namespace app\system\controller\passport;
use app\common\model\SystemMemberSms;

class Message extends Common{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'站内通知','url'=>'javascript:;']]);
    }

    /**
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 消息中心首页
     */
    public function index(int $type = 0){
        $condition = [];
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        switch ($type) {
            case 1:
                $condition['is_read'] = 0;
                break;
            case 2:
                $condition['is_read'] = 1;
                break;
        }
        SystemMemberSms::where(['member_miniapp_id' => $this->member_miniapp_id,'is_new' => 0])->update(['is_new' => 1]);
        $view['list'] = SystemMemberSms::where($condition)->order('create_time desc')->paginate(20);
        $view['type'] = $type;
        return view()->assign($view);
    }

    /**
     * @return \think\response\Json
     * 查看未通知信息
     */
    public function count(){
        $count = SystemMemberSms::where(['member_miniapp_id' => $this->member_miniapp_id,'is_new' => 0])->count();
        return enjson(200,'成功',$count);
    }

    /**
     * @param int $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 设为已读
     */
    public function clean(int $id = 0){
        if($id > 0){
            $count = SystemMemberSms::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->update(['is_read' => 1,'is_new' => 1]);
            return enjson(200,'成功',$count);
        }else{
            return enjson(204,'请稍后重试');
        }
    }

    /*
    * 删除
    */
    public function delete(){
        $ids = $this->request->param('ids/s');
        if(empty($ids)){
            return json(['code'=>403,'msg'=>'请选择要删除的消息']);
        }
        $result = SystemMemberSms::whereIn('id',(array)ids($ids,true))->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        }
    }
}