<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$

class searchAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        global $userinfo;
        $userinfo = $this->visitor->info;
        global $searchword;
        $searchword1 = $this->_request('searchword', trim);
        $searchword = urlencode($searchword1);
        $this->assign('searchword', $searchword1);
        $seoconfig = $this->_config_seo(C('wkcms_seo_config.search'));
        $seoconfig['title'] = $searchword1 . '-' . $seoconfig['title'];
        $this->assign('seoconfig', $seoconfig);
    }
    protected function _search() {
        //文档分类，自动查询，以便列表页和其他页面调用
        $cate = D('doc_cate')->where(array(
            'pid' => 0,
            'status' => 1
        ))->order('ordid')->select();
        foreach ($cate as $key => $value) {
            $mapcate['pid'] = array(
                'eq',
                $value['id']
            );
            $mapcate['status'] = 1;
            $cate[$key]['tcate'] = D('doc_cate')->where($mapcate)->order('ordid')->select();
            foreach ($cate[$key]['tcate'] as $key1 => $value1) {
                $mapcate1['pid'] = array(
                    'eq',
                    $value1['id']
                );
                $mapcate1['status'] = 1;
                $cate[$key]['tcate'][$key1]['scate'] = D('doc_cate')->where($mapcate1)->order('ordid')->select();
            }
        }
        $this->assign('cate', $cate); //所有分类，首页只取前八个大类
        $lm = $this->_request('lm', 'intval');
        $score = $this->_request('score', 'intval');
        $time = $this->_request('time', 'intval');
        $this->assign('search', array(
            'lm' => $lm,
            'time' => $time,
            'score' => $score,
        ));
        if ($lm) {
            switch ($lm) {
                case 1:
                    $ext = 'doc,docx,wps';
                    break;

                case 2:
                    $ext = 'pdf';
                    break;

                case 3:
                    $ext = 'ppt,pptx,dpt';
                    break;

                case 4:
                    $ext = 'xls,xlsx,et';
                    break;

                case 5:
                    $ext = 'txt';
                    break;

                default:
                    break;
            }
        }
        if ($score == 1) {
            $maxscore = 0;
        } elseif ($score == 2) {
            $minscore = 1;
        }
        $this->assign('ext', $ext);
        $this->assign('day', $time);
        $this->assign('minscore', $minscore);
        $this->assign('maxscore', $maxscore);
        return $data;
    }
    public function index() {
        $mod1 = D('doc_con');
        $data = $this->_search();
        global $searchword;
        if ($searchword == '') {
            $this->error('请输入查询关键字');
        }
        $searchword = urldecode($searchword);
        $this->assign('data', $data);
        $this->display();
    }
}

