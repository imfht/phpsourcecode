<?php
/*
 * template_lite plugin
 *
 * Type:     function
 * Name:     vars
 * Version:  0.1
 * Examples:
 */
function tpl_function_vars($params, &$tpl){
    $key = $params['key'];
    $array = $tpl->_vars[$key];

    $askey = $params['assign']?:'n'. $key;
    $params['as'] && $askey = $params['as'];

    $tpl->assign($askey,$array);
}
