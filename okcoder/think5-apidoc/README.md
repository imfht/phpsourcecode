# TP5接口文档管理

ThinkPHP5 API自动生成 layui美化

## 使用方法
#### 安装扩展
```
composer require okcoder/think5-apidoc dev-master
```

#### 配置参数
- 5.0版本

    安装好扩展后在 application\extra\ 文件夹下会生成 okcoder_doc.php 配置文件
- 5.1版本

    安装好扩展后在 application\config\ 文件夹下会生成 okcoder_doc.php 配置文件
```
<?php
return [
    'title'         => 'apidoc',                   # 文档title
    'version'       => '3.0',                               # 文档版本
    'copyright'     => 'Powered By OkCoder',          # 版权信息
    'password'      => '',                                  # 访问密码，为空不需要密码
    'qq'            => '1046512080',                        # 咨询QQ
    'document'      => [
        "explain" => [
            'name' => '说明',
            'list' => [
                '登录态'      => ['11'],
                'formId收集' => ['222', '2222'],
                '邀请有礼'     => ['333', '33333', '33333']
            ]
        ],
        "code"    => [
            'name' => '返回码',
            'list' => [
                '0'     => '成功',
                '1'     => '失败'
            ]
        ]
    ],
     // 全局请求header,一般存放token之类的
    'header'        => [

    ],
    // 全局请求参数
    'params'        => [
        '__uid' => 2
    ],
    // 需要生成文档的类(单版本)
    'controller'    => [
        'index/controller/Demo',
        'index/controller/Demo2',
    ],
    // 过滤、不解析的方法名称
    'filter_method' => [
        '_empty'
    ]
];
```


#### 单版本配置
新建控制器app/index/controller/Demo.php
```
<?php
namespace app\index\controller;

use think\Controller;
/**
 * @title   模块名称
 * @desc    我是模块名称
 * Class Index
 * @package app\index\controller
 */
class Demo extends Controller{
    /**
     * @title 方法1
     * @desc  类的方法1
     * @url   url('index/demo/index',true,'',true)
     *
     * @param int $page  0 999
     * @param int $limit 10
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
     public function index(){}
}
```
修改okcoder_doc.php 配置文件

```
'controller' => [
    'index/controller/Demo',
    'index/controller/Demo2',
]
```

 多版本配置
新建控制器app/index/controller/v2/Demo.php
```
<?php
namespace app\index\controller\v2;

use think\Controller;
/**
 * @title   模块名称
 * @desc    我是模块名称
 * Class Index
 * @package app\index\controller\v2
 */
class Demo extends Controller{
    /**
     * @title 方法1
     * @desc  类的方法1
     * @url   url('index/v2.demo/index',true,'',true)
     *
     * @param int $page  0 999
     * @param int $limit 10
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
     public function index(){}
}
```
修改okcoder_doc.php 配置文件

```
    'controller' => [
        [
            'name'=>'v2版本',
            'list'=>[
                'index\controller\v2\Demo', //控制器的命名空间+控制器名称(不需要加\\app)
                'index\controller\v2\Demo', //支持两层控制器URL自动生成
                'index\controller\v2\Demo'
            ]
        ],
        [
            'name'=>'v3版本',
            'list'=>[
                'index\controller\v3\Demo', //控制器的命名空间+控制器名称(不需要加\\app)
                'index\controller\v3\Demo', //支持两层控制器URL自动生成
                'index\controller\v3\Demo'
            ]
        ]
    ]
```
####3、书写规范

- 请参考Demo.php文件


####4、访问方法
- http://你的域名/doc 或者 http://你的域名/index.php/doc 

####5、预览(v1~v2)
![](https://gitee.com/uploads/images/2018/0623/112906_e31b8f7f_696921.png "1.png")
![](https://gitee.com/uploads/images/2018/0623/112915_017f26b9_696921.png "2.png")


### 赞助二维码

![](https://gitee.com/uploads/images/2018/0623/112959_9f84f1f7_696921.png "3.png")
![](https://gitee.com/uploads/images/2018/0623/113008_0014aa83_696921.jpeg "4.jpg")




### 更新日志


#### 2019年10月10日
- 更新readme

#### 2019年04月12号(V3.0.1)
- 模块描述字段错误修改

#### 2019年1月25号(V3.0)
- 版本迭代遗留bug修复

#### 2019年1月8号(V3.0)
- URL自动生成并并匹配路由
- 注释重构，请参考Demo.php


#### 2018年9月14日
- 新增多版本切换;
- 左侧菜单UI优化;
- 返回码与说明优化等


#### 2018年7月16日
- 修复dot红点bug

#### 2018年7月3日
- 支持二层控制器URL自动生成
- 新增ajax在线调试错误页面

#### 2018年7月2日
- 删除首页直接定位到说明页面

#### 2018年6月28日
- 在线调试接口从PHP的CURL改为ajax

#### 2018年6月28日
- 新增参数 dot
- 新增在线调试功能

#### 2018年6月27日
- 修改命名空间
- 优化QQ咨询弹窗

#### 2018年6月26日
- 新增咨询QQ
- 新增humpToLine驼峰转下划线,修复自动生成URL错误问题

#### 2018年6月25日
- 优化header/param/return表没有数据不显示问题
- 新增序言文档
- UI优化

#### 2018年6月22日
- 正式发布

