<?php
namespace app\api\controller\user;
use app\api\controller\BaseController;

class ContactController extends BaseController
{

	// 获取收获人列表
    public function index()
    {
        if (request()->isPost()){

            return $this->add();
        }else{
            $user_id = $this->get_user_id();
            $map = array();
            $map['status'] = 1;
            $map['user_id'] = $user_id;

            $contactlist = model('UserContact')->with('country,province,city,district')->where($map)->select()->toArray();
            $data['contact'] = $contactlist;
            return json(['data' => $data, 'msg' => '收获地址列表', 'code' => 1]);
        }
    }

	// 获取收获人详情
    public function detail()
    {
    	$user_id = $this->get_user_id();

    	$id = input('param.id');
    	$contact = model('UserContact')->with('country,province,city,district')->find($id);
        $data['contact'] = $contact;
        return json(['data' => $data, 'msg' => '收获地址详情', 'code' => 1]);
    }

    // 新增或更新收货人地址
    public function add()
    {
        $user_id = $this->get_user_id();

        $data = input('post.');
        $data['user_id'] = $user_id;
        
        if(input('post.id')){
            $result = model('UserContact')->update($data);
        }else{
            $result = model('UserContact')->create($data);
        }

        if($result){
            $info = ['data' => ['contact' => $result], 'msg' => 'success', 'code' => 1];
        }else{
            $info = ['data' => false, 'msg' => 'error', 'code' => 0];
        }

        return json($info);
    }

    //设置默认地址
    public function setDefault()
    {
        $user_id = $this->get_user_id();
        $contact_id = input('param.id');
        $result = model('User')->where('id',$user_id)->update(['contact_id' => $contact_id]);

        if($result){
            $info = ['data' => false, 'msg' => '设置成功', 'code' => 1];
        }else{
            $info = ['data' => false, 'msg' => '设置失败', 'code' => 0];
        }
        return json($info);
    }

    //删除地址
    public function del(){
        $ids = input('param.id');
        
        $result = model('UserContact')->destroy($ids);
        if($result){
            $info = ['data' => false, 'msg' => '删除成功', 'code' => 1];
        }else{
            $info = ['data' => false, 'msg' => '删除失败', 'code' => 0];
        }
        return json($info);
    }


}