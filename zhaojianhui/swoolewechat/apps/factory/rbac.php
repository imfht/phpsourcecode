<?php
/**
 * 权限组件
 */
global $php;
$config = $php->config['rbac'];
return new App\Component\RBAC($config);