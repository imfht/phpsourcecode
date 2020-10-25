<?php
namespace app\Mass\model;

use think\Model;

/**
 * WxMass
 *
 * @author WangWei
 * @version 1.0
 * @todo 数据库读写
 */
class WxMass extends Model {
	protected $type = [ 
			'news' => 'array',
			'update_time' => 'timestamp',
			'create_time' => 'timestamp',
			'send_time' => 'timestamp:Y-m-d H:i',
			'do_send_time' => 'timestamp' 
	];
	/**
	 * getMsgTypeAttr
	 * 数据属性转换
	 * @param mixed $value 数据
	 * @return string 转换后的结果
	 */
	public function getMsgTypeAttr($value) {
		$status = [ 
				'text' => '文本',
				'img' => '图片',
				'news' => '图文',
				'voice' => '音频',
				'video' => '视频',
				'music' => '音乐' 
		];
		return $status [$value];
	}
	/**
	 * init
	 * 注册事件；该事件在指定位置触发；支持多个事件
	 */
	protected static function init() {
		WxMass::event ( 'before_insert', function ($Wx) {
			if($Wx->msg_type!='图文'){
				unset($Wx->news);
				return true;
			}else{
				$r = $Wx->news;
				$r = array_filter ( $r );
				$r = array_values ( $r );
				$Wx->news = $r;
			}
			
		} );
		WxMass::event ( 'before_update', function ($Wx) {
			if (!empty($Wx->news)) {
				$r = $Wx->news;
				$r = array_filter ( $r );
				$r = array_values ( $r );
				$Wx->news = $r;
				if(empty($Wx->news)){
					return false;
				}
			}else {
				unset($Wx->news);
			}
		} );
	}
}