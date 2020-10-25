<?php

// 回复文章接口
namespace app\api\controller;

use expand\ApiReturn;

class Reply extends Base {
    public function initialize(){
        parent::initialize();
//		p('Index');
    }

	//回复列表
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
    	$ArchiveReply = new \app\common\model\ArchiveReply;
		$arc_reply = $ArchiveReply->where( ['aid'=>$data['aid'],'audit'=>1] )->order('id desc')->page($data['page'],$data['number'])->select();	//回复数据
		foreach ($arc_reply as $k => $v) {
			$arc_reply[$k]['username'] = $v->User->name?$v->User->name:$v->User->username;
			$arc_reply[$k]['avatar'] = $v->UserInfo->avatar_turn;
			$arc_reply[$k]['reply_num'] = $v->reply_num;	//评论数量
			$arc_reply[$k]['zan_num'] = $v->zan_num;	//赞数量
		}
		$return_data = $arc_reply->visible($this->fname)->toArray();	// 根据返回字段来返回
    	return ApiReturn::r(1,$return_data);
	}
	//回复文章
    public function add($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid= \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');

		$ArchiveReply = new \app\common\model\ArchiveReply;
		$data['uid'] = $uid;
		$add = $ArchiveReply->allowField(true)->save($data);	//添加数据
		if( $add ){
			if( confv('is_arc_audit','system') == 0 ){
				return ApiReturn::r(1,'','回复成功。');
			}else{
				return ApiReturn::r(1,'','回复成功，需要审核后才能显示。');
			}
		}else{
			return ApiReturn::r(0,'','回复失败！');
		}
    }

	//赞文章和赞评论接口
    public function zan($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
		$data['uid'] = $uid;
		$ZanLog = new \app\common\model\ZanLog;
		if( $data['type'] == 1 ){	//赞回复
			$find = $ZanLog->where( ['ar_id'=>$data['id'],'uid'=>$uid] )->find();
			if( $find ){
				return ApiReturn::r(0,'','你已经赞过了！');
			}
			$data['ar_id'] = $data['id'];
			unset($data['id']);
			$add = $ZanLog->allowField(true)->save($data);	//添加数据
			if( $add ){
				return ApiReturn::r(1,'','感谢您的赞！');
			}
		}else if( $data['type'] == 0 ){		//赞文章
			$find = $ZanLog->where( ['a_id'=>$data['id'],'uid'=>$uid] )->find();
			if( $find ){
				return ApiReturn::r(0,'','你已经赞过了！');
			}
			$data['a_id'] = $data['id'];
			unset($data['id']);
			$add = $ZanLog->allowField(true)->save($data);	//添加数据
			if( $add ){
				return ApiReturn::r(1,'','感谢您的赞！');
			}
		}else{
			return ApiReturn::r(0,'','点赞失败！');
		}
	}



}
