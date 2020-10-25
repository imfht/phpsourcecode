<?php

/**
 * 视图的职责暂被控制器和模版引擎分摊掉了
 */
class Mvc_View extends Discuz_Base {

    /**
     * 视图要用的变量全部存到这里
     * @var array 
     */
    private $_vars = array();

    /**
     * 设置视图中要用的各种变量
     * @param string $k 变量名
     * @param string $v 变量值
     * @return void 
     */
    public function setVar($k, $v) {
        $this->_vars[$k] = $v;
    }

    /**
     * 获取视图变量
     * @param string $k
     * @return mixed
     */
    public function getVar($k) {
        return isset($this->_vars[$k]) ? $this->_vars[$k] : null;
    }

    /**
     * 构造模版路径，并引入显示
     * 模版内作用范围全部集中在该方法的范围
     */
    public function display($tpl, $tpldir) {
        global $_G;

        extract($this->_vars);

        include_once $this->_template($tpl, $tpldir);
    }

    /**
     * 基于缓存机制的模版文件路径构造，供引入
     */
    private function _template($file, $tpldir = '', $gettplfile = false) {
        global $_G;
        static $_init_style = false;
        if ($_init_style === false) {
            $discuz = & discuz_core::instance();
            $discuz->_init_style();
            $_init_style = true;
        }

        $clonefile = '';
        $templateid = 'modules';

        $oldfile = $file;
        $file = empty($clonefile) || STYLEID != $_G['cache']['style_default']['styleid'] ? $file : $file . '_' . $clonefile;

        $file .=!empty($_G['inajax']) && ($file == 'common/header' || $file == 'common/footer') ? '_ajax' : '';
        $tpldir = $tpldir ? $tpldir : (defined('TPLDIR') ? TPLDIR : '');
        $templateid = $templateid ? $templateid : (defined('TEMPLATEID') ? TEMPLATEID : '');
        $filebak = $file;

        if (defined('IN_MOBILE') && !defined('TPL_DEFAULT') && strpos($file, 'mobile/') === false || $_G['forcemobilemessage']) {
            $file = 'mobile/' . $oldfile;
        }

        if (!$tpldir) {
            $tpldir = './template/default';
        }
        $tplfile = $tpldir . '/' . $file . '.htm';

        $file == 'common/header' && defined('CURMODULE') && CURMODULE && $file = 'common/header_' . $_G['basescript'] . '_' . CURMODULE;

        if (defined('IN_MOBILE') && !defined('TPL_DEFAULT')) {
            if (strpos($tpldir, 'plugin')) {
                if (!file_exists(DISCUZ_ROOT . $tpldir . '/' . $file . '.htm')) {
                    discuz_error::template_error('template_notfound', $tpldir . '/' . $file . '.htm');
                } else {
                    $mobiletplfile = $tpldir . '/' . $file . '.htm';
                }
            }
            !$mobiletplfile && $mobiletplfile = $file . '.htm';
            if (strpos($tpldir, 'plugin') && file_exists(DISCUZ_ROOT . $mobiletplfile)) {
                $tplfile = $mobiletplfile;
            } elseif (!file_exists(DISCUZ_ROOT . TPLDIR . '/' . $mobiletplfile)) {
                $mobiletplfile = './template/default/' . $mobiletplfile;
                if (!file_exists(DISCUZ_ROOT . $mobiletplfile) && !$_G['forcemobilemessage']) {
                    $tplfile = str_replace('mobile/', '', $tplfile);
                    $file = str_replace('mobile/', '', $file);
                    define('TPL_DEFAULT', true);
                } else {
                    $tplfile = $mobiletplfile;
                }
            } else {
                $tplfile = TPLDIR . '/' . $mobiletplfile;
            }
        }

        $cachefile = './data/template/' . (defined('STYLEID') ? STYLEID . '_' : '_') . $templateid . '_' . str_replace('/', '_', $tpldir . $file) . '.tpl.php';

        if (($templateid !== 'modules') && $templateid != 1 && !file_exists(DISCUZ_ROOT . $tplfile) && !file_exists(DISCUZ_ROOT . ($tplfile = $tpldir . $filebak . '.htm'))) {
            $tplfile = './template/default/' . $filebak . '.htm';
        }

        if ($gettplfile) {
            return $tplfile;
        }
        $this->checktplrefresh($tplfile, $tplfile, @filemtime(DISCUZ_ROOT . $cachefile), $templateid, $cachefile, $tpldir, $file);
        
        
        return DISCUZ_ROOT . $cachefile;
    }
    
    private function checktplrefresh($maintpl, $subtpl, $timecompare, $templateid, $cachefile, $tpldir, $file) {
	static $tplrefresh, $timestamp, $targettplname;
	if($tplrefresh === null) {
		$tplrefresh = getglobal('config/output/tplrefresh');
		$timestamp = getglobal('timestamp');
	}

	if(empty($timecompare) || $tplrefresh == 1 || ($tplrefresh > 1 && !($timestamp % $tplrefresh))) {
		if(empty($timecompare) || @filemtime(DISCUZ_ROOT.$subtpl) > $timecompare) {
			$template = new Mvc_Template();
			$template->parse_template($maintpl, $templateid, $tpldir, $file, $cachefile);
			if($targettplname === null) {
				$targettplname = getglobal('style/tplfile');
				if(!empty($targettplname)) {
					$targettplname = strtr($targettplname, ':', '_');
					update_template_block($targettplname, $template->blocks);
				}
				$targettplname = true;
			}
			return TRUE;
		}
	}
	return FALSE;
}

}
