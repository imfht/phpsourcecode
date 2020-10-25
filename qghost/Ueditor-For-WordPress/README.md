### **强大的百度开源富文本编辑器Ueditor正式登陆wordpress！**

此插件最早由taoqili开发，SamLiu改进,但两位作者均不再发布更新版本，大山在此基础上更新到Ueditor1.4.3。在此感谢两位前辈的付出。也欢迎登陆树新风www.shuxinfeng.cn 使用交流。
    之前一直使用wordpress自带的编辑器，后来感觉功能简单，安装了自带增强版编辑器TinyMCE Advanced，还是感觉不好用，对代码和音视频，图片等支持不太好。
    之后又陆续换了Kindeditor For WordPress，CKEditor for WordPress等，均有各种各样的BUG，最后发现了UEditor，但是官方已经不提供插件下载，网络上流传的版本也只支持到wordpress3.8.2。所以我就在taoqili、SamLiu两位大侠1.30的基础上更新到了现阶段百度Ueditor编辑器1.4.3的最新版本。
    就使用几天的感受来说，速度、稳定性均优于其他wordpress编辑器插件，故拿出来和大家分享。此插件已经支持到最新版本wordpress3.9.2，理论上是不支持wordpress3.3以下版本，另外Ueditor1.4.3以上版本将不再承诺支持ie6/ie7，如果在其他版本中有任何问题欢迎交流改进。

百度Ueditor编辑器wordpress插件编辑界面内容框增加了滚动条，默认自动宽度，高度500px，如需更改请修改配置文件editor_config.js。
同时百度Ueditor编辑器的图片上传功能也弥补了wordpress默认编辑器不能上传图片的bug。

**本插件为树新风www.shuxinfeng.cn原创插件**

百度Ueditor文本编辑器特点
1、功能全面
涵盖流行富文本编辑器特色功能，独创多种全新编辑操作模式。
2、用户体验
屏蔽各种浏览器之间的差异，提供良好的富文本编辑体验。
3、开源免费
开源基于BSD协议，支持商业和非商业用户的免费使用和任意修改。
4、定制下载 
细粒度拆分核心代码，提供可视化功能选择和自定义下载。
5、专业稳定
百度专业QA团队持续跟进，上千自动化测试用例支持。


1.4.3版本
bug修复&优化改进
修复hasContents接口在非ie下只有空格时判断还为真的问题
修复在粘贴word内容时，会误命中cm,pt这样的文本内容变成px的问题
优化删除编辑器再创建编辑器时，编辑器的容器id发生变化的问题
修复提交jsonp请求时，callback参数的xss漏洞
新增jsp后台多种服务器配置下的路径定位
修复ZeroClipboard的flash地址参数名称错误
修复getActionUrl的bug
整理配置参数，把遗漏在代码中的配置项整理到ueditor.config.js里
修复图片拉伸工具和编辑拉伸长高器的样式冲突
修复文字的unicode编码会被错误再次解析问题
添加消息提示功能，冒泡提示信息
优化上传功能提示，当后端配置项没正常加载，禁用上传功能
修复单图上传按钮和jqueryValidate不兼容的问题
简化了与jqueryValidate的结合操作，具体看_examples/jqueryValidateDemo.html
修复在删除编辑器后，再次创建时丢失原有id的问题
修复查找替换在一些块节点中会导致替换错误




