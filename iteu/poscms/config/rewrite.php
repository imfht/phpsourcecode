<?php

/**
 * Dayrui Website Management System
 * 
 * @since			version 2.0.0
 * @author			Dayrui <dayrui@gmail.com>
 * @license     	http://www.dayrui.com/license
 * @copyright		Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * 这里由开发者自定义伪静态规则,放在下面括号里面
 */
 

return array(


    /*-------------------站点[1] 站点URL规则 开始-----------------*/

    // 网站地图
    "sitemap.html"                                              =>	"sitemap/index",
    // 全模块搜索分页
    "so-(.+).html"                                              =>	"so/index/rewrite/$1",
    // 全模块搜索
    "so.html"                                                   =>	"so/index",
    // 共享模块搜索分页
    "search\/(.+).html"                                         =>	"search/index/rewrite/$1",
    // 共享模块搜索
    "search.html"                                               =>	"search/index",

    /*-------------------站点[1] 站点URL规则 结束-----------------*/



);

