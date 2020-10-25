# ThinkPHP开发助手

标签（空格分隔）： ThinkPHP 助手 帮助

---

 - 简介    
    - 说明 TPHelper能做的     
    - TPHelper不能的     
    - TPHelper组织架构    
 - 安装    
 - 开始    
    - 应用管理     
    - 模块管理    
    - 表单设计      
    - 如何调用     
    - 标签使用     
    - 内置标签     

说明
--
首先非常感谢提供了ThinkPHP（以下简称TP）这么优秀的PHP框架，尽管在PHP世界中框架多不胜数，但是符合中国人口味的仅此一家。    

基于TP大大缩减了我们的开发时间，多年下拉，其实TP的贡献不仅仅是节约了开发时间，TP是我们的工作更、更有意义。    

尽管如此，在实际开发中基于TP，我的工作还可以更细致，例如

 - 根据表名自动建立控制器、模型、视图文件夹。     
 - 根据实际需求建立控制器、模型的通用方法，例如 增、删、改、减    
 - 根据项目需求、建立对数据表的CRUD试图模板。     是否能统一同样的数据表，例如用户信息表、附件表等。    
 - 通用的公共控制器包含用户登录、用户退出。     通用的公共视图包含 用户登录、用户退出。    

TPHelper能做的
--

 - 创建整个系统的基础架构，不局限于网站后台         基于系统创建、删除系统的每个应用         
 - 基于应用创建、删除、设计每个应用模块     
 - 基于模块创建、删除、设计控制器、模型、视图     
 - 基于数据库的表设计控任意视图的表单     
 - TPHelper的视图基于Bootstrap3.3，所以可以用于如何基于 
Bootstrap的主题        
 - 表单完全后台设计，可以动态改变        
 - 调用表单简单，例如 {:tpIpt('name')}     还有更多细节请看使用文档    

 
 TPHelper不能的    
 --
问：TPHelper是一个通用的网站后台吗？类似oneThink    
答：不是，TPHelper只是辅助开发基于TP的工具。    
问：TPHelper是一个通用的后台权限管理系统吗    
答：不是    
问：TPHelper能自动生成网站后台吗    
答：不能    

TPHelper组织架构
--

 - App
    - Common 
        - Common ： 公共函数，需要复制到新建站点的对应目录    
        - Controller : 用于参考的公共控制器，请根据实际情况修改命名空间
        - Model : 用于参考的公共模型，请根据实际情况修改命名空间
    - TPHelper : TPHelper应用目录，不需要复制
 - PHP : TP文件夹（ThinkPHP 3.2）
 - Public
    -   se7en :主题文件基于BT 3.3
    -   tphelper :TPHelper所需要的资源文件，以后会去掉
    -   webupload :百度webuploader
    -   umeditor :百度编辑器 Mini版
    -   此目录下其他文件夹均是内置控件所需的资源文件，都是基于BT3.3的
 - Runtime
 - Uploads : 上传文件夹
 - Widget : 通用一些挂件如：登录文件、主题包等

安装
--

 - 下载 [TPHelper][1] 最新版 : 
 - TPHelper 不需要任何数据库，请直接放在web目录里面直接运行。


  [1]: https://git.oschina.net/anyhome/ThinkphpHelper/repository/archive?ref=master