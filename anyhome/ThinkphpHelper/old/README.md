> 目前还没有完全整理好，flat 是模板的文件，可以直接下载使用。数据库还未提交

----------


 ##

#ThinkPHP开发助手

 - 自动生成通用控制器、模型、HTML模板等
 - 内置FLAT模板，基于bootstartp3.0
 - 基础后台菜单管理，自动生成菜单
 - 内置完善的权限管理，能精确到按钮的操作
 - 表单字段的管理

基于ThinkPHP开发的后台多了，慢慢总结出来的经验，本助手目前只支持3.1.2之前的版本，多用于后台的开发。
 
 
- **菜单管理**
 
灵活的菜单管理，菜单的排序只需拖动操作，菜单的UR了可以自定义也可以根据关联的模块自动生成符合TP要求的链接。

![菜单管理][4]

 - **模块管理**
 
模块对应TP的控制器文件，能够有效的管理所有控制器文件，

![模块的配置][1]

 - **表单字段管理**
 
实际工作中我们常碰见需要经常修改字段中文名称，例如“名称”改为“标题”假如有多个界面，那就需要修改更多地方。这里统一存到数据库，统一修改，还可以设置字段的类型如 select、text、number、file、textaera、data

![表单字段管理][5]
 
  - **角色权限设置**
 
角色权限设置可以精确到每项操作，默认有index list add edit insert update delete 等，并且还可以自定义新增例如view read等。

![角色权限设置][3]

 - **用户角色关系管理**
 
方便只管的用户与角色的对应关系管理，直接拖动用户即可分配角色。

![用户与角色关系][2]


  [1]: http://static.oschina.net/uploads/space/2014/0509/093100_YqVP_1423274.png
  [2]: http://static.oschina.net/uploads/space/2014/0509/093100_ne0b_1423274.png
  [3]: http://static.oschina.net/uploads/space/2014/0509/093100_Ddkg_1423274.png
  [4]: http://static.oschina.net/uploads/space/2014/0509/093059_AGPX_1423274.png
  [5]: http://static.oschina.net/uploads/space/2014/0509/093059_OwKB_1423274.png