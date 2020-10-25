<?php
namespace Common\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class AdWidget extends Controller{

	public function run($name){
		$map['name'] = $name;
		$place = D('AdPlace')->where($map)->find();
		if (empty($place) || !$place) {
			echo "";return;
		}
		if ($place['status'] != '1') {
			echo "";return;
		}
		$ad = D('Ad')->where(array('place_id'=>$place['id'],'status'=>1))->select();
		foreach ($ad as $key => $value) {
			if ($value['photolist'] != '') {
				$photolist = explode(',', $value['photolist']);
				$listurl   = explode("\n", $value['listurl']);
				foreach ($photolist as $k => $val) {
					$value['image'][] = array('img'=>get_cover($val,'path'),'url'=>$listurl[$k]);
				}
			}else{
				$value['image'] = array();
			}
			if ($value['cover_id']) {
				$value['cover'] = get_cover($value['cover_id'],'path');
			}
			$list[] = $value;
		}
		switch ($place['show_type']) {
			//幻灯片显示
			case '1':
				$template = $place['template'] ? $place['template'] : "sider";
				break;
			//对联广告
			case '2':
				$template = $place['template'] ? $place['template'] : "couplet";
				break;
			//图片列表广告
			case '3':
				$template = $place['template'] ? $place['template'] : "image";
				break;
			//图文列表广告
			case '4':
				$template = $place['template'] ? $place['template'] : "images";
				break;
			//文字列表广告
			case '5':
				$template = $place['template'] ? $place['template'] : "text";
				break;
			//代码广告广告
			case '6':
				$template = $place['template'] ? $place['template'] : "code";
				break;
			default:
				$template = $place['template'] ? $place['template'] : "default";
				break;
		}
		$data = array(
			'place' => $place,
			'ad' => $list,
		);
		$this->assign($data);
		$this->display(T("Common@Ad/".$template));
	}
}