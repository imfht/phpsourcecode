<?php
namespace app\common\validate;
//网站导航验证器
use think\Validate;
use app\common\model\Navigation as Navigations;

class Navigation extends Validate {
	protected $rule = [
        'id' => 'require|number',
		'type|分类' => 'require|number',
		'pid|父级ID' => 'require|number|ispids',
		'name|导航名称' => 'require',
		'title|导航别名' => 'require|alpha',
		'url|URL' => 'require',
		'orderby|排序' => 'number',
		'icon|图标' => 'alphaDash',
		'iconcolor|图标颜色' => '/^[a-zA-Z0-9\#\-\_]+$/',
		'news|角标' => 'max:6',
		'bgcolor|角标背景' => 'alphaDash',

	];

	protected $scene = [
		'add' => ['name', 'title','pid', 'url', 'type', 'orderby','icon','iconcolor','news','bgcolor'],
		'edit' => ['id','name', 'title','pid', 'url', 'type', 'orderby','icon','iconcolor','news','bgcolor'],
		'name' => ['name','id'],
		'title' => ['title','id'],
		'url' => ['url','id'],
		'type' => ['type','id'],
		'orderby' => ['orderby','id'],
		'icon' => ['icon','id'],
		'iconcolor' => ['iconcolor','id'],
		'closed' => ['closed','id'],
		'target' => ['target','id'],
		'news' => ['news','id'],
		'bgcolor' => ['bgcolor','id']
	];
	protected function ispids($value,$rule,$data=[]) {	// 验证pid是不是顶级分类
		if( $value != 0 ){
			$info = Navigations::get(['id'=>$value]);
			if( !empty($info) ){
				if( $info['pid'] != 0 ){
					return '父级分类，请选择一级分类';
				}
			}
		}
		if( !empty($data['id']) ){
			$idData = Navigations::get(['id'=>$data['id']]);
			$fidData = Navigations::get(['id'=>$data['pid']]);
			if( $data['id'] == $data['pid'] ){
				return '请重新选择父级分类';
			}
			if( !empty($fidData) ){
				if( $data['type'] != $fidData['type'] ){
					return '父子级导航必须是同一个分类';
				}
			}
			$pidData = Navigations::where(['pid'=>$data['id'], 'type'=>$idData['type']])->count();//检测是否存在子导航
			if( $pidData > 0 ){
				if( $data['type'] != $idData['type'] || $data['pid'] != $idData['pid']){
					return '当前导航存在子级,不允许修改父级或分类.';
				}
			}
		}
		return true;
	}


}
