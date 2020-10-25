<?php
/**
 * Smarty文章内容截取插件
 *
 * @package Smarty
 * @author chengxuan <i@chengxuan.li>
 */
function smarty_modifier_truncate_article($string, $width=400) {
    return Comm\Str::truncateSummary($string, $width);
}
