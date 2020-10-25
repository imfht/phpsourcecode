######资源插件
//创建一个demo control并生成demo数据赋值模板的示例


用于创建resource
   >common   >js  >define  config.js.tpl  >define index.js
             >css
             >images
   >jquery
   >seajs
   >iEfix

plugins
     >util plugins
     >T

views   //不再每个control创建一个单独的文件夹，而是使用一个文件夹，包含所有静态html资源，这种方式更直观，html也容易与
design_views 相结合使用，直接复制过来即可用
       >index.html   》引入jquery|引入seajs  引入config和 index.js 》

目录结构，并赋值常用的资源

####检测生成config.js使用方式，index.php  include('../core/core.php');后添加如下语句即可
plugin('core/config')->on('end_init',function(){
    //动态检测是否需要重写js 中config.js
    plugin('mobile/resource')->check_update_config();
});

####初始化基本组件和插件方式
在index.php 最后添加：plugin('mobile/resource')->init();
即可；

demo为meirong2

根据act和op去resource/common/js加载相应的模块，根据op执行初始化函数方式实现自动依赖加载执行；
这样不用手动写每个页面script标签了，直接写页面的逻辑函数就行了；

###外部文件模块使用方法
修改了sea.ini.tpl
可以引用core/resource中公用资源文件；

define(['common/js/config','core/util/util'],function(require) {
    var Config = require('common/js/config');
    var Util = require('core/util/util');


###全新改变，自动创建的文件更接近项目初始化，更加省力，省时，省心





