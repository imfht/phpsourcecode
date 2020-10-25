<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @爱客网特有视频抓取类
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class VideosController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		if (! $this->visitor && in_array ( ACTION_NAME, array (
				'add',
				'delete' 
		) )) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor['userid'];
		}
		$this->_mod = D ( 'Common/Videos' );
	}
	public function add() {
		
		$typeid = $this->_get ( 'typeid', 'intval', '0' );
		$type = $this->_get ( 'type', 'trim' );
		
		$url = $this->_post ( 'url', 'trim,urldecode' );
		
		$userid = $this->userid;
		
		$arrVideo = getVideoInfo ( $url );
		// 解析
		if (! empty ( $arrVideo ['videourl'] )) {
			$seqnum = $this->_mod->where(array('typeid'=>$typeid,'type'=>$type))->count();
			
			$imgurl = empty ( $arrVideo ['imgurl'] ) ? __ROOT__.'/Public/Static/images/video_default.gif' : $arrVideo ['imgurl'];
			$arrJson = array (
					'userid' => $userid,
					'typeid' => $typeid,
					'type' => $type,
					'videourl' => $arrVideo ['videourl'], //视频地址
					'title' => $arrVideo ['title'],
					'imgurl' => $imgurl,
					'seqid' => $seqnum + 1,
					'url' => $url,//视频网站地址
					'ajaxurl' => U('home/videos/delete'),
					'addtime' => time ()
			);
			
			if(!false == $this->_mod->create($arrJson)){
				$videoid = $this->_mod->add();
			}
			if ($videoid > 0) {
				$arrJson['id'] = $videoid;
				$this->ajaxReturn ( $arrJson );
			}else{
				$arrJson = array (
						'r' => 1,
						'html' => "网络错误；请重新试试吧"
				);
				$this->ajaxReturn ( $arrJson );
			}
		} else {
			$arrJson = array (
					'r' => 1,
					'html' => "视频网址格式不正确,或是我们不支持的格式（请不要填写视频专辑地址）" 
			);
			$this->ajaxReturn ( $arrJson );
		}
	}
	public function delete(){
		$videoid = $this->_post('videoid','intval');
		if($videoid>0){
			$this->_mod->where(array('videoid'=>$videoid))->delete();
			$arrJson = array('r'=>0, 'html'=> '删除成功');
			$this->ajaxReturn ( $arrJson );
		}
	}

}