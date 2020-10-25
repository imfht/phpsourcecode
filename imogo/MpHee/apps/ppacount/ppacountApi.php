<?php
class ppacountApi extends baseApi{
	
	public function getMenu(){
		$userinfo = get_session('userinfo');
		if( $userinfo['pid'] == 0 ){
		return array(
					'sort'=>1,
					'title'=>'公众账号管理',
					'list'=>array(
						'账号列表'=>url('ppacount/index/ppacountlist'),
						'分权管理'=>url('ppacount/index/managelist'),
					)
			);
		}else{
		return array(
					'sort'=>1,
					'title'=>'公众账号管理',
					'list'=>array(
						'账号列表'=>url('ppacount/index/ppacountlist'),
					)
			);
		}
	}
}