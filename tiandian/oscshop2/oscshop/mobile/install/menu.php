<?php
$menu=array (
  0 => 
  array (
    'id' => 211,
    'module' => 'mobile',
    'pid' => 0,
    'title' => '移动端',
    'url' => '',
    'icon' => 'fa-mobile fa-lg',
    'sort_order' => 8,
    'type' => 'nav',
    'status' => 1,
    'children' => 
    array (
      0 => 
      array (
        'id' => 212,
        'module' => 'mobile',
        'pid' => 211,
        'title' => '代理分销',
        'url' => '',
        'icon' => '',
        'sort_order' => 1,
        'type' => 'nav',
        'status' => 1,
        'children' => 
        array (
          0 => 
          array (
            'id' => 213,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '代理管理',
            'url' => 'mobile/agent_backend/agent_list',
            'icon' => '',
            'sort_order' => 2,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 225,
                'module' => 'mobile',
                'pid' => 213,
                'title' => '编辑',
                'url' => 'mobile/agent_backend/edit_agent',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
          1 => 
          array (
            'id' => 214,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '代理审核',
            'url' => 'mobile/agent_backend/index',
            'icon' => '',
            'sort_order' => 1,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 224,
                'module' => 'mobile',
                'pid' => 214,
                'title' => '审核',
                'url' => 'mobile/agent_backend/pass',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
          2 => 
          array (
            'id' => 215,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '代理级别',
            'url' => 'mobile/agent_backend/level',
            'icon' => '',
            'sort_order' => 3,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 226,
                'module' => 'mobile',
                'pid' => 215,
                'title' => '新增',
                'url' => 'mobile/agent_backend/add_level',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
              1 => 
              array (
                'id' => 227,
                'module' => 'mobile',
                'pid' => 215,
                'title' => '编辑',
                'url' => 'mobile/agent_backend/edit_level',
                'icon' => '',
                'sort_order' => 2,
                'type' => 'auth',
                'status' => 1,
              ),
              2 => 
              array (
                'id' => 228,
                'module' => 'mobile',
                'pid' => 215,
                'title' => '删除',
                'url' => 'mobile/agent_backend/del_level',
                'icon' => '',
                'sort_order' => 3,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
          3 => 
          array (
            'id' => 216,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '提现申请',
            'url' => 'mobile/cash_backend/cash_apply',
            'icon' => '',
            'sort_order' => 4,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 229,
                'module' => 'mobile',
                'pid' => 216,
                'title' => '通过',
                'url' => 'mobile/cash_backend/pass_cash_apply',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
          4 => 
          array (
            'id' => 217,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '提现记录',
            'url' => 'mobile/cash_backend/cash_record',
            'icon' => '',
            'sort_order' => 5,
            'type' => 'nav',
            'status' => 1,
          ),
          5 => 
          array (
            'id' => 218,
            'module' => 'mobile',
            'pid' => 212,
            'title' => '分享记录',
            'url' => 'mobile/agent_backend/share',
            'icon' => '',
            'sort_order' => 6,
            'type' => 'nav',
            'status' => 1,
          ),
        ),
      ),
      1 => 
      array (
        'id' => 219,
        'module' => 'mobile',
        'pid' => 211,
        'title' => '自动回复',
        'url' => '',
        'icon' => '',
        'sort_order' => 2,
        'type' => 'nav',
        'status' => 1,
        'children' => 
        array (
          0 => 
          array (
            'id' => 221,
            'module' => 'mobile',
            'pid' => 219,
            'title' => '文字回复',
            'url' => 'mobile/reply_backend/text',
            'icon' => '',
            'sort_order' => 1,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 230,
                'module' => 'mobile',
                'pid' => 221,
                'title' => '新增',
                'url' => 'mobile/reply_backend/text_add',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
              1 => 
              array (
                'id' => 231,
                'module' => 'mobile',
                'pid' => 221,
                'title' => '编辑',
                'url' => 'mobile/reply_backend/text_edit',
                'icon' => '',
                'sort_order' => 2,
                'type' => 'auth',
                'status' => 1,
              ),
              2 => 
              array (
                'id' => 232,
                'module' => 'mobile',
                'pid' => 221,
                'title' => '删除',
                'url' => 'mobile/reply_backend/text_del',
                'icon' => '',
                'sort_order' => 3,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
          1 => 
          array (
            'id' => 222,
            'module' => 'mobile',
            'pid' => 219,
            'title' => '图文回复',
            'url' => 'mobile/reply_backend/news',
            'icon' => '',
            'sort_order' => 2,
            'type' => 'nav',
            'status' => 1,
            'children' => 
            array (
              0 => 
              array (
                'id' => 233,
                'module' => 'mobile',
                'pid' => 222,
                'title' => '新增',
                'url' => 'mobile/reply_backend/news_add',
                'icon' => '',
                'sort_order' => 1,
                'type' => 'auth',
                'status' => 1,
              ),
              1 => 
              array (
                'id' => 234,
                'module' => 'mobile',
                'pid' => 222,
                'title' => '编辑',
                'url' => 'mobile/reply_backend/news_edit',
                'icon' => '',
                'sort_order' => 2,
                'type' => 'auth',
                'status' => 1,
              ),
              2 => 
              array (
                'id' => 235,
                'module' => 'mobile',
                'pid' => 222,
                'title' => '删除',
                'url' => 'mobile/reply_backend/news_del',
                'icon' => '',
                'sort_order' => 3,
                'type' => 'auth',
                'status' => 1,
              ),
            ),
          ),
        ),
      ),
      2 => 
      array (
        'id' => 220,
        'module' => 'mobile',
        'pid' => 211,
        'title' => '自定义菜单',
        'url' => 'mobile/custom_menu/index',
        'icon' => '',
        'sort_order' => 3,
        'type' => 'nav',
        'status' => 1,
        'children' => 
        array (
          0 => 
          array (
            'id' => 236,
            'module' => 'mobile',
            'pid' => 220,
            'title' => '生成菜单',
            'url' => 'mobile/custom_menu/create_menu',
            'icon' => '',
            'sort_order' => 1,
            'type' => 'auth',
            'status' => 1,
          ),
          1 => 
          array (
            'id' => 237,
            'module' => 'mobile',
            'pid' => 220,
            'title' => '清空菜单',
            'url' => 'mobile/custom_menu/delete_menu',
            'icon' => '',
            'sort_order' => 2,
            'type' => 'auth',
            'status' => 1,
          ),
        ),
      ),
      3 => 
      array (
        'id' => 223,
        'module' => 'mobile',
        'pid' => 211,
        'title' => '配置管理',
        'url' => 'mobile/settings_backend/mobile',
        'icon' => '',
        'sort_order' => 4,
        'type' => 'nav',
        'status' => 1,
      ),
    ),
  ),
);
?>