<?php

use \Madphp\View;

/**
 * 函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

function render($tpl, $data = array())
{
    return View::fetch($tpl, $data);
}