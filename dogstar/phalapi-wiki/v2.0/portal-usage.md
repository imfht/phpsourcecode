# 运营平台

PhalApi的Portal运营平台，是提供给运营团队使用的管理后台。从PhalApi 2.12.0 及以上版本提供，可以非常方便进行数据和业务上的管理。  

运营平台有以下几个特点：  
 + 免费使用，可放心用于商业项目开发
 + 基于[layuimini](http://layuimini.99php.cn/)LAYUI MINI 后台管理模板和[layui](https://www.layui.com/)经典模块化前端框架开发运营平台界面，让后端开发也能轻松入手
 + 提供与后面界面配套的后台数据接口，基本不用写代码，就能实现后台数据的增删改查操作
 + 可视化运营平台安装，安装后即可使用
 + 丰富的应用超市和插件即将来临，敬请期待！  

## 安装运营平台

参考文档[下载与安装](http://docs.phalapi.net/#/v2.0/download-and-setup)，下载安装好PhalApi 2.2.0 及以上版本后，并且配置好数据库连接。然后访问运营平台：  
```
http://dev.phalapi.net/portal/
```

你也可以通过在线接口文档的右上角的顶部菜单【登录】进入。

![](http://cdn7.okayapi.com/yesyesapi_20200313114729_3e45027da1e6c215d1852c1aa48fb823.png)

> 温馨提示：把dev.phalapi.net换成你的域名；配置时需要把网站的根路径设置到public目录。  

如果第一次使用，会提示未安装，并自动跳转到安装页面。

![](http://cdn7.okayapi.com/yesyesapi_20200309172737_a4b73f5763b4d8758f367a2a34230830.png)

输入你需要初始化的管理员账号和自己的密码，然后点击【立即安装】。  

安装成功后，将会跳转到登录页面。

![](http://cdn7.okayapi.com/yesyesapi_20200309174512_4362a4853b3dcb860538aada234bb476.png)

登录成功后，进入运营平台首页。

## 使用运营平台

默认情况下，有三大版块：运营平台、页面示例和应用市场。

### 运营平台
可以根据项目业务的需求，添加需要的新菜单和功能界面。

首页，功能未实现，只是一个静态效果。
![](http://cdn7.okayapi.com/yesyesapi_20200309181436_29b086516a5ec57056fa575f5b7424c8.jpg)

菜单管理，可以实现菜单的查看，编辑和删除。

![](http://cdn7.okayapi.com/yesyesapi_20200309181753_86f46d36d2ea0df837945f6864d460e8.png)

CURD表格示例，在项目开发过程中，新业务的数据管理可以参考此示例。基本上把后台模板复制后简单修改下即可得到新的功能模块，后台接口只需要极少量的改动。下面会单独进行详细讲解。

可以实现表格数据的列表查询和搜索、增加、修改、删除和批量删除等操作。

![](http://cdn7.okayapi.com/yesyesapi_20200309182259_f7937a2780d1a53b0b0bb2208d4ca78e.jpg)

### 页面示例
页面示例，为方便大家开发运营平台的新界面和新功能，这里保留了layuimini原来的部分页面示例。开发时，可以参考或复制过来修改调整。

![](http://cdn7.okayapi.com/yesyesapi_20200309182633_100d5082e5bb4310273cc5c5c29d93ea.png)

### 应用市场

PhalApi将结合广大开发者提供优质的应用、插件和接口源码，通过应用市场提供给大家使用。目前正在开发中，敬请期待！


## 如何在运营平台开发一个新页面

下面，将讲述如何在运营平台开发一个新页面，主要步骤是：

 + 第1步：添加新页面菜单
 + 第2步：添加运营平台后台界面模板
 + 第3步：编写运营平台后端接口

以开发CURD示例的列表页为例，同步进行讲解。

## 第1步：添加新页面菜单

添加新页面菜单，你可以手动在【菜单管理】页面手动添加，也可以直接修改数据库表```phalapi_portal_menu```添加。

主要配置：  
 + title：菜单标题
 + icon：菜单图标，想知道有哪些图标？可以查看layui的[图标文档](https://www.layui.com/doc/element/icon.html)。
 + href：界面链接，使用相对于portal目录的路径，例如：```page/upload.html```，或相对于网站根路径，例如：```/portal/page/upload.html```。
 + target：目标窗口，_self表示当前页面打开，_target表示新窗口打开
 + sort_num：排序值，值越小越前面
 + parent_id：上一级菜单ID，特别为0时，表示是顶部的菜单

为方便管理，推荐对于菜单ID，从1~10000，统一约定预留作为PhalApi框架及运营平台使用。方便日后框架升级和安装其他应用，不会出现菜单ID冲突。对于10000至99999之间的ID，用于项目自身的开发，可由自行分配。对于100000后的ID，则由第三方应用分配的选择。  

小结：  
 + PhalApi官方预留的菜单ID区间：1~10000（1到1万）
 + 项目自身预留菜单ID区间：10001~99999（1万零1到9.999万）
 + 第三方应用预留菜单ID区间：100000~无穷大（10万起）

目前已确定的顶部菜单ID有：  

菜单ID|菜单名称|说明
---|---|---
1|运营平台|运营平台的主要功能区
2|页面示例|layuimini的页面示例参考
3|应用市场|基于PhalApi开发的应用、插件、接口等源码市场


比如，需要添加以下这个【CURD表格示例】菜单：
![](http://cdn7.okayapi.com/yesyesapi_20200309183948_3ae3092a115d64b720c31feb9c85ebd6.png)

可以执行以下sql插入语句：
```sql
insert into `phalapi_test`.`phalapi_portal_menu` 
( `target`, `title`, `href`, `sort_num`, `parent_id`, `icon`)  
values 
( '_self', 'CURD表格示例', 'page/phalapi-curd-table.html', '5', '1', 'fa fa-list-alt');
```

## 第2步：添加运营平台后台界面模板

可以参考./public/portal/page目录下面原有的页面模板，或者【页面示例】的模板示例，复制一份合适的进行修改。 

这里，参考的是原来的```./public/portal/page/table.html```模板，复制一份到：./public/portal/page/phalapi-curd-table.html```，注意模板路径和要上面的菜单路径对应。  

首先，修改需要搜索的表单字段。

```
        <fieldset class="table-search-fieldset">
            <legend>搜索信息</legend>
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">ID</label>
                            <div class="layui-input-inline">
                                <input type="text" name="id" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">标题</label>
                            <div class="layui-input-inline">
                                <input type="text" name="title" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-inline">
                                <input type="text" name="state" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit  lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>
```

界面效果：  
![](http://cdn7.okayapi.com/yesyesapi_20200309211257_7bccb3e2dfa23d9cb857cf183ba42fdf.png)

接下来，在前端模板中配置需要调用的运营平台接口获取列表数据。  

```javascript
url: '/?s=Portal.CURD.TableList', // 换成相应的运营平台接口
```

继续配置，把默认的接口返回结果转换成layui表格需要的格式。  
```javascript
            parseData: function(res){ //res 即为原始返回的数据
                return {        
                    "code": res.ret == 200 ? 0 : res.ret, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.data.total, //解析数据长度
                    "data": res.data.items //解析数据列表
                };          
            },  
```

最后，再配置需要展示的表格数据。  
```javascript
            cols: [[        
                {type: "checkbox", width: 50, fixed: "left"},
                {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'title', minWidth: 50, title: '标题'},
                {field: 'content', minWidth: 80, title: '内容', sort: true},
                {field: 'post_date', minWidth: 80, title: '发布时间', sort: true},
                {
                    field: 'state', minWidth: 50, align: 'center', templet: function (d) {
                        if (d.state == 0) {
                            return '<span class="layui-badge layui-bg-red">关闭</span>';
                        } else {
                            return '<span class="layui-badge-rim">开启</span>';
                        }
                    }, title: '状态', sort: true
                },
                {title: '操作', minWidth: 50, templet: '#currentTableBar', fixed: "right", align: "center"}
            ]],
```

表格界面展示效果如下：  
![](http://cdn7.okayapi.com/yesyesapi_20200309211651_f7be5e390a423373de8f5683c37e5acc.png)

前面javascript代码组合起来完整是：  
```javascript
        table.render({
            elem: '#currentTableId',
            url: '/?s=Portal.CURD.TableList', // 换成相应的运营平台接口
            toolbar: '#toolbarDemo',
            defaultToolbar: ['filter', 'exports', 'print', {
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips'
            }],             
            parseData: function(res){ //res 即为原始返回的数据
                return {        
                    "code": res.ret == 200 ? 0 : res.ret, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.data.total, //解析数据长度
                    "data": res.data.items //解析数据列表
                };          
            },                  
            cols: [[        
                {type: "checkbox", width: 50, fixed: "left"},
                {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'title', minWidth: 50, title: '标题'},
                {field: 'content', minWidth: 80, title: '内容', sort: true},
                {field: 'post_date', minWidth: 80, title: '发布时间', sort: true},
                {
                    field: 'state', minWidth: 50, align: 'center', templet: function (d) {
                        if (d.state == 0) {
                            return '<span class="layui-badge layui-bg-red">关闭</span>';
                        } else {
                            return '<span class="layui-badge-rim">开启</span>';
                        }
                    }, title: '状态', sort: true
                },
                {title: '操作', minWidth: 50, templet: '#currentTableBar', fixed: "right", align: "center"}
            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 15,
            page: true
        });
```
## 第3步：编写运营平台后端接口

上面示例中，接口请求的链接类似：  
```
http://dev.phalapi.net/?s=Portal.CURD.TableList&page=1&limit=15
```

返回的结果数据类似是：  
```
{
    "ret": 200,
    "data": {
        "total": 2,
        "items": [
            {
                "id": 2,
                "title": "版本更新",
                "content": "主要改用composer和命名空间，并遵循psr-4规范。",
                "state": 1,
                "post_date": "2017-07-08 12:10:58"
            },
            {
                "id": 1,
                "title": "PhalApi",
                "content": "欢迎使用PhalApi 2.x 版本!",
                "state": 0,
                "post_date": "2017-07-08 12:09:43"
            }
        ]
    },
    "msg": ""
}
```

对应的PHP接口代码是：  
```php
<?php
namespace Portal\Api;

use Portal\Common\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function getDataModel() {
        return new \Portal\Model\CURD();
    }
}

```

关于PhalApi运营后台的数据接口，会有文档另外介绍，这里不再赘述。  

