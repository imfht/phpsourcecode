<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:01.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateNewsReply {
	public function up() {
		//新增微站文章管理->未排序时，文章按修改时间倒序排序
		if (!pdo_fieldexists('site_article', 'edittime')) {
			pdo_query('ALTER TABLE '.tablename('news_reply')." CHANGE `media_id` `media_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图文素材的media_id';");
		}

		$have_media_id = pdo_getall('news_reply', array('media_id <>' => ''));
		$change_arr = array();
		foreach ($have_media_id as $val) {
			$is_num_str = intval($val['media_id']) == 0 ? false : true;
			if ($is_num_str) {
				$media_id = pdo_getcolumn('wechat_attachment', array('id' => $val['media_id']), 'media_id');
				if (empty($media_id)) {
					$media_id = '';
				}
				pdo_update('news_reply', array('media_id' => $media_id), array('id' => $val['id']));
			}
		}
	}
}
