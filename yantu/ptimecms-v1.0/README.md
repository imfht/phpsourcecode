#ptimecms

基于phalcon的现代化CMS系统

##特点

**v0.00001，大部分待实现**

###快

基于phalcon框架开发

###现代

每个页面生成独立二维码供用户传播，页面内容可分享至微博、微信等社交平台

###简单

全站只包含分类、文章、相册、链接、专题、标签、评论7个实体

###响应式布局

基础模版采用响应式布局，适配手机、平板、电脑等各种分辨率屏幕

###灵活

首页、导航等全站内容自定义

###URL自定义

支持任意页面URL的自定义

###搜索引擎友好

支持页面的标题、关键字、描述自定义

###模版机制

可自由定制前端页面样式

###无后端管理

采用所见及所得的内容管理方式，直接在前台进行修改。（目前采用[ptimeims](https://git.oschina.net/hillsdong/ptimemis)进行管理）

##前端数据

前端采用phalcon自带的volt引擎，也可使用纯php开发，下面是每个页面的可用数据。

###头尾部

- 全局设置信息：setting 一维
- 一级导航：nav 二维
- 二级导航：nav.{one}.sons 二维
- 友情链接：favolink 二维  

###首页

```
/
```

- 板块基础信息：categories.{one}.base 二维
- 板块列表信息：categories.{one}.list 二维  

###列表页
```
/category/{id}
```

- 当前分类信息：category 一维
- 兄弟分类信息：brother 二维
- 祖先分类信息：parent 二维
- 当前列表信息：result 二维
- 列表项作者：result.{one}.user 一维
- 热门信息：hotResult 二维

###内容页
```
/{object}/{id}
```

- 当前分类信息：category 一维
- 兄弟分类信息：brother 二维
- 祖先分类信息：parent 二维
- 当前内容信息：result 一维
- 当前内容作者：result.{one}.user 一维
- 热门信息：hotResult 二维

###搜索结果页
```
/search/{content}
```

- 文章结果信息：articles 二维
- 链接结果信息：links 二维
- 相册结果信息：albums 二维
- 专题结果信息：topics 二维

###实体评论
```
/{object}/{id}/comment
```

- PUT 创建,提交 father_id,content（评论内容） 字段
- GET 获取列表
- DELETE 删除 /{object}/{id}/comment/{id}

###更多数据
```
/{object}/more
```

- GET 获取列表 page,content(搜索关键字，可选),category_id(可选) 字段 