<?php
namespace app\api\controller\user;
use app\api\controller\BaseController;

class IndexController extends BaseController
{
	// 获取用户信息
    public function index()
    {
        $user_id = $this->get_user_id();
        if (request()->isPost()){
            $data['username'] = input('param.username');
            $re = model('User')->where('id', $user_id)->update($data);
            if($re){
                $user = model('User')->with('contact,avater')->find($user_id);

        	    $data['user'] = $user;
        	    return json(['data' => $data, 'msg' => '修改成功', 'code' => 1]);
        	}else{
        	    return json(['data' => false, 'msg' => '修改失败', 'code' => 0]);
        	} 
        }else{
            $user = model('User')->with('contact,avater')->find($user_id);

        	$data['user'] = $user;
            return json(['data' => $data, 'msg' => '用户信息', 'code' => 1]);
        }	
    }

    //上传用户头像
    public function avater()
    {
        $user_id = $this->get_user_id();
        // 获取表单上传文件
        $file = $this->request->file('image');
        
    	// 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            $item = array();
            $item['name'] = $info->getInfo('name');
            $item['type'] = $info->getInfo('type');
            $item['savename'] = $info->getFilename();
        	$item['savepath'] = date("Ymd") .'/';

            $re = model('File')->create($item);
        	$data['file'] = $re;
        	if($re){
        	    model('User')->where('id',$user_id)->setField('avater_id', $re->id);
        	    return json(['data' => $data, 'msg' => '上传成功', 'code' => 1]);
        	}else{
        	    return json(['data' => $data, 'msg' => '上传失败', 'code' => 0]);
        	}    
        } else {
            // 上传失败获取错误信息
            $msg = $this->error($file->getError());
            return json(['data' => $data, 'msg' => $msg, 'code' => 0]);
        }
    }

}