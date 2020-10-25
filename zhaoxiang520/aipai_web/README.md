# WebApi

WebApi是一款致力于API开发的后端PHP框架，它主要灵感来源于PhalApi和ThinkPHP，细心的朋友可能会发现在框架中有使用到ThinkPHP的部分源码，比如Cache部分。

> 注：框架并不是一个较为成熟的产品，有很多不完善的地方，发现问题，或者对功能有更高的要求，欢迎提交issue。当然，更加希望大家能够以此为基础，去做自己的个性化开发。

## 【环境需求】

* PHP (>=5.6.0)
* MySQL (>=5.6.0)
* Nginx (>=1.10.0)


## 【接口返回码说明】

206 GET 请求成功, 但是只返回一部分，参考：上文中范围分页
422 Unprocessable Entity: 请求被服务器正确解析，但是包含无效字段
429 Too Many Requests: 因为访问频繁，你已经被限制访问，稍后重试

```
                                    +---  200 （OK） - 表示已在响应中发出
                                    |---  204 （无内容） - 资源有空表示
                                    |---  301 （Moved Permanently） - 资源的URI已被更新
                                    |---  303 （See Other） - 其他（如，负载均衡）
                                    |---  304 （not modified）- 资源未更改（缓存）
        GET ---- 安全 ---- 幂等 ---- |
                                    |---  400 （bad request）- 指代坏请求（如，参数错误）
                                    |---  404 （not found）- 资源不存在
                                    |---  406 （not acceptable）- 服务端不支持所需表示
                                    |---  500 （internal server error）- 通用错误响应
                                    +---  503 （Service Unavailable）- 服务端当前无法处理请求
```
```
                                    +---  200 （OK）- 资源已被删除
                                    |---  301 （Moved Permanently）- 资源的URI已更改
                                    |---  303 （See Other）- 其他，如负载均衡
                                    |---  400 （bad request）- 指代坏请求
   DELETE ---- 不安全 ---- 幂等 ---- |
                                    |---  404 （not found）- 资源不存在
                                    |---  409 （conflict）- 通用冲突
                                    |---  500 （internal server error）- 通用错误响应
                                    +---  503 （Service Unavailable）- 服务端当前无法处理请求
```
```
                                    +---  200 （OK）- 如果已存在资源被更改
                                    |---  201 （created）- 如果新资源被创建
                                    |---  301 （Moved Permanently）- 资源的URI已更改
                                    |---  303 （See Other）- 其他（如，负载均衡）
                                    |---  400 （bad request）- 指代坏请求
                                    |---  404 （not found）- 资源不存在
      PUT ---- 不安全 ---- 幂等 ---- |
                                    |---  406 （not acceptable）- 服务端不支持所需表示
                                    |---  409 （conflict）- 通用冲突
                                    |---  412 （Precondition Failed）- 前置条件失败（如执行条件更新时的冲突）
                                    |---  415 （unsupported media type）- 接受到的表示不受支持
                                    |---  500 （internal server error）- 通用错误响应
                                    +---  503 （Service Unavailable）- 服务当前无法处理请求
```
```
                                    +---  200（OK）- 如果现有资源已被更改
                                    |---  201（created）- 如果新资源被创建
                                    |---  202（accepted）- 已接受处理请求但尚未完成（异步处理）
                                    |---  301（Moved Permanently）- 资源的URI被更新
                                    |---  303（See Other）- 其他（如，负载均衡）
                                    |---  400（bad request）- 指代坏请求
   POST ---- 不安全 ---- 不幂等 ---- |---  404（not found）- 资源不存在
                                    |---  406（not acceptable）- 服务端不支持所需表示
                                    |---  409（conflict）- 通用冲突
                                    |---  412（Precondition Failed）- 前置条件失败（如执行条件更新时的冲突）
                                    |---  415（unsupported media type）- 接受到的表示不受支持
                                    |---  500（internal server error）- 通用错误响应
                                    +---  503（Service Unavailable）- 服务当前无法处理请求
```


## 【已完成的部分】

* 文件自动加载实现
* 异常处理
* 配置文件管理
* URL解析
* Log日志处理
* 缓存驱动(只支持Redis)
* 输入输出数据预处理
* 会话ID相关
* 多语言支持
* 数据库驱动(只支持MySQL)-请参见[Medoo](http://medoo.in/doc)

## 【计划中的部分】

* 接口安全（数据过滤和验证）
* 接口安全（接口数据加解密）
* 接口安全（HTTPS协议支持）
* 文档自动生成接口
* Session处理
* Cookie处理

## 【特色】

* 采用PHP命名空间实现自动加载
* 搭配有相配套的Web前端框架（TODO）
* 多级配置，多项目支持

## 【安装】

1. clone项目
2. 上传代码到web根目录
3. 配置Nginx，请参见[demo](https://zxblog.our-dream.cn/index.php/archives/537.html)
4. 访问域名即可

> 推荐浏览器插件`JSON-handle`

## 【更新历史】

* 2016-09-05 第一个bate版本发布

## 【致谢】

* [ThinkPHP V5](http://www.thinkphp.cn/topic/40195.html)
* [PhalApi](http://www.phalapi.net/)
* [Medoo](http://medoo.in/)

感谢前辈们的开源精神，感谢大牛们提供的优秀的框架架构思路，也希望用了这个框架的朋友也能够为开源贡献出你的力量！