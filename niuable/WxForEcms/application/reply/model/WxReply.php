<?php
namespace app\reply\model;

use think\Model;

class WxReply extends Model {
	protected $type = [ 
			'update_time' => 'timestamp',
			'create_time' => 'timestamp',
			'news' => 'array' 
	];
	/**
	 * init
	 * 注册事件；支持多个事件；该事件在指定位置触发
	 */
	protected static function init() {
		WxReply::event ( 'before_insert', function ($WxReply) {
			$r = $WxReply->news;
			$r = array_filter ( $r );
			$r=array_values($r);
			$WxReply->news = $r;
		} );
		WxReply::event ( 'before_update', function ($WxReply) {
			$r = $WxReply->news;
			if($r == NULL){
			    return;
			}
			$r = array_filter ( $r );
			$r=array_values($r);
			$WxReply->news = $r;
		} );
		WxReply::event ( 'before_write', function ($WxReply) {
			$r = $WxReply->news;
			if($r == NULL){
			    return;
			}
			$r = array_filter ( $r );
			$r=array_values($r);
			$WxReply->news = $r;
		} );
	}
}