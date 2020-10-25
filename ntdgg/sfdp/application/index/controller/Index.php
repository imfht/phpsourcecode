<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;
use sfdp\sfdp;

class Index  extends Controller{
    public function index(){
      return $this->fetch();
    }
	public function welcome(){
	 
      return $this->fetch();
    }
	public function wfupsave($attr_id)
    {
        $files = $this->request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = \Env::get('root_path') . '/public/uploads/';
            $info = $file->move($path);
            if ($info) {
                $data[] = $info->getSaveName();
            } else {
                $error[] = $file->getError();
            }
        }
		
        return json(['msg'=>$data,'code'=>0,'info'=>$info->getInfo('name'),'attr_id'=>$attr_id]);
    }
}