<?php
namespace app\common\upgrade;

class U23{
	public function up(){
		if(table_field('cms_content3','zhibo_status')){
			return ;
		}
	    $sql = "
ALTER TABLE  `qb_cms_content3` ADD  `zhibo_status` TINYINT( 1 ) NOT NULL COMMENT  '直播状态,1直播预告,2直播进行中,3直播已结束',ADD  `size_type` TINYINT( 1 ) NOT NULL COMMENT  '0是横屏,1是竖屏';
ALTER TABLE  `qb_cms_content3` ADD  `start_time` INT( 10 ) NOT NULL COMMENT  '直播开始时间',ADD  `stop_time` INT( 10 ) NOT NULL COMMENT  '直播结束时间';
ALTER TABLE  `qb_cms_content3` ADD INDEX (  `zhibo_status` );

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'zhibo_status', '直播状态', 'radio', 'tinyint(1) NOT NULL DEFAULT ''0''', '', '0|非直播内容\r\n1|直播预告\r\n2|直播进行中\r\n3|直播已结束', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'size_type', '横屏或竖屏', 'radio', 'tinyint(1) NOT NULL DEFAULT ''0''', '0', '0|横屏\r\n1|是竖屏', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'start_time', '直播开始时间', 'datetime', 'int(10) NOT NULL DEFAULT ''0''', '', '', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'stop_time', '直播结束时间', 'datetime', 'int(10) NOT NULL DEFAULT ''0''', '', '', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
		
		";
		into_sql($sql);
	}
}