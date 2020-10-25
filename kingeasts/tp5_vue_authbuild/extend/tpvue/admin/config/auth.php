<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: auth.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------

return [
    // 不验证权限的控制器,区分大小写
    'no_check_controller'=>[
        'admin/index/home',
        'admin/admin/delCache',
        'admin/login/loginOut',
        'admin/access/manager/resetPassword',
        'admin/file/upload'
    ]
];