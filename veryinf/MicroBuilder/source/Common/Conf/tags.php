<?php
/**
 * 行为嵌入点定义
 */
$tags = array();
$tags['app_init'][] = 'Common\Behavior\AppInitBehavior';

if(defined('IN_APP') && IN_APP === true) {
    $tags['view_begin'][]       = 'App\Behavior\AuthBehavior';
} else {
    $tags['view_begin'][]       = 'Common\Behavior\WebTemplateBehavior';
    $tags['action_begin'][]     = 'Common\Behavior\WebAuthBehavior';
}

return $tags;
