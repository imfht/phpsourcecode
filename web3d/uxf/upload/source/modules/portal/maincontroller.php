<?php
define('APPTYPEID', 4);
define('CURSCRIPT', 'portal');

require_once libfile('function/home');
require_once libfile('function/portal');

runhooks();

class MainController extends Mvc_Controller {

    public function actIndex() {
        global $_G;
        
        list($navtitle, $metadescription, $metakeywords) = get_seosetting('portal');
        if (!$navtitle) {
            $navtitle = $_G['setting']['navs'][1]['navname'];
            $nobbname = false;
        } else {
            $nobbname = true;
        }
        if (!$metakeywords) {
            $metakeywords = $_G['setting']['navs'][1]['navname'];
        }
        if (!$metadescription) {
            $metadescription = $_G['setting']['navs'][1]['navname'];
        }

        if (isset($_G['makehtml'])) {
            Helper_MakeHTML::portal_index();
        }

        include_once template('diy:portal/index');
    }
    
    public function actList() {
        global $_G;
        
        $_G['catid'] = $catid = max(0, intval($_GET['catid']));
        if (empty($catid)) {
            showmessage('list_choose_category', dreferer());
        }
        $portalcategory = &$_G['cache']['portalcategory'];
        $cat = $portalcategory[$catid];

        if (empty($cat)) {
            showmessage('list_category_noexist', dreferer());
        }
        require_once libfile('function/portalcp');
        $categoryperm = getallowcategory($_G['uid']);
        if ($cat['closed'] && !$_G['group']['allowdiy'] && !$categoryperm[$catid]['allowmanage']) {
            showmessage('list_category_is_closed', dreferer());
        }

        if (!isset($_G['makehtml'])) {
            if (!empty($cat['url']))
                dheader('location:' . $cat['url']);
            if (defined('SUB_DIR') && $_G['siteurl'] . substr(SUB_DIR, 1) != $cat['caturl'] || !defined('SUB_DIR') && $_G['siteurl'] != substr($cat['caturl'], 0, strrpos($cat['caturl'], '/') + 1)) {
                dheader('location:' . $cat['caturl'], '301');
            }
        }

        $cat = category_remake($catid);
        $navid = 'mn_P' . $cat['topid'];
        foreach ($_G['setting']['navs'] as $navsvalue) {
            if ($navsvalue['navid'] == $navid && $navsvalue['available'] && $navsvalue['level'] == 0) {
                $_G['mnid'] = $navid;
                break;
            }
        }
        $page = max(1, intval($_GET['page']));
        foreach ($cat['ups'] as $val) {
            $cats[] = $val['catname'];
        }

        $bodycss = array($cat['topid'] => 'pg_list_' . $cat['topid']);
        if ($cat['upid']) {
            $bodycss[$cat['upid']] = 'pg_list_' . $cat['upid'];
        }
        $bodycss[$cat['catid']] = 'pg_list_' . $cat['catid'];
        $cat['bodycss'] = implode(' ', $bodycss);

        $catseoset = array(
            'seotitle' => $cat['seotitle'],
            'seokeywords' => $cat['keyword'],
            'seodescription' => $cat['description']
        );
        $seodata = array('firstcat' => $cats[0], 'secondcat' => $cats[1], 'curcat' => $cat['catname'], 'page' => intval($_GET['page']));
        list($navtitle, $metadescription, $metakeywords) = get_seosetting('articlelist', $seodata, $catseoset);
        if (!$navtitle) {
            $navtitle = Helper_SEO::get_title_page($cat['catname'], $_G['page']);
            $nobbname = false;
        } else {
            $nobbname = true;
        }
        if (!$metakeywords) {
            $metakeywords = $cat['catname'];
        }
        if (!$metadescription) {
            $metadescription = $cat['catname'];
        }

        if (isset($_G['makehtml'])) {
            Helper_MakeHTML::portal_list($cat);
        }

        $file = 'portal/list:' . $catid;
        $tpldirectory = '';
        $primaltplname = $cat['primaltplname'];
        if (strpos($primaltplname, ':') !== false) {
            list($tpldirectory, $primaltplname) = explode(':', $primaltplname);
        }
        include template('diy:' . $file, NULL, $tpldirectory, NULL, $primaltplname);
    }

    

}
