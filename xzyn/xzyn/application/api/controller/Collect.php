<?php

// 收藏文章接口
namespace app\api\controller;

use expand\ApiReturn;

class Collect extends Base {
    public function initialize(){
        parent::initialize();

    }

	//收藏文章列表接口
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
    	$Collect = new \app\common\model\Collect;
		$aidarr = $Collect->aidArr($uid);
		$Archive = new \app\common\model\Archive;
		$where[] = ['id','in',$aidarr];
		$arc_data = $Archive->api_arclist($where,'id DESC',$data['page'],$data['number'],$this->fname);		//列表数据
		return ApiReturn::r(1,$arc_data);
	}

	//收藏文章接口
    public function add($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
    	$Collect = new \app\common\model\Collect;
		$c_data = $Collect->where( ['aid'=>$data['aid'],'uid'=>$uid] )->find();
		$Archive = new \app\common\model\Archive;
		$arcdata = $Archive->where(['id'=>$data['aid']])->find();
		if( empty($arcdata) ){
			return ApiReturn::r(0,'','文章不存在！');
		}
		if( $data['type'] == 1 ) {
			if( $arcdata['writer'] == $uid ){
				return ApiReturn::r(0,'','不能收藏自己的文章！');
			}
			if( !empty($c_data) ){
				return ApiReturn::r(0,'','您已经收藏过了！');
			}
			$data['uid'] = $uid;
			$add = $Collect->allowField(true)->save($data);	//添加数据
			if( $add ){
				return ApiReturn::r(1,'','收藏成功！');
			}
		}
		if( $data['type'] == 0 && !empty($c_data) ){
			$del = $Collect->where( ['aid'=>$data['aid'],'uid'=>$uid] )->delete();
			if( $del ){
				return ApiReturn::r(1,'','已经取消收藏！');
			}
		}
		return ApiReturn::r(0);
	}


}
