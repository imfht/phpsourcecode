# 常见问题

（最后更新：2015-10-1）

## 该微服务适用范围是哪里？

主要应用在传媒界等需要内网进行内容检测的地方。

## 为什么定位为内网服务？不能外网使用？

该微服务的考虑是服务于公司内部建设，同时由于作为演示原因，故定位为内网服务。

考虑到内网性能消耗问题，该微服务默认实现了HTTP普通鉴权(Basic Authentication)方式验证。

该方式在非https环境下有被MITM窃听风险，并且其验证hash参数量较少，信息摘要不足或较弱，容易被重放，故不建议发布到外网。

若要发布到外网，请做好IP控制，

或仿照```/protected/App/Class/Apphook/MicroServiceApiRequestValidate_BasicAuth.php```自己写个新的API认证方式，

然后在```/protected/App/Config/Default_ControllerMicroServiceApi.php```，修改如下配置变量：

```
$config['microserviceapi_request_validate_class'] = 'Apphook\MicroServiceApiRequestValidate_BasicAuth';
```

## 为什么请求公共参数要放置于url中？

这是为了方便web日志记录、统计和审计。


## 我只想留下微服务，删除所有demo，怎么办？

删除```index.php```和```cmsadmin.php```入口即可。

不放心的，请同时删除目录：

* /protected/App/Class/ControllerIntro
* /protected/App/Class/Controller
* /protected/App/tpl/controller
* /protected/App/tpl/controllerintro


## 后续有什么todo list？

有的，当前想到的有：

* 分离出demo和微服务。当前为了比赛，而集合在一起，后续要分开。
* 测试用例。虽然阿里巴巴SDK已经有了完整的测试用例，但该微服务因为赶工，没有写，后续要补上。
* 页面简易分发器核心的```\SCH60\Kernel\Config```类需要重写。
* 增加分词功能等内容分析建议接口
