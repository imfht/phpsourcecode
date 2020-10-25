#mzphp-todo

将 [mzphp框架](http://git.oschina.net/mz/mzphp2) 结合 vuejs、vux、vue-resource 做的 todoList。代码量不多，核心代码就100来行，重点在于演示和互相学习。

**问：为什么要将 mzphp 和 vue、vux 结合做项目呢？** 

1. 技术所趋（认真脸！）；
2. mzphp 支持 scss 语法，static 标签可以打包压缩前端 js 和 css，擅长于结合前端做项目；
3. 个人比较喜欢 vux 界面；
4. 网上找的 vue todo demo 做的不是特别友好，而且没有和 PHP 结合的示例项目。

嗯，就这么简单。



无图无真相：

![demo1](http://git.oschina.net/mz/mzphp-todo/attach_files/download?i=66263&u=http%3A%2F%2Ffiles.git.oschina.net%2Fgroup1%2FM00%2F00%2F6E%2FPaAvDFfdAHuAFGtBAAXD2ERH8oo277.jpg%3Ftoken%3Da015334c1038b208558fcbcd1db81eb1%26ts%3D1474101314%26attname%3Dtodo_demo.jpg "在这里输入图片标题")

### 相关说明

添加 vux 组件在 view/inc/vux.htm 中：

![输入图片说明](http://git.oschina.net/mz/mzphp-todo/attach_files/download?i=66261&u=http%3A%2F%2Ffiles.git.oschina.net%2Fgroup1%2FM00%2F00%2F6E%2FPaAvDFfc-5uAJp1dAAbr8BBKWf4665.jpg%3Ftoken%3D752c90aa8df70ce950d380d4a3977f72%26ts%3D1474100066%26attname%3Dcomponents.jpg "在这里输入图片标题")

这里用了 mzphp 特有的 static 打包文件语法。
添加组件时，您只需要复制一行，修改为对应的 vux 组件名.
（例如 components/tab-item 改为 components/loading）

然后访问 index.php 直接食用即可。(注：如果添加了组件，请在访问地址后加 index.php?_debug 或者删除 static/目录下「下划线」 _ 开头的 js)

static/v1/common.js 中已经有自动注册 vux 组件的方法：

```
// auto register component
(function (window) {
    for (var index in window) {
        if (index.search(/^vux/ig) > -1) {
            var componentName = index.replace(/^vux/i, '');
            var firstChar = componentName.substring(0, 1);
            componentName = firstChar + componentName.substring(1).replace(/([A-Z])/g, '-$1');
            Vue.component(componentName.toLocaleLowerCase(), window[index]);
        }
    }
})(window);
```

有问题欢迎提问。