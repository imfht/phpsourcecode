<?php
/**
 * 用于判断是否为今天还是昨天
 * @param string $publishDate
 */
function smarty_modifier_time($publishDate)
{
    if (empty($publishDate)) {
        
        return false;
    }
    $publishDate=date("Y-m-d",$publishDate);
    $curDate = date("Y-m-d");
    $yesterday = date("Y-m-d", strtotime('-1 day'));
    $publishDate = substr($publishDate, 0, 10);
    
    if ($curDate === $publishDate) {
        return 1;
    } else if ($yesterday === $publishDate) {
        return 2;
    } else {
        return 3;
    }
}

