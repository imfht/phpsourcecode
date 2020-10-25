<?php
return array(
    'app_begin' => array('Behavior\CheckLangBehavior'),
    'app_init' => array('Common\Behavior\InitHookBehavior'),
    'action_begin' => array('Common\Behavior\InitModuleInfoBehavior'),
);