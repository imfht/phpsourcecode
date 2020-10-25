# 数据层封装目录
@author：fukaiyao

@datetime：2020-1-8

数据工厂：\workerbase\classs\datalevels\DaoFactory

将数据代码统一封装，以便在各个应用中统一单例调用：

* 通过Dao类型创建对象：
DaoFactory::getDao(DaoType::COMMON_TEST_TEST);

目录结构：
```code
├─datalevels             数据模块目录
│  ├─common              common业务目录（自定义）
│  │  ├─test             子业务test目录(自定义)
│  │  │   ├─Test.php     子业务具体实现类
│  │  │   │   
├─DaoType.php            数据类型定义文件
```


