<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



/**
 * 删除非空目录（v2.0.12后版本弃用）
 *
 * @param	string	$dir	目录名称
 * @return	bool|void
 */
function dr_rmdir($dir) {
	
	if (!$dir || is_file(trim($dir, DIRECTORY_SEPARATOR).'/index.php')) {
        return FALSE;
    }
    
	@rmdir($dir);
}

// 格式化资料块

function dr_get_block_value($value) {

	if (!$value['content']) {
		$value['i'] = 1;
		$value['value_1'] = '';
	} else {
		if (preg_match('/\{i-([1-9]+)\}:/U', $value['content'], $preg)) {
			$value['i'] = intval($preg[1]);
			$value['value_'.$value['i']] = str_replace($preg[0], '', $value['content']);
			if ($value['i'] == 4) {
				$value['value_'.$value['i']] = dr_string2array($value['value_'.$value['i']]);
			}
		} else {
			$value['i'] = 1;
			$value['value_1'] = $value['content'];
		}
	}

	return $value;
}



/**
 * 模块列表第一页生成静态异步链接
 *
 * @param	intval	$id		文档id
 * @param	intval	$url	是否返回url
 * @return  string
 */
function dr_module_create_list_file($id, $url = 0) {

	$file = 'index.php?'.(defined('IS_SHARE') && IS_SHARE ? '' : 's='.APP_DIR.'&').'c=home&m=create_list_html&id='.$id;
	if ($url) {
        return $file;
    }

	return '<script src="'.$file.'"></script>';
}

/**
 * 模块内容生成静态异步链接
 *
 * @param	intval	$id		文档id
 * @param	intval	$url	是否返回url
 * @return  string
 */
function dr_module_create_show_file($id, $url = 0) {

	$file = 'index.php?'.(defined('IS_SHARE') && IS_SHARE ? '' : 's='.APP_DIR.'&').'c=show&m=create_html&id='.$id;
	if ($url) {
        return $file;
    }

	return '<script src="'.$file.'"></script>';
}

/**
 * 模块内容扩展生成静态异步链接
 *
 * @param	intval	$id		文档id
 * @param	intval	$url	是否返回url
 * @return  string
 */
function dr_module_create_extend_file($id, $url = 0) {

	$file = 'index.php?'.(defined('IS_SHARE') && IS_SHARE ? '' : 's='.APP_DIR.'&').'c=extend&m=create_html&cid='.$id;

	if ($url) {
        return $file;
    }

	return '<script src="'.$file.'"></script>';
}

/**
 * 管理员权限划分表格
 *
 * @param	array	$role	角色缓存
 * @param	array	$value	值
 * @return  string
 */
function dr_admin_rule($role, $value = NULL) {

	if (!$role) {
        return NULL;
    }
	
	$html = '';
	foreach ($role as $id => $t) {
		if ($id > 1) {
			$html.= '<div class="form-group">';
			$html.= '	<label class="col-md-2 control-label">'.$t['name'].'：</label>';
			$html.= '	<div class="col-md-9">';
			$html.= '	<div class="checkbox-list">';
			$html.= '		<label class="checkbox-inline"><input name="data[setting][admin]['.$id.'][show]" class="dr_show" type="checkbox" value="1" '.(isset($value[$id]['show']) && $value[$id]['show'] ? 'checked' : '').' /> '.fc_lang('管理').'</label>';
            $html.= '		<label class="checkbox-inline"><input name="data[setting][admin]['.$id.'][add]" class="dr_add" type="checkbox" value="1" '.(isset($value[$id]['add']) && $value[$id]['add'] ? 'checked' : '').' /> '.fc_lang('添加').'</label>';
            $html.= '		<label class="checkbox-inline"><input name="data[setting][admin]['.$id.'][edit]" class="dr_edit" type="checkbox" value="1" '.(isset($value[$id]['edit']) && $value[$id]['edit'] ?  'checked' : '').' /> '.fc_lang('修改').'</label>';
            $html.= '		<label class="checkbox-inline"><input name="data[setting][admin]['.$id.'][del]" class="dr_del" type="checkbox" value="1" '.(isset($value[$id]['del']) && $value[$id]['del'] ? 'checked' : '').' /> '.fc_lang('删除').'</label>';
            $html.= '	</div>';
            $html.= '	</div>';
			$html.= '</div>';
		}
	}
	
	return $html;
}

function dr_member_rule($groups, $data) {

	if (!$groups) {
        return NULL;
    }

	$html = '';
	$value = $data['permission'];
	foreach ($groups as $id => $group) {
		if ($id > 2) {
			$html.= '<div class="form-group">';
			$html.= '	<label class="col-md-2 control-label">'.$group['name'].'：</label>';
			$html.= '	<div class="col-md-9">';
            $html.= '	</div>';
			$html.= '</div>';
			foreach ($group['level'] as $level) {
				$id = $group['id'].'_'.$level['id'];
				$html.= '<div class="form-group">';
				$html.= '	<label class="col-md-2 control-label">'.$level['name'].'：</label>';
				$html.= '	<div class="col-md-9">';
				$html.= '	<div class="checkbox-list">';
				$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][show]" class="dr_show" type="checkbox" value="1" '.(isset($value[$id]['show']) && $value[$id]['show'] ? 'checked' : '').' /> '.fc_lang('拒绝访问').'</label>';
				$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][add]" class="dr_add" type="checkbox" value="1" '.(isset($value[$id]['add']) && $value[$id]['add'] ? 'checked' : '').' /> '.fc_lang('添加').'</label>';
				$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][edit]" class="dr_edit" type="checkbox" value="1" '.(isset($value[$id]['edit']) && $value[$id]['edit'] ?  'checked' : '').' /> '.fc_lang('修改').'</label>';
				$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][del]" class="dr_del" type="checkbox" value="1" '.(isset($value[$id]['del']) && $value[$id]['del'] ? 'checked' : '').' /> '.fc_lang('删除').'</label>';
				$html.= '		<label class="checkbox-inline"><a href="javascript:;" onclick="dr_member_rule(\''.$id.'\', \''.dr_url(APP_DIR ? APP_DIR."/category/rule"  : "category_share/rule", array("catid"=>$data['id'], "id"=>$id)).'\', \''.$group['name'].'-'.$level['name'].'\')" class="blue"> '.fc_lang('规则设置').'</a> <span id="dr_status_'.$id.'"></span></label>';
				$html.= '	</div>';
				$html.= '	</div>';
				$html.= '</div>';
			}
		} else {
			$id = $group['id'];
			$html.= '<div class="form-group">';
			$html.= '	<label class="col-md-2 control-label">'.$group['name'].'：</label>';
			$html.= '	<div class="col-md-9">';
			$html.= '	<div class="checkbox-list">';
			$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][show]" class="dr_show" type="checkbox" value="1" '.(isset($value[$id]['show']) && $value[$id]['show'] ? 'checked' : '').' /> '.fc_lang('拒绝访问').'</label>';
			$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][add]" class="dr_add" type="checkbox" value="1" '.(isset($value[$id]['add']) && $value[$id]['add'] ? 'checked' : '').' /> '.fc_lang('添加').'</label>';
			$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][edit]" class="dr_edit" type="checkbox" value="1" '.(isset($value[$id]['edit']) && $value[$id]['edit'] ?  'checked' : '').' /> '.fc_lang('修改').'</label>';
			$html.= '		<label class="checkbox-inline"><input name="rule['.$id.'][del]" class="dr_del" type="checkbox" value="1" '.(isset($value[$id]['del']) && $value[$id]['del'] ? 'checked' : '').' /> '.fc_lang('删除').'</label>';
			$html.= '		<label class="checkbox-inline"><a href="javascript:;" onclick="dr_member_rule(\''.$id.'\', \''.dr_url(APP_DIR ? APP_DIR."/category/rule" : "category_share/rule", array("catid"=>$data['id'], "id"=>$id)).'\', \''.$group['name'].'-'.$level['name'].'\')" class="blue"> '.fc_lang('规则设置').'</a> <span id="dr_status_'.$id.'"></span></label>';
			$html.= '	</div>';
			$html.= '	</div>';
			$html.= '</div>';
		}
	}

	return $html;
}

/**
 * 通过会员名称取会员id
 *
 * @param	string	$username
 * @return  intval
 */
function get_member_id($username) {

	if (!$username) {
        return 0;
    }
	
	$ci	= &get_instance();
	$data = $ci->db
			   ->select('uid')
			   ->where('username', $username)
			   ->limit(1)
			   ->get('member')
			   ->row_array();
			   
	return (int)$data['uid'];
}

/**
 * 通过会员ui取会员OAuth昵称
 *
 * @param	intval	$uid
 * @return  string
 */
function get_member_nickname($uid) {

	if (!$uid) {
        return '';
    }
	
	$ci	= &get_instance();
	$data = $ci->db->select('nickname')->where('uid', (int)$uid)->get('member_oauth')->row_array();
	return $data['nickname'];
}

/**
 * 通过会员ui取会员字段
 *
 * @param	intval	$uid
 * @return  string
 */
function get_member_value($uid, $value = 'username') {

	if (!$uid) {
        return '';
    }
	
	$ci	= &get_instance();
	$data = $ci->db->select($value)->where('uid', (int)$uid)->get('member')->row_array();
	return $data[$value];
}

/**
 * 附件信息
 *
 * @param	string	$key
 * @return  array
 */
function dr_file_info($key) {

	if (!$key) {
        return NULL;
    }
	
	if (is_numeric($key)) {
		$info = get_attachment($key);
		if (!$info) {
            return NULL;
        }
		$info['icon'] = is_file(WEBPATH.'statics/admin/images/ext/'.$info['fileext'].'.gif') ? THEME_PATH.'admin/images/ext/'.$info['fileext'].'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
		$info['size'] = dr_format_file_size($info['filesize']);
		$info['name'] = dr_strcut($info['filename'], 20).'.'.$info['fileext'];
		return $info;
	} else {
		return array('icon' => THEME_PATH.'admin/images/ext/url.gif', 'size' => '');
	}
}
 
/**
 * 字段输出表单
 *
 * @param	string	$username
 * @return  intval
 */
function dr_field_input($name, $type, $option, $value = NULL, $id = 0) {

	$ci	= &get_instance();
	$ci->load->library('Dfield', array(APP_DIR));

	$field = $ci->dfield->get($type);
	if (!is_object($field)) {
        return NULL;
    }
	
	A_Field::set_input_format('{value}');
	
	return preg_replace('/(<div class="on.+<\/div>)/U', '', $field->input($name, $name, $option, $value, $id));
}

/**
 * 目录扫描
 *
 * @param	string	$source_dir		Path to source
 * @param	int	$directory_depth	Depth of directories to traverse
 *						(0 = fully recursive, 1 = current dir, etc)
 * @param	bool	$hidden			Whether to show hidden files
 * @return	array
 */
function dr_dir_map($source_dir, $directory_depth = 0, $hidden = FALSE) {

	if ($fp = @opendir($source_dir)) {
	
		$filedata = array();
		$new_depth = $directory_depth - 1;
		$source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		
		while (FALSE !== ($file = readdir($fp))) {
			if ($file === '.' OR $file === '..'
            OR ($hidden === FALSE && $file[0] === '.')
            OR !@is_dir($source_dir.$file)) {
				continue;
			}
			if (($directory_depth < 1 OR $new_depth > 0)
            && @is_dir($source_dir.$file)) {
				$filedata[$file] = dr_dir_map($source_dir.DIRECTORY_SEPARATOR.$file, $new_depth, $hidden);
			} else {
				$filedata[] = $file;
			}
		}
		closedir($fp);
		return $filedata;
	}
	
	return FALSE;
}