<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 高级版专享函数库（请勿复制与转载）
 *
 */

function dr_help_url($id) {
    return 'http://www1.dayrui.com/help-doc/read-'.$id.'.html';
}

/**
 * 多语言输出
 *
 * @param	多个参数
 * @return	string|NULL
 */
function fc_lang() {

    $param = func_get_args();
    if (empty($param)) {
        return NULL;
    }

    // 取第一个作为语言名称
    $string = $param[0];
    unset($param[0]);

    // 调用语言包内容
    $lang = get_instance()->lang->line($string);
    $string = $lang ? $lang : $string;
    
    // 替换
    $string = get_instance()->replace_lang($string);

    return $param ? vsprintf($string, $param) : $string;
}



/**
 * 多语言输出
 *
 * @param	多个参数
 * @return	string|NULL
 */
function dr_lang() {

    $param = func_get_args();
    if (empty($param)) {
        return NULL;
    }

    if (count($param) == 1) {
        return lang($param[0]);
    }

    // 取第一个作为语言名称
    $string = $param[0];
    unset($param[0]);

    return vsprintf(lang($string), $param);
}

/**
 * 网站风格目录
 *
 * @return	string|NULL
 */
function dr_get_theme() {

    if (!function_exists('dr_dir_map')) {
        return array('default');
    }

    return array_diff(dr_dir_map(WEBPATH.'statics/', 1), array('avatar', 'admin', 'comment', 'emotions', 'js', 'oauth', 'watermark', 'space'));

}

/*********************以上为兼容函数(兼容v3 Larval框架结构算法)*******************/



/**
 * 模块评论js调用
 *
 * @param	intval	$id
 * @return	string
 */
function dr_module_comment($dir, $id) {
    $url = SITE_URL."index.php?s=".$dir."&c=comment&m=index&r=1&id={$id}&js=dr_ajax_module_comment_{$id}";
    return "<div id=\"dr_module_comment_{$id}\"></div><script type=\"text/javascript\">
	function dr_ajax_module_comment_{$id}(type, page) {
	    $.ajax({type: \"GET\", url: \"{$url}&type=\"+type+\"&page=\"+page+\"&\"+Math.random(), dataType:\"jsonp\",
            success: function (data) {
                $(\"#dr_module_comment_{$id}\").html(data.html);
            }
        });
	}
	dr_ajax_module_comment_{$id}(0, 1);
	</script>";
}

/**
 * 模块扩展评论js调用
 *
 * @param	intval	$id
 * @return	string
 */
function dr_extend_comment($dir, $id) {

    $url = SITE_URL."index.php?s=".$dir."&c=ecomment&m=index&r=1&id={$id}&js=dr_ajax_extend_comment_{$id}";
    return "<div id=\"dr_extend_comment_{$id}\"></div><script type=\"text/javascript\">
	function dr_ajax_extend_comment_{$id}(type, page) {
	    $.ajax({type: \"GET\", url: \"{$url}&type=\"+type+\"&page=\"+page+\"&\"+Math.random(), dataType:\"jsonp\",
            success: function (data) {
                $(\"#dr_extend_comment_{$id}\").html(data.html);
            }
        });
	}
	dr_ajax_extend_comment_{$id}(0, 1);
	</script>";
}

/**
 * 获取6位数字随机验证码
 */
function dr_randcode() {
    return rand(100000, 999999);
}

/**
 * 获取镜像下载地址
 * 
 * @param	string	$name		字段名称
 * @param	intval	$value		文件id
 * @param	string	$dirname	模块目录
 * @param	intval	$catid		栏目id
 * @return	array
 */
function dr_down_server($name, $value, $dirname = APP_DIR) {

    if (!is_numeric($value)) {
        return array();
    }

    $file = get_attachment($value);
    $file = $file['_attachment'];
    $server = array();
    $module = get_module($dirname, SITE_ID);

    if ($module['field'][$name]) {
        $server = $module['field'][$name]['setting']['option']['server'];
    } elseif ($module['extend'][$name]) {
        $server = $module['extend'][$name]['setting']['option']['server'];
    }

    if (!$server) {
        return array();
    }

    $ci = &get_instance();
    $data = $ci->get_cache('downservers');
    if (!$data) {
        return array();
    }

    $return = array();

    foreach ($server as $id) {
        $return[] = array(
            'name' => $data[$id]['name'],
            'url' => trim($data[$id]['server'], '/').'/'.$file
        );
    }

    return $return;
}

/**
 * 删除目录及目录下面的所有文件
 * 
 * @param	string	$dir		路径
 * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
 */
function dr_dir_delete($dir) {

    $dir = str_replace('\\', '/', $dir);
    if (substr($dir, -1) != '/') {
        $dir = $dir . '/';
    }
    if (!is_dir($dir)) {
        return FALSE;
    }

    $list = glob($dir . '*');
    foreach ($list as $v) {
        is_dir($v) ? dr_dir_delete($v) : @unlink($v);
    }

    return @rmdir($dir);
}

/**
 * discuz加密/解密
 */
function dr_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    if (!$string) {
        return '';
    }

    $ckey_length = 4;

    $key = md5($key ? $key : SYS_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 统计模块表单数量
 *
 * @param	intval	$cid	模块内容id
 * @param	intval	$mid	模块表单id
 * @param	string	$module	模块目录
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_mform_total($cid, $mid, $module = APP_DIR, $cache = 10000) {

    $ci = &get_instance();
    $name = 'mform-total-'.$module.'-'.$mid.'-'.$cid;
    $data = $ci->get_cache_data($name);
    if (!$data) {
        $data = $ci->db->where('cid', (int)$cid)->count_all_results(SITE_ID.'_'.$module.'_form_'.$mid);
        $ci->set_cache_data($name, $data, $cache ? $cache : 10000);
    }

    return $data;
}

function dr_array2array($a1, $a2) {

    if (!$a1 || !$a2) {
        return array();
    } elseif ($a1 && !$a2) {
        return $a1;
    } elseif (!$a1 && $a2) {
        return $a2;
    }

    return array_merge($a1, $a2);

}

/**
 * 调用模块的评论数
 *
 * @param	intval	$dir	目录
 * @param	intval	$cid	主题id
 * @return	string
 */
function dr_module_comment_total($dir, $cid, $name = 0) {

    $ci = &get_instance();
    $name = 'module-comment-total-'.$cid.$dir;
    $data = $ci->get_cache_data($name);
    if (!$data) {
        $ci->load->model('comment_model');
        $ci->comment_model->module($dir);
        $data = $ci->comment_model->total_info($cid);
        $ci->set_cache_data($name, $data, (int)SYS_CACHE_COMMENT);
    }

    return $name ? (isset($data[$name]) ? $data[$name] : '') : '';
}

/**
 * 调用模块扩展的评论数
 *
 * @param	intval	$dir	目录
 * @param	intval	$cid	主题id
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_extend_comment_total($dir, $cid, $name = 0) {

    $ci = &get_instance();
    $name = 'extend-comment-total-'.$cid.$dir;
    $data = $ci->get_cache_data($name);
    if (!$data) {
        $ci->load->model('comment_model');
        $ci->comment_model->extend($dir);
        $data = $ci->comment_model->total_info($cid);
        $ci->set_cache_data($name, $data, (int)SYS_CACHE_COMMENT);
    }

    return $name ? (isset($data[$name]) ? $data[$name] : '') : '';
}

/**
 * 调用会员详细信息（自定义字段需要手动格式化）
 *
 * @param	intval	$uid	会员uid
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_member_info($uid, $cache = -1) {

    $ci = &get_instance();
    $data = $ci->get_cache_data('member-info-'.$uid);
    if (!$data) {
        $data = $ci->member_model->get_member($uid);
        $ci->set_cache_data('member-info-'.$uid, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}

/**
 * 调用会员实名认证信息（自定义字段需要手动格式化）
 *
 * @param	intval	$uid	会员uid
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_member_auth($uid, $cache = -1) {

    $ci = &get_instance();
    $data = $ci->get_cache_data('member-auth-'.$uid);
    if (!$data) {
        $data = $ci->db->where('uid', $uid)->get('member_auth')->row_array();
        if (!$data) {
            return array();
        }
        $ci->set_cache_data('member-auth-'.$uid, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}

/**
 * 获取到上级邀请者的信息
 *
 * @param	intval	$uid	我的uid
 * @param	string	$name	字段信息
 * @return
 */
function dr_get_invite($uid, $name = 'uid') {

    if (!dr_is_app('invite')) {
        return '';
    }

    $ci = &get_instance();
    $data = $ci->db->where('rid', $uid)->get('member_invite')->row_array();
    return $data[$name] ? $data[$name] : '';

}

/**
 * 调用会员空间详细信息（自定义字段需要手动格式化）
 *
 * @param	intval	$uid	会员uid
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_space_info($uid, $cache = -1) {

    if (!MEMBER_OPEN_SPACE) {
        return '';
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('space-info-'.$uid);
    if (!$data) {
        $data = $ci->db->where('uid', $uid)->limit(1)->get('space')->row_array();
        if (!$data) {
            return NULL;
        }
        $data['url'] = dr_space_url($uid);
        $ci->set_cache_data('space-info-'.$uid, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}

/**
 * 调用会员SNS的详细信息
 *
 * @param	intval	$uid	会员uid
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_sns_info($uid, $cache = -1) {

    if (!MEMBER_OPEN_SPACE) {
        return '';
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('sns-info-'.$uid);
    if (!$data) {
        $data = $ci->member_model->get_sns($uid);
        $ci->set_cache_data('sns-info-'.$uid, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}

/**
 * 模块内容收费内容js调用
 *
 * @param	intval	$id
 * @return	string
 */
function dr_show_buy($id) {
    $url = SITE_URL."index.php?s=".APP_DIR."&c=api&m=buy&id={$id}";
    return "<div id=\"dr_buy_html_{$id}\"></div><script type=\"text/javascript\">
	$.ajax({type: \"GET\", url: \"{$url}&\"+Math.random(), dataType:\"jsonp\",
	    success: function (data) {
			$(\"#dr_buy_html_{$id}\").html(data.html);
	    }
	});
	</script>";
}


/**
 * 模块扩展内容收费内容js调用
 *
 * @param	intval	$id
 * @return	string
 */
function dr_extend_buy($eid) {
    $url = SITE_URL."index.php?s=".APP_DIR."&c=api&m=buy&eid={$eid}";
    return "<div id=\"dr_buy_html_{$eid}\"></div><script type=\"text/javascript\">
	$.ajax({type: \"GET\", url: \"{$url}&\"+Math.random(), dataType:\"jsonp\",
	    success: function (data) {
			$(\"#dr_buy_html_{$eid}\").html(data.html);
	    }
	});
	</script>";
}

/**
 * 检测会员在线情况
 */
function dr_member_online($uid, $type) {
    return "<script type=\"text/javascript\" src=\"".SITE_URL."index.php?s=member&c=api&m=online&uid={$uid}&type={$type}\"></script>";
}

/**
 * 用于视频播放器字段输出
 *
 * @param	array	$value		字段值
 * @param	intval	$width		宽度
 * @param	intval	$height		高度
 * @param	intval	$auto		是否自动播放
 * @param	intval	$time		是否显示广告，值为广告倒计时
 * @param	string	$next_url	下一集url
 * @param	string	$thumb		视频分享图片
 * @return	array
 */
function dr_player($value, $width, $height, $auto = 0, $time = 5, $next_url = '', $thumb = '') {

    $name = md5($value['file']);
    $file = dr_get_file($value['file']);

    $width = $width ? $width : '100%';
    $height = $height ? $height : '100%';
    // 模板数据
    $data = array(
        'file' => $file,
        'name' => $name,
        'width' => $width,
        'height' => $height,
        'next_url' => $next_url,
        'thumb' => dr_get_file($thumb),
        'server_url' => SITE_URL.'api/ckplayer/',
        'params' => '',
    );

    // 自动播放
    $data['params'].='p:\''.$auto.'\','.PHP_EOL;

    // 下一集
    if (!$next_url) {
        $data['params'].='e:\'5\','.PHP_EOL;
    } else {
        $data['params'].='e:\'0\','.PHP_EOL;
    }

    // 解析地址
    $data['params'].='        f:\''.$file.'\','.PHP_EOL;

    // 定时点处理
    if ($value['point']) {
        $k = $n = '';
        foreach ($value['point'] as $i => $note) {
            $k.= $i.'|';
            $n.= $note.'|';
        }
        $data['params'].='        k:\''.trim($k, '|').'\','.PHP_EOL;
        $data['params'].='        n:\''.trim($n, '|').'\',';
    }

    // 广告效果
    if ($time) {

        $ci = &get_instance();
        $video = $ci->get_cache('poster-video-'.SITE_ID);
        if ($video && dr_is_app('adm')) {
            $ci->load->add_package_path(FCPATH.'app/adm/');
            $ci->load->model('poster_model');
            $poster = $ci->poster_model->poster($video);
            if ($poster) {
                $value = dr_string2array($poster['value']);
                $ad = array(
                    'url' => urlencode($ci->poster_model->get_url($poster['id'])),
                    'file' => dr_get_file($value['file']),
                );
                // 前置广告
                $data['params'].='        l:\''.$ad['file'].'\','.PHP_EOL;
                $data['params'].='        t:\''.$time.'\','.PHP_EOL;
                $data['params'].='        r:\''.$ad['url'].'\','.PHP_EOL;
                // 暂停广告
                $data['params'].='        d:\''.$ad['file'].'\','.PHP_EOL;
                $data['params'].='        u:\''.$ad['url'].'\','.PHP_EOL;
            }
        }
    }

    // 引入JS
    if (!defined('CKPLAYER_JS')) {
        define('CKPLAYER_JS', 1);
        $data['js_code'] = '<script type="text/javascript" src="'.SITE_URL.'index.php?c=api&m=ckplayer&at=js"></script>';
        $data['js_code'].= '<script type="text/javascript" src="'.$data['server_url'].'config/offlights.js" charset="utf-8"></script>';
        $data['js_code'].= '<script type="text/javascript" src="'.$data['server_url'].'ckplayer.js" charset="utf-8"></script>';
    }

    $code = file_get_contents(WEBPATH.'api/ckplayer/config/code.html');

    // 兼容php5.5
    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($data);
        $code = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $code);
        unset($rep);
    } else {
        extract($data);
        $code = preg_replace('#{([a-z_0-9]+)}#Ue', "\$\\1", $code);
    }

    return $code;
}

/**
 * 验证码图片获取
 */
function dr_code($width, $height, $url = '') {
    $url = '/index.php?c=api&m=captcha&width='.$width.'&height='.$height;
    return '<img align="absmiddle" style="cursor:pointer;" onclick="this.src=\''.$url.'&\'+Math.random();" src="'.$url.'" />';
}

/**
 * 排序操作
 */
function ns_sorting($name) {

    $value = $_GET['order'] ? $_GET['order'] : '';
    if (!$value) {
        return 'sorting';
    }

    if (strpos($value, $name) === 0 && strpos($value, 'asc') !== FALSE) {
        return 'sorting_asc';
    } elseif (strpos($value, $name) === 0 && strpos($value, 'desc') !== FALSE) {
        return 'sorting_desc';
    }

    return 'sorting';
}

/**
 * 移除order字符串
 */
function dr_member_order($url) {

    $data = @explode('&', $url);
    if ($data) {
        foreach ($data as $t) {
            if (strpos($t, 'order=') === 0) {
                $url = str_replace('&' . $t, '', $url);
            } elseif (strpos($t, 'action=') === 0) {
                $url = str_replace('&' . $t, '', $url);
            }
        }
    }

    return $url;
}

/**
 * 统计图表调用
 */
function dr_chart($file, $width, $height) {


}

/**
 * 百度地图调用
 */
function dr_baidu_map($value, $zoom = 5, $width = 600, $height = 400) {

    if (!$value) {
        return NULL;
    }

    $id = 'dr_map_'.rand(0, 99);
    $width = $width ? $width : '100%';
    list($lngX, $latY) = explode(',', $value);

    return '<script type=\'text/javascript\' src=\'http://api.map.baidu.com/api?v=1.4\'></script>
	<div id="' . $id . '" style="width:' . $width . 'px; height:' . $height . 'px; overflow:hidden"></div>
	<script type="text/javascript">
	var mapObj=null;
	lngX = "' . $lngX . '";
	latY = "' . $latY . '";
	zoom = "' . $zoom . '";		
	var mapObj = new BMap.Map("'.$id.'");
	var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
	mapObj.addControl(ctrl_nav);
	mapObj.enableDragging();
	mapObj.enableScrollWheelZoom();
	mapObj.enableDoubleClickZoom();
	mapObj.enableKeyboard();//启用键盘上下左右键移动地图
	mapObj.centerAndZoom(new BMap.Point(lngX,latY),zoom);
	drawPoints();
	function drawPoints(){
		var myIcon = new BMap.Icon("' . THEME_PATH . 'admin/images/mak.png", new BMap.Size(27, 45));
		var center = mapObj.getCenter();
		var point = new BMap.Point(lngX,latY);
		var marker = new BMap.Marker(point, {icon: myIcon});
		mapObj.addOverlay(marker);
	}
	</script>';
}

/**
 * 任意字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	intval	$id
 * @return	array
 */
function dr_field_options_id($id, $name = '') {

    $id = (int)$id;
    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('field-info-'.$id);
    if (!$data) {
        $data = $ci->db->where('id', $id)->get('field')->row_array();
        if (!$data) {
            return NULL;
        }
        $data['setting'] = dr_string2array($data['setting']);
        $option = $data['setting']['option']['options'];
        if (!$option) {
            return NULL;
        }
        $data = explode(
            PHP_EOL,
            str_replace(
                array(chr(13), chr(10)),
                PHP_EOL,
                $option
            )
        );
        $return = array();
        foreach ($data as $t) {
            if ($t) {
                if (strpos($t, '|') !== FALSE) {
                    list($n, $v) = explode('|', $t);
                    $v = is_null($v) || !strlen($v) ? '' : trim($v);
                } else {
                    $v = $n = trim($t);
                }
                $return[$v] = trim($n);
            }
        }
        $ci->set_cache_data('field-info-'.$id, $return, 10000);
        return $return;
    }

    return $name && isset($data[$name]) ? $data[$name] : $data;
}

/**
 * 模块字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	string	$name
 * @param	intval	$catid
 * @param	string	$dirname
 * @return	array
 */
function dr_field_options($name, $catid = 0, $dirname = MOD_DIR) {

    if (!$name) {
        return NULL;
    }

    $module = get_module($dirname, SITE_ID);
    if (!$module) {
        return NULL;
    }

    $field = $catid && isset($module['category'][$catid]['field'][$name]) ? $module['category'][$catid]['field'][$name] : $module['field'][$name];
    if (!$field) {
        return NULL;
    }

    $option = $field['setting']['option']['options'];
    if (!$option) {
        return NULL;
    }

    $data = explode(
        PHP_EOL,
        str_replace(
            array(chr(13), chr(10)),
            PHP_EOL,
            $option
        )
    );
    $return = array();

    foreach ($data as $t) {
        if ($t) {
            if (strpos($t, '|') !== FALSE) {
                list($n, $v) = explode('|', $t);
                $v = is_null($v) || !strlen($v) ? '' : trim($v);
            } else {
                $v = $n = trim($t);
            }
            $return[$v] = trim($n);
        }
    }

    return $return;
}

/**
 * 会员字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	string	$name
 * @param	intval	$catid
 * @param	string	$dirname
 * @return	array
 */
function dr_member_field_options($name) {

    if (!$name) {
        return NULL;
    }

    $ci = &get_instance();
    $field = $ci->get_cache('member', 'field', $name);
    if (!$field) {
        return NULL;
    }

    $option = $field['setting']['option']['options'];
    if (!$option) {
        return NULL;
    }

    $data = explode(
        PHP_EOL,
        str_replace(
            array(chr(13), chr(10)),
            PHP_EOL,
            $option
        )
    );
    $return = array();

    foreach ($data as $t) {
        if ($t) {
            if (strpos($t, '|') !== FALSE) {
                list($n, $v) = explode('|', $t);
                $v = is_null($v) || !strlen($v) ? '' : trim($v);
            } else {
                $v = $n = trim($t);
            }
            $return[$v] = trim($n);
        }
    }

    return $return;
}

/**
 * 空间字段的选项值（用于options参数的字段，如复选框、下拉选择框、单选按钮）
 *
 * @param	string	$name
 * @param	intval	$catid
 * @param	string	$dirname
 * @return	array
 */
function dr_space_field_options($name) {

    if (!$name) {
        return NULL;
    }

    $ci = &get_instance();
    $field = $ci->get_cache('member', 'spacefield', $name);
    if (!$field) {
        return NULL;
    }

    $option = $field['setting']['option']['options'];
    if (!$option) {
        return NULL;
    }

    $data = explode(
        PHP_EOL,
        str_replace(
            array(chr(13), chr(10)),
            PHP_EOL,
            $option
        )
    );
    $return = array();

    foreach ($data as $t) {
        if ($t) {
            if (strpos($t, '|') !== FALSE) {
                list($n, $v) = explode('|', $t);
                $v = is_null($v) || !strlen($v) ? '' : trim($v);
            } else {
                $v = $n = trim($t);
            }
            $return[$v] = trim($n);
        }
    }

    return $return;
}

/**
 * 资料块内容
 *
 * @param	intval	$id
 * @return	array
 */
function dr_block($id, $type = 0, $site = 0) {
    $ci = &get_instance();
    $site = $site ? $site : SITE_ID;
    return $ci->get_cache('block-'.$site, $id, $type);
}

/**
 * 输出广告
 *
 * @param	intval	$id
 * @return	array
 */
function dr_poster($id) {

    if (!dr_is_app('adm')) {
        return;
    }

    $ci = &get_instance();
    $ci->load->add_package_path(FCPATH.'app/adm/');
    $ci->load->model('poster_model');

    return $ci->poster_model->code($id);
}

/**
 * 输出广告列表
 *
 * @param	intval	$id
 * @return	array
 */
function dr_poster_list($id, $all = 99) {

    if (!dr_is_app('adm')) {
        return;
    }

    $ci = &get_instance();
    $ci->load->add_package_path(FCPATH.'app/adm/');
    $ci->load->model('poster_model');
    $data = $ci->poster_model->poster($id, $all ? $all : 1);
    if (!$data) {
        return array();
    }

    $r = array();
    foreach ($data as $t) {
        $v = dr_string2array($t['value']);
        $r[] = array(
            'name' => $t['name'],
            'file' => dr_get_file($v['file']),
            'url' => $ci->poster_model->get_url($t['id']),
        );
    }
    return $r;
}

/**
 * 联动菜单调用
 *
 * @param	string	$code	菜单代码
 * @param	intval	$id		菜单id
 * @param	intval	$level	调用级别，1表示顶级，2表示第二级，等等
 * @param	string	$name	菜单名称，如果有显示它的值，否则返回数组
 * @return	array
 */
function dr_linkage($code, $id, $level = 0, $name = '') {

    if (!$id) {
        return false;
    }

    $ci = &get_instance();
    $link = $ci->get_cache('linkage-'.SITE_ID.'-'.$code);
    $cids = $ci->get_cache('linkage-'.SITE_ID.'-'.$code.'-id');
    if (is_numeric($id)) {
        // id 查询
        $id = $cids[$id];
        $data = $link[$id];
    } else {
        // 别名查询
        $data = $link[$id];
    }
    $pids = @explode(',', $data['pids']);
    if ($level == 0) {
        return $name ? $data[$name] : $data;
    }

    if (!$pids) {
        return $name ? $data[$name] : $data;
    }

    $i = 1;
    foreach ($pids as $pid) {
        if ($pid) {
            $pid = $cids[$pid]; // 把id转化成cname
            if ($i == $level) {
                return $name ? $link[$pid][$name] : $link[$pid];
            }
            $i++;
        }
    }

    return $name ? $data[$name] : $data;
}

/**
 * 记录信息调用
 *
 * @param	string	$string
 * @return	string
 */
function dr_lang_note($string) {

    return $string;
}

/**
 * 会员头像
 *
 * @param	intval	$uid
 * @param	string	$size
 * @return	string
 */
function dr_avatar($uid, $size = '45') {

    if ($uid) {
        // 判断Ucenter公共头像
		$size = $size > 100 ? 180 : $size;
        if (defined('UC_API')) {
            $data = dr_member_info($uid);
            if ($data) {
                list($ucenter) = uc_get_user($data['username']);
                return UC_API.'/avatar.php?uid='.$ucenter.'&size='.($size == 45 ? 'small' : 'big');
            }
        } else {
            foreach (array('png', 'jpg', 'gif', 'jpeg') as $ext) {
                if (is_file(SYS_UPLOAD_PATH.'/member/'.$uid.'/'.$size.'x'.$size.'.'.$ext)) {
                    return SYS_ATTACHMENT_URL.'member/'.$uid.'/'.$size.'x'.$size.'.'.$ext;
                }
            }
        }
    }

    return $size == 45 ? THEME_PATH.'admin/images/avatar_45.png' : THEME_PATH.'admin/images/avatar_90.png';
}

/**
 * 是否是一个有效的应用
 *
 * @param	string	$name
 * @return	bool
 */
function dr_is_app($name) {

    $ci = &get_instance();
    if (!$name || !is_dir(FCPATH.'app/'.$name)) {
        return FALSE;
    } elseif (@in_array($name, $ci->get_cache('app'))) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * 显示星星
 *
 * @param	intval	$num
 * @param	intval	$starthreshold	星星数在达到此阈值(设为 N)时，N 个星星显示为 1 个月亮、N 个月亮显示为 1 个太阳。
 * @return	string
 */
function dr_show_stars($num, $starthreshold = 4) {

    $str = '';
    $alt = 'alt="Rank: '.$num.'"';

    for ($i = 3; $i > 0; $i--) {
        $numlevel = intval($num / pow($starthreshold, ($i - 1)));
        $num = ($num % pow($starthreshold, ($i - 1)));
        for ($j = 0; $j < $numlevel; $j++) {
            $str.= '<img align="absmiddle" src="'.THEME_PATH.'admin/images/star_level'.$i.'.gif" '.$alt.' />';
        }
    }

    return $str;
}

/**
 * 模块内容阅读量显示js
 *
 * @param	intval	$id
 * @return	string
 */
function dr_show_hits($id) {
    return "<span id=\"dr_show_hits_{$id}\">0</span><script type=\"text/javascript\">
                $.ajax({
                    type: \"GET\",
                    url:\"".SITE_URL."index.php?c=api&m=hits&module=".MOD_DIR."&id={$id}\",
                    dataType: \"jsonp\",
                    success: function(data){
			            $(\"#dr_show_hits_{$id}\").html(data.html);
                    },
                    error: function(){ }
                });
    </script>";
}

/**
 * 模块内容阅读量显示js
 *
 * @param	intval	$id
 * @return	string
 */
function dr_extend_hits($id) {
    return "<span id=\"dr_extend_hits_{$id}\">0</span><script type=\"text/javascript\">
                $.ajax({
                    type: \"GET\",
                    async: false,
                    url:\"".SITE_URL."index.php?c=api&m=ehits&module=".MOD_DIR."&id={$id}\",
                    dataType: \"jsonp\",
                    success: function(data){
			            $(\"#dr_extend_hits_{$id}\").html(data.html);
                    },
                    error: function(){ }
                });
    </script>";
}

/**
 * 模型内容阅读量显示js
 *
 * @param	intval	$id
 * @return	string
 */
function dr_space_show_hits($mid, $id) {
    return "<span id=\"dr_space_show_hits_{$id}\">0</span><script type=\"text/javascript\">
                $.ajax({
                    type: \"GET\",
                    async: false,
                    url:\"".SITE_URL."index.php?s=member&c=api&m=hits&mid=".$mid."&id={$id}\",
                    dataType: \"jsonp\",
                    success: function(data){
			            $(\"#dr_space_show_hits_{$id}\").html(data.html);
                    },
                    error: function(){ }
                });
    </script>";
}

/**
 * 调用远程数据
 *
 * @param	string	$url
 * @return	string
 */
function dr_catcher_data($url) {

    // fopen模式
    if (ini_get('allow_url_fopen')) {
        $data = @file_get_contents($url);
        if ($data !== FALSE) {
            return $data;
        }
    }

    // curl模式
    if (function_exists('curl_init') && function_exists('curl_exec')) {
        $ch = curl_init($url);
        $data = '';
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    return NULL;
}

/**
 * 附件信息
 *
 * @param	intval	$id
 * @return  array
 */
function get_attachment($id) {

    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $info = $ci->get_cache_data("attachment-{$id}");
    if ($info) {
        // 附件缓存
        return $info;
    }

    $data = $ci->db->where('id', (int)$id)->get('attachment')->row_array();
    if (!$data) {
        return NULL;
    }

    $info = $ci->db->where('id', (int)$id)->get('attachment_'.(int)$data['tableid'])->row_array();
    if (!$info) {
        // 未使用的文件查找
        $info = $ci->db->where('id', (int)$id)->get('attachment_unused')->row_array();
    }

    if (!$info) {
        return NULL;
    }

    // 合并变量
    $info = $data + $info;
    $info['_attachment'] = trim($info['attachment'], '/');

    // 远程图片
    $url = $info['remote'] ? $ci->get_cache('attachment', $data['siteid'], 'data', $info['remote'], 'url') : '';
    $info['attachment'] = $url ? $url.'/'.$info['_attachment'] : dr_ck_attach($info['_attachment']);

	
    // 附件属性信息
    $attachinfo = dr_string2array($info['attachinfo']);

    // 验证图片是否具有高宽属性
    if (in_array($info['fileext'], array('jpg', 'gif', 'png'))
        && (!isset($attachinfo['width']) || !$attachinfo['width'])) {
        list($attachinfo['width'], $attachinfo['height']) = @getimagesize(dr_file($info['attachment']));
        // 更新到数据表
    }
    unset($info['attachinfo']);

    $info = $attachinfo ? $info + $attachinfo : $info;
	
    $ci->set_cache_data("attachment-{$id}", $info, SYS_CACHE_ATTACH); // 保存附件缓存

    return $info;
}

function dr_ck_attach($file) {

    if (!SYS_UPLOAD_DIR) {
        return $file;
    } elseif (strpos($file, SYS_UPLOAD_DIR) === 0) {
        return trim(str_replace(SYS_UPLOAD_DIR, '', $file), '/');
    } elseif (strpos($file, 'member/uploadfile/') === 0) {
        return trim(str_replace('member/uploadfile/', '', $file), '/');
    } else {
        return $file;
    }
}


/**
 * 调用缩略图函数
 */
function dr_image($id, $size = 0) {

    if (!$id) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    $info = get_attachment($id);
    if (!$info) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    // 远程图片
    if (isset($info['remote']) && $info['remote']) {
        $file = $info['attachment'];
    } else {
        $file = SYS_ATTACHMENT_URL.$info['attachment'];
    }

    if ($size) {
        return str_replace(
            basename($info['attachment']),
            basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
            $file
        );
    } else {
        return $file;
    }
}


/**
 * 生成缩略图函数
 * @param  $img    图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 * @param  $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 */
function dr_thumb2($img, $width = 100, $height = 100, $autocut = 1) {

    if (!$img) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    // 当图片是附件id
    if (is_numeric($img)) {
        $ci = &get_instance();
        return $ci->html_thumb2("$img-$width-$height-$autocut");
    } else {
        return dr_file($img);
    }
}

/**
 * 图片显示
 *
 * @param	string	$img	图片id或者路径
 * @param	intval	$width	输出宽度
 * @param	intval	$height	输出高度
 * @param	intval	$water	是否水印
 * @param	intval	$size	缩略图尺寸
 * @return  url
 */
function dr_thumb($img, $width = NULL, $height = NULL, $water = 0, $size = 0) {


    if (!$img) {
        return THEME_PATH.'admin/images/nopic.gif';
    }

    if (is_numeric($img)) { // 表示附件id
        $ci = &get_instance();
        return $ci->html_thumb("$img-$width-$height-$water-$size");
    }

    $img = dr_file($img);

    return $img ? $img : THEME_PATH.'admin/images/nopic.gif';
}

/**
 * 下载文件
 *
 * @param	string	$id
 * @return  array
 */
function dr_down_file($id) {

    if (!$id) {
        return '';
    }

    if (is_numeric($id)) { // 表示附件id
        $info = get_attachment($id);
        if ($info) {
            return SITE_URL."index.php?s=member&c=api&m=file&id=$id";
        }
    }

    $file = dr_file($id);

    return $file ? $file : '';
}

/**
 * 文件真实地址
 *
 * @param	string	$id
 * @return  array
 */
function dr_get_file($id) {

    if (!$id) {
        return '';
    }

    if (is_numeric($id)) { // 表示附件id
        $info = get_attachment($id);
        $id = $info['attachment'] ? $info['attachment'] : '';
    }

    $file = dr_file($id);

    return $file ? $file : '';
}

/**
 * 完整的文件路径
 *
 * @param	string	$url
 * @return  string
 */
function dr_file($url) {

    if (!$url || strlen($url) == 1) {
        return NULL;
    } elseif (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
        return $url;
    } elseif (strpos($url, SITE_PATH) !== FALSE && SITE_PATH != '/') {
        return $url;
    } elseif (substr($url, 0, 1) == '/') {
        return SITE_URL.substr($url, 1);
    }

    return SYS_ATTACHMENT_URL . $url;
}

/**
 * 全局变量调用
 *
 * @param	string	$name	别名
 * @return
 */
function dr_var($name) {
    return  get_instance()->get_cache('sysvar', $name);
}

/**
 * 格式化自定义字段内容
 *
 * @param	string	$field	字段类型
 * @param	string	$value	字段值
 * @param	array	$cfg	字段配置信息
 * @param	string	$dirname模块目录
 * @return
 */
function dr_get_value($field, $value, $cfg = NULL, $dirname = NULL) {

    $ci = &get_instance();
    $ci->load->library('dfield', array($dirname ? $dirname : MOD_DIR));

    $obj = $ci->dfield->get($field);
    if (!$obj) {
        return $value;
    }

    return $obj->output($value, $cfg);
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function dr_safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    return $string;
}

/**
 * 字符截取
 *
 * @param	string	$str
 * @param	intval	$length
 * @param	string	$dot
 * @return  string
 */
function dr_strcut($string, $length, $dot = '...') {

    $charset = 'utf-8';
    if (strlen($string) <= $length) {
        return $string;
    }

    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    $strcut = '';

    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length)
                break;
        }
        if ($noc > $length)
            $n -= $tn;
        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut.= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    return $strcut . $dot;
}

/**
 * 清除HTML标记
 *
 * @param	string	$str
 * @return  string
 */
function dr_clearhtml($str) {

    $str = str_replace(
        array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $str
    );

    $str = preg_replace("/\<[a-z]+(.*)\>/iU", "", $str);
    $str = preg_replace("/\<\/[a-z]+\>/iU", "", $str);
    $str = preg_replace("/{.+}/U", "", $str);
    $str = str_replace(array(chr(13), chr(10), '&nbsp;'), '', $str);
    $str = strip_tags($str);

    return trim($str);
}

/**
 * 模块缓存数据
 *
 * @param	string	$dirname	名称
 * @param	intval	$siteid		站点id
 * @return  array
 */
function get_module($dirname, $siteid = SITE_ID) {

    $ci = &get_instance();
    $ci->load->library('dcache');
    $dirname = $dirname == 'MOD_DIR' ? MOD_DIR : $dirname;
    $data = $ci->get_cache('module-'.$siteid.'-'.$dirname);

    if (!$data) {
        $ci->load->model('module_model');
        $ci->module_model->cache($dirname);
        $data = $ci->get_cache('module-'.$siteid.'-'.$dirname);
    }

    return $data;
}

/**
 * 随机颜色
 *
 * @return	string
 */
function dr_random_color() {

    $str = '#';

    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, 15);
        switch ($randNum) {
            case 10: $randNum = 'A';
                break;
            case 11: $randNum = 'B';
                break;
            case 12: $randNum = 'C';
                break;
            case 13: $randNum = 'D';
                break;
            case 14: $randNum = 'E';
                break;
            case 15: $randNum = 'F';
                break;
        }
        $str.= $randNum;
    }

    return $str;
}

/**
 * 友好时间显示函数
 *
 * @param	int		$time	时间戳
 * @return	string
 */
function dr_fdate($sTime, $formt = 'Y-m-d') {

    if (!$sTime) {
        return '';
    }

    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date('z',$cTime)) - intval(date('z',$sTime));
    $dYear = intval(date('Y',$cTime)) - intval(date('Y',$sTime));

    //n秒前，n分钟前，n小时前，日期
    if ($dTime < 60 ) {
        if ($dTime < 10) {
            return '刚刚';
        } else {
            return intval(floor($dTime / 10) * 10).'秒前';
        }
    } elseif ($dTime < 3600 ) {
        return intval($dTime/60).'分钟前';
    } elseif( $dTime >= 3600 && $dDay == 0  ){
        return intval($dTime/3600).'小时前';
    } elseif( $dDay > 0 && $dDay<=7 ){
        return intval($dDay).'天前';
    } elseif( $dDay > 7 &&  $dDay <= 30 ){
        return intval($dDay/7).'周前';
    } elseif( $dDay > 30 ){
        return intval($dDay/30).'个月前';
    } elseif ($dYear==0) {
        return date('m月d日', $sTime);
    } else {
        return date($formt, $sTime);
    }
}

/**
 * 时间显示函数
 *
 * @param	int		$time	时间戳
 * @param	string	$format	格式与date函数一致
 * @param	string	$color	当天显示颜色
 * @return	string
 */
function dr_date($time = NULL, $format = SITE_TIME_FORMAT, $color = NULL) {

    $time = (int) $time;
    if (!$time) {
        return '';
    }

    $format = $format ? $format : SITE_TIME_FORMAT;
    $string = date($format, $time);
    if (strpos($string, '1970') !== FALSE) {
        return '';
    }

    return $color && $time >= strtotime(date('Y-m-d 00:00:00')) && $time <= strtotime(date('Y-m-d 23:59:59')) ? '<font color="' . $color . '">' . $string . '</font>' : $string;
}

/**
 * JSON数据输出
 *
 * @param	int				$status	状态
 * @param	string|array	$code	返回数据
 * @param	string|int		$id		表单名称|返回Id
 * @return	string
 */
function dr_json($status, $code = '', $id = 0, $rid = 0) {

    if (defined('IS_API_AUTH') && IS_API_AUTH) {
        $data = array(
            'msg' => $code,
            'field' => strpos($id, 'http') === 0 ? '' : $id,
            'code' => $status ? 1 : 0,
            'id' => (int)$rid,
        );
        $return = $_GET['return'];
        if ($return) {
            $temp = $data;
            $data = array();
            foreach ($temp as $i => $t) {
                $data[$i.'_'.$return] = $t;
            }
        }
        return json_encode($data);
    }

    return json_encode(array('status' => $status, 'code' => $code, 'id' => $id));
}


/**
 * 将对象转换为数组
 *
 * @param	object	$obj	数组对象
 * @return	array
 */
function dr_object2array($obj) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if ($_arr && is_array($_arr)) {
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? dr_object2array($val) : $val;
            $arr[$key] = $val;
        }
    }
    return $arr;
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array
 */
function dr_string2array($data) {
    if (is_array($data)) {
        return $data;
    } elseif (!$data) {
        return array();
    } elseif (strpos($data, 'a:') === 0) {
        return unserialize(stripslashes($data));
    } else {
        return @json_decode($data, true);
    }
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data	数组
 * @return	string
 */
function dr_array2string($data) {
    return $data ? json_encode($data) : '';
}

/**
 * 递归创建目录
 *
 * @param	string	$dir	目录名称
 * @return	bool|void
 */
function dr_mkdirs($dir) {
    if (!$dir) {
        return FALSE;
    }
    if (!is_dir($dir)) {
        dr_mkdirs(dirname($dir));
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
    }
}

/**
 * 设置表单 input 或者 textarea 字段的值
 *
 * @param	string	$name	表单名称data[$name]
 * @param	string	$value	修改时的值$data[$name]
 * @return	string	
 */
function dr_set_value($name, $value = NULL) {
    return isset($_POST['data'][$name]) ? $_POST['data'][$name] : $value;
}

/**
 * 设置表单 select 字段的值
 *
 * @param	string	$name	表单名称data[$name]
 * @param	string	$value	修改时的值$data[$name]
 * @return	string	
 */
function dr_set_select($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' selected';
    }
    if ($value == $field) {
        return ' selected';
    }
}

/**
 * 设置表单 radio 字段的值
 *
 * @param	string	$name		表单名称data[$name]
 * @param	string	$value		修改时的值$data[$name]
 * @param	string	$field		当前选项的value值
 * @param	string	$default	默认选中状态
 * @return	string|void
 */
function dr_set_radio($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' checked';
    }
    if ($value == $field) {
        return ' checked';
    }
}

/**
 * 设置表单 checkbox 字段的值
 *
 * @param	string	$name		表单名称data[$name]
 * @param	array	$value		修改时的值$data[$name] 复选框为数组格式值
 * @param	string	$field		当前选项的value值
 * @param	string	$default	默认选中状态
 * @return	string|void
 */
function dr_set_checkbox($name, $value = NULL, $field = NULL, $default = FALSE) {
    $value = dr_set_value($name, $value);
    if ($value === NULL && $default == TRUE) {
        return ' checked';
    }
    if (@is_array($value) && in_array($field, $value)) {
        return ' checked';
    }
}

/**
 * 汉字转为拼音
 *
 * @param	string	$word
 * @return	string
 */
function dr_word2pinyin($word) {
    if (!$word) {
        return '';
    }
    $ci = &get_instance();
    $ci->load->library('pinyin');
    return $ci->pinyin->result($word);
}

/**
 * 格式化输出文件大小
 *
 * @param	int	$fileSize	大小
 * @param	int	$round		保留小数位
 * @return	string
 */
function dr_format_file_size($fileSize, $round = 2) {

    if (!$fileSize) {
        return 0;
    }

    $i = 0;
    $inv = 1 / 1024;
    $unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');

    while ($fileSize >= 1024 && $i < 8) {
        $fileSize *= $inv;
        ++$i;
    }

    $temp = sprintf("%.2f", $fileSize);
    $value = $temp - (int) $temp ? $temp : $fileSize;

    return round($value, $round) . $unit[$i];
}

/**
 * 关键字高亮显示
 *
 * @param	string	$string		字符串
 * @param	string	$keyword	关键字
 * @return	string
 */
function dr_keyword_highlight($string, $keyword) {
    return $keyword != '' ? str_ireplace($keyword, '<font color=red><strong>' . $keyword . '</strong></font>', $string) : $string;
}

function dollar($value, $include_cents = TRUE) {
    if (!$include_cents) {
        return "$" . number_format($value);
    } else {
        return "$" . number_format($value, 2, '.', ',');
    }
}

/**
 * Base64加密
 *
 * @param	string	$string
 * @return	string
 */
function dr_base64_encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

/**
 * Base64解密
 *
 * @param	string	$string
 * @return	string
 */
function dr_base64_decode($string) {
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data.= substr('====', $mod4);
    }
    return base64_decode($data);
}

// 兼容老版本

/**
 * 将语言转为实际内容
 *
 * @param	array	$_name	语言名称
 * @param	string	$lang	语言名称
 * @return	string
 */
function dr_lang2name($_name, $lang = SITE_LANGUAGE) {

    if (!$_name) {
        return NULL;
    }

    $name = dr_string2array($_name);
    if (!$name) {
        return lang($_name);
    }

    return isset($name[$lang]) ? $name[$lang] : $name['zh-cn'];
}

/**
 * 将实际内容转为语言
 *
 * @param	string	$value	实际内容
 * @param	array	$data	原语言数据
 * @return	string
 */
function dr_name2lang($value, $data = array()) {

    if (!is_array($data)) {
        $data = dr_string2array($data);
    }

    if (!isset($data['zh-cn'])) {
        $data['zh-cn'] = $value;
    }
    $data[SITE_LANGUAGE] = $value;

    return dr_array2string($data);
}

/**
 * 将数组转化为xml格式
 *
 * @param	array	$arr		数组
 * @param	bool	$htmlon		是否开启html模式
 * @param	bool	$isnormal	是否不全空格
 * @param	intval	$level		当前级别
 * @return	string
 */
function dr_array2xml($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1) {
    $space = str_repeat("\t", $level);
    $string = $level == 1 ? "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<result>\r\n" : '';
    foreach ($arr as $k => $v) {
        if (!is_array($v)) {
            $string.= $space."<$k>".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</$k>\r\n";
        } else {
            $name = is_numeric($k) ? 'item' . $k : $k;
            $string.= $space."<$name>\r\n".dr_array2xml($v, $htmlon, $isnormal, $level + 1).$space."</$name>\r\n";
        }
    }
    $string = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $string);
    return $level == 1 ? $string.'</result>' : $string;
}

if (!function_exists('gethostbyname')) {
    function gethostbyname($domain) {
        return $domain;
    }
}

/**
 *
 * 正则替换和过滤内容
 *
 * @param   $html
 */
function dr_preg_html($html){
    $p = array("/<[a|A][^>]+(topic=\"true\")+[^>]*+>#([^<]+)#<\/[a|A]>/",
        "/<[a|A][^>]+(data=\")+([^\"]+)\"[^>]*+>[^<]*+<\/[a|A]>/",
        "/<[img|IMG][^>]+(src=\")+([^\"]+)\"[^>]*+>/");
    $t = array('topic{data=$2}','$2','img{data=$2}');
    $html = preg_replace($p, $t, $html);
    $html = strip_tags($html, "<br/>");
    return $html;
}

/**
 * 格式化微博内容中url内容的长度
 * @param   string  $match 匹配后的字符串
 * @return  string  格式化后的字符串
 */
function _format_feed_content_url_length($match) {
    return '<a href="'.$match[1].'" target="_blank">'.$match[1].'</a>';
}

// 替换互动内容
function dr_sns_content($content) {

    // 替换话题URL
    if (preg_match_all('/\[TOPIC\-URL\-([0-9]+)\]/Ui', $content, $match)) {
        foreach ($match[1] as $t) {
            $url = defined('IS_SPACE') ? dr_space_sns_url(IS_SPACE, 'topic', $t) : dr_member_url('sns/topic', array('id' => $t));
            $content = str_replace('[TOPIC-URL-'.$t.']', $url, $content);
        }
    }

    // 替换表情
    if (preg_match_all('/\[([a-z0-9]+)\]/Ui', $content, $match)) {
        foreach ($match[1] as $t) {
            if (is_file(WEBPATH.'api/emotions/'.$t.'.gif')) {
                $content = str_replace('['.$t.']', '<img src="'.SITE_URL.'api/emotions/'.$t.'.gif" />', $content);
            }
        }
    }

    return $content;
}

/**
 * 动态详情
 *
 * @param	intval	$id	    动态id
 * @param	intval	$cache	缓存时间
 * @return	string
 */
function dr_sns_feed($id, $cache = -1) {

    $ci = &get_instance();
    $data = $ci->get_cache_data('sns-feed-'.$id);
    if (!$data) {
        $data = $ci->db->where('id', $id)->get('sns_feed')->row_array();
        $ci->set_cache_data('sns-feed-'.$id, $data, $cache > 0 ? $cache : SYS_CACHE_MEMBER);
    }

    return $data;
}

/**
 * 好友关系
 *
 * @param	intval	$uid    我的id
 * @param	intval	$uid2   对方id
 * @return	string
 */
function dr_sns_follow($uid, $uid2) {

    if (!$uid || !$uid2
        || $uid == $uid2) {
        // id不存在或者是自己时表示未关注吧
        return -1;
    }

    $ci = &get_instance();
    $data = $ci->db->select('isdouble')->where('fid', $uid)->where('uid', $uid2)->get('sns_follow')->row_array();
    if (!$data) {
        return -1; // 未关注
    } elseif ($data['isdouble']) {
        return 1; // 相互关注
    } else {
        return 0; // 已经关注
    }
}

// 二维码
function dr_qrcode_url($text, $uid = 0, $level = 'L', $size = 5) {
    return SITE_URL.'index.php?c=api&m=qrcode&uid='.urlencode($uid).'&text='.urlencode($text).'&size='.$size.'&level='.$level;
}

// 过滤非法字段
function dr_get_order_string($str, $order) {

    if (substr_count($str, ' ') >= 2
        || strpos($str, '(') !== FALSE
        || strpos($str, 'undefined') === 0
        || strpos($str, ')') !== FALSE ) {
        return $order;
    }

    return $str ? $str : ($order ? $order : 'id desc');

}

// 兼容性判断
if (!function_exists('ctype_digit')) {
    function ctype_digit($num) {
        if (strpos($num, '.') !== FALSE) {
            return false;
        }
        return is_numeric($num);
    }
}

// 兼容性判断
if (!function_exists('ctype_alpha')) {
    function ctype_alpha($num) {
        if (strpos($num, '.') !== FALSE) {
            return false;
        }
        return is_numeric($num);
    }
}

// 极验验证调用方式
function dr_geetest($product = 'embed', $submit = '') {

    $add = '';
    $rid = rand(0, 99);
    $product == 'popup' && $add = 'gt_captcha_obj.bindOn("#'.$submit.'");';

    return '
    <div class="box" id="div_geetest_lib_'.$rid.'">
        <div id="div_id_embed_'.$rid.'"></div>
        <script type="text/javascript">
            var gtFailbackFrontInitial = function(result) {
                var s = document.createElement("script");
                s.id = "gt_lib";
                s.src = "http://static.geetest.com/static/js/geetest.0.0.0.js";
                s.charset = "UTF-8";
                s.type = "text/javascript";
                document.getElementsByTagName("head")[0].appendChild(s);
                var loaded = false;
                s.onload = s.onreadystatechange = function() {
                if (!loaded && (!this.readyState|| this.readyState === "loaded" || this.readyState === "complete")) {
                    loadGeetest(result);
                    loaded = true;
                }
                };
            }
            var loadGeetest = function(config) {
window.gt_captcha_obj = new window.Geetest({
                    gt : config.gt,
                    challenge : config.challenge,
                    lang: "'.SITE_LANGUAGE.'",
                    product : "'.$product.'",
                    offline : !config.success
                });
                gt_captcha_obj.appendTo("#div_id_embed_'.$rid.'");
                '.$add.'
            }
            s = document.createElement("script");
            s.src = "http://api.geetest.com/get.php?callback=gtcallback";
            $("#div_geetest_lib_'.$rid.'").append(s);
            var gtcallback =( function() {
var status = 0, result, apiFail;
                return function(r) {
                    status += 1;
                    if (r) {
                        result = r;
                        setTimeout(function() {
                            if (!window.Geetest) {
                                apiFail = true;
                                gtFailbackFrontInitial(result)
                            }
                        }, 1000)
                    }
                    else if(apiFail) {
                        return
                    }
                    if (status == 2) {
                        loadGeetest(result);
                    }
                }
            })()

            $.ajax({url : "/index.php?s=member&c=api&m=geetest&rand="+Math.round(Math.random()*100),
                type : "get",
                dataType : "JSON",
                success : function(result) {
                    console.log(result);
                    gtcallback(result)
                }
            })
        </script>
    </div>';
}

// 两数折扣
function dr_discount($price, $nowprice) {

    if ($nowprice <= 0) {
        return 0;
    }

    return round(10 / ($price / $nowprice), 1);
}

// 提取tag
function dr_tag_list($dir, $keyword) {

    if (!$keyword) {
        return array();
    }

    $mod = get_module($dir);
    if (!$mod) {
        return array();
    }

    $data = array();
    $array = explode(',', $keyword);
    foreach ($array as $t) {
        $t = trim($t);
        if ($t) {
            $data[$t] = dr_tag_url($mod, $t);
        }
    }

    return $data;
}

/**
 * 邮箱或手机号码登录
 *
 * @param	string	$dir	目录名称
 * @return	bool|void
 */
function dr_vip_login($db, $value) {

    if (preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $value)) {
        // 邮箱登录
        return $db->select('`uid`, `password`, `salt`, `email`, `username`')
            ->where('email', $value)
            ->limit(1)
            ->get('member')
            ->row_array();
    } else {
        // 手机登录
        $phone = (int)$value;
        if (strlen($phone) == 11) {
            return $db->select('`uid`, `password`, `salt`, `email`, `username`')
                ->where('phone', $phone)
                ->limit(1)
                ->get('member')
                ->row_array();
        }
        return NULL;
    }

}

/**
 * 获取站点表单内容函数
 *
 * @param	intval	$id 	表单id
 * @param	string	$form	表单表名称
 * @param	string	$field	显示字段，默认为全部数组
 * @param	intval	$sid	站点id，默认为当前站点
 * @param	intval	$cache	缓存时间，默认为10000秒
 * @return	array|void
 */
function dr_vip_form($id, $form, $field = 0, $sid = 0, $cache = 0) {

    $ci = &get_instance();
    $sid = $sid ? $sid : SITE_ID;
    $name = 'form-data-'.$sid.'-'.$form.'-'.$id;
    $data = $ci->get_cache_data($name);
    if (!$data) {
        $data = $ci->site[$sid]->where('id', $id)->get($sid.'_form_'.$form)->row_array();
        $data2 = $ci->site[$sid]->where('id', $id)->get($sid.'_form_'.$form.'_data_'.(int)$data['tableid'])->row_array();
        if ($data2) {
            $data = array_merge($data, $data2);
        }
        if (!$data) {
            return false;
        }
        $ci->set_cache_data($name, $data, $cache ? $cache : SYS_CACHE_FORM);
    }

    return $field && isset($data[$field]) ? $data[$field] : $data;

}

/**
 * 获取模块表单内容函数
 *
 * @param	intval	$id 	表单id
 * @param	string	$dir	模块目录
 * @param	string	$form	表单表名称
 * @param	string	$field	显示字段，默认为全部数组
 * @param	intval	$sid	站点id，默认为当前站点
 * @param	intval	$cache	缓存时间，默认为10000秒
 * @return	array|void
 */
function dr_vip_mform($id, $dir, $form, $field = 0, $sid = 0, $cache = 0) {

    $ci = &get_instance();
    $sid = $sid ? $sid : SITE_ID;
    $name = 'mform-data-'.$sid.'-'.$form.'-'.$id.'-'.$dir;
    $data = $ci->get_cache_data($name);
    if (!$data) {
        $data = $ci->site[$sid]->where('id', $id)->get($sid.'_'.$dir.'_form_'.$form)->row_array();
        $data2 = $ci->site[$sid]->where('id', $id)->get($sid.'_'.$dir.'_form_'.$form.'_data_'.(int)$data['tableid'])->row_array();
        if ($data2) {
            $data = array_merge($data, $data2);
        }
        if (!$data) {
            return false;
        }
        $ci->set_cache_data($name, $data, $cache ? $cache : SYS_CACHE_FORM);
    }

    return $field && isset($data[$field]) ? $data[$field] : $data;

}

// 获取栏目数据及自定义字段
function dr_cat_value() {

    $get = func_get_args();
    if (empty($get)) {
        return NULL;
    }

    if (is_numeric($get[0]) && MOD_DIR) {
        // 值是栏目id时，表示当前模块
        $name = 'module-'.SITE_ID.'-'.MOD_DIR;
    } else {
        // 指定模块
        $name = strpos($get[0], '-') ? 'module-'.$get[0] : 'module-'.SITE_ID.'-'.$get[0];
        unset($get[0]);
    }

    $i = 0;
    $param = array();
    foreach ($get as $t) {
        if ($i == 0) {
            $param[] = $name;
            $param[] = 'category';
        }
        $param[] = $t;
        $i = 1;
    }
    $ci = &get_instance();

    return call_user_func_array(array($ci, 'get_cache'), $param);
}

// 获取共享栏目数据及自定义字段
function dr_share_cat_value($id, $field='') {

    $get = func_get_args();
    if (empty($get)) {
        return NULL;
    }

    $i = 0;
    $param = array();
    foreach ($get as $t) {
        if ($i == 0) {
            $param[] = 'module-'.SITE_ID.'-share';
            $param[] = 'category';
        }
        $param[] = $t;
        $i = 1;
    }

    $ci = &get_instance();

    return call_user_func_array(array($ci, 'get_cache'), $param);
}


/**
 *  @desc 根据两点间的经纬度计算距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 */
function dr_distance($lat1, $lng1, $lat2, $lng2, $mark = '米,千米') {

    $earthRadius = 6367000; // approximate radius of earth in meters

    /*
      Convert these degrees to radians
      to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    $value = round($calculatedDistance);

    $dw = '';
    $mark = @explode(',', $mark);
    if ($value < 1000) {
        $dw = isset($mark[0]) ? $mark[0] : '';
    } elseif ($value >= 1000) {
        $dw = isset($mark[1]) ? $mark[1] : '';
    }

    return $value.$dw;
}


/**
 *计算某个经纬度的周围某段距离的正方形的四个点
 *
 *@param lng float 经度
 *@param lat float 纬度
 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 *@return array 正方形的四个点的经纬度坐标
 */
function dr_square_point($lng, $lat, $distance = 0.5){

    $distance = $distance ? $distance : 1;
    $r = 6371; //地球半径，平均半径为6371km
    $dlng =  2 * asin(sin($distance / (2 * $r)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);

    $dlat = $distance/$r;
    $dlat = rad2deg($dlat);

    return array(
        'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
        'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
        'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
        'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
    );
}

// ajax调用动态内容
function dr_ajax_html($id, $tpl, $params = array()) {

    $params = dr_array2string($params);
    if (strlen($params) > 100) {
        return '<font color="red">【'.$id.'】参数太多</font>';
    }

    $url = SITE_URL."index.php?c=api&m=html&name=".$tpl."&params=".$params;
    return "<script type=\"text/javascript\">
	    $.ajax({type: \"GET\", url: \"{$url}&\"+Math.random(), dataType:\"jsonp\",
            success: function (data) {
                $(\"#{$id}\").html(data.html);
            }
        });
	</script>";
}


// 链接菜单默认图标
function dr_get_icon($uri) {

    if (strpos($uri, 'admin/module/index')) {
        return 'icon-cogs';
    } elseif (strpos($uri, 'verify')) {
        return 'icon-retweet';
    } elseif (strpos($uri, 'draft')) {
        return 'icon-edit';
    } elseif (strpos($uri, 'category')) {
        return 'icon-list';
    } elseif (strpos($uri, 'tag')) {
        return 'icon-tags';
    } elseif (strpos($uri, 'page')) {
        return 'icon-adn';
    } elseif (strpos($uri, 'form')) {
        return 'icon-table';
    } elseif (strpos($uri, 'html')) {
        return 'icon-html5';
    } elseif (strpos($uri, 'field')) {
        return 'icon-plus-sign-alt';
    } elseif (strpos($uri, 'config')) {
        return 'icon-cogs';
    } elseif (strpos($uri, 'tpl')) {
        return 'icon-folder-close';
    } elseif (strpos($uri, 'theme')) {
        return 'icon-picture';
    }

    return 'icon-th-large';
}

// 模块默认图标
function dr_get_icon_m($dir) {

    if ($dir == 'news') {
        return 'icon-tasks';
    } elseif ($dir == 'book') {
        return 'icon-book';
    } elseif ($dir == 'bbs' || $dir == 'weixin') {
        return 'icon-comments';
    } elseif ($dir == 'buy' || $dir == 'shop' || $dir == 'sell') {
        return 'icon-shopping-cart';
    } elseif ($dir == 'down') {
        return 'icon-circle-arrow-down';
    } elseif ($dir == 'fang') {
        return 'icon-reorder';
    } elseif ($dir == 'music') {
        return 'icon-music';
    } elseif ($dir == 'photo') {
        return 'icon-picture';
    } elseif ($dir == 'video') {
        return 'icon-facetime-video';
    }

    return 'icon-table';
}

//
function dr_get_icon_left($name) {

    if (strpos($name, '新闻') !== FALSE ) {
        return 'icon-tasks';
    } elseif (strpos($name, '图书') !== FALSE) {
        return 'icon-book';
    } elseif (strpos($name, '微') !== FALSE) {
        return 'icon-comments';
    } elseif (strpos($name, '商') !== FALSE) {
        return 'icon-shopping-cart';
    } elseif (strpos($name, '下载') !== FALSE) {
        return 'icon-circle-arrow-down';
    } elseif (strpos($name, '房') !== FALSE) {
        return 'icon-reorder';
    } elseif (strpos($name, '音') !== FALSE) {
        return 'icon-music';
    } elseif (strpos($name, '图') !== FALSE) {
        return 'icon-picture';
    } elseif (strpos($name, '视频') !== FALSE) {
        return 'icon-facetime-video';
    } elseif (strpos($name, '评论') !== FALSE) {
        return 'fa fa-comments-o';
    } elseif (strpos($name, '功能') !== FALSE) {
        return 'fa fa-cog';
    } elseif (strpos($name, '风格') !== FALSE) {
        return 'fa fa-folder';
    } elseif (strpos($name, ' ') !== FALSE) {
        return 'icon';
    }

    return 'icon-table';

}

// 替换菜单字符
function dr_replace_m_uri($link, $id, $dir) {

    if (strpos($link['uri'], '{id}') === FALSE
        && strpos($link['uri'], '{dir}') === FALSE) {
        return trim($dir.'/'.$link['uri'], '/');
    } else {
        return str_replace(array('{id}', '{dir}'), array($id, $dir), $link['uri']);
    }
}

// 格式化生成文件
function dr_format_html_file($file) {

    if (strpos($file, 'http://') !== false) {
        return;
    }

    $dir = dirname($file);
    $file = basename($file);
    $root = WEBPATH;
    // 多网站时生成到指定目录
    if (SITE_ID > 1) {
        $root = WEBPATH.'html/'.SITE_ID.'/';
        if (!is_dir($root)) {
            dr_mkdirs($root, TRUE);
        }
    }
    if ($dir != '.' && !is_dir($root.$dir)) {
        dr_mkdirs($root.$dir, TRUE);
    }

    $hfile = str_replace('./', '', $root.$dir.'/'.$file);
    // 判断是否为目录形式
    if (strpos($file, '.html') === FALSE
        && strpos($file, '.htm') === FALSE
        && strpos($file, '.shtml') === FALSE) {
        dr_mkdirs($hfile, TRUE);
    }

    // 如果是目录就生成一个index.html
    if (is_dir($hfile)) {
        $dir.= '/'.$file;
        $file = 'index.html';
        $hfile = str_replace('./', '', $root.$dir.'/'.$file);
    }

    return $hfile;
}

// 删除静态文件
function dr_delete_html_file($url) {

    $file = dr_format_html_file($url);
    if (is_file($file)) {
        unlink($file);
    }
}

// 获取当前模板目录
function dr_tpl_path($file) {
    
    $path = IS_MEMBER ? TPLPATH.(IS_MOBILE ? 'mobile' : 'pc').'/member/'.MEMBER_TEMPLATE.'/' : TPLPATH.(IS_MOBILE ? 'mobile' : 'pc').'/web/'.SITE_TEMPLATE.'/';
    APP_DIR && APP_DIR != 'member' ? $path.= APP_DIR.'/' : $path.= 'common/';
    
    if (is_file($path.$file)) {
        return $path.$file;
    }
    
    return false;
}

// 判断满足定向跳转的条件 1单页,2模块首页,3栏目页,4内容,5扩展
function dr_is_redirect($type, $url) {

    if (!defined('SITE_URL_301') || !SITE_URL_301) {
        return;
    }

    // 不调整的条件
    if (!$url || strpos($url, 'http') === FALSE) {
        return; // 为空时排除
    } elseif (IS_MOBILE || SITE_MOBILE === TRUE) {
        return; // 排除移动端
    } elseif ($type > 1 && defined('CT_HTML_FILE')) {
        return; // 排除生成
    } elseif (intval($_GET['page']) > 1) {
        return; // 排除分页
    } elseif (SITE_FID && in_array($type, array(2, 3))) {
        return; // 排除分站
    }

    // 跳转
    $url != dr_now_url() && redirect($url, 'location', '301');

}

// 字段表单控件输出
function dr_field_form($id, $value = '', $html = '{value}') {

    $id = (int)$id;
    if (!$id) {
        return NULL;
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('field-value-'.$id);
    if (!$data) {
        $data = $ci->db->where('id', $id)->get('field')->row_array();
        if (!$data) {
            return NULL;
        }
        $data['setting'] = dr_string2array($data['setting']);
        $ci->set_cache_data('field-value-'.$id, $data, 10000);
    }

    $ci->load->library('Dfield', array(APP_DIR));
    $field = $ci->dfield->get($data['fieldtype']);
    if (!is_object($field)) {
        return NULL;
    }

    if ($html) {
        $field->set_input_format($html);
    }

    return preg_replace('/(<div class="on.+<\/div>)/U', '', $field->input($data['name'], $data['fieldname'], $data['setting'], $value, 0));
}

// http模式
function dr_http_prefix($url) {
    return (defined('SYS_HTTPS') && SYS_HTTPS ? 'https://' : 'http://').$url;
}

// 评论汇总信息
function dr_comment($name, $id) {
    
    if (!$id || !$name) {
        return NULL;
    }

    $ci = &get_instance();
    $data = $ci->get_cache_data('comment-'.$name.'-'.$id);
    if (!$data) {
        $data = $ci->db->where('cid', $id)->get($name.'_comment_index')->row_array();
        if (!$data) {
            return NULL;
        }
        $ci->set_cache_data('comment-'.$name.'-'.$id, $data, 10000);
    }
    
    return $data;
}

// 处理带Emoji的数据，type=0表示写入数据库前的emoji转为HTML，为1时表示HTML转为emoji码
function dr_weixin_emoji($msg, $type = 1){
    if ($type == 0) {
        $msg = json_encode($msg);
    } else {
        $txt = json_decode($msg);
        if ($txt !== null) {
            $msg = $txt;
        }
    }
    return $msg;
}

/**
 * 过滤emoji表情
 * @param type $str
 * @return type
 */
function dr_clear_emoji($str){
    $tmpStr = json_encode($str); //暴露出unicode
    $tmpStr = preg_replace("#(\\\ud[0-9a-f]{3})#ie","", $tmpStr);
    $new_str = json_decode($tmpStr);
    return $new_str;
}

// 判断是否支持回复
function dr_comment_is_reply($reply, $member, $cuid) {

    if ($reply == 1) {
        // 都允许
        return 1;
    } elseif ($reply == 2) {
        // 仅自己
        if ($member['uid'] == $cuid) {
            // 自己的评论
            return 1;
        } elseif ($member['adminid']) {
            return 1; // 管理员可以回复
        } else {
            return 0;
        }
    } else {
        // 禁止所有
        return 0;
    }
}

// 将同步代码转为数组
function dr_member_sync_url($string) {

    if (preg_match_all('/src="(.+)"/iU', $string, $match)) {
        return $match[1];
    }

    return array();
}

