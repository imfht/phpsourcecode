## 一、简介
很多同学在用ThinkPHP做开发集成ueditor富文本编辑器很头疼，需要配置的东西太多，一时分不清各种前后台配置，思路顿时混乱，索性干脆直接用官方的demo，扩展性不说，但殊不知有很大的安全隐患，希望这种同学仔细做好权限控制，毕竟埋下的是地雷。

很多前辈写过这个插件，我为什么要重复造轮子？

1. 站在巨人的肩膀上，看前辈的代码觉得有可压缩的空间

2. 为大家提供新的实现思路，交流心得
 

此插件的特色：

1. 针对thinkphp3.2开发，其他版本未做测试，因UEditor 1.4.3.3 存在word图片转存无效的bug，所以采用了上个版本，请注意

2. 简单，便捷，无部署之忧，核心代码就一个控制器，稍加配置即可直接使用

3. 功能完整，实现了ueditor后台的全部功能

## 二、使用步骤
ueditor目录中包含两个文件夹，Public/ueditor 为编辑器目录，Application为应用目录，将两个目录合并到你的项目下

##### 前台 View层 引入
```
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" >
//实例化编辑器
var ue = UE.getEditor('editor',{serverUrl :'{:U('模块/Ueditor/index')}' });
</script>
```

##### 后台自定义配置
1. Application/Common/Conf 文件夹下的ueditorconfig.json 为后端通信配置文件
2. Controller中 ueditorController.class.php 为核心控制器，引入时请注意下命名空间，默认为Home, 即Home模块
3. 在ueditorController.class.php中配置上传文件的根目录，默认为Upload目录，可根据自己需要调整
```
'rootPath'  =>  './Upload/', // 设置上传根目录
```

至此我们就可以使用了，如果遇到困难可以参考下demo中的集成方式
