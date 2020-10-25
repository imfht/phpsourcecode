# 业务层封装目录
@author：fukaiyao

@datetime：2020-1-3

业务工厂：\workerbase\classs\ServiceFactory

将业务代码统一封装，以便在各个应用中统一单例调用，2种创建方式：

* 通过业务类型创建对象（推荐）：
ServiceFactory::getService(SrvType::COMMON_TEST_TEST);

* 应用目录下对应命名空间创建：
$test = ServiceFactory::getService('workerbase\classs\Config');

目录结构：
```code
├─services               业务模块目录
│  ├─common              common业务目录（自定义）
│  │  ├─test             子业务test目录(自定义)
│  │  │   ├─Test.php     子业务具体实现类
│  │  │   │
├─SrvType.php            业务类型定义文件
```


