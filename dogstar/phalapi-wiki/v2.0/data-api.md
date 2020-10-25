# DataApi通用数据接口

为了进一步实现接口低代码编程，为了能以更少的代码，实现更多的接口，满足更广业务需求，PhalApi结合多年的接口系统和项目开发经验，从PhalApi 2.13.0 版本起推出PhalApi\Api\DataApi通用数据接口。  

它的特色在于，可以针对单个数据库表提供一套完整的、常用的、基本的数据接口，以自动完成对数据库表的CURD基本操作，避免重复接口开发。  

## DataApi有哪些接口？

如果需要实现对数据库表的数据管理，进行常见的增删改查操作，那么可以让你的Api接口类直接继承```PhalApi\Api\DataApi```基类。继承后便可自动拥有一套基本的数据接口。  

目前有5个数据接口（后面会进一步扩展）：  
 + 创建新数据，```{命名空间}.{接口类名}.CreateData```
 + 批量删除，```{命名空间}.{接口类名}.DeleteDataIDs```
 + 获取一条数据，```{命名空间}.{接口类名}.GetData```
 + 获取表格列表数据，```{命名空间}.{接口类名}.TableList```
 + 更新数据，```{命名空间}.{接口类名}.UpdateData```

> 温馨提示：```PhalApi\Api\DataApi```基类当前刚发布，后续会继续扩展新的数据接口，若继承后有新加的接口方法，有可能会在日后升级PhalApi后有命名冲突。

上面中，```{命名空间}```和```{接口类名}```由开发者指定。例如你放在App命名空间下，创建的接口类名叫CURD，那么就会拥有```App.CURD.CreateData```等系列接口。

## 如何使用DataApi通用数据接口？

对于后端开发，使用DataApi通用数据接口需要完成以下4个步骤。  
 + 1、编写接口子类，继承```PhalApi\Api\DataApi```基类
 + 2、重载并实现```PhalApi\Api\DataApi::userCheck()```，完成用户身份验证
 + 3、重载并实现```PhalApi\Api\DataApi::getDataModel()```，完成数据模型Model子类实例的返回
 + 4、添加Model子类，并设计添加对应的数据库表

第一步，编写Api子类，继承于```PhalApi\Api\DataApi```基类，类名是自定义，其他数据接口则是自动由基类继承而来。  

```php
<?php
namespace App\Api;
use PhalApi\Api\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
}
```
> 温馨提示：```PhalApi\Api\DataApi```需要PhalApi 2.13.0 及以上版本支持。

这时候接口类还不能使用。

第二步，必须重载并实现```PhalApi\Api\DataApi::userCheck()```，完成用户身份验证。这样设计的目的，是让开发者明确知道需要对请求接口的客户端的用户身份进行验证，避免接口被非法调用，以免数据被非法操作。如果确实不需要进行身份验证，重载后可进行空操作。

```php
<?php
namespace App\Api;
use PhalApi\Api\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function userCheck() {
        // TODO 完成对用户身份的验证，例如登录账号、权限、会话等判断
        // 如果检测不通过，直接抛出PhalApi\Exception\BadRequestException异常，中止接口执行并返回错误信息
        // 如果确实不需要进行身份验证，重载后可进行空操作
    }
}
```

第三步，必须重载并实现```PhalApi\Api\DataApi::getDataModel()```，完成数据模型Model子类实例的返回。此方法返回的是对应数据库表的Model子类，以便接口可以进行绑定，知道需要通过Model实例来操作哪个数据库表。  
```php
<?php
namespace App\Api;
use PhalApi\Api\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function userCheck() {
        // TODO 记得要进行验证
    }

    protected function getDataModel() {
        return new \App\Model\CURD();
    }
}
```

PhalApi一直推荐使用ADM分层模式，这里暂时没有引入Domain层，原因是为了在使用这套基本的数据接口时减轻使用成本。但不排除会续会升级调整，支持Domain的可选引入。


第四步，编写Model子类，须继承于```PhalApi\Model\DataModel```框架的DataModel数据基类，并且指定表名（如果表名与自动匹配的表名一样则可不手动设置）。  

```php
<?php
namespace App\Model;
use PhalApi\Model\DataModel;

class CURD extends DataModel {
    public function getTableName($id) {
        return 'phalapi_curd';
    }
}
```

## 定制你的数据接口

考虑```PhalApi\DataApi```基类当前刚上线，近期会根据项目中的情况和需求继续丰富和扩展，因此建议继承后若需要添加新的接口时，命名可以特色化一下，避免日后框架升级时有方法名冲突，影响项目的正常使用。  

此外，当前也提供了一些接口可以进行简单的配置。下面分别介绍。  

### 获取表格列表数据

> 前端调用的接口是：```{命名空间}.{Api类名}.TableList```

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

// 查询条件
protected function getTableListWhere($where) {
    return $where;
}

// 取到列表数据后的加工处理
protected function afterTableList($items) {
    return $items;
}
```
当需要定制时，只需要重载并实现即可。下同。

### 创建新数据

> 前端调用的接口是：```{命名空间}.{Api类名}.CreateData```

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
protected function beforeCreateData($newData) {
    return $newData;
}
```

举个例子，若需要在创建数据时自动加上服务器当前的时间，那么可以这样配置。  
```php
<?php
namespace App\Api;
use PhalApi\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    // 前面代码略……

    // 追加发布时间
    protected function createDataMoreData($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s');
        return $newData;
    }
}
```

### 批量删除

> 前端调用的接口是：```{命名空间}.{Api类名}.TableList```

暂无定制配置接口。


### 获取一条数据

前端调用的接口是：```{命名空间}.{Api类名}.GetData```

内部PHP配置接口：
```php
// 获取单个数据时需要返回的字段
protected function getDataSelect() {
    return '*';
}

protected function getGetDataWhere($where) {
    return $where;
}

// 取到数据后的加工处理
protected function afterGetData($data) {
    return $data;
}
```

### 更新数据

> 前端调用的接口是：```{命名空间}.{Api类名}.UpdateData```

```php
// 更新时必须提供的字段
protected function updateDataRequireKeys() {
    return array();
}

// 更新时不允许更新的字段
protected function updateDataExcludeKeys() {
    return array();
}

// 获取更新数据的条件
protected function getUpdateDataWhere($where) {
    return $where;
}

protected function beforeUpdateData($updateData) {
    return $updateData;
}
```

如果需要调整接口参数，以及返回结果，也可以直接重载对外提供的接口和方法。

## 如何屏蔽不需要的接口？

如果不需要用到其中的部分接口，你可以通过重载方式来屏蔽。重载后添加```@ignore```注释，就可以隐藏不显示在在线接口列表文档，但通过接口详情页还是能直接访问。如果想进一步关闭接口功能，可以在重载后再抛出4xx系列异常。例如屏蔽创建数据的接口。  

```php
<?php
namespace App\Api;
use PhalApi\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    // 前面代码略……

    /**
     * @ignore
     */
    public function createData() {
        throw new \PhalApi\Exception\BadRequestException('此接口已关闭');
    }
}
```

## 如何修改接口文档？

通过重载的方式，继承原来的接口后，可以修改注释从而修改接口文档的接口标题、接口描述、接口返回、接口异常等内容。如果需要修改接口参数，那么可以在```getRules()```方法中先获取父类的参数规则，再加以调整，最后返回。  

例如：  
```php
class CURD extends Api {
    // 前面代码略……

    /**
     * 发布一篇新的博客文章
     * @desc 进行博客文章的发布，发布后内容进入待审状态
     * @return int id 新博客文章的ID
     */
    public function createData() {
        return parent::createData();
    }
}
```

## 如何处理统一返回格式？

如果需要对本系列接口的返回结果进行再加工处理，以便转换成需要的格式或结构，可重载以下方法。  
```php
/**
 * 返回数据结果
 * - 方便统一进行加工再处理
 * @param array|mixed $result 等返回的数据结果
 * @return array|mixed 加工后的数据结果
 */
protected function returnDataResult($result) {
    return $result;
}
```
