# 微信企业WebHooks机器人

#### 项目介绍
微信企业WebHooks机器人

#### 软件架构
软件架构说明 使用TP5作为基础框架


#### 安装教程

1. 部署到web项目
2. 修改application下的config里面参数

注册企业微信（https://work.weixin.qq.com ），
1.获取CorpID
![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133004_e6f57ab7_327005.png "企业微信1.png")
2.获取AgentID，和SECRET，需要 在后台新建一个应用：
![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133015_1f1e0077_327005.png "企业微信2.png")

![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133027_3c51d16a_327005.png "企业微信3.png")
```
//微信企业接口调配置
    'weichat_entper_api_setting' => [ 
        "CorpID" => "ww1e7*******",
        "AgentID" => "1000005",
        "SECRET" => "5rG9gsMKKGGgvu0WKfjRfkmu33WA1GwRkMTiJkcce_Y",
    ],
```


#### 使用说明

1.找到项目管理的WebHooks
![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133227_f19dea1c_327005.png "webHOOK1.png")
2.添加WebHooks
![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133828_e95de341_327005.jpeg "TIM截图20180919133722.jpg")

#### 体验

![输入图片说明](https://images.gitee.com/uploads/images/2018/0919/133300_6118e261_327005.png "测试体验.png")

