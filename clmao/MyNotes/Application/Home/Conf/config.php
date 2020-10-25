<?php

return array(
    'HTML_CACHE_ON' => true, 
    'HTML_CACHE_TIME' => 60, 
    'HTML_READ_TYPE' => 0,
    'HTML_FILE_SUFFIX' => '.tpl',
    'HTML_CACHE_RULES' => array(
        'content' => array('{:module}_{:controller}_{:action}_{id}',-1),
        'index' => array('{:module}_{:controller}_{:action}_{p}',-1),
        'category' => array('{:module}_{:controller}_{:action}_{id}_{p}',-1),
    ),
);