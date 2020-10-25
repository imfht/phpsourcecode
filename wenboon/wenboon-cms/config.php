<?php return array (
  'DB_HOST' => '127.0.0.1',
  'DB_USER' => 'root',
  'DB_PWD' => '',
  'DB_NAME' => 'wenboon',
  'DB_PREFIX' => 'wb_',
  'TMPL_TEMPLATE_SUFFIX' => '.html',
  'SESSION_TYPE' => 'Db',
  'RBAC_SUPERADMIN' => 'admin',
  'ADMIN_AUTH_KEY' => 'superadmin',
  'USER_AUTH_ON' => true,
  'USER_AUTH_TYPE' => 1,
  'USER_AUTH_KEY' => 'uid',
  'NOT_AUTH_MODULE' => '',
  'NOT_AUTH_ACTION' => 'addUserHandle,modifyUserHandle,addRoleHandle,modifyRoleHandle,accessHandle,addNodeHandle,addhandle,modifyhandle,attachment,attachmentorders,attachmentdel,review,reviewdel,reviewshow,iddhandle,imodifyhandle,aaddhandle,amodifyhandle,handle,getfields,mHandle,addfieldHandle,modifyfieldHandle,siteHandle',
  'RBAC_ROLE_TABLE' => 'wb_role',
  'RBAC_USER_TABLE' => 'wb_role_user',
  'RBAC_ACCESS_TABLE' => 'wb_access',
  'RBAC_NODE_TABLE' => 'wb_node',
  'URL_CASE_INSENSITIVE' => true,
  'TAG_NESTED_LEVEL' => 5,
  'URL_MODEL' => 1,
  'URL_HTML_SUFFIX' => 'html',
  'APP_AUTOLOAD_PATH' => '@.TagLib',
  'TAGLIB_PRE_LOAD' => 'Hd',
  'TAGLIB_BUILD_IN' => 'Cx,Hd',
  'TMPL_ACTION_ERROR' => 'error/dispatch_jump',
  'TMPL_ACTION_SUCCESS' => 'error/dispatch_jump',
  'M_INSTALL' => 1,
  'M_VER' => '1.3.2',
  'M_DEBUG' => 1,
  'HTML_CACHE_ON' => 0,
  'HTML_FILE_SUFFIX' => '.shtml',
  'HTML_CACHE_RULES' => 
  array (
    'Wenbon:a' => 
    array (
      0 => '{:module}_{:action}_{cid}{t}_{id}',
      1 => 0,
    ),
    'Wenbon:b' => 
    array (
      0 => '{:module}_{:action}_{catid}{t}_{itemid}',
      1 => 0,
    ),
    'Wenbon:c' => 
    array (
      0 => '{:module}_{:action}_{catid}',
      1 => 0,
    ),
    'Index:index' => 
    array (
      0 => '{:module}_{:action}',
      1 => 0,
    ),
  ),
  'URL_ROUTER_ON' => 1,
  'URL_ROUTE_RULES' => 
  array (
    'cat/:catid\\d' => 'Wenbon/b',
    'cat/:catid\\s' => 'Wenbon/c',
    ':t/:itemid\\d' => 'Wenbon/b',
  ),
);