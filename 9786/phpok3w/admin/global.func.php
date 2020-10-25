<?php
function msg($msg = errmsg, $forward = 'goback', $time = '1')
{
    global $CFG;
    if (!$msg && $forward && $forward != 'goback') dheader($forward);
    include DT_ROOT . '/admin/template/msg.tpl.php';
    exit;
}

function dialog($dcontent)
{
    global $CFG;
    include DT_ROOT . '/admin/template/dialog.tpl.php';
    exit;
}

function tpl($file = 'index', $mod = 'destoon')
{
    global $CFG, $DT;
    return $mod == 'destoon' ? DT_ROOT . '/admin/template/' . $file . '.tpl.php' : DT_ROOT . '/module/' . $mod . '/admin/template/' . $file . '.tpl.php';
}

function progress($sid, $fid, $tid)
{
    if ($tid > $sid && $fid < $tid)
    {
        $p = dround(($fid - $sid) * 100 / ($tid - $sid), 0, true);
        if ($p > 100) $p = 100;
        $p = $p . '%';
    } else
    {
        $p = '100%';
    }
    return '<table cellpadding="0" cellspacing="0" width="100%" style="margin:0"><tr><td><div class="progress"><div style="width:' . $p . ';">&nbsp;</div></div></td><td style="color:#666666;font-size:10px;width:40px;text-align:center;">' . $p . '</td></tr></table>';
}

function show_menu($menus = array())
{
    global $module, $file, $action;
    $menu = '';
    foreach ($menus as $id => $m)
    {
        if (isset($m[1]))
        {
            $extend = isset($m[2]) ? $m[2] : '';
            $menu .= '<td id="Tab' . $id . '" class="tab"><a href="' . $m[1] . '" ' . $extend . '>' . $m[0] . '</a></td><td class="tab_nav">&nbsp;</td>';
        } else
        {
            $class = $id == 0 ? 'tab_on' : 'tab';
            $menu .= '<td id="Tab' . $id . '" class="' . $class . '"><a href="javascript:Tab(' . $id . ');">' . $m[0] . '</a></td><td class="tab_nav">&nbsp;</td>';
        }
    }
    include DT_ROOT . '/admin/template/menu.tpl.php';
}

function update_setting($item, $setting)
{
    global $db;
    $db->query("DELETE FROM {$db->pre}setting WHERE item='$item'");
    foreach ($setting as $k => $v)
    {
        if (is_array($v)) $v = implode(',', $v);
        $db->query("INSERT INTO {$db->pre}setting (item,item_key,item_value) VALUES ('$item','$k','$v')");
    }
    return true;
}

function get_setting($item)
{
    global $db;
    $setting = array();
    $query = $db->query("SELECT * FROM {$db->pre}setting WHERE item='$item'");
    while ($r = $db->fetch_array($query))
    {
        $setting[$r['item_key']] = $r['item_value'];
    }
    return $setting;
}

function update_category($CAT)
{
    global $db, $DT;
    $linkurl = listurl($CAT);
    if ($DT['index']) $linkurl = str_replace($DT['index'] . '.' . $DT['file_ext'], '', $linkurl);
    $db->query("UPDATE {$db->pre}category SET linkurl='$linkurl' WHERE catid=" . $CAT['catid']);
}

function tips($tips)
{
    echo ' <img src="admin/image/help.png" width="11" height="11" title="' . $tips . '" alt="tips" class="c_p" onclick="Dconfirm(this.title, \'\', 450);" />';
}

function array_save($array, $arrayname, $file)
{
    $data = var_export($array, true);
    $data = "<?php\n" . $arrayname . " = " . $data . ";\n?>";
    return file_put($file, $data);
}

function fetch_url($url)
{
    global $db;
    $fetch = array();
    $tmp = parse_url($url);
    $domain = $tmp['host'];
    $r = $db->get_one("SELECT * FROM {$db->pre}fetch WHERE domain='$domain' ORDER BY edittime DESC");
    if ($r)
    {
        $content = file_get($url);
        if ($content)
        {
            $content = convert($content, $r['encode'], DT_CHARSET);
            preg_match("/<title>(.*)<\/title>/isU", $content, $m);
            if (isset($m[1])) $fetch['title'] = trim($r['title'] ? str_replace($r['title'], '', $m[1]) : $m[1]);
            preg_match("/<meta[\s]+name=['\"]description['\"] content=['\"](.*)['\"]/isU", $content, $m);
            if (isset($m[1])) $fetch['introduce'] = $m[1];
            list($f, $t) = explode('[content]', $r['content']);
            if ($f && $t)
            {
                $s = strpos($content, $f);
                if ($s !== false)
                {
                    $e = strpos($content, $t, $s);
                    if ($e !== false && $e > $s)
                    {
                        $fetch['content'] = substr($content, $s + strlen($f), $e - $s - strlen($f));
                    }
                }
            }
        }
    }
    return $fetch;
}

function edition($k = -1)
{
    $E = array();
    $E[0] = DT_DOMAIN;
    $E[1] = '&#20010;&#20154;&#29256;';
    return $k >= 0 ? $E[$k] : $E;
}

function admin_log($force = 0)
{
    global $DT, $db, $moduleid, $file, $action, $_username, $DT_QST, $DT_IP, $DT_TIME;
    if ($force) $DT['admin_log'] = 2;
    if (!$DT['admin_log'] || !$DT_QST || ($moduleid == 1 && $file == 'index')) return false;
    if ($DT['admin_log'] == 2 || ($DT['admin_log'] == 1 && ($file == 'setting' || in_array($action, array('delete', 'edit', 'move', 'clear', 'add')))))
    {
        if (strpos($DT_QST, 'file=log') !== false) return false;
        $fpos = strpos($DT_QST, '&forward');
        if ($fpos) $DT_QST = substr($DT_QST, 0, $fpos);
        $logstring = get_cookie('logstring');
        if ($DT_QST == $logstring) return false;
        $db->query("INSERT INTO {$db->pre}admin_log(qstring, username, ip, logtime) VALUES('$DT_QST','$_username','$DT_IP','$DT_TIME')");
        set_cookie('logstring', $DT_QST);
    }
}

function admin_online()
{
    global $DT, $db, $moduleid, $_username, $DT_QST, $DT_IP, $DT_TIME;
    if (!$DT['admin_online'] || !$_username) return false;
    $qstring = $DT_QST;
    $fpos = strpos($qstring, '&forward');
    if ($fpos) $qstring = substr($qstring, 0, $fpos);
    $qstring = preg_replace("/rand=([0-9]{1,})\&/", "", $qstring);
    $db->query("REPLACE INTO {$db->pre}admin_online (sid,username,ip,moduleid,qstring,lasttime) VALUES ('" . session_id() . "','$_username','$DT_IP','$moduleid','$qstring','$DT_TIME')");
    $lastime = $DT_TIME - $DT['online'];
    $db->query("DELETE FROM {$db->pre}admin_online WHERE lasttime<$lastime");
}

function admin_check()
{
    global $CFG, $db, $_admin, $_userid, $moduleid, $file, $action, $catid, $_catids, $_childs;
    if (in_array($file, array('logout', 'cloud', 'mymenu', 'search', 'ip', 'mobile'))) return true; //All user
    if ($moduleid == 1 && $file == 'index') return true;
    if ($CFG['founderid'] && $CFG['founderid'] == $_userid) return true; //Founder
    if ($_admin == 2)
    {
        $R = cache_read('right-' . $_userid . '.php');
        if (!$R) return false;
        if (!isset($R[$moduleid])) return false;
        if (!$R[$moduleid]) return true; //Module admin
        if (!isset($R[$moduleid][$file])) return false;
        if (!$R[$moduleid][$file]) return true;
        if ($action && $R[$moduleid][$file]['action'] && !in_array($action, $R[$moduleid][$file]['action'])) return false;
        if (!$R[$moduleid][$file]['catid']) return true;
        $_catids = implode(',', $R[$moduleid][$file]['catid']);
        if ($catid)
        {
            if (in_array($catid, $R[$moduleid][$file]['catid'])) return true;
            //Childs
            $result = $db->query("SELECT catid,child,arrchildid FROM {$db->pre}category WHERE moduleid=$moduleid AND catid IN ($_catids)");
            while ($r = $db->fetch_array($result))
            {
                $_childs .= ',' . ($r['child'] ? $r['arrchildid'] : $r['catid']);
            }
            if (strpos($_childs . ',', ',' . $catid . ',') !== false) return true;
            return false;
        }
    } else if ($_admin == 1)
    {
        if (in_array($file, array('admin', 'setting', 'module', 'area', 'database', 'template', 'skin', 'log', 'update', 'group', 'fields', 'loginlog'))) return false; //Founder || Common Admin Only
    }
    return true;
}

function item_check($itemid)
{
    global $db, $table, $_child, $moduleid;
    if ($moduleid == 3) return true;
    $fd = 'itemid';
    if ($moduleid == 2 || $moduleid == 4) $fd = 'userid';
    $r = $db->get_one("SELECT catid FROM {$table} WHERE `$fd`=$itemid");
    if ($r && $_child && in_array($r['catid'], $_child)) return true;
    return false;
}

function city_check($itemid)
{
    global $db, $table, $_areaid, $moduleid;
    if ($moduleid == 3) return true;
    $fd = 'itemid';
    if ($moduleid == 2 || $moduleid == 4) $fd = 'userid';
    $r = $db->get_one("SELECT areaid FROM {$table} WHERE `$fd`=$itemid");
    if ($r && $_areaid && in_array($r['areaid'], $_areaid)) return true;
    return false;
}

function split_content($moduleid, $part)
{
    global $db, $CFG, $MODULE;
    $table = $db->pre . $moduleid . '_' . $part;
    $fd = $moduleid == 4 ? 'userid' : 'itemid';
    if ($db->version() > '4.1' && $CFG['db_charset'])
    {
        $type = " ENGINE=MyISAM DEFAULT CHARSET=" . $CFG['db_charset'];
    } else
    {
        $type = " TYPE=MyISAM";
    }
    $db->query("CREATE TABLE IF NOT EXISTS `{$table}` (`{$fd}` bigint(20) unsigned NOT NULL default '0',`content` longtext NOT NULL,PRIMARY KEY  (`{$fd}`))" . $type . " COMMENT='" . $MODULE[$moduleid]['name'] . "内容_" . $part . "'");
}

function split_sell($part)
{
    global $db, $CFG, $MODULE;
    $sql = file_get(DT_ROOT . '/file/setting/split_sell.sql');
    $sql or dalert('请检查文件file/setting/split_sell.sql是否存在');
    $sql = str_replace('destoon_sell', $db->pre . 'sell_' . $part, $sql);
    if ($db->version() > '4.1' && $CFG['db_charset'])
    {
        $sql .= " ENGINE=MyISAM DEFAULT CHARSET=" . $CFG['db_charset'];
    } else
    {
        $sql .= " TYPE=MyISAM";
    }
    $sql .= " COMMENT='" . $MODULE[5]['name'] . "分表_" . $part . "';";
    $db->query($sql);
}

function seo_title($title, $show = '')
{
    /*	$SEO = array(
            'modulename'		=>	'模块名称',
            'page'				=>	'页码',
            'sitename'			=>	'网站名称',
            'sitetitle'			=>	'网站SEO标题',
            'sitekeywords'		=>	'网站SEO关键词',
            'sitedescription'	=>	'网站SEO描述',
            'catname'			=>	'分类名称',
            'cattitle'			=>	'分类SEO标题',
            'catkeywords'		=>	'分类SEO关键词',
            'catdescription'	=>	'分类SEO描述',
            'showtitle'			=>	'内容标题',
            'showintroduce'		=>	'内容简介',
            'kw'				=>	'关键词',
            'areaname'			=>	'地区',
            'delimiter'			=>	'分隔符',
        );
        if(is_array($show)) {
            foreach($show as $v) {
                if(isset($SEO[$v])) echo '<a href="javascript:_into(\''.$title.'\', \'{'.$SEO[$v].'}\');" title="{'.$SEO[$v].'}">{'.$SEO[$v].'}</a>&nbsp;&nbsp;';
            }
        } else {
            foreach($SEO as $k=>$v) {
                $title = str_replace($v, '$seo_'.$k, $title);
            }
            return $title;
        }*/
}

function seo_check($str)
{
    foreach (array('<', '>', '(', ')', ';', '?', '\\', '"', "'") as $v)
    {
        if (strpos($str, $v) !== false) return false;
    }
    return true;
}

function install_file($file, $dir, $extend = 0)
{
    $content = "<?php\n";
    if ($extend == 1) $content .= "define('DT_REWRITE', true);\n";
    $content .= "require 'config.inc.php';\n";
    $content .= "require '../common.inc.php';\n";
    $content .= "require DT_ROOT.'/module/'.\$module.'/" . $file . ".inc.php';\n";
    $content .= '?>';
    return file_put(DT_ROOT . '/' . $dir . '/' . $file . '.php', $content);
}

function list_dir($dir)
{
    $dirs = array();
    $files = glob(DT_ROOT . '/' . $dir . '/*');
    if (is_array($files))
    {
        include DT_ROOT . '/' . $dir . '/these.name.php';
        foreach ($files as $v)
        {
            if (is_file($v)) continue;
            $v = basename($v);
            $n = isset($names[$v]) ? $names[$v] : $v;
            $dirs[] = array('dir' => $v, 'name' => $n);
        }
    }
    return $dirs;
}

function pass_encode($str)
{
    $len = strlen($str);
    if ($len < 1) return '';
    $new = '';
    for ($i = 0; $i < $len; $i++)
    {
        $new .= ($i == 0 || $i == $len - 1) ? $str{$i} : '*';
    }
    return $new;
}

function pass_decode($new, $old)
{
    return $new == pass_encode($old) ? $old : $new;
}

?>