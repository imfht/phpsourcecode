<?php
defined('IN_SYSTEM') or exit('Access Denied');
function deditor($moduleid = 1, $textareaid = 'content', $toolbarset = 'Default', $width = 500, $height = 400) {
	global $DT, $MODULE, $_userid;
	$moddir = defined('DT_ADMIN') ? $MODULE[2]['moduledir'].'/' : '';
	$editor = '';
	$editor .= '<script type="text/javascript">var ModuleID = '.$moduleid.';';
	$editor .= 'var DTAdmin = '.(defined('DT_ADMIN') ? 1 : 0).';';
	$editor .= 'var EDPath = "'.$moddir.'fckeditor/";';
	$editor .= 'var ABPath = "'.$MODULE[2]['linkurl'].'fckeditor/";';
	$editor .= 'var EDW = "'.$width.'";';
	$editor .= 'var EDH = "'.$height.'";';
	$width = is_numeric($width) ? $width.'px' : $width;
	$height = is_numeric($height) ? $height.'px' : $height;
	$editor .= 'var FCKID = "'.$textareaid.'";';
	$editor .= '</script>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'fckeditor/fckeditor.js"></script>';
	$editor .= '<script type="text/javascript">';
	$editor .= 'window.onload = function() {';
	$editor .= 'var sBasePath = "'.$moddir.'fckeditor/";';
	$editor .= 'var oFCKeditor = new FCKeditor("'.$textareaid.'");';
	$editor .= 'oFCKeditor.Width = "'.$width.'";';
	$editor .= 'oFCKeditor.Height = "'.$height.'";';
	$editor .= 'oFCKeditor.BasePath = sBasePath;';
	$editor .= 'oFCKeditor.ToolbarSet = "'.$toolbarset.'";';
	$editor .= 'oFCKeditor.ReplaceTextarea();';
	$editor .= '}';
	$editor .= '</script>';
	$save = $textareaid == 'content' && $_userid && $DT['save_draft'];
	if($DT['save_draft'] == 2 && !defined('DT_ADMIN')) $save = false;
	$editor .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/fckeditor.js"></script>';
	if($save) $editor .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/draft.js"></script>';
	echo $editor;
}

function dstyle($name, $value = '') {
	global $destoon_style_id;
	$style = $color = '';
	if(preg_match("/^#[0-9a-zA-Z]{6}$/", $value)) $color = $value;
	if(!$destoon_style_id) {
		$destoon_style_id = 1;
		$style .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/color.js"></script>';
	} else {
		$destoon_style_id++;
	}
	$style .= '<input type="hidden" name="'.$name.'" id="color_input_'.$destoon_style_id.'" value="'.$color.'"/><img src="'.DT_PATH.'file/image/color.gif" width="21" height="18" align="absmiddle" id="color_img_'.$destoon_style_id.'" style="cursor:pointer;background:'.$color.'" onclick="color_show('.$destoon_style_id.', Dd(\'color_input_'.$destoon_style_id.'\').value, this);"/>';
	return $style;
}

function dcalendar($name, $value = '', $sep = '-') {
	global $destoon_calendar_id;
	$calendar = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	if(!$destoon_calendar_id) {
		$destoon_calendar_id = 1;
		$calendar .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/calendar.js"></script>';
	}
	$calendar .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="10" onfocus="ca_show(\''.$id.'\', this, \''.$sep.'\');" readonly ondblclick="this.value=\'\';"/> <img src="'.DT_STATIC.'file/image/calendar.gif" align="absmiddle" onclick="ca_show(\''.$id.'\', this, \''.$sep.'\');" style="cursor:pointer;"/>';
	return $calendar;
}

function dselect($sarray, $name, $title = '', $selected = 0, $extend = '', $key = 1, $ov = '', $abs = 0) {
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="'.$ov.'">'.$title.'</option>';
	foreach($sarray as $k=>$v) {
		if(!$v) continue;
		$_selected = ($abs ? ($key ? $k : $v) === $selected : ($key ? $k : $v) == $selected) ? ' selected=selected' : '';
		$select .= '<option value="'.($key ? $k : $v).'"'.$_selected.'>'.$v.'</option>';
	}	
	$select .= '</select>';
	return $select;
}

function dcheckbox($sarray, $name, $checked = '', $extend = '', $key = 1, $except = '', $abs = 0) {
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$checkbox = $sp = '';
	foreach($sarray as $k=>$v) {
		if(in_array($key ? $k : $v, $except)) continue;
		$sp = in_array($key ? $k : $v, $checked) ? ' checked ' : '';
		$checkbox .= '<input type="checkbox" name="'.$name.'" value="'.($key ? $k : $v).'"'.$sp.$extend.'> '.$v.'&nbsp;';
	}
	return $checkbox;
}

function type_select($item, $cache = 0, $name = 'typeid', $title = '', $typeid = 0, $extend = '', $all = '') {
	$TYPE = get_type($item, $cache);
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($all) $select .= '<option value="-1"'.($typeid == -1 ? ' selected=selected' : '').'>'.$all.'</option>';
	if($title) $select .= '<option value="0"'.($typeid == 0 ? ' selected=selected' : '').'>'.$title.'</option>';
	foreach($TYPE as $k=>$v) {
		$select .= ' <option value="'.$k.'"'.($k == $typeid ? ' selected' : '').'> '.$v['typename'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function url_select($name, $ext = 'htm', $type = 'list', $urlid = 0, $extend = '') {
	global $L;
	include DT_ROOT."/api/url.inc.php";
	$select = '<select name="'.$name.'" '.$extend.'>';
	$types = count($urls[$ext][$type]);
	for($i = 0; $i < $types; $i++) {
		$select .= ' <option value="'.$i.'"'.($i == $urlid ? ' selected' : '').'>'.$L['url_eg'].' '.$urls[$ext][$type][$i]['example'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function tpl_select($file = 'index', $module = '', $name = 'template', $title = '', $template = '', $extend = '') {
	include load('include.lang');
	global $CFG, $destoon_tpl_id;
	if(!$destoon_tpl_id) {
		$destoon_tpl_id = 1;
	} else {
		$destoon_tpl_id++;
	}
    $tpldir = $module ? DT_ROOT."/template/".$CFG['template']."/".$module : DT_ROOT."/template/".$CFG['template'];
	@include $tpldir."/these.name.php";
	$select = '<span id="destoon_template_'.$destoon_tpl_id.'"><select name="'.$name.'" '.$extend.'><option value="">'.$title.'</option>';
	$files = glob($tpldir."/*.htm");
	foreach($files as $tplfile)	{
		$tplfile = basename($tplfile);
		$tpl = str_replace('.htm', '', $tplfile);
		if(preg_match("/^".$file."-(.*)/i", $tpl) || !$file) {//$file == $tpl || 
			$selected = ($template && $tpl == $template) ? 'selected' : '';
            $templatename = (isset($names[$tpl]) && $names[$tpl]) ? $names[$tpl] : $tpl;
			$select .= '<option value="'.$tpl.'" '.$selected.'>'.$templatename.'</option>';
		}
	}
	$select .= '</select></span>';
	if(defined('DT_ADMIN')) $select .= '&nbsp;&nbsp;<a href="javascript:tpl_edit(\''.$file.'\', \''.$module.'\', '.$destoon_tpl_id.');" class="t">'.$L['post_edit'].'</a> &nbsp;<a href="javascript:tpl_add(\''.$file.'\', \''.$module.'\');" class="t">'.$L['post_new'].'</a>';
	return $select;
}

function group_select($name = 'groupid', $title = '', $groupid = '', $extend = '') {
	global $GROUP;
	if(!$GROUP) $GROUP = cache_read('group.php');
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	foreach($GROUP as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $groupid ? ' selected' : '').'>'.$v['groupname'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function group_checkbox($name = 'groupid', $checked = '', $except = '1,2,4') {
	global $GROUP, $L;
	$GROUP or $GROUP = cache_read('group.php');
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$str = $sp = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	foreach($GROUP as $k=>$v) {
		if(in_array($k, $except)) continue;
		$sp = in_array($k, $checked) ? ' checked' : '';
		$str .= '<input type="checkbox" name="'.$name.'" value="'.$k.'"'.$sp.' id="'.$id.$k.'"/><label for="'.$id.$k.'"> '.$v['groupname'].'&nbsp; </label>';
	}
	return '<span id="group_'.$id.'">'.$str.'</span>&nbsp;<a href="javascript:check_box(\'group_'.$id.'\', true);">'.$L['select_all'].'</a> / <a href="javascript:check_box(\'group_'.$id.'\', false);">'.$L['clear_all'].'</a>';
}

function module_checkbox($name = 'moduleid', $checked = '', $except = '1,2,3,4') {
	global $MODULE;
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$str = $sp = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	foreach($MODULE as $k=>$v) {
		if(in_array($k, $except) || $v['islink']) continue;
		$sp = in_array($k, $checked) ? ' checked' : '';
		$str .= '<li><input type="checkbox" name="'.$name.'" value="'.$k.'"'.$sp.' id="'.$id.$k.'"/><label for="'.$id.$k.'"> '.$v['name'].'&nbsp; </label></li>';
	}
	return '<ul class="mods">'.$str.'</ul>';
}

function module_select($name = 'moduleid', $title = '', $moduleid = '', $extend = '', $except = '1,2,3') {
	global $MODULE, $L;
	$except = $except ? explode(',', $except) : array();
	$title or $title = $L['choose'];
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	foreach($MODULE as $k=>$v) {
		if(in_array($k, $except) || $v['islink']) continue;
		$select .= '<option value="'.$k.'"'.($k == $moduleid ? ' selected' : '').'>'.$v['name'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function homepage_select($name, $title = '', $groupid = 0, $itemid = 0, $extend = '') {
	global $db, $L;
	$title or $title = $L['choose'];
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	$result = $db->query("SELECT * FROM {$db->pre}style ORDER BY listorder DESC,itemid DESC");
	while($r = $db->fetch_array($result)) {
		$select .= '<option value="'.$r['itemid'].'"'.($r['itemid'] == $itemid ? ' selected' : '').'>'.$r['title'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function product_select($name = 'pid', $title = '', $pid = 0, $extend = '') {
	global $PRODUCT;
	$PRODUCT or $PRODUCT = cache_read('product.php');
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="0">'.$title.'</option>';
	foreach($PRODUCT as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $pid ? ' selected' : '').'>'.$v['title'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function category_select($name = 'catid', $title = '', $catid = 0, $moduleid = 1, $extend = '') {
	$option = cache_read('catetree-'.$moduleid.'.php', '', true);
	if($option) {
		if($catid) $option = str_replace('value="'.$catid.'"', 'value="'.$catid.'" selected', $option);
		$select = '<select name="'.$name.'" '.$extend.' id="catid_1">';
		if($title) $select .= '<option value="0">'.$title.'</option>';
		$select .= $option ? $option : '</select>';
		return $select;
	} else {
		return ajax_category_select($name, $title, $catid, $moduleid, $extend);
	}
}

function get_category_select($title = '', $catid = 0, $moduleid = 1, $extend = '', $deep = 0, $cat_id = 1) {
	global $db, $_child;
	$_child or $_child = array();
	$parents = array();
	if($catid) {
		$r = $db->get_one("SELECT child,arrparentid FROM {$db->pre}category WHERE catid=$catid");
		$parents = explode(',', $r['arrparentid']);
		if($r['child']) $parents[] = $catid;
	} else {
		$parents[] = 0;
	}
	$select = '';
	foreach($parents as $k=>$v) {
		if($deep && $deep <= $k) break;
		$select .= '<select onchange="load_category(this.value, '.$cat_id.');" '.$extend.'>';
		if($title) $select .= '<option value="0">'.$title.'</option>';
		$condition = $v ? "parentid=$v" : "moduleid=$moduleid AND parentid=0";
		$result = $db->query("SELECT catid,catname FROM {$db->pre}category WHERE $condition ORDER BY listorder,catid ASC");
		while($c = $db->fetch_array($result)) {
			$selectid = isset($parents[$k+1]) ? $parents[$k+1] : $catid;
			$selected = $c['catid'] == $selectid ? ' selected' : '';
			if($_child && !in_array($c['catid'], $_child)) continue;
			$select .= '<option value="'.$c['catid'].'"'.$selected.'>'.$c['catname'].'</option>';
		}
		$select .= '</select> ';
	}
	return $select;
}

function ajax_category_select($name = 'catid', $title = '', $catid = 0, $moduleid = 1, $extend = '', $deep = 0) {
	global $cat_id;
	if($cat_id) {
		$cat_id++;
	} else {
		$cat_id = 1;
	}
	$catid = intval($catid);
	$deep = intval($deep);
	$select = '';
	$select .= '<input name="'.$name.'" id="catid_'.$cat_id.'" type="hidden" value="'.$catid.'"/>';
	$select .= '<span id="load_category_'.$cat_id.'">'.get_category_select($title, $catid, $moduleid, $extend, $deep, $cat_id).'</span>';
	$select .= '<script type="text/javascript">';
	if($cat_id == 1) $select .= 'var category_moduleid = new Array;';
	$select .= 'category_moduleid['.$cat_id.']="'.$moduleid.'";';
	if($cat_id == 1) $select .= 'var category_title = new Array;';
	$select .= 'category_title['.$cat_id.']=\''.$title.'\';';
	if($cat_id == 1) $select .= 'var category_extend = new Array;';
	$select .= 'category_extend['.$cat_id.']=\''.$extend.'\';';
	if($cat_id == 1) $select .= 'var category_catid = new Array;';
	$select .= 'category_catid['.$cat_id.']=\''.$catid.'\';';
	if($cat_id == 1) $select .= 'var category_deep = new Array;';
	$select .= 'category_deep['.$cat_id.']=\''.$deep.'\';';
	$select .= '</script>';
	if($cat_id == 1) $select .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/category.js"></script>';
	return $select;
}

function get_area_select($title = '', $areaid = 0, $extend = '', $deep = 0, $id = 1) {
	global $db;
	$parents = array();
	if($areaid) {
		$r = $db->get_one("SELECT child,arrparentid FROM {$db->pre}area WHERE areaid=$areaid");
		$parents = explode(',', $r['arrparentid']);
		if($r['child']) $parents[] = $areaid;
	} else {
		$parents[] = 0;
	}
	$select = '';
	foreach($parents as $k=>$v) {
		if($deep && $deep <= $k) break;
		$v = intval($v);
		$select .= '<select onchange="load_area(this.value, '.$id.');" '.$extend.'>';
		if($title) $select .= '<option value="0">'.$title.'</option>';
		$result = $db->query("SELECT areaid,areaname FROM {$db->pre}area WHERE parentid=$v ORDER BY listorder,areaid ASC");
		while($a = $db->fetch_array($result)) {
			$selectid = isset($parents[$k+1]) ? $parents[$k+1] : $areaid;
			$selected = $a['areaid'] == $selectid ? ' selected' : '';
			$select .= '<option value="'.$a['areaid'].'"'.$selected.'>'.$a['areaname'].'</option>';
		}
		$select .= '</select> ';
	}
	return $select;
}

function ajax_area_select($name = 'areaid', $title = '', $areaid = 0, $extend = '', $deep = 0) {
	global $area_id;
	if($area_id) {
		$area_id++;
	} else {
		$area_id = 1;
	}
	$areaid = intval($areaid);
	$deep = intval($deep);
	$select = '';
	$select .= '<input name="'.$name.'" id="areaid_'.$area_id.'" type="hidden" value="'.$areaid.'"/>';
	$select .= '<span id="load_area_'.$area_id.'">'.get_area_select($title, $areaid, $extend, $deep, $area_id).'</span>';
	$select .= '<script type="text/javascript">';
	if($area_id == 1) $select .= 'var area_title = new Array;';
	$select .= 'area_title['.$area_id.']=\''.$title.'\';';
	if($area_id == 1) $select .= 'var area_extend = new Array;';
	$select .= 'area_extend['.$area_id.']=\''.$extend.'\';';
	if($area_id == 1) $select .= 'var area_areaid = new Array;';
	$select .= 'area_areaid['.$area_id.']=\''.$areaid.'\';';
	if($area_id == 1) $select .= 'var area_deep = new Array;';
	$select .= 'area_deep['.$area_id.']=\''.$deep.'\';';
	$select .= '</script>';
	if($area_id == 1) $select .= '<script type="text/javascript" src="'.DT_STATIC.'file/script/area.js"></script>';
	return $select;
}

function level_select($name, $title = '', $level = 0, $extend = '') {
	global $MOD, $L;
	$names = isset($MOD['level']) && $MOD['level'] ? $MOD['level'] : '';
	$names = $names ? explode('|', trim($names)) : array();
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="0">'.$title.'</option>';
	for($i = 1; $i < 10; $i++) {
		$n = isset($names[$i-1]) ? ' '.$names[$i-1] : '';
		$select .= '<option value="'.$i.'"'.($i == $level ? ' selected' : '').'>'.$i.' '.$L['level'].$n.'</option>';
	}
	$select .= '</select>';
	return $select;
}

function is_url($url) {
	return preg_match("/^[http|https]\:\/\/[a-z0-9\/\.\#\&\?\;\,]{4,}$/", $url);
}

function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

function is_telephone($telephone) {
	return preg_match("/^[0-9\-\+]{7,}$/", $telephone);
}

function is_qq($qq) {
	return preg_match("/^[1-9]{1}[0-9]{4,12}$/", $qq);
}

function is_gbk($string) {
	return preg_match("/^([\s\S]*?)([\x81-\xfe][\x40-\xfe])([\s\S]*?)/", $string);
}

function is_date($date, $sep = '-') {
	if(strlen($date) == 8) $date = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
	if(strlen($date) > 10 || strlen($date) < 8)  return false;
	list($year, $month, $day) = explode($sep, $date);
	return checkdate($month, $day, $year);
}

function is_image($file) {
	return preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($file));
}

function is_user($username) {
	global $db;
	$r = $db->get_one("SELECT username FROM {$db->pre}member WHERE username='$username'");
	return $r ? true : false;
}

function is_password($username, $password) {
	global $db;
	if(strlen($password) < 6) return false;
	$r = $db->get_one("SELECT password FROM {$db->pre}member WHERE username='$username'");
	if(!$r) return false;
	return $r['password'] == (is_md5($password) ? md5($password) : md5(md5($password)));
}

function is_payword($username, $payword) {
	global $db;
	if(strlen($payword) < 6) return false;
	$r = $db->get_one("SELECT payword,password FROM {$db->pre}member WHERE username='$username'");
	if(!$r) return false;
	$r['payword'] = $r['payword'] ? $r['payword'] : $r['password'];
	return $r['payword'] == (is_md5($payword) ? md5($payword) : md5(md5($payword)));
}

function gb2py($text, $exp = '') {
	if(!$text) return '';
	if(strtolower(DT_CHARSET) != 'gbk') $text = convert($text, DT_CHARSET, 'gbk');
	$data = array();
	$tmp = @file(DT_ROOT.'/file/table/gb-pinyin.table');
	if(!$tmp) return '';
	$tmps = count($tmp);
	for($i = 0; $i < $tmps; $i++) {
		$tmp1 = explode("	", $tmp[$i]);
		$data[$i]=array($tmp1[0], $tmp1[1]);
	}
	$r = array();
	$k = 0;
	$textlen = strlen($text);
	for($i = 0; $i < $textlen; $i++) {
		$p = ord(substr($text, $i, 1));		
		if($p > 160) {
			$q = ord(substr($text, ++$i, 1));
			$p = $p*256+$q-65536;
		}
        if($p > 0 && $p < 160) {
            $r[$k] = chr($p);
        } elseif($p< -20319 || $p > -10247) {
            $r[$k] = '';
        } else {
            for($j = $tmps-1; $j >= 0; $j--) {
                if($data[$j][1]<=$p) break;
            }
            $r[$k] = $data[$j][0];
        }
		$k++;
	}
	return implode($exp, $r);
}

function match_userid($file) {
	$file = basename($file);
	if(preg_match("/\-([0-9]{2}+)\-([0-9]{1,}+)\./", $file, $m)) {
		return $m[2];
	} else {
		return 0;
	}
}

function clear_link($content) {
	return preg_replace_callback("/<a[^>]*>(.*?)<\/a>/is", "_clear_link", $content);
}

function _clear_link($matchs) {
	if(strpos($matchs[0], DT_PATH) !== false) return $matchs[0];
	if(DT_DOMAIN && strpos($matchs[0], DT_DOMAIN) !== false) return $matchs[0];
	return $matchs[1];
}

function save_remote($content, $ext = 'jpg|jpeg|gif|png|bmp', $self = 0) {
	global $DT, $DT_TIME, $_userid;
	if(!$_userid || !$content) return $content;
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) return $content;
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if(in_array($url, $urls)) continue;
		$urls[$url] = $url;		
		if(strpos($url, '://') === false) continue;
		if(!$self) {
			if(DT_DOMAIN) {
				if(strpos($url, '.'.DT_DOMAIN.'/') !== false) continue;
			} else {
				if(strpos($url, DT_PATH) !== false) continue;
			}
		}
		$filedir = 'file/upload/'.timetodate($DT_TIME, $DT['uploaddir']).'/';
		$filepath = DT_PATH.$filedir;
		$fileroot = DT_ROOT.'/'.$filedir;
		$file_ext = file_ext($url);
		$filename = timetodate($DT_TIME, 'H-i-s').'-'.rand(10, 99).'-'.$_userid.'.'.$file_ext;
		$newfile = $fileroot.$filename;
		if(file_copy($url, $newfile)) {
			if(is_image($newfile)) {
				if(!@getimagesize($newfile)) {
					file_del($newfile);
					continue;
				}
				if($DT['water_type']) {
					$image = new image($newfile);
					if($DT['water_type'] == 2) {
						$image->waterimage();
					} else if($DT['water_type'] == 1) {
						$image->watertext();
					}
				}
			}
			$oldpath[] = $url;
			$newurl = linkurl($filepath.$filename);
			if($dftp) {
				$exp = explode("file/upload/", $newurl);
				if($ftp->dftp_put($filedir.$filename, $exp[1])) {
					$newurl = $DT['remote_url'].$exp[1];
					file_del($newfile);
				}
			}
			$newpath[] = $newurl;
		}
	}
	unset($matches);
	return str_replace($oldpath, $newpath, $content);
}

function save_local($content) {
	global $DT, $DT_TIME, $_userid;
	if($content == '<br type="_moz" />') return '';//FireFox
	if($content == '&nbsp;') return '';//Chrome
	$content = preg_replace("/allowScriptAccess=\"always\"/i", "", $content);
	$content = preg_replace("/allowScriptAccess/i", "allowscr-iptaccess", $content);
	if(strpos($content, 'data:image') === false) return $content;
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+)\\1/i", $content, $matches)) return $content;
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if(in_array($url, $urls)) continue;
		$urls[$url] = $url;		
		if(strpos($url, 'data:image') === false) continue;
		if(strpos($url, ';base64,') === false) continue;
		$t1 = explode(';base64,', $url);
		$t2 = explode('/', $t1[0]);
		$file_ext = $t2[1];
		in_array($file_ext, array('jpg', 'gif', 'png')) or $file_ext = 'jpg';
		$filedir = 'file/upload/'.timetodate($DT_TIME, $DT['uploaddir']).'/';
		$filepath = DT_PATH.$filedir;
		$fileroot = DT_ROOT.'/'.$filedir;
		$filename = timetodate($DT_TIME, 'H-i-s').'-'.rand(10, 99).'-'.$_userid.'.'.$file_ext;
		$newfile = $fileroot.$filename;
		if(!is_image($newfile)) continue;
		if(file_put($newfile, base64_decode($t1[1]))) {
			if(!@getimagesize($newfile)) {
				file_del($newfile);
				continue;
			}
			if($DT['water_type']) {
				$image = new image($newfile);
				if($DT['water_type'] == 2) {
					$image->waterimage();
				} else if($DT['water_type'] == 1) {
					$image->watertext();
				}
			}
			$oldpath[] = $url;
			$newurl = linkurl($filepath.$filename);
			if($dftp) {
				$exp = explode("file/upload/", $newurl);
				if($ftp->dftp_put($filedir.$filename, $exp[1])) {
					$newurl = $DT['remote_url'].$exp[1];
					file_del($newfile);
				}
			}
			$newpath[] = $newurl;
		}
	}
	unset($matches);
	return str_replace($oldpath, $newpath, $content);
}

function save_thumb($content, $no, $width = 120, $height = 90) {
	global $DT, $DT_TIME, $_userid;
	if(!$_userid || !$content) return '';
	$ext = 'jpg|jpeg|gif|png|bmp';
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) return '';
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if($k == $no - 1) {
			$filedir = 'file/upload/'.timetodate($DT_TIME, $DT['uploaddir']).'/';
			$filepath = DT_PATH.$filedir;
			$fileroot = DT_ROOT.'/'.$filedir;
			$file_ext = file_ext($url);
			$filename = timetodate($DT_TIME, 'H-i-s').'-'.rand(10, 99).'-'.$_userid.'.'.$file_ext;
			$newfile = $fileroot.$filename;
			if(file_copy($url, $newfile)) {
				if(is_image($newfile)) {					
					if(!@getimagesize($newfile)) {
						file_del($newfile);
						return '';
					}
					$image = new image($newfile);
					$image->thumb($width, $height);
				}
				$newurl = linkurl($filepath.$filename);
				if($dftp) {
					$exp = explode("file/upload/", $newurl);
					if($ftp->dftp_put($filedir.$filename, $exp[1])) {
						$newurl = $DT['remote_url'].$exp[1];
						file_del($newfile);
					}
				}
				return $newurl;
			}
		}
	}
	unset($matches);
	return '';
}

function delete_local($content, $userid, $ext = 'jpg|jpeg|gif|png|bmp|swf') {
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) {
		foreach($matches[2] as $url) {
			delete_upload($url, $userid);
		}
		unset($matches);
	}
}

function delete_diff($new, $old, $ext = 'jpg|jpeg|gif|png|bmp|swf') {
	global $_userid;
	$new = stripslashes($new);
	$diff_urls = $new_urls = $old_urls = array();
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $old, $matches)) {
		foreach($matches[2] as $url) {
			$old_urls[] = $url;
		}
	} else {
		return;
	}
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $new, $matches)) {
		foreach($matches[2] as $url) {
			$new_urls[] = $url;
		}
	}
	foreach($old_urls as $url) {
		in_array($url, $new_urls) or $diff_urls[] = $url;
	}
	if(!$diff_urls) return;
	foreach($diff_urls as $url) {
		delete_upload($url, $_userid);
	}
	unset($new, $old, $matches, $url, $diff_urls, $new_urls, $old_urls);
}

function delete_upload($file, $userid) {
	global $CFG, $DT, $DT_TIME, $ftp, $db;
	if(!defined('DT_ADMIN') && (!$userid || $userid != match_userid($file))) return false;
	$fileurl = $file;
	if(strpos($file, 'file/upload') === false) {//Remote
		if($DT['ftp_remote'] && $DT['remote_url']) {
			if(strpos($file, $DT['remote_url']) !== false) {
				if(!is_object($ftp)) {
					require_once DT_ROOT.'/include/ftp.class.php';
					$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
				}
				$file = str_replace($DT['remote_url'], '', $file);
				$ftp->dftp_delete($file);
				if(strpos($file, '.thumb.') !== false) {
					$ext = file_ext($file);
					$F = str_replace('.thumb.'.$ext, '', $file);
					$ftp->dftp_delete($F);
					$F = str_replace('.thumb.'.$ext, '.middle.'.$ext, $file);
					$ftp->dftp_delete($F);
				}
			}
		}
	} else {
		$exp = explode("file/upload/", $file);
		$file = DT_ROOT.'/file/upload/'.$exp[1];
		if(is_file($file) && strpos($exp[1], '..') === false) {
			file_del($file);
			if(strpos($file, '.thumb.') !== false) {
				$ext = file_ext($file);
				file_del(str_replace('.thumb.'.$ext, '', $file));
				file_del(str_replace('.thumb.'.$ext, '.middle.'.$ext, $file));
			}
		}
	}
	if($DT['uploadlog']) $db->query("DELETE FROM {$db->pre}upload_".($userid%10)." WHERE item='".md5($fileurl)."'");
}

function clear_upload($content = '', $itemid = 0) {
	global $CFG, $DT, $db, $session, $_userid;
	if(!is_object($session)) $session = new dsession();
	if(!isset($_SESSION['uploads']) || !$_SESSION['uploads'] || !$content) return;
	$update = array();
	foreach($_SESSION['uploads'] as $file) {
		if(strpos($content, $file) === false) {
			delete_upload($file, $_userid);
		} else {
			if($DT['uploadlog'] && $itemid) $update[] = "'".md5($file)."'";
		}
	}
	if($update) $db->query("UPDATE {$db->pre}upload_".($_userid%10)." SET itemid=$itemid WHERE item IN (".implode(',', $update).")");
	$_SESSION['uploads'] = array();
}

function check_period($period) {
	global $DT_TIME;
	if($period) {
		if(strpos($period, ',') === false) {
			$period = explode('|', $period);
			foreach($period as $p) {
				$p = str_replace(':', '.', $p);
				$p = explode('-', $p);
				$f = $p[0];
				$t = $p[1];
				$n = date('G.i', $DT_TIME);
				if(($f > $t && ($n > $f || $n < $t)) || ($f < $t && $n > $f && $n < $t)) return true;
			}
			return false;
		} else {
			return strpos(','.$period.',', ','.date('w', $DT_TIME).',') === false ? false : true;
		}
	} else {
		return false;
	}
}

function get_status($status, $check) {
	global $DT;
	if(!$check && $DT['check_week'] && check_period($DT['check_week'])) $check = true;
	if(!$check && $DT['check_hour'] && check_period($DT['check_hour'])) $check = true;
	if($status == 0) {
		return 0;
	} else if($status == 1) {
		return 2;
	} else if($status == 2) {
		return 2;
	} else if($status == 3) {
		return $check ? 2 : 3;
	} else if($status == 4) {
		return $check ? 2 : 3;
	} else {
		return 2;
	}
}

function reload_captcha() {
	return 'try{parent.reloadcaptcha();}catch(e){}';
}

function reload_question() {
	return 'try{parent.reloadquestion();}catch(e){}';
}

function sync_weibo($site, $moduleid, $itemid) {
	$file = $site == 'qzone' ? 'qq/qzone.php' : $site.'/post.php';
	return 'document.write(\'<img src="'.DT_PATH.'api/oauth/'.$file.'?auth='.encrypt($moduleid.'-'.$itemid).'" width="1" height="1"/>\');';
}
?>