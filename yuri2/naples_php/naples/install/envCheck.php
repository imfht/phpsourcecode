<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/3/16
 * Time: 13:58
 * 检测环境是否满足要求
 */

if (function_exists('opcache_reset')) {
    opcache_reset();
}
include __DIR__ . '/probe.php';