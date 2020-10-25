<?php
namespace addons\common\coupon\controller;

class Active extends Base
{
	public function config(){
		if(request()->isPost()){
			$config_data = input('post.');
			// 判断是修改还是添加
			if($config_data['id']){
				$result = x_model('AddonsCommonCouponActiveConfig')->where(['id'=>$config_data['id']])->update($config_data);
				// 修改
				if($result){
					$this->success("修改成功", cookie("prevUrl"));
				}else{
					$this->error("新增失败", request()->url());
				}
			}else{
				$result = x_model('AddonsCommonCouponActiveConfig')->create($config_data);
				// 添加
				if($result){
					$this->success("新增成功", cookie("prevUrl"));
				}else{
					$this->error("新增失败", request()->url());
				}
			}
		}else{
			$if_exist = x_model('AddonsCommonCouponActiveConfig')->find();
			if($if_exist){
				// 修改活动设置
				$config = x_model('AddonsCommonCouponActiveConfig')->with('file')->where(['id'=>$if_exist['id']])->find();
				$putOn_url = request()->root(true);
				$this->assign('putOn_url',$putOn_url);
				$this->assign('config',$config);
				return view('active_config');
			}else{
				// 添加活动设置
				return view('active_config');
			}
		}
	}
}