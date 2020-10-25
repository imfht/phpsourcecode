## 升级指导
复制升级目录中对应版本下文件覆盖即可，如需要更新库后台会提示执行更新。
原版本查看  application/system/version.php [后面会调整直接显示]

#### 更新提示
##### v1.8.0 -> v1.8.1 

```
 覆盖升级 
 - 合并数据库表不规范的情况、系统表合并修改为system前缀
 - 优化程序调用的目录结构
 - 优化图片上传配置为后台管理，不用手动修改配置文件
 - 新增腾讯云市场接入
 - 新增关注公众号后扫码直接登录
 - 修正用户管理端的一些关键交互问题
 - 修正默认安全密码问题
 - 修复了一些已知问题
```

#### 更新提示
##### v1.8.1 -> v1.8.5

```
 覆盖方式(更新文件比较多,全量覆盖以下目录)
  - route
  - application/system
  - application/common
  - public/common
 执行SQL语句更新数据库
  - ALTER TABLE `ai_system_member` ADD COLUMN `auth` INT(11) NULL DEFAULT 0 AFTER `ticket`;
  - ALTER TABLE `ai_system_miniapp` ADD COLUMN `is_diyapp` TINYINT(1) NULL DEFAULT 0 AFTER `is_lock`;
```

```
 覆盖升级 
 - 统一用户的基础属性
 - 增加应用的全局权限控制 在app/config/auth.php 中配置
 - 增加用户管理菜单的权限判断（隐藏或显示）
 - 优化模板布局 layout 的统一应用方式
 - 调整优化用户管理的布局方式,简化应用开发流程
 - 增加应用开发脚手架 Demo 开发实例
 - 把平台官方的模板目录移动到根目录/themes,资源目录移动到/public/themes 利用常量 __THEMES__ 路径引入
 - 优化和简化API的签名验证方式
```

#### 更新提示
##### v1.8.5 -> v1.8.6

```
 覆盖升级 
 - 增加不同应用的判断方式，如 $this->is_mp、$this->is_lightapp、$this->is_minapp
 - 修复微信开发平台升级导致的提交审核失败问题
 - 增加优化了全局应用的权限控制功能
 - 修复腾讯应用市场的接入BUG
 - 修复其它已知问题
```
#### 更新提示
##### v1.8.6 -> v1.8.7

```
 覆盖升级 
 - 修复简化不同应用的判断方式，如 $this->is_mp、$this->is_lapp、$this->is_webapp
 - 新增微信提交审核时候新增的接口内容
 - 新增小程序和公众号全局附件上传功能
 - 修改后台框架子管理的UI细节,修复权限判断BUG
 - 修复其它已知问题
 - 升级vendor中的其它库版本
```