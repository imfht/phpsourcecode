本项目主要是用于将kindeditor编辑器更换为markdown编辑器，自2016年4月3日起使用hook方式对markdown内容进行扩展显示。以后变动会支持

#### 2016-04-06
1. 修复移动版回帖小bug
2. 支持移动版


#### 2016-04-03

1. 使用扩展模板方式扩展编辑器
2. 使用hook方式扩展模板，从而以较少的代码实现前台markdown代码的html转换
3. 暂时未处理移动端展示
4. 更新版本号为3.0 并将版本适配设置仅为5.*，以表示与之前扩展方式的不同。

ps：如果遇到权限问题可以在`/system/framework/seo.class.php`第`104`行后增加

``` php
        if($items[1]=='index'  && in_array($module,['book','sitemap','links'])){
            return seo::convertURI($module,'index',$params,$pageID);
        }
```

#### 2015-10

1. 将thinkmd替换为editormd。
2. 使用扩展模板方式扩展编辑器
3. 使用扩展模板方式重写前台view模板，来保证markdown代码的展示

#### 展示的处理方法

1. 找到需要处理的节点，利用蝉知内置的一些dom属性来获取相关节点
2. 替换掉可能出现问题的字符串（可以在`/www/template/common/ext/footer.front.editmd.hook.php`）中使用replace方法进行批量替换
3. 然后将原dom节点下的内容置空
4. 使用`editormd`内置的`markdowntohtml`方法重新写入到原dom节点下
