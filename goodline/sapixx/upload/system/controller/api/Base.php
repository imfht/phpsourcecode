<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * API服务
 */
namespace app\system\controller\api;
use app\common\controller\Api;
use app\common\model\SystemUser;

class Base extends Api{
    
    /**
     * 邀请用户判断
     * @return void
     */
    protected function getUCodeUser(){
        $ucode = de_code(strtoupper($this->request->param('ucode/s')));
        if(empty($ucode)){
            return json(['code'=>204,'msg'=>'邀请码为空']);
        }
        $result = SystemUser::where(['id'=> $ucode])->field('nickname,invite_code,face')->find();
        if(empty($result)){
            return enjson(204);
        }
        return enjson(200,$result);
    }
}