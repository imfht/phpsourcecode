# 运营平台后端接口

运营平台的接口，主要的调用方是运营平台前端，因此和产品的客户端调用在使用对象、鉴权方式和使用场景都不同，因此PhalApi框架为此专门添加了一顶级的命令空间Portal。

所以，运营平台需要的后台接口，都应放Portal大分类下。如下图所示。
 
![](http://cdn7.okayapi.com/yesyesapi_20200309213546_4a69cc347feddc5e25e90e4c6180de61.png)

对应的源代码路径是：```src/portal```目录下。同样分为Api、Domain、Model、Common这几个目录。 

```bash
$ tree ./src/portal 
./src/portal
├── Api
│   ├── Admin.php
│   ├── CURD.php
│   └── Page.php
├── Common
│   ├── Admin.php
│   ├── Api.php
│   └── DataApi.php
├── Domain
│   ├── Admin.php
│   └── Menu.php
└── Model
    ├── Admin.php
    ├── CURD.php
    └── Menu.php

4 directories, 11 files
```

## 运营平台的管理员

对于运营平台，首先需要介绍的是运营平台的管理员。在安装时会提示初始化管理员的账号和密码。管理员数据存放在```phalapi_portal_admin```中，分普通管理员和超级管理员。  

如果需要新建管理员，可以使用```./bin/phalapi-create-portal-admin.php```脚本命令创建，例如：
```
$ php ./bin/phalapi-create-portal-admin.php                           
Usage: ./bin/phalapi-create-portal-admin.php <username> <password> [role=admin|super]

$ php ./bin/phalapi-create-portal-admin.php demo 123456
运营平台管理员账号创建成功！

$ php ./bin/phalapi-create-portal-admin.php demo 123456
运营平台管理员账号已存在，不能重复创建！
```

## DI中的admin服务与接口

注意到在./config/di.php注入配置文件中，往容器中注册添加了admin服务。  
```
// portal后台管理员
$di->admin = new Portal\Common\Admin();
```

随后，在你有需要的地方，你可以使用以下接口：  
 + ```\PhalApi\DI()->admin->login($id, $username, $role)```，登录接口，在成功登录后开启session会话并纪录管理员信息
 + ```\PhalApi\DI()->admin->logout()，退出当前管理员会话```
 + ```\PhalApi\DI()->admin->check($isStopIfNoLogin = TRUE)```，检测管理员是否登录
 + ```\PhalApi\DI()->admin->id```，获取当前管理员ID，只读不写
 + ```\PhalApi\DI()->admin->username```，获取当前管理员账号，只读不写
 + ```\PhalApi\DI()->admin->role```，获取当前管理员角色（super或admin或其他自定义的角色名），只读不写

默认下，admin会话是基于服务端的SESSION实现的。

## 运营平台接口基类

如前文所述，所有运营平台的接口，都应放置在Portal顶级命名空间下，方便统一管理、维护和查找。同时，全部的运营平台接口Api实现子类都应继承于```Portal\Common\Api```运营平台接口基类。在此接口基类中，会自动进行管理员的登录态检测。  

例如：  
```php
<?php
namespace Portal\Api;
use Portal\Common\Api;

/**
 * 运营平台接口
 */
class Page extends Api {
}
```

## 运营平台数据接口基类

如果需要实现对数据库表格的数据管理，进行常见的增删改查操作，那么可以让你的Api接口类直接继承```Portal\Common\DataApi```基类。继承后便可自动拥有与前端模板自动匹配的数据接口API。例如：  

![](http://cdn7.okayapi.com/yesyesapi_20200309215534_a6fd104082107ae6b9eab6d97e85feea.png)

上面有5个数据接口（后面会进一步扩展）：  
 + 创建新数据，Portal.CURD.CreateData
 + 批量删除，Portal.CURD.DeleteDataIDs
 + 获取一条数据，Portal.CURD.GetData
 + 获取表格列表数据，Portal.CURD.TableList
 + 更新数据，Portal.CURD.UpdateData

而背后只需要的编写的PHP代码主要分为两步。  

第一步，编写Api子类，继承于```Portal\Common\DataApi```基类，并必须重载```getDataModel()```方法，返回对应数据库表的Model子类。  

```php
<?php
namespace Portal\Api;
use Portal\Common\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function getDataModel() {
        return new \Portal\Model\CURD();
    }
}
```

类名是自定义，其他数据接口则是自动由基类继承而来。  

第二步，编写Model子类，须继承于```PhalApi\Model\DataModel```框架的DataModel数据基类，并且指定表名（如果表名与自动匹配的表名一样则可不手动设置）。  

```php
<?php
namespace Portal\Model;
use PhalApi\Model\DataModel;

class CURD extends DataModel {
    public function getTableName($id) {
        return 'phalapi_curd';
    }
}
```

## 定制你的数据接口

考虑```Portal\Common\DataApi```基类当前刚上线，近期会根据项目中的情况和需求继续丰富和扩展，因此建议继承后若需要添加新的接口时，命名可以特色化一下，避免日后框架升级时有方法名冲突，影响项目的正常使用。  

此外，当前也提供了一些接口可以进行简单的配置。下面分别介绍。  

### 获取表格列表数据

> 前端调用的接口是：```Portal.{Api类名}.TableList```

内部PHP配置接口：  
```php
// 列表返回的字段
protected function getTableListSelect() {
    return '*';
}

// 列表的默认排序
protected function getTableListOrder() {
    return 'id DESC';
}

// 取到列表数据后的加工处理
protected function afterTableList($items) {
    return $items;
}
```
当需要定制时，只需要重载并实现即可。下同。

### 创建新数据

> 前端调用的接口是：```Portal.{Api类名}.CreateData```

内部PHP配置接口：
```php
// 必须提供的字段
protected function createDataRequireKeys() {
    return array();
}

// 不允许客户端写入的字段
protected function createDataExcludeKeys() {
    return array();
}

// 创建时更多初始化的数据
protected function createDataMoreData($newData) {
    return $newData;
}
```

举个例子，若需要在创建数据时自动加上服务器当前的时间，那么可以这样配置。  
```php
<?php
namespace Portal\Api;
use Portal\Common\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function getDataModel() {
        return new \Portal\Model\CURD();
    }

    // 追加发布时间
    protected function createDataMoreData($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s');
        return $newData;
    }
}
```

### 批量删除

> 前端调用的接口是：```Portal.{Api类名}.TableList```

暂无定制配置接口。


### 获取一条数据

前端调用的接口是：```Portal.{Api类名}.GetData```

内部PHP配置接口：
```php
// 获取单个数据时需要返回的字段
protected function getDataSelect() {
    return '*';
}

// 取到数据后的加工处理
protected function afterGetData($data) {
    return $data;
}
```

### 更新数据

> 前端调用的接口是：```Portal.{Api类名}.UpdateData```

```php
// 更新时必须提供的字段
protected function updateDataRequireKeys() {
    return array();
}

// 更新时不允许更新的字段
protected function updateDataExcludeKeys() {
    return array();
}

protected function beforeUpdateData($updateData) {
    return $updateData;
}
```

如果需要调整接口参数，以及返回结果，也可以直接重载对外提供的接口和方法。


