<?php
/**
*语言包配置文件
*QQ:767912290
*by grysoft
**/
//默认语言
define('SITELANGUAGE','cn');
define('QD_lang','cn@en@');
define('QD_lang_name','中文@英文@');
define('QD_lang_title','中文标题@title@');
define('QD_lang_summary','中文摘要@summary@');

define('QD_lang_tags','首页@@@');define('QD_lang_tags_1','');define('QD_lang_tags_2','');
/*语言包配置,此行上下代码已标记切勿随意更改内容和位置*/
define('QD_lang_tags_0','首页@Home@');

/******/
function lang($tags)
{
	$lang         = $_SESSION[TB_PREFIX.'doclang'];
	$langlist     = explode('@',QD_lang);
	$langtagslist = explode('@',QD_lang_tags);

	eval('$langtags = QD_lang_tags_'.array_search($tags,$langtagslist).';');
	$langarr  =  explode('@',$langtags);
	
	if(!empty($langarr[array_search($lang,$langlist)]))
		return  $langarr[array_search($lang,$langlist)];
	else
		return  'Null';
}