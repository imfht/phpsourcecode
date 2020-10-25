<?php
/**
 * 分页插件修饰器
 *
 * @package Smarty
 * @author chengxuan <i@chengxuan.li>
 */
function smarty_modifier_page_number($pager) {
    $page_number_max = 10;
    
    
    //只对Pager对象进行操作
    if(!($pager instanceof Comm\Pager)) {
        return '';
    }
    
    $result = '<nav class="pagination-nav">';
    $result .= '<ul class="pagination">';
    
    //确定起始页页码
    $start = intval($pager->page - ($page_number_max / 2));
    if($start + $page_number_max - 1 > $pager->page_total) {
        $start = $pager->page_total - $page_number_max + 1;
    }
    $start = max($start, 1);
    
    //确定结束页页码
    $end = $start + $page_number_max - 1;
    $end = min($end, $pager->page_total);
    
    //上一页和首页
    if($start != 1) {
        $result .= '<li><a href="'. $pager->showHref(1) .'" aria-label="Previous"> <span aria-hidden="true">«</span> </a></li>';
        $result .= '<li><a href="'. $pager->showHref($pager->page - 1) .'" aria-label="Previous"> <span aria-hidden="true">&lt;</span> </a></li>';
    }
    
    //中间循环几页
    for($i = $start; $i <= $end; ++$i) {
        $result .= '<li';
        if($i == $pager->page) {
            $result .= ' class="active"';
        }
        $result .= '>';
        
        $result .= '<a href="'. $pager->showHref($i) .'">' . $i . '</a>';
        $result .= '</li>';
    }
    
    
    //下一页和末页
    if($end != $pager->page_total) {
        $result .= '<li><a href="'. $pager->showHref($pager->page + 1) .'" aria-label="Next"> <span aria-hidden="true">&gt;</span> </a></li>';
        $result .= '<li><a href="'. $pager->showHref($pager->page_total) .'" aria-label="Next"> <span aria-hidden="true">»</span> </a></li>';
        
    }
    
    $result .= '</ul>';
    $result .= '</nav>';
    
    return $result;
}

