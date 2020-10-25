<?php
namespace app\admin\controller;
use think\Validate;
use app\admin\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Admin extends Base{
	/**
	 * 显示管理员列表
	 */
	public function index(){
		$data = db('admin')->select();
		$this->assign('admins',$data);
		return $this->fetch();
	}
	/**
	 * 添加管理员
	 */
	public function add(){
		if(request()->isPost()){
			$data = [
				'uid' => input('uid'),
				'username' => input('username'),
				'password' => md5(input('password')),
				'nickname' => input('nickname'),
				'email' => input('email'),
				'qq' => input('qq'),
				'add_time' => time(),
				'salt' => rand_string(12,''),
				'type' => input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['username','require','ئەزالىق نامىنى كىرگۈزۈڭ !'],
                ['email','require|email','خەت ساندۇقىنى كىرگۈزۈڭ!|خەت ساندۇقى خاتا بۇلۇپ قالدى!'],
                ['nickname','require','تور نامىنى كىرگۈزۈڭ!'],
                ['password','require|min:8','مەخپىي نۇمۇرنى كىرگۈزۈڭ!|مەخپىي نۇمۇر 8 خانىدىن چوڭ بولسۇن!']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('admin')->insert($data);
				$this->success('قوشۇش مۇۋاپىقيەتلىك بولدى！','admin/admin/index');
			}else {
	            // 验证失败 输出错误信息
                $this->error($validate->getError());
	        }
			return;		
		}
		return $this->fetch();
	}
	/**
	 * 编辑管理员
	 */
	public function edit(){
		if(request()->isPost()){
			$data = [
				'uid' => input('uid'),
				'username' => input('username'),
				'password' => md5(input('password')),
				'nickname' => input('nickname'),
				'email' => input('email'),
				'qq' => input('qq'),
				'salt' => rand_string(12,''),
				'type' => input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['username','require','ئەزالىق نامىنى كىرگۈزۈڭ !'],
                ['email','require|email','خەت ساندۇقىنى كىرگۈزۈڭ!|خەت ساندۇقى خاتا بۇلۇپ قالدى!'],
                ['nickname','require','تور نامىنى كىرگۈزۈڭ!'],
                ['password','require|min:8','مەخپىي نۇمۇرنى كىرگۈزۈڭ!|مەخپىي نۇمۇر 8 خانىدىن چوڭ بولسۇن!']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('admin')->update($data);
				$this->success('تەھرىرلەش مۇۋاپىقيەتلىك بولدى！','admin/admin/index');
			}else {
	            // 验证失败 输出错误信息
                $this->error($validate->getError());
	        }
			return;
		}
		$id = input('uid');
		$data = db('admin')->find($id);
		$this->assign('admins',$data);
		return $this->fetch();
	}
	/**
	 * 删除管理员
	 */
	public function del(){
		$id = input('uid');
		if($id != 1){
			$result = db('admin')->delete($id);
			if($result){
				$this->success('ئۆچۈرۈش مۇۋاپىقيەتلىك بولدى！！','admin/admin/index');
			}else {
	            $this->error('ئۆچۈرۈش مەغلۇپ بولدى！');
	        }
    	}else{
    		$this->error('ئالاھىدە باشقۇرغۇچىنى ئۆچۈرۈشكە بولمايدۇ!');
    	}
	}
}