# 首次安装和使用方法

（最后更新：2015-10-1）

## 申请阿里云安全接口权限

到[阿里云安全“云盾魔方”](http://csc.aliyun.com/)，申请阿里云安全权限。

具体请查阅[相关文档](http://csc.aliyun.com/doc.htm?spm=0.0.0.0.Oy6mS0&categoryId=101921)。

## 下载代码

当前版本为照顾新手，已经同步一份[“阿里巴巴SDK”](http://git.oschina.net/horseluke/AlibabaSDK)，请直接下载本仓库代码即可。

## 修改配置

### 发布到生产环境需要注意的地方？

（1）由于定位内网应用所限，其验证加密控制较弱，本微服务不建议发布到外网，最佳适用场景是内网。

若要发布到外网，请做好IP控制。详细见faq[“为什么定位为内网服务？不能外网使用？”](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun/blob/master/doc/Faq.md)。

（2）若要发布到生产环境，请同时修改```index.php```、```cmsadmin.php```、和```microsrvapi.php```，把以下变量进行修改：

```
define("D_DEBUG", 1);    //生产环境请改为0

/*
//生产环境请改成Production，
然后你就可以在不修改```/protected/App/Config/Default.php```、和对应的```Default_{D_CONTROLLER_NAME}.php```的情况下，
起一个```Default_{D_CONTROLLER_NAME}_{D_ENV}.php```文件以配置系统。
写法见```Default.php```
*/
define("D_ENV", 'Dev');
```

### 如果只是运行demo查看调用淘宝开放平台和OAuth 2.0的方法？

这种情况，只需要修改```protected/App/Config/Default.php```的如下变量：

```
$config['TAOBAO_APPKEY'] = '';    //淘宝开放平台为你申请的阿里云安全应用所提供的appkey
$config['TAOBAO_APPSECRET'] = '';    //淘宝开放平台为你申请的阿里云安全应用所提供的appsecret

$config['ALIBABASDK_FILE_LOG_DIR'] = '';   //调用淘宝开放平台接口的文件日志存放目录
```

相关示例代码见：

* /protected/App/Class/Controller/Editor/Content.php。内容检测接口alibaba.security.yundun.spam.validate使用方法代码
* /protected/App/Class/Service/AliyunCsc。封装利用“阿里巴巴SDK”（\AlibabaSDK\Taobao\TaobaoClient）调用淘宝开放平台的代码
* /protected/App/Class/Controller/User/Login.php。封装利用“阿里巴巴SDK”（\AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient）调用淘宝开放平台OAuth 2.0登录的代码


### 如果想运行内容安全微服务？

这种情况，首先按照上面的“如果只是运行demo查看调用淘宝开放平台和OAuth 2.0的方法？”修改相关文件。

然后，在```/protected/App/Config/Default_ControllerMicroServiceApi_Dev.php```，如果需要外网访问，或部署到内网，则需要修改如下变量：

```
/**
 * 允许使用本微服务的ip。默认为本地ip数组。
 * 如果要让所有ip均可访问，请将其赋值为*，即：
 *     $config['microserviceapi_allow_ip'] = "*";
 * 注意：本设置为全局设置，只有通过了本设置，才进入各应用自己的allow_ip配置中
 */
$config['microserviceapi_allow_ip'] = array(
    '127.0.0.1',
    '0.0.0.0',
);
```


接着，还是在```/protected/App/Config/Default_ControllerMicroServiceApi_Dev.php```，按照```$config['client_app_1']```，定义一个自己的app：

```
$config['client_app_{微服务APPID，数字}'] = array(
    'appid' => {微服务APPID，数字},
    'appsecret' => '{微服务APPSECRET，任意字符串}',
    //allow_ip可以不设置，不设置时，等同于如下效果：仅本地ip数组可使用。
    /*
    'allow_ip' => array(
        '127.0.0.1',
        '0.0.0.0',
    ),
    */
);
```

最后，修改```protected/App/Config/Default.php```的如下变量：

```
$config['MICROSRV_GATEWAYURL'] = '';    //microsrvapi.php的访问url地址，如http://127.0.0.1/demo/microsrvapi.php
$config['MICROSRV_APPID'] = '';    //{刚才定义的微服务APPID，数字}
$config['MICROSRV_APPSECRET'] = '';    //{刚才定义的微服务APPSECRET，任意字符串}

$config['MICROSRV_FILE_LOG_DIR'] = '';    //调用微服务SDK的文件日志存放目录
```

相关示例代码见：

* /protected/App/Class/Apphook/InitControllerMicroServiceApi.php。微服务验证调用有效性方法
* /protected/App/Class/ControllerMicroServiceApi。微服务定义和编写方法
* /protected/ThirdPartyLoadByPsr4/MicrosrvSDK。微服务SDK示例



## 运行

* ```http://{url}/index.php```。demo说明
* ```http://{url}/cmsadmin.php```。集成阿里巴巴SDK和微服务的模拟应用示例入口
* ```http://{url}/microsrvapi.php```。微服务接口入口