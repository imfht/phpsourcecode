<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Page.class.php 110 2016-04-27 03:05:36Z lixiaohui $
 *  @created    2015-10-10
 *  分页
 * =============================================================================                   
 */

namespace org;

class Page
{

    private $total;  //总记录
    private $pagesize; //每页显示多少条
    private $limit;  //limit
    private $page;  //当前页码
    private $pagenum;  //总页码
    private $url;  //地址
    private $bothnum;  //两边保持数字分页的量
    private $query_fu = '&';

    //构造方法初始化

    public function __construct($_total, $_pagesize=12,$param='')
    {
        if (empty($_SERVER['QUERY_STRING'])) {
            $this->query_fu = '?';
        }
        $this->total = $_total ? $_total : 1;
        $this->pagesize = $_pagesize;
        $this->pagenum = ceil($this->total / $this->pagesize);
        $this->page = $this->setPage();
        $this->limit = ($this->page - 1) * $this->pagesize . ",$this->pagesize";
        $this->url = $this->setUrl();
        $this->bothnum = 2;
    }

    //拦截器
    function __get($_key)
    {
        return $this->$_key;
    }

    //获取当前页码
    private function setPage()
    {
        $page = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $page = $page < 1 ? 1 : $page;
        $page = $page > $this->pagenum ? $this->pagenum : $page;
        return $page;
    }

    //获取地址
    private function setUrl()
    {
        $_url = $_SERVER["REQUEST_URI"];
        $_par = parse_url($_url);
        if (isset($_par['query'])) {
            parse_str($_par['query'], $_query);
            unset($_query['page']);
            if (!empty($_query)) {
                $_url = $_par['path'] . '?' . http_build_query($_query) . '&';
            } else {
                $_url = $_par['path'] . '?' . http_build_query($_query);
            }
        } else {
            $_url = $_par['path'] . '?';
        }
        return $_url;
    }

//数字目录

    private function pageList()
    {
        $_pagelist = '';
        for ($i = $this->bothnum; $i >= 1; $i--) {
            $_page = $this->page - $i;
            if ($_page < 1)
                continue;
            $_pagelist .= ' <a href="' . $this->url . 'page=' . $_page . '">' . $_page . '</a> ';
        }
        $_pagelist .= ' <span class="active">' . $this->page . '</span> ';
        for ($i = 1; $i <= $this->bothnum; $i++) {
            $_page = $this->page + $i;
            if ($_page > $this->pagenum)
                break;
            $_pagelist .= ' <a href="' . $this->url . 'page=' . $_page . '">' . $_page . '</a> ';
        }
        return $_pagelist;
    }

    //首页
    private function first()
    {
        if ($this->page > $this->bothnum + 1) {
            return ' <a href="' . $this->url . '">1</a><span class="omit">...</span>';
        }
    }

    //上一页
    private function prev()
    {
        if ($this->page == 1) {
            return '<span class="disabled">上一页</span>';
        }
        return ' <a href="' . $this->url . 'page=' . ($this->page - 1) . '">上一页</a> ';
    }

    //下一页
    private function next()
    {
        if ($this->page == $this->pagenum) {
            return '<span class="disabled">下一页</span>';
        }
        return ' <a href="' . $this->url . 'page=' . ($this->page + 1) . '">下一页</a> ';
    }

    //尾页
    private function last()
    {
        if ($this->pagenum - $this->page > $this->bothnum) {
            return ' <span class="omit">...</span><a href="' . $this->url . 'page=' . $this->pagenum . '">' . $this->pagenum . '</a> ';
        }
    }

    //分页信息
    public function showpage()
    {
        $_page = '';
        $_page .= ' <a href="javascript:void(0)">共' . $this->total . '条</a>';
        $_page .= $this->first();
        $_page .= $this->pageList();
        $_page .= $this->last();
        $_page .= $this->prev();
        $_page .= $this->next();
        return $_page;
    }

}
