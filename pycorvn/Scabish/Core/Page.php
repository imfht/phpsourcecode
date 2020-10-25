<?php
namespace Scabish\Core;

use SCS;

/**
 * Scabish\Core\Page
 * 分页导航显示类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-02-06
 */
class Page {
    
    public $current; // 当前页码
    public $total = 0; // 记录总数
    public $size = 10; // 每页显示记录数
    public $column = 5; // 导航显示最大分页栏数
    
    private static $_instance;
    
    public function __clone() {}
    
    public static function Instance($page = 0) {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            self::$_instance->SetCurrent($page);
        }
        return self::$_instance;
    }
    
    public function SetCurrent($page) {
        $this->current = (intval($page) > 1) ? intval($page) : 1;
    }
    
    /**
     * 获取分页导航串形如：<< 2 3 4 5 ６ 7 8 9 10 11 >>
     * 可自定义每一项页码html格式，每一项必须包含{url}、{page}、{active}占位符，
     * {url}表示当前项url，{page}表示当前项页码，{active}表示当前项为激活项
     * SCS::Page()->Create('<li class="{active}"><a href="{url}">{page}</a></li>');
     * @param string $template 分页html模板
     * @return string
     */                                    
    public function Create($total = 0, $size = 0) {
        $total = $total ? : $this->total;
        $size = $size ? : $this->size;
        $template = '<li class="{active}"><a href="{url}">{page}</a></li>';
        $url = SCS::Url()->Rebuild();
        $totalPage = ceil($total/$this->size);
        $numToShow = min($totalPage - 1, $this->column - 1);
        $firstIdx = max(1, $this->current - floor($numToShow/2));
        if($firstIdx + $numToShow > $totalPage) {
            $firstIdx = max(1, $totalPage - $numToShow);
        }
        if($totalPage <= 1) return '';
        $links = preg_replace(array('/{url}/i', '/{page}/i', '/{active}/i'), 
            array($url.'/page/1', '&lt;&lt;', (1 == $this->current) ? 'active' : ''), $template);
        for($i = $firstIdx; $i <= $numToShow + $firstIdx; $i++) {
            $links .= preg_replace(array('/{url}/i', '/{page}/i', '/{active}/i'), 
                array($url.'/page/'.$i, $i, ($i == $this->current) ? 'active' : ''), $template);
        }
        $links .= preg_replace(array('/{url}/i', '/{page}/i', '/{active}/i'), 
            array($url.'/page/'.$totalPage, '&gt;&gt;', ($totalPage == $this->current) ? 'active' : ''), $template);
        $links .= '<li><a href="javascript:;" style="color:#ccc;"><strong>'.$total.' records</a></strong></li>';
        return $links;
    }
    
}