<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 后台公共调用模块
 */
namespace app\system\controller\admin;
use app\common\controller\Admin;
use app\common\facade\Upload;

class Common extends Admin{

    public function initialize() {
        parent::initialize();
    }

    /**
     * 图片文件上传与管理
     */
    public function upload(){
        if(request()->isPost()){
            return json(Upload::index());
        }else{
            $view['input']  = $this->request->param('input');
            $view['close']  = $this->request->param('close/d',0);
            $view['path']   = $this->request->param('path','/');
            $view['tab']    = $this->request->param('tab');
            $view['lists']  = Upload::directoryResource($view['path']);
            return view('upload',$view); 
        }
    }

    /**
     * 模板选择
     */
    public function tpl(){
        $view['input']  = $this->request->param('input');
        $view['path']   = $this->request->param('path','/');
        $view['lists']  = Upload::directoryResource($view['path'],true);
        return view('tpl',$view); 
    }

    /**
     * 证书上传
     */
    public function cert(){
        if(request()->isPost()){
            return json(Upload::cert());
        }else{
            $view['input'] = $this->request->param('input');
            return view()->assign($view); 
        }
    }
}