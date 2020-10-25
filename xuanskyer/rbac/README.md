# rbac

RBAC component for PHP applications.

权限管理组件

## 安装
* 在项目的composer.json文件中的require项中添加：

```
"furthestworld/rbac": "~1.0"
```
并更新composer依赖：`composer update`

* 在需要使用RBAC服务的地方添加：

```

require_once __ROOT__ . '/vendor/autoload.php';
use Rbac\RbacService;
```

* 初始化RBAC服务：

```
$db_config = [
    "dbtype" = mysql,
    "host" = "mysql host",
    "port" = "mysql port",
    "username" = "mysql username",
    "password" = "mysql password",
    "dbname" = "your db name",
    "charset" => "utf8"
];
$rbac_config = [
    //rbac认证配置
    'USER_AUTH_ON'      => true,
    'USER_AUTH_TYPE'    => 2,                  // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'     => 'user_auth_key',    // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'    => 'administrator',
    'ADMIN_USER_ID'     => 22,                 //超级管理员ID
    'RBAC_ROLE_TABLE'   => 'rbac_role',         
    'RBAC_USER_TABLE'   => 'rbac_role_user',
    'RBAC_ACCESS_TABLE' => 'rbac_access',
    'RBAC_NODE_TABLE'   => 'rbac_node',
];

//$cacheService = new RbacCacheService();   //如果需要缓存，则传入实例化后的第三方缓存服务对象

RbacService::init($db_config, $rbac_config, $cacheService);
```

* enjoy~ :)

> RBAC对应数据表： `src/data/rbac.sql`
