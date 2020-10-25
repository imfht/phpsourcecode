<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once libfile('function/plugin');

cpheader();

if (!$admincp->isfounder) {
    cpmsg('noaccess_isfounder', '', 'error');
}

$submenu = array(
    array('packs_list', 'packs', !$operation),
    array('packs_validator', 'packs&operation=upgradecheck&formhash=' . FORMHASH, $operation == 'upgradecheck'),
    array('cloudaddons_pack_link', 'cloudaddons'),
);

if (!$operation) {
    showsubmenu('nav_packs', $submenu);
    $addonmd5dir = DISCUZ_ROOT . './data/addonmd5';
    $addonmd5sdir = dir($addonmd5dir);
    $pcakfiles = $pcaklist = array();
    while ($entry = $addonmd5sdir->read()) {
        if (!in_array($entry, array('.', '..')) && !is_dir($addonmd5dir . '/' . $entry)) {
            $ent = explode('.', trim($entry));
            if (count($ent) == 3 && $ent[1] == 'pack' && $ent[2] == 'xml') {
                if (ispluginkey($ent[0])) {
                    $pcakfiles[] = $ent[0];
                }
            }
        }
    }
    if (!$pcakfiles) {
        cpmsg('pack_not_found', '', 'error');
    } else {
        krsort($pcakfiles);
        loadcache('addoninfo_pack');
        $packinfos = $_G['cache']['addoninfo_pack'];
        $nowpackinfos = array();
        if (is_array($packinfos)) {
            foreach ($packinfos as $key => $value) {
                if (in_array($key, $pcakfiles)) {
                    $nowpackinfos[$key] = $value;
                }
            }
            unset($packinfos);
        } else {
            $packinfos = array();
        }
        foreach ($pcakfiles as $value) {
            if (!isset($nowpackinfos[$value]) || $nowpackinfos[$value]['dateline'] + 864000 < TIMESTAMP) {
                $nowpackinfos[$value] = get_packinfo($value);
            }
            $pack = array(
                'identifier' => $value,
                'name' => $nowpackinfos[$value]['name'] ? $nowpackinfos[$value]['name'] : $value,
                'dateline' => filemtime($addonmd5dir . '/' . $value . '.pack.xml'),
            );
            $pcaklist[] = $pack;
        }
        savecache('addoninfo_pack', $nowpackinfos);
        showpcaklist($pcaklist);
    }
	showtableheader('', 'psetting');
    showsubmit('', '', '', '<a href="' . ADMINSCRIPT . '?action=cloudaddons">' . cplang('cloudaddons_pack_link') . '</a>');
    showtablefooter();
} elseif ($operation == 'delete') {

    $identifier = trim($_GET['identifier']);
    if (ispluginkey($identifier)) {

        $xmlfile = DISCUZ_ROOT . './data/addonmd5/' . $identifier . '.pack.xml';
        if (!file_exists($xmlfile)) {
            cpmsg('packs_delete_ok', 'action=packs', 'succeed');
        }

        $step = max(1, intval($_GET['step']));
        showsubmenusteps('packs_delete', array(
            array('packs_delete_confirm', $step == 1),
            array('packs_delete_completed', $step == 2)
        ));

        loadcache('addoninfo_pack');
        $packinfos = $_G['cache']['addoninfo_pack'];

        $discuzfiles = $discuzfilesmd5 = array();
        $discuzfiles = @file('./source/admincp/discuzfiles.md5');
        foreach ($discuzfiles as $line) {
            $file = '/' . trim(substr(str_replace('*./', '*', $line), 34));
            $discuzfilesmd5[$file] = substr($line, 0, 32);
        }

        if (!submitcheck('deletepacksubmit')) {

            showtips('packs_delete_tips');
            showtableheader('packs_delete_title');
            showsubtitle(array('filename', '', 'lastmodified', 'packs_delete_status'));
            $importfile = DISCUZ_ROOT . './data/addonmd5/' . $identifier . '.pack.xml';
            $importtxt = @implode('', file($importfile));
            $packarray = getimportdata('Discuz! Addon MD5');
            $systemflag = false;
            foreach ($packarray as $file => $md5) {
                if (!is_dir($file) && !empty($file)) {
                    if (!file_exists(DISCUZ_ROOT . $file)) {
                        $class = 'del';
                    } else {
                        $realpath = '/' . str_replace(array(DISCUZ_ROOT, '\\'), array('', '/'), realpath(DISCUZ_ROOT . $file));
                        if ($realpath == $file) {
                            if (isset($discuzfilesmd5[$file])) {
                                $class = 'unfixed';
                                $systemflag = true;
                            } elseif (md5_file(DISCUZ_ROOT . $file) != $md5) {
                                $class = 'unknown';
                            } else {
                                $class = 'correct';
                            }
                        } else {
                            $class = 'del';
                        }
                    }
                    echo '<tr><td><em class="files2 bold">' . dhtmlspecialchars($file) . '</em></td><td style="text-align: right">' . number_format(filesize(DISCUZ_ROOT . $file)) . ' Bytes&nbsp;&nbsp;</td><td>' . dgmdate(filemtime(DISCUZ_ROOT . $file)) . '</td><td><em class="' . $class . '">&nbsp;</em></td></tr>';
                }
            }
            showtablefooter();
            showformheader("packs&operation=delete&identifier=$identifier&step=2");
            showtableheader();
            if ($systemflag) {
                cpmsg('packs_delete_invalid', '', 'error', array('pack' => '<a href="' . ADMINSCRIPT . '?action=cloudaddons&id=' . $identifier . '.pack">' . dhtmlspecialchars($packinfos[$identifier]['name']) . '</a>'), '', false);
            } else {
                showsubmit('deletepacksubmit', cplang('packs_delete_submit') . ' ' . dhtmlspecialchars($packinfos[$identifier]['name']), '', '<button onclick="location.href=\'' . ADMINSCRIPT . '?action=packs\'" type="button" class="btn">' . cplang('packs_delete_cancel') . '</button>');
            }
            showtablefooter();
            showformfooter();
        } else {
            if (file_exists(DISCUZ_ROOT . './data/addonpack/' . $identifier . '.uninstall.php')) {
                dheader('location: ' . $_G['siteurl'] . 'data/addonpack/' . $identifier . '.uninstall.php');
            }
            $importfile = DISCUZ_ROOT . './data/addonmd5/' . $identifier . '.pack.xml';
            $importtxt = @implode('', file($importfile));
            $packarray = getimportdata('Discuz! Addon MD5');
            $systemflag = false;
            foreach ($packarray as $file => $md5) {
                if (!is_dir($file) && !empty($file)) {
                    $realpath = '/' . str_replace(array(DISCUZ_ROOT, '\\'), array('', '/'), realpath(DISCUZ_ROOT . $file));
                    if ($realpath == $file) {
                        if (isset($discuzfilesmd5[$file])) {
                            $systemflag = true;
                            break;
                        }
                    }
                }
            }

            if ($systemflag) {
                cpmsg('packs_delete_invalid', '', 'error', array(), '', false);
            } else {
                foreach ($packarray as $delfile => $md5) {
                    if (!is_dir($delfile)) {
                        if (file_exists(DISCUZ_ROOT . $delfile)) {
                            $realpath = '/' . str_replace(array(DISCUZ_ROOT, '\\'), array('', '/'), realpath(DISCUZ_ROOT . $delfile));
                            if ($realpath == $delfile) {
                                if (@unlink(DISCUZ_ROOT . $delfile)) {
                                    $dellist[] = $delfile;
                                } else {
                                    $faillist[] = $delfile;
                                }
                            } else {
                                $faillist[] = $delfile;
                            }
                        } else {
                            $dellist[] = $delfile;
                        }
                    }
                }
                @unlink(DISCUZ_ROOT . './data/addonmd5/' . $identifier . '.pack.xml');

                showtips('packs_delete_ok_tips', 'tips', TRUE, 'packs_delete_ok_title');
                showtableheader('');
                showsubtitle(array('filename', 'packs_delete_status'));

                if ($dellist && is_array($dellist)) {
                    foreach ($dellist as $file) {
                        echo '<tr><td><em class="files2 bold">' . $file . '</em></td><td><em class="correct">&nbsp;</em></td></tr>';
                    }
                }

                if ($faillist && is_array($faillist)) {
                    foreach ($faillist as $file) {
                        echo '<tr><td><em class="files2 bold">' . $file . '</em></td><td><em class="del">&nbsp;</em></td></tr>';
                    }
                }
                echo '<tr><td><button onclick="location.href=\'' . ADMINSCRIPT . '?action=packs\'" type="button" class="btn">' . cplang('packs_delete_back') . '</button></td><td></td></tr>';
                showtablefooter();
            }
        }
    } else {
        cpmsg('packs_identifier_invalid', '', 'error');
    }
} elseif ($operation == 'upgradecheck' && FORMHASH == $_GET['formhash']) {
    showsubmenu('nav_packs', $submenu);
    $addonmd5dir = DISCUZ_ROOT . './data/addonmd5';
    $addonmd5sdir = dir($addonmd5dir);
    $pcakfiles = $pcaklist = $result = array();
    while ($entry = $addonmd5sdir->read()) {
        if (!in_array($entry, array('.', '..')) && !is_dir($addonmd5dir . '/' . $entry)) {
            $ent = explode('.', trim($entry));
            if (count($ent) == 3 && $ent[1] == 'pack' && $ent[2] == 'xml') {
                if (ispluginkey($ent[0])) {
                    $pcakfiles[] = $ent[0];
                }
            }
        }
    }

    $errarray = $newarray = array();
    if (!$pcakfiles) {
        cpmsg('pack_not_found', '', 'error');
    } else {
        $addonids = array();
        foreach ($pcakfiles as $value) {
            if (ispluginkey($value)) {
                $addonids[] = $value . '.pack';
            }
        }
        $checkresult = dunserialize(cloudaddons_upgradecheck($addonids));

        loadcache('addoninfo_pack');
        $packinfos = $_G['cache']['addoninfo_pack'];

        foreach ($pcakfiles as $value) {
            $addonid = $value . '.pack';
            if (isset($checkresult[$addonid])) {
                list($return, $newver, $sysver) = explode(':', $checkresult[$addonid]);
                $packname = $packinfos[$value]['name'] ? $packinfos[$value]['name'] : $value . '.pack';
                if ($return == 0) {
                    $errarray[] = '<a href="' . ADMINSCRIPT . '?action=cloudaddons&id=' . $value . '.pack" target="_blank">' . dhtmlspecialchars($packname) . '</a>';
                } elseif ($newver) {
                    $newarray[] = '<a href="' . ADMINSCRIPT . '?action=cloudaddons&id=' . $value . '.pack" target="_blank">' . dhtmlspecialchars($packname) . ' ' . $newver . '</a>';
                }
            }
        }
    }
    if (!$newarray && !$errarray) {
        cpmsg('packs_validator_noupdate', '', 'error');
    } else {
        showtableheader();
        if ($newarray) {
            showtitle('packs_validator_newversion');
            foreach ($newarray as $row) {
                showtablerow('class="hover"', array(), array($row));
            }
        }
        if ($errarray) {
            showtitle('packs_validator_error');
            foreach ($errarray as $row) {
                showtablerow('class="hover"', array(), array($row));
            }
        }
        showtablefooter();
    }
}

function showpcaklist($pcaklist) {
    global $_G, $lang;
    echo '<ul class="plb cl">';
    foreach ($pcaklist as $pack) {
        echo '<li onmouseover="getMemo(this, \'' . $pack['identifier'] . '\')">
				<div id="base_' . $pack['identifier'] . '">
					<div class="x1 cl">
					<a href="' . ADMINSCRIPT . '?action=cloudaddons&id=' . $pack['identifier'] . '.pack" target="_blank" class="avt">
					<img src="' . CLOUDADDONS_WEBSITE_URL . '/resource/pack/' . $pack['identifier'] . '.png" onerror="this.src=\'static/image/admincp/plugin_logo.png\';this.onerror=null" />
					</a>
					<h5>' . dhtmlspecialchars($pack['name']) . '</h5>
					<p class="cl mtn">' . dgmdate($pack['dateline']) . '</p>' . '</div>
					<div class="x2 cl" id="memo_' . $pack['identifier'] . '" style="display:none">
					  <a href="' . ADMINSCRIPT . '?action=packs&operation=delete&identifier=' . $pack['identifier'] . '">' . $lang['packs_config_delete'] . '</a>&nbsp;
					  <a href="' . ADMINSCRIPT . '?action=cloudaddons&id=' . $pack['identifier'] . '.pack" target="_blank" title="' . $lang['cloudaddons_linkto'] . '">' . $lang['packs_config_desc'] . '</a>&nbsp;
					</div>
				</div>
			</li>';
    }
    echo "</ul><script>function getMemo(obj, id) { var baseobj = $('base_' + id);var memoobj = $('memo_' + id);baseobj.className = 'over';memoobj.style.display = '';if(!obj.onmouseout) {obj.onmouseout = function () {baseobj.className = '';memoobj.style.display = 'none';}}}</script>";
}

function get_packinfo($pack) {
    global $_G;
    $retirn = array('name' => $pack, 'dateline' => TIMESTAMP);
    if (ispluginkey($pack)) {
        $url = CLOUDADDONS_WEBSITE_URL . '/?@' . $pack . '.pack';
        $content = dfsockopen($url);
        $content = diconv($content, 'GBK');
        preg_match('/<a href="' . addcslashes(CLOUDADDONS_WEBSITE_URL, '?*+^$.[]()|/') . '\/\?\@' . $pack . '\.pack">([^<]*)<\/a>/iUs', $content, $matche);
        if ($matche[1]) {
            $retirn['name'] = $matche[1];
        }
    }
    return $retirn;
}

?>