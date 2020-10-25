<?php
namespace app\common\upgrade;

use think\Db;

class U24{
	public static function up(){
	    
	    $listdb = Db::name('cms_module')->column(true);
	    foreach ($listdb AS $rs){
	        $id = $rs['id'];
	        if (!table_field('cms_content'.$id,'myfid')) {
	            into_sql("
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'cms_mysort@id,name@uid', '<script>if($(\"#atc_myfid\").children().length<1)$(\"#form_group_myfid\").hide();</script>', 1, {$id}, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 0);
ALTER TABLE  `qb_cms_content{$id}` ADD  `myfid` INT( 7 ) NOT NULL COMMENT  '我的分类';
");
	        }
	    }
		  
	}
}


