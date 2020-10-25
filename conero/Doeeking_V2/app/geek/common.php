<?php
// 模块公共函数 - 导航条生成器
function  geek_navBar($view,$page)
{    
    $name = request()->controller();
    //$view = $page->view;
    $name = strtolower($name);
    $navsTpl = [
        "index" => ["text"=>"概述","url"=>"/conero/geek.html"],
        "project" => ["text"=>"项目","url"=>"/conero/geek/project.html"],
        "lang" => ["text"=>"语言","url"=>"/conero/geek/lang.html"],
    ];    
    $name = array_key_exists($name,$navsTpl)? $name:'';
    $nav = '';
    foreach($navsTpl as $k=>$v){
        $nav .= '<li role="presentation" '.($k == $name? 'class="active"':'').'><a href="'.$v['url'].'">'.$v['text'].'</a></li>';
    }
    // 动态导航栏
    $dynamicNavPlus = property_exists($page,'dynamicNavPlus')? $page->dynamicNavPlus:null;
    if(empty($dynamicNavPlus)) $dynamicNavPlus = '';
    elseif(is_array($dynamicNavPlus)) $nav .= '<li role="presentation" class="active"><a href="'.($dynamicNavPlus['url']? $dynamicNavPlus['url']:'javascript:void(0);').'">'.$dynamicNavPlus['text'].'</a></li>';
    elseif(is_string($dynamicNavPlus)) $nav .= $dynamicNavPlus;

    $footer = '
        <footer>
            <div class="container">
                <p class="text-center">网站访问统计： '.($page->aboutVisit()).'</p>
                <p class="text-center">&copy; 2014-'.date('Y').' Joshua Evan Ellis Warner Conero Doeeking Yang, Inc.</p>
            </div>
        </footer>
    ';
    $view->assign('geekFooter',$footer);
    $view->assign('geekNavBar',$nav);    
}