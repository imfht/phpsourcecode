# proxy-test

#### 项目介绍
Proxy验证助手支持HTTP代理验证.做这个的工具的目的的分享免费代理的网站不少,不过好多代理都是不能用的.所以直接用这个工具查一下显示能用的保证是能用的.

#### 软件架构
Proxy验证助手采用php curl 中CURLOPT_PROXY 进行验证,去获取https://www.baidu.com/robots.txt
里面的内容并且验证是否能获取成功.
为了大批量验证用ajax 做了循环依次查询大大增加了效率



#### 使用说明

可单独验证,可批量验证,也可以采集后验证.采集后验证验证暂未开源出来可以自行修改处理我的主站了采用功能,现在采集支持的网站不多.

http://tool.bitefu.net/proxy/

需要api验证的也可以用我自己的api[支持jsonp]

http://tool.bitefu.net/proxy/testproxy.php?ip=221.130.18.125&port=80


新增pac支持

````
pac.php 默认配置
pac.php/5.pac 5为自动随机获取的代理数量
pac.php?max=5 为自动随机获取的代理数量
pac.php?domain=null 不限制支持的域名
pac.php?domain=baidu.com,ip.cn 自定义支持的域名
````