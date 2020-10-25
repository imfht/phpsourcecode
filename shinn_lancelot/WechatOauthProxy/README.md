<p align="center">
  <img src="http://resource.hillpy.com/image/oauth_proxy.png" alt="WechatOauthProxy" width="150">
</p>

<p align="center">
    <a href="https://github.com/shinn-lancelot/WechatOauthProxy/issues"><img src="https://img.shields.io/github/issues/shinn-lancelot/WechatOauthProxy.svg" alt="Issues"></a>
    <a href="https://github.com/shinn-lancelot/WechatOauthProxy"><img src="https://img.shields.io/github/stars/shinn-lancelot/WechatOauthProxy.svg" alt="Star"></a>
    <a href="https://github.com/shinn-lancelot/WechatOauthProxy"><img src="https://img.shields.io/github/forks/shinn-lancelot/WechatOauthProxy.svg" alt="Fork"></a>
    <a href="https://github.com/shinn-lancelot/WechatOauthProxy/blob/master/LICENSE"><img src="https://img.shields.io/github/license/shinn-lancelot/WechatOauthProxy.svg" alt="License"></a>
    <a href="https://github.com/shinn-lancelot/WechatOauthProxy/releases"><img src="https://img.shields.io/github/release/shinn-lancelot/WechatOauthProxy.svg" alt="Github Release"></a>
</p>

### 核心功能

* 代理微信授权登陆并进行转发
##### 请求源用get方式请求代理微信授权登录地址，代理服务器获取参数后向微信服务器发起请求，从而获取code或获取access_token及openid，最后代理服务器将转发回请求源。避免了微信公众号授权登录接口只能同时由同一域名发起的限制。

### 其他特性

* 限制代理接口请求源
* 填写授权登录回调域名的验证文件（txt格式）来实现代理域名在公众号微信授权登陆域名的绑定

### 安装使用

1. 本项目仅支持php环境，其他后端语言不支持。将本项目部署到服务器上（demo目录内为应用请求端向代理端发起请求的例子，可删除）。
2. 需要使用一个新域名（如：oauth.xx.com）解析到服务器公网ip上，此域名将作为微信授权登录的回调域名，修改web服务器配置：将新域名应用目录指定到该项目根目录，并重启web服务器。
3. 访问 http://oauth.xx.com/login.php 输入用户名密码登录后台(用户名：admin 密码：123456)。点击“添加微信公众号授权登录域名验证文件内容”按钮，进入页面后，在输入框填写授权登录回调域名的验证文件（txt格式）的内容，然后点击“提交”按钮。成功提交后，点击页面中的“复制微信授权回调域名”按钮复制回调域名（oauth.xx.com）。
4. 复制成功后，进入微信平台，在微信授权回调域名处填写复制的回调域名（oauth.xx.com）即可。
5. 此时代理地址（get）：http://oauth.xx.com/index.php 。<br/>
   请求参数：<br/>
   
   | 参数 | 解释 | 是否必须 | 备注 |
   | ------ | ------ | ----- | ----- |
   | app_id | 公众号id|   是   |       |
   | scope  | 微信登录授权作用域 | 是 | 可选值："snsapi_base"或"snsapi_userinfo"|
   | redirect_uri | 授权回调地址 | 是 | 一般为发起授权登录的请求地址，需要用urlencode处理 |
   | proxy_scope | 代理操作作用域，用于判断获取code还是access_token|否|可选值："code"或"access_token"，默认"code"|
   | app_secret | 公众号密钥 | 否 | 若proxy_scope为access_token,则此参数也需要|
   | oauth_type | 授权类型，1：公众平台，2：开放平台 |否| 可选值:1或2，默认1|
   | state | 重定向参数 | 否 |   |
6. 根据请求地址及参数访问（使用header()函数）即可。若proxy_scope参数为"code"，则返回的地址将会带有code和state参数。若proxy_scope参数为"access_token"，则返回的地址将会带有access_token和openid参数。

### 接口请求举例说明

* 获取code

    1. 代理项目地址为 "http://oauth.xx.com/index.php"。
    2. 首先必须将公众号授权回调域名设置为 "oauth.xx.com"。
    3. 在 "http://request.xx.com/index.php" 页面内请求代理地址： "http://oauth.xx.com/index.php?app_id=APPID&scope=SCOPE&redirect_uri=REDIRECT_URI"。
    4. 正常情况下最终将跳转到 "http://request.xx.com/index.php?code=CODE&state=STATE"。
    5. 获取到code后，再通过微信授权登录接口获取access_token、获取用户信息即可。

* 获取access_token

    1. 代理项目地址为 "http://oauth.xx.com/index.php"。
    2. 首先必须将公众号授权回调域名设置为 "oauth.xx.com"
    3. 在 "http://request.xx.com/index.php" 页面内请求代理地址： "http://oauth.xx.com/index.php?app_id=APPID&scope=SCOPE&proxy_scope=access_token&app_secret=APPSECRET&redirect_uri=REDIRECT_URI"。
    4. 正常情况下最终将跳转到 "http://request.xx.com/index.php?access_token=ACCESS_TOKEN&openid=OPENID"。
    5. 后续根据微信授权登录接口用access_token及openid获取用户信息即可。


### 其它说明

* 本项目中的access_token与微信基础接口调用凭据的access_token无关，仅用于微信授权登录使用。
* 若仅获取code，请注意将access_token返回数据做缓存处理。
* 若获取access_token，则无需再对access_token返回数据单独缓存。若部署在Linux服务器，由于需要创建目录及文件，所以项目需要修改权限。另外，php环境需要开启session、cookie。还有，由于获取access_token需要传递app_secret参数，为减小app_secret泄露的风险，代理地址建议启用https。
* 后台可以添加接口调用安全域名（即请求来源，仅安全域名下的请求可正常进行接口调用）。不添加安全域名无限制。
* 为了安全性，请及时修改管理员密码。
* 管理员账号密码保存在common/user.json中。虽然密码已加密，为确保信息安全，建议使用Web服务器（比如apache、nginx）限制用户直接请求json文件。

###### apache(httpd.conf)

```
<FilesMatch \.(json)$>
    Order allow,deny
    Deny from all
</FilesMatch>
```

###### nginx(nginx.conf)

```
location ~ .*\.(json)$ {
    deny all
}
```
    
### 体验地址

##### 使用的是微信公众测试号的授权登录接口，仅供测试学习。进入后请先识别二维码关注测试号（测试号粉丝上限为100个）。<br>代理端url：http://wxoauth.hillpy.com/index.php<br>请求端url：http://test.hillpy.com/wxoauth/index.php<br>
![test](http://qr.liantu.com/api.php?text=http://test.hillpy.com/wxoauth/index.php&w=300)

### 仓库地址

[Github](https://github.com/shinn-lancelot/WechatOauthProxy "WechatOauthProxy")<br>
[Gitee](https://gitee.com/shinn_lancelot/WechatOauthProxy "WechatOauthProxy")<br>

### 协议

[MIT](https://github.com/shinn-lancelot/WechatOauthProxy/blob/master/LICENSE "MIT")<br>