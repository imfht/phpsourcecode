# Fish-MD
Fish-MD是一款非常轻量级的Markdown云笔记工具，可以支持云端的数据同步功能。

和常规的markdown编辑工具相比，增加了图片的粘贴功能，可以将剪贴板中的图片，通过ctrl+v直接将图片添加到笔记中。（图片会在本地做一个高压缩，然后再上传到服务器）

**为什么要开发Fish-MD**
>原本是用有道云笔记的，但是有道云笔记的Markdown对图片的支持不是非常友好，普通的笔记对代码的高亮又不好，所以萌发了自己做个自己想要的笔记工具，纯属个人爱好。

**编译后的应用程序，不需要安装，解压即可使用**
>编译环境VC14 x64
https://pan.baidu.com/s/1kVR76WR#list/path=%2FFish-MD

如果需要x86的小伙伴给我留言，我再编译一份

还有需要配置方法的也可以跟我留言

**注册路径**
>http://mt.wtulip.com/qmd/sign-up.html

**口令**
>*No cross, no crown.*

## 客户端
客户端主要采用C++进行开发，UI库选择了QT，Markdown是采用了应用程序中内嵌浏览器来实现的，通过javascript和C++之间的交互来实现客户端的基本功能。

用到了sqlite作为本地文档的持久化缓存，加快文档加载，和在断网情况下也可以正常使用（不能云同步而已）

由于习惯性的使用了boost的一些基本库，后期可以改为c++11的库

windows客户端依赖的库：
- boost 1.6
- sqlite 3
- qt 5.9

![image](http://mt.wtulip.com/qmd/api/image?imageId=62156f47e7ba45e52fac71dbb82bd63f)

![image](http://mt.wtulip.com/qmd/api/image?imageId=e873d2928704f4d7a94ade114af1e2ea)

## 服务器端
服务端采用laravel框架和dingo，以restful的方式导出api接口

服务器端依赖的库：
- php >= 5.5.9（建议php7）
- laravel 5.2
- dingo 1.0


## 联系方式
欢迎来骚扰
crazycooler@qq.com

有个小伙伴发邮件希望能建一个QQ群，方便一起交流。

群号：241068624

![image](http://mt.wtulip.com/qmd/api/image?imageId=25b72249b7313ba4d5f15f8c18f91e8f)


## license
GPL v2

