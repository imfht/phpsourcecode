<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */


// 地址前缀部分
function dr_url_prefix($type, $mod, $cat = array(), $fid = 0, $site = SITE_ID) {

    $ci	= &get_instance();
    $dir = isset($mod['dirname']) ? $mod['dirname'] : '';
    $domain = isset($mod['domain']) ? $mod['domain'] : '';

    if ($cat) {
        $dir = isset($cat['mid']) ? $cat['mid'] : $dir;
        $domain = isset($cat['domain']) ? $cat['domain'] : $domain;
    }

    // 默认主网站的地址
    $site_url = $ci->site_info[$site]['SITE_PC'] ? $ci->site_info[$site]['SITE_PC'] : $ci->site_info[$site]['SITE_URL'];
    if ($type == 'php') {
        return $domain ? $domain.'index.php?' : $site_url.'index.php?'.(!$mod ? '' : 's='.$dir.'&');
    } elseif ($type == 'cat_show_ext_php') {
        return $domain ? $domain.'index.php?' : $site_url.'index.php?'.($mod['share'] ? '' : 's='.$dir.'&');
    } elseif ($type == 'rewrite') {
        return $domain ? $domain : $site_url;
    }
}

/**
 * 移动版的模块首页URL地址
 *
 * @param	string	$dir
 * @param	intval	$page
 * @return	string
 */
function dr_mobile_module_url($dir, $page = NULL) {
    return '/index.php?s='.$dir.($page ? '&page='.$page : '');
}

/**
 * 移动版的模块内容URL地址
 *
 * @param	string	$dir
 * @param	intval	$id
 * @param	intval	$page
 * @return	string
 */
function dr_mobile_show_url($dir, $id, $page = NULL) {
    return '/index.php?'.($dir == 'share' || !$dir ? '' : 's='.$dir.'&').'c=show&id='.$id.($page ? '&page='.$page : '');
}

/**
 * 移动版的模块内容扩展URL地址
 *
 * @param	string	$dir
 * @param	intval	$id
 * @param	intval	$page
 * @return	string
 */
function dr_mobile_extend_url($dir, $id, $page = NULL) {
    return '/index.php?'.($dir == 'share' || !$dir ? '' : 's='.$dir.'&').'c=extend&id='.$id.($page ? '&page='.$page : '');
}

/**
 * 移动版的模块栏目URL地址
 *
 * @param	string	$dir
 * @param	intval	$id
 * @param	intval	$page
 * @return	string
 */
function dr_mobile_category_url($dir, $id, $page = NULL) {
    return '/index.php?'.($dir == 'share' || !$dir ? '' : 's='.$dir.'&').'c=category&id='.$id.($page ? '&page='.$page : '');
}

/**
 * 移动版的单页URL地址
 *
 * @param	string	$dir
 * @param	intval	$id
 * @param	intval	$page
 * @return	string
 */
function dr_mobile_page_url($dir, $id, $page = NULL) {
    return '/index.php?'.($dir ? 's='.$dir.'&' : '').'c=page&id='.$id.($page ? '&page='.$page : '');
}

/**
 * 伪静态代码处理
 *
 * @param	array	$params	参数数组
 * @return	string
 */
function dr_rewrite_encode($params, $join = '-') {
	
	if (!$params) {
        return '';
    }
	
	$url = '';
	foreach ($params as $i => $t) {
		$url.= $join.$i.$join.$t;
	}
	
	return trim($url, $join);
}
 
/**
 * 伪静态代码转换为数组
 *
 * @param	string	$params	参数字符串
 * @return	array
 */
function dr_rewrite_decode($params, $join = '-') {
	
	if (!$params) {
        return NULL;
    }
	
	$i = 0;
	$array = explode($join, $params);
	
	$return = array();
	foreach ($array as $k => $t) {
        $i%2 == 0 && $return[str_replace('$', '_', $t)] = isset($array[$k+1]) ? $array[$k+1] : '';
		$i ++;
	}
	
	return $return;
}
 
/**
 * 空间搜索url组合
 *
 * @param	array	$params		搜索参数数组
 * @param	string	$name		当前参数名称
 * @param	string	$value		当前参数值
 * @param	string	$urlrule	搜索url规则
 * @return	string
 */
function dr_space_search_url($params = NULL, $name = NULL, $value = NULL, $urlrule = NULL) {
	
	$params = $params ? $params : array();
	
	if ($name) {
		if (strlen($value)) {
			$params[$name] = $value;
		} else {
			unset($params[$name]);
		}
	}
	if ($params) {
		foreach ($params as $i => $t) {
			if (strlen($t) == 0) {
                unset($params[$i]);
            }
		}
	}

    if (IS_MOBILE) {
        // 移动端
        return SITE_URL.'index.php?s=space&c=search&'.@http_build_query($params);
    } else {
        // PC端
        $ci	= &get_instance();
        $space =$ci->get_cache('member', 'setting', 'space');
        $space['domain'] = $space['domain'] ? dr_http_prefix($space['domain'].'/') : '';
        $space['dirname'] = 'space';
        if ($space['rule']['space_search']) {
            $url = !$params ? trim($space['rule']['space_search'], '/') : str_replace('{param}', dr_rewrite_encode($params), trim($space['rule']['space_search_page'], '/'));
            return dr_url_prefix('rewrite', $space, array(), SITE_FID).$url;
        } else {
            return dr_url_prefix('php', $space, array(), SITE_FID).trim('c=search&'.@http_build_query($params), '&');
        }
    }
}

/**
 * 全局搜索url组合
 *
 * @param	array	$params		搜索参数数组
 * @param	string	$name		当前参数名称
 * @param	string	$value		当前参数值
 * @param	string	$urlrule	搜索url规则
 * @param	string	$moddir		强制定位到模块
 * @return	string
 */
function dr_so_url($params = NULL, $name = NULL, $value = NULL, $urlrule = NULL) {

    if ($name) {
        if (strlen($value)) {
            $params[$name] = $value;
        } else {
            unset($params[$name]);
        }
    }
    if (is_array($params)) {
        foreach ($params as $i => $t) {
            if (strlen($t) == 0) unset($params[$i]);
        }
    }

    if (IS_MOBILE) {
        // 移动端
        return '/index.php?c=so&'.@http_build_query($params);
    } else {
        // PC端
        $ci	= &get_instance();
        $rule = $ci->get_cache('urlrule', (int)SITE_REWRITE, 'value');
        if ($rule['so_search']) {
            $url = !$params ? trim($rule['so_search'], '/') : str_replace('{param}', dr_rewrite_encode($params), trim($rule['so_search_page'], '/'));
            return dr_url_prefix('rewrite', array(), array(), SITE_FID).$url;
        } else {
            return dr_url_prefix('php', array(), array(), SITE_FID).trim('c=so&'.@http_build_query($params), '&');
        }
    }

}

// 共享模块搜索
function dr_share_search_url($params = NULL, $name = NULL, $value = NULL, $urlrule = NULL) {

    if ($name) {
        if (strlen($value)) {
            $params[$name] = $value;
        } else {
            unset($params[$name]);
        }
    }
    if (is_array($params)) {
        foreach ($params as $i => $t) {
            if (strlen($t) == 0) unset($params[$i]);
        }
    }

    if (IS_MOBILE) {
        // 移动端
        return '/index.php?c=search&'.@http_build_query($params);
    } else {
        // PC端
        $ci	= &get_instance();
        $rule = $ci->get_cache('urlrule', (int)SITE_REWRITE, 'value');
        if ($rule['share_search']) {
            $url = !$params ? trim($rule['share_search'], '/') : str_replace('{param}', dr_rewrite_encode($params), trim($rule['share_search_page'], '/'));
            return dr_url_prefix('rewrite', array(), array(), SITE_FID).$url;
        } else {
            return dr_url_prefix('php', array(), array(), SITE_FID).trim('c=search&'.@http_build_query($params), '&');
        }
    }
}
 
/**
 * 搜索url组合
 *
 * @param	array	$params		搜索参数数组
 * @param	string	$name		当前参数名称
 * @param	string	$value		当前参数值
 * @param	string	$urlrule	搜索url规则
 * @param	string	$moddir		强制定位到模块
 * @return	string
 */
function dr_search_url($params = NULL, $name = NULL, $value = NULL, $urlrule = NULL, $moddir = NULL) {
	
	defined('MOD_DIR') && MOD_DIR && $dir = MOD_DIR;
	if (!is_array($params) && $params && is_dir(WEBPATH.$params)) {
		$dir = (string)$params;
		$params = array();
	} else {
		$params = is_array($params) ? $params : array();
	}
	$dir = $moddir ? $moddir : $dir;

    // 当是分站且没有绑定域名自动加上参数
    SITE_FID && !isset($params['fid']) && defined('SITE_BRANCH_DOMAIN') && !SITE_BRANCH_DOMAIN && $params['fid'] = SITE_FID;


	if ($name) {
		if (strlen($value)) {
			$params[$name] = $value;
		} else {
			unset($params[$name]);
		}
	}
	if (is_array($params)) {
		foreach ($params as $i => $t) {
			if (strlen($t) == 0) {
                unset($params[$i]);
            }
		}
	}

    if (IS_MOBILE) {
        // 移动端
        return '/index.php?s='.$dir.'&c=search&'.@http_build_query($params);
    } else {
        // PC端
        $ci	= &get_instance();
        $mod = $ci->get_cache('module-'.SITE_ID.'-'.$dir);
        $rule = $ci->get_cache('urlrule', (int)$mod['site'][SITE_ID]['urlrule'], 'value');
        if ($rule && $rule['search']) {
            $data['modname'] = $mod['dirname'];
            $data['param'] = dr_rewrite_encode($params);
            $url = ltrim($params ? $rule['search_page'] : $rule['search'], '/');
            // 兼容php5.5
            if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                $rep = new php5replace($data);
                $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
                $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
                unset($rep);
            } else {
                $url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
                $url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
            }
            /*
            // 表示分站
            if (SITE_FID) {
                if (!$mod['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名时，调用分站主站的URL
                    $site_url = dr_fenzhan_url(SITE_FID); // 分站url
                    if (strpos($site_url, 'index.php') === FALSE) {
                        // 在分站主站绑定域名的情况下，省略fid
                        if ($params) {
                            return $site_url.'/'.$mod['dirname'].'/search-'.dr_rewrite_encode($params).'.html';
                        } else {
                            return $site_url.'/'.$mod['dirname'].'/search.html';
                        }
                    }
                }
            }*/
            return dr_url_prefix('rewrite', $mod, array(), SITE_FID).$url;
        } else {
            // 表示分站
            /*
            if (SITE_FID) {
                if (!$mod['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名时，调用分站主站的URL
                    $site_url = dr_fenzhan_url(SITE_FID); // 分站url
                    if (strpos($site_url, 'index.php') === FALSE) {
                        // 在分站主站绑定域名的情况下，省略fid
                        return $site_url.'/'.$mod['dirname'].'/index.php?c=search&'.@http_build_query($params);
                    }
                }
            }*/
            return dr_url_prefix('php', $mod, array(), SITE_FID).trim('c=search&'.@http_build_query($params), '&');
        }
    }
	
}

/**
 * tag的url
 *
 * @param	array	$module
 * @param	string	关键字
 * @return	string	地址
 */
function dr_tag_url($module, $name, $page = 0) {

	$name = trim($name);
	if (!$name) {
        return '?name参数为空';
    }

    $page && $data['page'] = $page = is_numeric($page)  ? ($page > 1 ? $page : 0) : $page;

    $module = is_array($module) ? $module : get_module($module);
    if (!$module) {
        return '?module参数为空';
    }

    // 查询tag库
	$ci	= &get_instance();
    $name = dr_word2pinyin($name);
	
    if (IS_MOBILE) {
        // 移动端
        return SITE_URL.'index.php?s='.$module['dirname'].'&c=tag&name='.$name.($page ? '&page='.$page : '');
    } else {
        // PC端
        $rule = $ci->get_cache('urlrule', (int)$module['site'][SITE_ID]['urlrule'], 'value');
        if ($rule && $rule['tag']) {
            $data['tag'] = $name;
            $data['modname'] = $module['dirname'];
            $url = ltrim($page ? $rule['tag_page'] : $rule['tag'], '/');
            // 兼容php5.5
            if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                $rep = new php5replace($data);
                $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
                $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
                unset($rep);
            } else {
                $url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
                $url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
            }
            /*
            // 表示分站
            if (SITE_FID) {
                $site_url = dr_fenzhan_url(SITE_FID); // 分站url
                // 此地址相对于网站根目录时
                if (strpos($url, '/') === 0) {
                    $url = ltrim($url, '/');
                    if (!$module['site'][SITE_ID]['domain']) {
                        // 此模块未绑定域名才有效
                        if (strpos($site_url, 'index.php') !== FALSE) {
                            // 分站没绑定域名，站点地址为当前站点域名
                            return $ci->site_info[SITE_ID]['SITE_PC'].$url;
                        } else {
                            // 分站绑了域名就用分站的域名
                            return $site_url.'/'.$url;
                        }
                    }
                }
                // 普通模式
                if (!$module['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名时，调用分站主站的URL
                    if (strpos($site_url, 'index.php') !== FALSE) {
                        // 分站没绑定域名，站点地址为当前站点域名
                        return $ci->site_info[SITE_ID]['SITE_PC'].$module['dirname'].'/'.$url;
                    } else {
                        return $site_url.'/'.$module['dirname'].'/'.$url;
                    }
                } else {
                    // 此模块绑定了域名时,原样输出
                    return $module['url'].$url;
                }
            }
            // 此地址相对于网站根目录时
            if (strpos($rule, '/') === 0) {
                if (!$module['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名才有效
                    return SITE_URL.$url;
                }
            }
            */
            return dr_url_prefix('rewrite', $module, array(), SITE_FID).$url;
        } else {
            // 表示分站
            /*
            if (SITE_FID) {
                if (!$module['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名时，调用分站主站的URL
                    $site_url = dr_fenzhan_url(SITE_FID); // 分站url
                    if (strpos($site_url, 'index.php') === FALSE) {
                        // 在分站主站绑定域名的情况下，省略fid
                        return $site_url.'/'.$module['dirname'].'/index.php?c=tag&name='.$name;
                    }
                }
                return $module['url'].'index.php?c=tag&fid='.SITE_FID.'&name='.$name;
            }
            */
            return dr_url_prefix('php', $module, array(), SITE_FID).'c=tag&name='.$name.($page ? '&page='.$page : '');
        }
    }

}

// 会员空间域名
function dr_space_domain($uid) {

    $ci	= &get_instance();
    $domain = $ci->get_cache('member', 'setting', 'space', 'spacedomain');
    if (!$domain) {
        return NULL;
    }

    $data = $ci->get_cache_data('member-space-domain-'.$uid);
    if (!$data) {
        $space = $ci->db->where('uid', $uid)->get('space_domain')->row_array();
        if (!$space) {
            return NULL;
        }
        $data = $space['domain'];
        $ci->set_cache_data('member-space-domain-'.$uid, $data, SYS_CACHE_MEMBER);
    }

    return dr_http_prefix($data.'.'.$domain.'/');

}

/**
 * 会员空间url
 *
 * @param	intval	$uid
 * @return	string	地址
 */
function dr_space_url($uid = 0) {


    if (!$uid) {
        return SPACE_URL;
    }

    $ci	= &get_instance();
    $domain = dr_space_domain($uid);
    if ($domain) {
        return $domain;
    }

    $space = $ci->get_cache('member', 'setting', 'space');
    $space['domain'] = $space['domain'] ? dr_http_prefix($space['domain'].'/') : '';
    $space['dirname'] = 'space';
	if ($space['rule']['uhome']) {
        return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace('{uid}', $uid, $space['rule']['uhome']);
	} else {
		return dr_url_prefix('php', $space, array(), SITE_FID).'uid='.$uid;
	}
}

// 空间内容列表
function dr_space_list_url($uid, $id, $page = FALSE) {

	$ci	= &get_instance();
    $space = $ci->get_cache('member', 'setting', 'space');
    $domain = dr_space_domain($uid);
    $space['domain'] = $domain ? $domain : ($space['domain'] ? dr_http_prefix($space['domain'].'/') : '');
    $space['dirname'] = 'space';
    if ($domain) {
        // 绑定域名时的情况
        $rule = $page ? $space['rule']['ulist_domain_page'] : $space['rule']['ulist_domain'];
    } else {
        // 未绑定域名
        $rule = $page ? $space['rule']['ulist_page'] : $space['rule']['ulist'];
    }
    if ($rule) {
        return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace(array('{uid}', '{id}', '{page}'), array($uid, $id, '[page]'), $rule);
    } else {
        return dr_url_prefix('php', $space, array(), SITE_FID).($domain ? '' : 'uid='.$uid.'&').'action=category&id='.$id.($page ? '&page=[page]' : '');
    }
}

// 空间内容详细
function dr_space_show_url($uid, $mid, $id, $page = FALSE) {

    $ci	= &get_instance();
    $space = $ci->get_cache('member', 'setting', 'space');
    $domain = dr_space_domain($uid);
    $space['domain'] = $domain ? $domain : ($space['domain'] ? dr_http_prefix($space['domain'].'/') : '');
    $space['dirname'] = 'space';
    if ($domain) {
        // 绑定域名时的情况
        $rule = $page ? $space['rule']['ushow_domain_page'] : $space['rule']['ushow_domain'];
    } else {
        // 未绑定域名
        $rule = $page ? $space['rule']['ushow_page'] : $space['rule']['ushow'];
    }
    if ($rule) {
        return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace(array('{uid}', '{id}', '{page}', '{mid}'), array($uid, $id, '[page]', $mid), $rule);
    } else {
        return dr_url_prefix('php', $space, array(), SITE_FID).($domain ? '' : 'uid='.$uid.'&').'action=show&mid='.$mid.'&id='.$id.($page ? '&page=[page]' : '');
    }
}

// 空间SNS页面
function dr_space_sns_url($uid, $name, $page = 0, $page2 = 0) {

    $ci	= &get_instance();
    $space = $ci->get_cache('member', 'setting', 'space');
    $domain = dr_space_domain($uid);
    $space['domain'] = $domain ? $domain : ($space['domain'] ? dr_http_prefix($space['domain'].'/') : '');
    $space['dirname'] = 'space';

    if ($name == 'show') {
        // 微博动态内容页
        if ($domain) {
            // 绑定域名时的情况
            $rule = $space['rule']['sns_show_domain'];
        } else {
            // 未绑定域名
            $rule = $space['rule']['sns_show'];
        }
        if ($rule) {
            return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace(array('{uid}', '{id}'), array($uid, $page), $rule);
        } else {
            return dr_url_prefix('php', $space, array(), SITE_FID).($domain ? '' : 'uid='.$uid.'&').'action=sns&name='.$name.'&id='.$page;
        }
    } elseif ($name == 'topic') {
        $id = $page;
        $page = $page2;
        // 微博话题页
        if ($domain) {
            // 绑定域名时的情况
            $rule = $page ? $space['rule']['sns_topic_domain_page'] : $space['rule']['sns_topic_domain'];
        } else {
            // 未绑定域名
            $rule = $page ? $space['rule']['sns_topic_page'] : $space['rule']['sns_topic'];
        }
        if ($rule) {
            return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace(array('{uid}', '{id}', '{page}'), array($uid, $id, '[page]'), $rule);
        } else {
            return dr_url_prefix('php', $space, array(), SITE_FID).($domain ? '' : 'uid='.$uid.'&').'action=sns&name='.$name.'&id='.$id.($page ? '&page=[page]' : '');
        }
    } else {
        if ($domain) {
            // 绑定域名时的情况
            $rule = $page ? $space['rule']['sns_domain_page'] : $space['rule']['sns_domain'];
        } else {
            // 未绑定域名
            $rule = $page ? $space['rule']['sns_page'] : $space['rule']['sns'];
        }
        if ($rule) {
            return dr_url_prefix('rewrite', $space, array(), SITE_FID).str_replace(array('{uid}', '{name}', '{page}'), array($uid, $name, '[page]'), $rule);
        } else {
            return dr_url_prefix('php', $space, array(), SITE_FID).($domain ? '' : 'uid='.$uid.'&').'action=sns&name='.($page ? '&page=[page]' : '');
        }
    }
}

/**
 * 会员动态内容URL地址
 *
 * @param	intval	$id
 * @return	string
 */
function dr_sns_feed_url($uid, $id) {
    return dr_space_sns_url($uid, 'show', $id);
}

/**
 * 模块内容分页链接
 *
 * @param	string	$urlrule
 * @param	intval	$page
 * @return	string	地址
 */
function dr_content_page_url($urlrule, $page) {
	return str_replace('{page}', $page, $urlrule);
}

/**
 * 联动菜单包屑导航
 *
 * @param	string	$code	联动菜单代码
 * @param	intval	$id		id
 * @param	string	$symbol	间隔符号
 * @param	string	$url	url地址格式，必须存在{linkage}，否则返回不带url的字符串
 * @return	string
 */
function dr_linkagepos($code, $id, $symbol = ' > ', $url = NULL) {

	if (!$code || !$id) {
        return NULL;
    }
	
	$ci	= &get_instance();
	$url = $url ? urldecode($url) : NULL;
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

    $name = array();
	$pids = @explode(',', $data['pids']);

    if (SITE_FID) {
        $r = 0;
        $fid = $link[SITE_FID]['ii'];
        foreach ($pids as $pid) {
            if (!$pid) {
                continue;
            }
            $pid == $fid && $r = 1;
            if ($r) {
                $row = $link[$cids[$pid]];
                $name[] = $url ? "<a href=\"".str_replace('{linkage}', $row['cname'], $url)."\">{$row['name']}</a>" : $row['name'];
            }
        }
        $name[] = $url ? "<a href=\"".str_replace('{linkage}', $id, $url)."\">{$data['name']}</a>" : $data['name'];
    } else {
        foreach ($pids as $pid) {
            $pid && $name[] = $url ? "<a href=\"".str_replace('{linkage}', $cids[$pid], $url)."\">{$link[$cids[$pid]]['name']}</a>" : $link[$cids[$pid]]['name'];
        }
        $name[] = $url ? "<a href=\"".str_replace('{linkage}', $id, $url)."\">{$data['name']}</a>" : $data['name'];
    }

	
	return implode($symbol, $name);
}

/**
 * 模块栏目面包屑导航
 *
 * @param	intval	$catid	栏目id
 * @param	string	$symbol	面包屑间隔符号
 * @param	string	$url	是否显示URL
 * @param	string	$html	格式替换
 * @return	string
 */
function dr_catpos($catid, $symbol = ' > ', $url = TRUE, $html= '') {

	if (!$catid) {
        return NULL;
    }
	
	$ci	= &get_instance();
	$cat = $ci->get_cache('module-'.SITE_ID.'-'.MOD_DIR, 'category');
	if (!isset($cat[$catid])) {
        return NULL;
    }
	
	$name = array();
	$array = explode(',', $cat[$catid]['pids']);
	foreach ($array as $id) {
		if ($id && $cat[$id]) {
            $murl = !IS_MOBILE ? $cat[$id]['url'] : dr_mobile_category_url(MOD_DIR, $id);
            /*
        if (SITE_FID) {
            if (IS_PC) {
                $murl = $cat[$id]['fenzhan'][SITE_FID];
            } else {
                $murl = SITE_URL.MOD_DIR.'/index.php?c=category&fid='.SITE_FID.'&id='.$id;
            }
            }
            */
			$name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array($murl, $cat[$id]['name']), $html): "<a href=\"{$murl}\">{$cat[$id]['name']}</a>") : $cat[$id]['name'];
		}
	}

    $murl = !IS_MOBILE ? $cat[$catid]['url'] : dr_mobile_category_url(MOD_DIR, $catid);
    /*
if (SITE_FID) {
    if (!IS_MOBILE) {
        $murl = $cat[$id]['fenzhan'][SITE_FID];
    } else {
        $murl = SITE_URL.MOD_DIR.'/index.php?c=category&fid='.SITE_FID.'&id='.$id;
    }
    }*/
	$name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array($murl, $cat[$catid]['name']), $html): "<a href=\"{$murl}\">{$cat[$catid]['name']}</a>") : $cat[$catid]['name'];
	
	return implode($symbol, $name);
}
 
/**
 * 模块栏目层次关系
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	string	$symbol
 * @return	string
 */
function dr_get_cat_pname($mod, $cat, $symbol = '_') {

	if (!$cat['pids']) {
        return $cat['name'];
    }
	
	$name = array();
	$array = explode(',', $cat['pids']);
	
	foreach ($array as $id) {
        $id && $mod['category'][$id] && $name[] = $mod['category'][$id]['name'];
	}

	$name[] = $cat['name'];
	krsort($name);
	
	return implode($symbol, $name);
}

/**
 * 单页面包屑导航
 *
 * @param	intval	$id
 * @param	string	$symbol
 * @param	string	$html
 * @return	string
 */
function dr_page_catpos($id, $symbol = ' > ', $html = '') {

	if (!$id) {
        return NULL;
    }
	
	$ci	= &get_instance();
	$page = $ci->get_cache('page-'.SITE_ID, 'data');
	$page = defined('MOD_DIR') && MOD_DIR ? $page[MOD_DIR] : $page['index'];
	if (!isset($page[$id])) {
        return NULL;
    }
	
	$name = array();
	$array = explode(',', $page[$id]['pids']);
	foreach ($array as $i) {
		if ($i && $page[$i]) {
            $murl = SITE_MOBILE === TRUE ? SITE_URL.(defined('MOD_DIR') && MOD_DIR ? MOD_DIR.'/' : '').'index.php?c=page&id='.$i : $page[$i]['url'];
			$name[] = $html ? str_replace(array('{url}', '{name}'), array($murl, $page[$i]['name']), $html) : "<a href=\"{$murl}\">{$page[$i]['name']}</a>";
		}
	}

    $murl = SITE_MOBILE === TRUE ? SITE_URL.(defined('MOD_DIR') && MOD_DIR ? MOD_DIR.'/' : '').'index.php?c=page&id='.$id : $page[$id]['url'];
    $name[] = $html ? str_replace(array('{url}', '{name}'), array($murl, $page[$id]['name']), $html) : "<a href=\"{$murl}\">{$page[$id]['name']}</a>";
	
	return implode($symbol, $name);
}
 
/**
 * 单页层次关系
 *
 * @param	intval	$id
 * @param	string	$symbol
 * @return	string
 */
function dr_get_page_pname($id, $symbol = '_') {

	$ci	= &get_instance();
	$page = $ci->get_cache('page-'.SITE_ID, 'data');
	$page = defined('MOD_DIR') && MOD_DIR ? $page[MOD_DIR] : $page['index'];
	if (!$page[$id]['pids']) {
        return $page[$id]['name'];
    }
	
	$name = array();
	$array = explode(',', $page[$id]['pids']);
	
	foreach ($array as $i) {
        $i && $page[$i] && $name[] = $page[$i]['name'];
	}
	
	$name[] = $page[$id]['name'];
	krsort($name);
	
	return implode($symbol, $name);
}

/**
 * 会员空间模型栏目面包屑导航
 *
 * @param	intval	$uid	会员id
 * @param	intval	$catid	栏目id
 * @param	string	$symbol	面包屑间隔符号
 * @param	string	$url	是否显示URL
 * @return	string
 */
function dr_space_catpos($uid, $catid, $symbol = ' > ', $url = TRUE, $html= '') {

	if (!$uid || !$catid) {
        return NULL;
    }
	
	$ci	= &get_instance();
	$ci->load->model('space_category_model');
	$cat = $ci->space_category_model->get_data(0, $uid, 1);
	if (!isset($cat[$catid])) {
        return NULL;
    }
	
	$name = array();
	$array = explode(',', $cat[$catid]['pids']);
	
	foreach ($array as $id) {
        $id && $cat[$id] && $name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array(dr_space_list_url($uid, $id), $cat[$id]['name']), $html) : "<a href=\"".dr_space_list_url($uid, $id)."\">{$cat[$id]['name']}</a>") : $cat[$id]['name'];
	}
	
	$name[] = $url ? ($html ? str_replace(array('{url}', '{name}'), array(dr_space_list_url($uid, $catid), $cat[$id]['name']), $html) : "<a href=\"".dr_space_list_url($uid, $catid)."\">{$cat[$catid]['name']}</a>") : $cat[$catid]['name'];
	
	return implode($symbol, $name);
}

/**
 * 模块内容SEO信息
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	intval	$page
 * @return	array
 */
function dr_show_seo($mod, $data, $page = 1) {

	$seo = array();
	
	$cat = $mod['category'][$data['catid']];
    $data['page'] = $page;
	$data['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$data['name'] = $data['catname'] = dr_get_cat_pname($mod, $cat, $data['join']);
	$data['modulename'] = $data['modname'] = $mod['name'];
	
	$meta_title = $cat['setting']['seo']['show_title'] ? $cat['setting']['seo']['show_title'] : '['.fc_lang('第%s页', '{page}').'{join}]{title}{join}{name}{join}{modulename}{join}{SITE_NAME}';

	$meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($data);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        unset($rep);
	} else {
		extract($data);
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$\\1", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
	}
	
	if (is_array($data['keywords'])) {
		foreach ($data['keywords'] as $key => $t) {
			$seo['meta_keywords'].= $key.',';
		}
		$seo['meta_keywords'] = trim($seo['meta_keywords'], ',');
	} else {
		$seo['meta_keywords'] = $data['keywords'];
	}

    $seo['meta_description'] = htmlspecialchars(dr_clearhtml($data['description']));

	return $seo;
}

/**
 * 模块栏目SEO信息
 *
 * @param	array	$mod
 * @param	array	$cat
 * @param	intval	$page
 * @return	array
 */
function dr_category_seo($mod, $cat, $page = 1) {

	$seo = array();
	$cat['page'] = $page;
	$cat['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$cat['name'] = $cat['catname'] = dr_get_cat_pname($mod, $cat, $cat['join']);
	$cat['modulename'] = $cat['modname'] = $mod['name'];
	
	$meta_title = $cat['setting']['seo']['list_title'] ? $cat['setting']['seo']['list_title'] : '['.fc_lang('第%s页', '{page}').'{join}]{modulename}{join}{SITE_NAME}';
	
	$meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

	
	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($cat);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_keywords']);
        $seo['meta_keywords'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_keywords']);
        $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_description']);
        $seo['meta_description'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_description']);
        unset($rep);
	} else {
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
		$seo['meta_keywords'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $cat['setting']['seo']['list_keywords']);
		$seo['meta_keywords'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_keywords']);
		$seo['meta_description'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$cat[\\1]", $cat['setting']['seo']['list_description']);
		$seo['meta_description'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_description']);
	}

    $seo['meta_description'] = htmlspecialchars(dr_clearhtml($seo['meta_description']));

	return $seo;
}

/**
 * 模块SEO信息
 *
 * @param	array	$mod
 * @return	array
 */
function dr_module_seo($mod) {

	$seo = array();
	$mod['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$mod['modulename'] = $mod['modname'] = $mod['name'];
	$meta_title = $mod['site'][SITE_ID]['module_title'] ? $mod['site'][SITE_ID]['module_title'] : $mod['name'].$mod['join'].SITE_TITLE;
	
	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($mod);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $mod['site'][SITE_ID]['module_keywords']);
        $seo['meta_keywords'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_keywords']);
        $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $mod['site'][SITE_ID]['module_description']);
        $seo['meta_description'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_description']);
        unset($rep);
	} else {
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$mod[\\1]", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
		$seo['meta_keywords'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$mod[\\1]", $mod['site'][SITE_ID]['module_keywords']);
		$seo['meta_keywords'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_keywords']);
		$seo['meta_description'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$mod[\\1]", $mod['site'][SITE_ID]['module_description']);
		$seo['meta_description'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_description']);
	}

    $seo['meta_description'] = htmlspecialchars(dr_clearhtml($seo['meta_description']));
	
	return $seo;
}

/**
 * 模块内容URL地址
 *
 * @param	array	$mod
 * @param	array	$data
 * @param	mod	$page
 * @return	string
 */
function dr_show_url($mod, $data, $page = NULL) {

	if (!$mod || !$data) {
        return SITE_URL;
    }

    // 商铺地址
    if ($mod['dirname'] == 'store' && is_file(FCPATH.'module/store/config/.store')) {
        return dr_store_show_url($data, 'index');
    }
	
	$cat = $mod['category'][$data['catid']];

    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $ci	= &get_instance();
    $fid = isset($data['fid']) && $data['fid'] ? intval($data['fid']) : 0;
    if ($fid) {
        // 判断fid参数是否生效
        $linkage = dr_linkage(SITE_LID, $fid);
        if ($linkage) {
            $pids = explode(',', $linkage['pids']);
            $pids[] = $fid;
            foreach ($pids as $i) {
                if ($i && $fz = $ci->get_cache('branch-fenzhan-'.SITE_ID, $i)) {
                    $fid = $data['fid'] = $fz['cname'];
                    break;
                }
            }
        }
    }

    $rule = $ci->get_cache('urlrule', (int)$cat['setting']['urlrule'], 'value');
	if ($rule && $rule['show']) {
		// URL模式为自定义，且已经设置规则
        $data['modname'] = $mod['dirname'];
		$cat['pdirname'].= $cat['dirname'];
		$data['dirname'] = $cat['dirname'];
		$inputtime = isset($data['_inputtime']) ? $data['_inputtime'] : $data['inputtime'];
		$data['y'] = date('Y', $inputtime);
		$data['m'] = date('m', $inputtime);
		$data['d'] = date('d', $inputtime);
		$data['fid'] = (int)$fid;
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $cat['pdirname']);
		$url = ltrim($page ? $rule['show_page'] : $rule['show'], '/');
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}
        /*
        // 表示分站
        if ($fid) {
            $site_url = dr_fenzhan_url($fid); // 分站url
            // 此地址相对于网站根目录时
            if (strpos($url, '/') === 0) {
                $url = ltrim($url, '/');
                if (!$mod['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名才有效
                    if (strpos($site_url, 'index.php') !== FALSE) {
                        // 分站没绑定域名，站点地址为当前站点域名
                        return $ci->site_info[SITE_ID]['SITE_PC'].$url;
                    } else {
                        // 分站绑了域名就用分站的域名
                        return $site_url.'/'.$url;
                    }
                }
            }
            // 普通模式
            if (!$mod['site'][SITE_ID]['domain']) {
                // 此模块未绑定域名时，调用分站主站的URL
                if (strpos($site_url, 'index.php') !== FALSE) {
                    // 分站没绑定域名，站点地址为当前站点域名
                    return $ci->site_info[SITE_ID]['SITE_PC'].$mod['dirname'].'/'.$url;
                } else {
                    return $site_url.'/'.$mod['dirname'].'/'.$url;
                }
            } else {
                // 此模块绑定了域名时,原样输出
                return $mod['url'].$url;
            }
        }
		// 此地址相对于网站根目录时
        if (strpos($url, '/') === 0) {
            $url = ltrim($url, '/');
            if (!$mod['site'][SITE_ID]['domain']) {
                // 此模块未绑定域名才有效
                return SITE_URL.$url;
            }
        }*/
		return dr_url_prefix('rewrite', $mod, $cat, $fid).$url;
	}

    /*
    // 表示分站
    if ($fid) {
        if (!$mod['site'][SITE_ID]['domain']) {
            // 此模块未绑定域名时，调用分站主站的URL
            $site_url = dr_fenzhan_url($data['fid']); // 分站url
            if (strpos($site_url, 'index.php') === FALSE) {
                // 在分站主站绑定域名的情况下，省略fid
                return $site_url.'/'.$mod['dirname'].'/index.php?c=show&id='.$data['id'].($page ? '&page='.$page : '');
            }
            return SITE_URL.$mod['dirname'].'/index.php?c=show&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        } else {
            // 此模块绑定了域名时
            return $mod['url'].'index.php?c=show&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        }
    }
    */

	return dr_url_prefix('cat_show_ext_php', $mod, $cat, $fid).'c=show&id='.$data['id'].($page ? '&page='.$page : '');
}

/**
 * 模块内容扩展SEO信息
 *
 * @param	array	$mod
 * @param	array	$cat
 * @return	array
 */
function dr_extend_seo($mod, $data) {

	$seo = array();
	$cat = $mod['category'][$data['catid']];
	$data['extend'] = $data['name'];
	$data['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
	$data['name'] = $data['catname'] = dr_get_cat_pname($mod, $cat, $data['join']);
	$data['modulename'] = $data['modname'] = $mod['name'];
	
	$meta_title = $cat['setting']['seo']['extend_title'] ? $cat['setting']['seo']['extend_title'] : '{extend}{join}{ctitle}{join}{name}{join}{modulename}{join}{SITE_NAME}';
	$meta_title = str_replace('{title}', '{ctitle}', $meta_title);

	$meta_title = $_GET['page'] > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

	// 兼容php5.5
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $rep = new php5replace($data);
        $seo['meta_title'] = preg_replace_callback("#{([a-z_0-9]+)}#U", array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        unset($rep);
	} else {
		extract($data);
		$seo['meta_title'] = preg_replace('#{([a-z_0-9]+)}#Ue', "\$\\1", $meta_title);
		$seo['meta_title'] = preg_replace('#{([A-Z_]+)}#Ue', "\\1", $seo['meta_title']);
	}
	
	$seo['meta_keywords'] = $data['keywords'];
	$seo['meta_description'] = dr_clearhtml($seo['meta_description']);
	
	return $seo;
}

/**
 * 模块扩展内容URL地址
 *
 * @param	array	$mod
 * @param	array	$data
 * @return	string
 */
function dr_extend_url($mod, $data, $page = 0) {

	if (!$mod || !$data) {
        return SITE_URL;
    }

    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $ci	= &get_instance();
	$cat = $mod['category'][$data['catid']];
    $fid = isset($data['fid']) && $data['fid'] ? intval($data['fid']) : 0;
    if ($fid) {
        // 判断fid参数是否生效
        $linkage = dr_linkage(SITE_LID, $fid);
        if ($linkage) {
            $pids = explode(',', $linkage['pids']);
            $pids[] = $fid;
            foreach ($pids as $i) {
                if ($i && $fz = $ci->get_cache('branch-fenzhan-'.SITE_ID, $i)) {
                    $fid = $data['fid'] = $fz['cname'];
                    break;
                }
            }
        }
    }

	$rule = $ci->get_cache('urlrule', (int)$cat['setting']['urlrule'], 'value');
	if ($rule && $rule['extend']) {
		// URL模式为自定义，且已经设置规则
		$cat['pdirname'].= $cat['dirname'];
		$data['dirname'] = $cat['dirname'];
		$data['modname'] = $mod['dirname'];
		$inputtime = isset($data['_inputtime']) ? $data['_inputtime'] : $data['inputtime'];
		$data['y'] = date('Y', $inputtime);
		$data['m'] = date('m', $inputtime);
		$data['d'] = date('d', $inputtime);
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $cat['pdirname']);
        $url = ltrim($page ? $rule['extend_page'] : $rule['extend'], '/');
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}
        /*
        // 表示分站
        if ($fid) {
            $site_url = dr_fenzhan_url($fid); // 分站url
            // 此地址相对于网站根目录时
            if (strpos($url, '/') === 0) {
                $url = ltrim($url, '/');
                if (!$mod['site'][SITE_ID]['domain']) {
                    // 此模块未绑定域名才有效
                    if (strpos($site_url, 'index.php') !== FALSE) {
                        // 分站没绑定域名，站点地址为当前站点域名
                        return $ci->site_info[SITE_ID]['SITE_PC'].$url;
                    } else {
                        // 分站绑了域名就用分站的域名
                        return $site_url.'/'.$url;
                    }
                }
            }
            // 普通模式
            if (!$mod['site'][SITE_ID]['domain']) {
                // 此模块未绑定域名时，调用分站主站的URL
                if (strpos($site_url, 'index.php') !== FALSE) {
                    // 分站没绑定域名，站点地址为当前站点域名
                    return $ci->site_info[SITE_ID]['SITE_PC'].$mod['dirname'].'/'.$url;
                } else {
                    return $site_url.'/'.$mod['dirname'].'/'.$url;
                }
            } else {
                // 此模块绑定了域名时,原样输出
                return $mod['url'].$url;
            }
        }
        // 此地址相对于网站根目录时
        if (strpos($url, '/') === 0) {
            $url = ltrim($url, '/');
            if (!$mod['site'][SITE_ID]['domain']) {
                // 此模块未绑定域名才有效
                return SITE_URL.$url;
            }
        }*/
        return dr_url_prefix('rewrite', $mod, $cat, SITE_FID).$url;
	}

    /*
    // 表示分站
    if ($fid) {
        if (!$mod['site'][SITE_ID]['domain']) {
            // 此模块未绑定域名时，调用分站主站的URL
            $site_url = dr_fenzhan_url($data['fid']); // 分站url
            if (strpos($site_url, 'index.php') === FALSE) {
                // 在分站主站绑定域名的情况下，省略fid
                return $site_url.'/'.$mod['dirname'].'/index.php?c=extend&id='.$data['id'].($page ? '&page='.$page : '');
            }
            return SITE_URL.$mod['dirname'].'/index.php?c=extend&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        } else {
            // 此模块绑定了域名时
            return $mod['url'].'index.php?c=extend&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        }
    }
    */

    return dr_url_prefix('cat_show_ext_php', $mod, $cat, $fid) . 'c=extend&id='.$data['id'].($page ? '&page='.$page : '');
}

/**
 * 模块栏目URL地址
 *
 * @param	array	$mod
 * @param	array	$data
 * @param	intval	$page
 * @return	string
 */
function dr_category_url($mod, $data, $page = NULL, $site = SITE_ID, $fid = 0) {

	$ci	= &get_instance();
	if (!$mod || !$data) {
        return $ci->site_info[$site]['SITE_URL'];
    }

    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $cat = $data;
	$rule = isset($data['setting']['urlrule']) ? $ci->get_cache('urlrule', (int)$data['setting']['urlrule'], 'value') : 0;
	
	if ($rule && $rule['list']) {
		// URL模式为自定义，且已经设置规则
		$data['fid'] = $fid;
		$data['modname'] = $mod['dirname'];
		$data['pdirname'].= $data['dirname'];
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $data['pdirname']);
		$url = ltrim($page ? $rule['list_page'] : $rule['list'], '/');
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}
        /*
        // 表示分站
        if ($fid) {
            $site_url = dr_fenzhan_url($fid, $site); // 分站url
            // 此地址相对于网站根目录时
            if (strpos($url, '/') === 0) {
                $url = ltrim($url, '/');
                if (!$mod['site'][$site]['domain']) {
                    // 此模块未绑定域名才有效
                    if (strpos($site_url, 'index.php') !== FALSE) {
                        // 分站没绑定域名，站点地址为当前站点域名
                        return $ci->site_info[$site]['SITE_PC'].$url;
                    } else {
                        // 分站绑了域名就用分站的域名
                        return $site_url.'/'.$url;
                    }
                }
            }
            // 普通模式
            if (!$mod['site'][$site]['domain']) {
                // 此模块未绑定域名时，调用分站主站的URL
                if (strpos($site_url, 'index.php') !== FALSE) {
                    // 分站没绑定域名，站点地址为当前站点域名
                    return $ci->site_info[$site]['SITE_PC'].$mod['dirname'].'/'.$url;
                } else {
                    return $site_url.'/'.$mod['dirname'].'/'.$url;
                }
            } else {
                // 此模块绑定了域名时,原样输出
                return $mod['url'].$url;
            }
        }
        // 此地址相对于网站根目录时
        if (strpos($url, '/') === 0) {
            $url = ltrim($url, '/');
            if (!$mod['site'][$site]['domain']) {
                // 此模块未绑定域名才有效
                return $ci->site_info[$site]['SITE_PC'].$url;
            }
        }
        */
		return dr_url_prefix('rewrite', $mod, $cat, $fid, $site) . $url;
	}

    /*
    // 表示分站
    if ($fid) {
        if (!$mod['site'][$site]['domain']) {
            // 此模块未绑定域名时，调用分站主站的URL
            $site_url = dr_fenzhan_url($fid, $site); // 分站url
            if (strpos($site_url, 'index.php') === FALSE) {
                // 在分站主站绑定域名的情况下，省略fid
                return $site_url.'/'.$mod['dirname'].'/index.php?c=category&id='.$data['id'].($page ? '&page='.$page : '');
            }
            return SITE_URL.$mod['dirname'].'/index.php?c=category&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        } else {
            // 此模块绑定了域名时
            return $mod['url'].'index.php?c=category&fid='.$fid.'&id='.$data['id'].($page ? '&page='.$page : '');
        }
    }
*/

    return dr_url_prefix('cat_show_ext_php', $mod, $cat, $fid, $site) . 'c=category&id='.(isset($cat['id']) ? $cat['id'] : 0).($page ? '&page='.$page : '');
}

/*
 * 单页URL地址
 *
 * @param	array	$data
 * @param	intval	$page
 * @return	string
 */
function dr_page_url($data, $page = NULL, $site = SITE_ID) {

    $ci	= &get_instance();
	if (!$data) {
        return $ci->site_info[$site]['SITE_URL'];
    }

    $page && $data['page'] = $page = is_numeric($page) ? max((int)$page, 1) : $page;

    $module = $data['module'] ? $ci->get_cache('module-'.$site.'-'.$data['module']) : array();

	$rule = $ci->get_cache('urlrule', (int)$data['urlrule'], 'value');
	
	if ($rule && $rule['page'] && $rule['page_page']) {
		// URL模式为自定义，且已经设置规则
		$data['pdirname'].= $data['dirname'];
		$data['pdirname'] = str_replace('/', $rule['catjoin'], $data['pdirname']);
		$url = $page ? $rule['page_page'] : $rule['page'];
		// 兼容php5.5
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $url = preg_replace_callback("#{([a-z_0-9]+)}#Ui", array($rep, 'php55_replace_data'), $url);
            $url = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $url);
            unset($rep);
		} else {
			$url = preg_replace('#{([a-z_0-9]+)}#Uei', "\$data[\\1]", $url);
			$url = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $url);
		}
		return dr_url_prefix('rewrite', $module, array(), 0, $site).$url;
	}

	return dr_url_prefix('php', $module, array(), 0, $site).'c=page&id='.$data['id'].($page ? '&page='.$page : '');
}


// 模块URL
function dr_module_url($mod, $sid) {

    // 绑定域名的情况下
    if ($mod['site'][$sid]['domain']) {
        return dr_http_prefix($mod['site'][$sid]['domain'].'/');
    }

    $ci	= &get_instance();

    // 自定义规则的情况下
    $rule = $ci->get_cache('urlrule', (int)$mod['site'][$sid]['urlrule'], 'value');
    $domain = isset($ci->site_info[$sid]['SITE_PC']) && $ci->site_info[$sid]['SITE_PC'] ? $ci->site_info[$sid]['SITE_PC'] : SITE_URL;

    if ($rule['module']) {
        return $domain.str_replace('{modname}', $mod['dirname'], $rule['module']);
    }

    return $domain.'index.php?s='.$mod['dirname'];
}


/**
 * url函数
 *
 * @param	string	$url		URL规则，如home/index
 * @param	array	$query		相关参数
 * @return	string	项目入口文件.php?参数
 */
function dr_url($url, $query = array(), $self = SELF) {

	if (!$url) {
        return $self;
    }

    // 当是分站且没有绑定域名自动加上参数
    SITE_FID && !isset($query['fid']) && defined('SITE_BRANCH_DOMAIN') && !SITE_BRANCH_DOMAIN && $query['fid'] = SITE_FID;

	$url = strpos($url, 'admin') === 0 ? substr($url, 5) : $url;
	$url = trim($url, '/');

    // 判断是否后台首页
    if ($self != 'index.php' && ($url == 'home/index' || $url == 'home/home')) {
        return SELF;
    }

	$url = explode('/', $url);
	$uri = array();

	switch (count($url)) {
		case 1:
			$uri['c'] = 'home';
			$uri['m'] = $url[0];
			break;
		case 2:
			$uri['c'] = $url[0];
			$uri['m'] = $url[1];
			break;
		case 3:
			$uri['s'] = $url[0];
            // 非后台且非会员中心的模块地址
			if (is_dir(FCPATH.'module/'.$uri['s']) && $self == 'index.php' && !IS_MEMBER) {
				$ci	= &get_instance();
				$mod = $ci->get_cache('module-'.SITE_ID.'-'.$uri['s']);
                if ($mod['domain']) {
                    unset($uri['s']);
                    $self = $mod['url'].'index.php';
                } else {
                    $self = SITE_URL.'index.php';
                }
			}
			$uri['c'] = $url[1];
			$uri['m'] = $url[2];
			break;
	}

    $query && $uri = @array_merge($uri, $query);

	return $self.'?'.@http_build_query($uri);
}

/**
 * 会员url函数
 *
 * @param	string	$url 	URL规则，如home/index
 * @param	array	$query	相关参数
 * @return	string	地址
 */
function dr_member_url($url = '', $query = array(), $self = 'index.php') {

	if (!$url || $url == 'home/index' || $url == '/') {
        return MEMBER_URL;
    }

    // 当是分站且没有绑定域名自动加上参数
    SITE_FID && !isset($query['fid']) && defined('SITE_BRANCH_DOMAIN') && !SITE_BRANCH_DOMAIN && $query['fid'] = SITE_FID;

	$url = strpos($url, 'admin') === 0 ? substr($url, 5) : $url;
	$url = trim($url, '/');
	$url = explode('/', $url);
	$uri = array('s' => 'member');
	
	switch (count($url)) {
		case 1:
			$uri['c'] = 'home';
			$uri['m'] = $url[0];
			break;
		case 2:
			$uri['c'] = $url[0];
			$uri['m'] = $url[1];
			break;
		case 3:
			$uri['s'] = $url[0];
            // 当存在三个参数时,表示模块或应用的会员中心
            if ($uri['s'] != 'member') {
                if (is_dir(FCPATH.'module/'.$uri['s'])) {
                    $uri['mod'] = $uri['s'];
                    $uri['s'] = 'member';
                } elseif (is_dir(FCPATH.'app/'.$uri['s'])) {
                    $uri['app'] = $uri['s'];
                    $uri['s'] = 'member';
                }
            }
			$uri['c'] = $url[1];
			$uri['m'] = $url[2];
			break;
	}

    $query && $uri = @array_merge($uri, $query);

    if (defined('MEMBER_URL_RULE')
        || strpos(MEMBER_URL, 'index.php') !== false) {
        // 未绑定域名的情况下
        return SITE_URL.$self.'?'.@http_build_query($uri);
    } else {
        unset($uri['s']);
        return MEMBER_URL.$self.'?'.@http_build_query($uri);
    }
}

/**
 * 当前URL
 */
function dr_now_url() {

    $pageURL = 'http';
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $pageURL.= 's';

    $pageURL.= '://';
    if (strpos($_SERVER['HTTP_HOST'], ':') !== FALSE) {
        $url = explode(':', $_SERVER['HTTP_HOST']);
        $url[0] ? $pageURL.= $_SERVER['HTTP_HOST'] : $pageURL.= $url[0];
    } else {
        $pageURL.= $_SERVER['HTTP_HOST'];
    }
    
    $pageURL.= $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];

    return $pageURL;
}

/**
 * dialog弹出框窗口的URL
 *
 * @param	string	$url	地址
 * @param	string	$func	指向函数，如add，edit等
 * @param	string	$cache	更新缓存地址
 * @return	string
 */
function dr_dialog_url($url, $func) {
	return "javascript:dr_dialog('{$url}', '{$func}');";
}

// php 5.5 以上版本的正则替换方法
class php5replace {

    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    // 替换常量值 for php5.5
    public function php55_replace_var($value) {
        $v = '';
        @eval('$v = '.$value[1].';');
        return $v;
    }

    // 替换数组变量值 for php5.5
    public function php55_replace_data($value) {
        return $this->data[$value[1]];
    }

    // 替换函数值 for php5.5
    public function php55_replace_function($value) {

        if (function_exists($value[1])) {
            if ($value[2] == '$data') {
                $param = $this->data;
            } else {
                $param = $value[2];
            }
            return call_user_func_array($value[1], is_array($param) ? $param : @explode(',', $param));
        }

        return $value[0];
    }

}

// 模块内容评论地址
function dr_module_comment_url($module, $cid, $page = 0) {

    $module = is_array($module) ? $module : get_module($module);
    if (!$module) {
        return SITE_URL;
    }

    // 表示分站
    if (SITE_FID && !$module['site'][SITE_ID]['domain']) {
        // 此模块未绑定域名时，调用分站主站的URL
        $site_url = dr_fenzhan_url(SITE_FID); // 分站url
        if (strpos($site_url, 'index.php') === FALSE) {
            // 在分站主站绑定域名的情况下，省略fid
            return $site_url.'/'.$module['dirname'].'/index.php?c=comment&id='.$cid.($page ? '&page='.$page : '');
        }
    } elseif (!$module['site'][SITE_ID]['domain']) {
        // 此模块未绑定域名时
        return SITE_URL.'index.php?s='.$module['dirname'].'&c=comment&id='.$cid.($page ? '&page='.$page : '');
    }

    return $module['url'].'index.php?c=comment&id='.$cid.($page ? '&page='.$page : '');

}

// 模块扩展内容评论地址
function dr_extend_comment_url($module, $cid, $page = 0) {

    $module = is_array($module) ? $module : get_module($module);
    if (!$module) {
        return SITE_URL;
    }

    // 表示分站
    if (SITE_FID && !$module['site'][SITE_ID]['domain']) {
        // 此模块未绑定域名时，调用分站主站的URL
        $site_url = dr_fenzhan_url(SITE_FID); // 分站url
        if (strpos($site_url, 'index.php') === FALSE) {
            // 在分站主站绑定域名的情况下，省略fid
            return $site_url.'/'.$module['dirname'].'/index.php?c=ecomment&id='.$cid.($page ? '&page='.$page : '');
        }
    } elseif (!$module['site'][SITE_ID]['domain']) {
        // 此模块未绑定域名时
        return SITE_URL.'index.php?s='.$module['dirname'].'c=ecomment&id='.$cid.($page ? '&page='.$page : '');
    }

    return $module['url'].'index.php?c=ecomment&id='.$cid.($page ? '&page='.$page : '');

}