## 简介

这个版本是为想使用Thinkphp3.2但是php版本是5.3以下的人专门制作的。  
ThinkPHP3.2使用了命名空间，使用了\_\_callstatic魔术魔法，使用了闭包函数，使用了\_\_DIR\_\__等5.3才有的新特性，这导致强制要求使用php5.3+才可以感受Thinkphp3.2的最新架构。  
但是众多的虚拟主机以及国内现状导致了目前php5.2才是主流php版本，并且绝大多数人不可以对php进行升级，所以才开发了这个版本。

## 结构
├─Application 示例目录  
│  ├─Common 公共目录  
│  ├─Home  示例目录  
│  ├─Lang  多语言示例  
│  ├─Runtime  
│  ├─THeme 多模版示例  
├─Public 公共资源文件  
├─Thinkphp TP核心框架  

## 调整

* __命名空间__，之前使用命名空间，在这里需要把类带上目录。  
 * 比如\Think\Controller 只需要改为Think_Controller  
 * 比如\Think\Storage\Driver\File 只需要改为Think\_Storage\_Driver\_File  
 * 当然，为了方便使用控制器，模型等并不需要用如此长的文件名仍旧使用IndexController即可  
 * 使用这种规范的类其最主要是可以进行自动加载及避免冲突，否则上面Think\_Storage\_Driver\_File如果都使用File这个类名的话Cache、log等驱动也有文件的方式，这就会有冲突的风险。

## 示例添加  
为方便入门，增加了一些示例。使用时请将根目录SQL导入数据库并修改相应配置文件的数据库链接即可  
* Hello World
* 表单提交
* AJAX提交
* CURD
* 分页
* 页面Trace
* 路由(3.2 路由bug 暂未添加)
* 多语言
* 多模板
* 模板继承
* 关联操作
* Todo 图片上传 RBAC AUTH 简易Blog

## 参考  
* Thinkphp相关技术文档
* Yaf框架

## Todo

* 云环境支持
* 完善扩展类

## Author

* 原ThinkPHP框架团队
* PTCMS_杰少 EMAIL:admin#ptcms.com