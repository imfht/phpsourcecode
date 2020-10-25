# Model数据模型层与数据库操作

Model层称为数据模型层，负责技术层面上对数据信息的提取、存储、更新和删除等操作，数据可来自内存，也可以来自持久化存储媒介，甚至可以是来自外部第三方系统。

可以说，PhalApi的Model层是广义上的数据层，而非狭义的数据层。但考虑到大部分数据都是来自于数据库的操作，所以后面会重点讲解如何进行数据库操作。

先一个抽象概括的图来了解Model层所处的位置和重要性。

![](http://cdn7.okayapi.com/yesyesapi_20190420133608_ee1aa5a6adda2c56e05bcaf75da3541f.jpeg)

在Model包的左侧，是它的上游，也就是它的调用方或者客户端。从Api层开始，再调用到Domain领域层，再调用Model层。

而在右侧，另一方面，Model的实现依赖于其需要处理的数据来源。当数据是传统的数据库时，则可以使用NotORM（后面会详细和重点介绍）；当数据是存在高效缓存时，如Redis、Memcached时可以使用PhalApi\Cache接口的具体实现类；如果数据是来自第三方远程系统，则可以通过CURL的方式进行通信。



## 一个简单的Model例子

简单地，我们可以通过一个简单的例子来入门。

假设，我们需要查找user表中，id = 1的用户信息。可以这样编写Model类。

新增App\Model\User.php文件，编写：

```php
<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {

    public function getUserInfo($id) {
        return $this->getORM()->where('id', 1)->fetchOne();
    }
}
```

然后，就可以在Domain层使用了。

```php
namespace App\Domain;

use App\Model\User as UserModel;

class User {
    public function getUserInfo() {
        $userId = 1;
        $model = new UserModel();
        return $model->getUserInfo($userId);
    }
}
```

最后，就可以在Api层调用封装好的Domain层。

## 传统的Model数据库层

基于接口的处理重点在于数据，而数据的来源就目前而言，又主要来自数据库，而数据库又集中以MySQL开源数据库为主。因此，我们将重点讲解在PhalApi中如何在Model层使用MySQL数据库。

涉及的内容和知识点，整理成数据库大章节。主要分为：

 + [数据库连接](http://docs.phalapi.net/#/v2.0/database-connect)
 + [数据库与NotORM](http://docs.phalapi.net/#/v2.0/database-notorm)
 + [数据库使用和查询](http://docs.phalapi.net/#/v2.0//database-usage)
 + [数据库分库分表策略](http://docs.phalapi.net/#/v2.0/database-multi)
 + [连接多个数据库](http://docs.phalapi.net/#/v2.0/database-other)
 + [定制你的Model基类](http://docs.phalapi.net/#/v2.0/database-model)

详细可分别查看上面的文档。
