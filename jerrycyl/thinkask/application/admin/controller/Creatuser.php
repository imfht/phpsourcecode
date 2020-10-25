<?php
namespace app\Admin\controller;
use app\common\controller\AdminBase;
class Creatuser extends AdminBase{

	public function creat(){

		return $this->fetch('admin/creatuser/creat'); 
	}

       /**
 * [creatuser 生成用户]
 * @return [type] [description]
 */
public function ajax_creatuser(){
     if($this->request->isAJax()){
        $star_time = $this->request->only(['star_time']);
        $end_time = $this->request->only(['end_time']);
        $password = $this->request->only(['password']);
        $num = $this->request->only(['num']);
        for ($i=0; $i < $num['num']; $i++) { 
           $data['email'] = rand_email();
           $data['valid_email'] = 1;

           $data['mobile'] = rand_phone();
           $data['salt'] = rand_str(4);
           $data['avatar_file'] = rand(2033,4031);
           $data['user_name'] = rand_user();
           $data['sex'] = rand(1,3);
           $data['jobs'] = rand(0,38);
           $data['reg_time'] = rand(strtotime($star_time['star_time']),strtotime($end_time['end_time']));
           $data['group_id'] = rand(3,9);
           $data['password'] = encode_pwd($password['password'],$data['salt']);
           $where = ['user_name'=>$data['user_name']];
           if(!model('Base')->getone('users',['where'=>$where])){
             model('Base')->getadd('users',$data);
           }
        }
        $this->success('生成成功');
    }
        
}

    public function install(){//安装方法必须实现
        return true;//安装成功返回true，失败false
    }

    public function uninstall(){//卸载方法必须实现
        return true;//卸载成功返回true，失败false
    }
    
    //实现的comment钩子方法
    public function comment($param){
        echo Comments($param['post_table'],$param['post_id'],array('post_title'=>$param['post_title']));
    }

}
