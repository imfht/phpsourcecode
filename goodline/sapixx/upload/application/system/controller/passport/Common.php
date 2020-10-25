<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户公共调用模块
 */
namespace app\system\controller\passport;
use app\common\controller\Manage;
use app\common\facade\Upload;

class Common extends Manage{
   
    /**
     * 图片文件上传与管理
     */
    public function upload(){
        $userpath = strtolower(create_code($this->user->id));
        if(request()->isPost()){
            return json(Upload::index($userpath));
        }else{
            $view['input']  = $this->request->param('input');
            $view['close']  = $this->request->param('close/d',0);
            $view['path']   = $this->request->param('path','/');
            $view['tab']    = $this->request->param('tab');
            $view['lists']  = Upload::directoryResource($view['path'],false,$userpath);
            return view()->assign($view); 
        }
    }

    /**
     * 证书上传
     */
    public function cert($miniapp_id){
        if(request()->isPost()){
            if($this->member_miniapp_id != $miniapp_id){
                return json(['error'=>1,'message'=>'应用不存在']);
            }
            return json(Upload::cert($miniapp_id));
        }else{
            $view['input']      = $this->request->param('input');
            $view['miniapp_id'] = $miniapp_id;
            return view()->assign($view); 
        }
    }
}