<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2019 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*
* '路由标识' => array (
*    0 => '伪静态链接',
*    1 => '动态链接',
*  )
*/
defined('iPHP') OR exit('What are you doing?');

return '{
    "forms":[
        "/forms",
        "api.php?app=forms"
    ],
    "forms:save":[
        "/forms/save",
        "api.php?app=forms&do=save"
    ],
    "forms:id":[
        "/forms/{id}/",
        "api.php?app=forms&id={id}"
    ]
}';