
<h3>一、项目介绍</h3>

Svg Badge Tool是一个快速生成SVG的小工具，支持传入参数进行生成svg的可外链小图标。

<h3>二、快速接入</h3>

**1. 普通标签**

![SVG](https://svg.hamm.cn/badge.svg?key=芬&value=必得&color=ff4500&radius=3 "SVG") ![SVG](https://svg.hamm.cn/badge.svg?key=博客&value=Hamm.cn "SVG")

```
接入地址：https://svg.hamm.cn/badge.svg
参数说明：
    key:标签头 必须
    value:标签内容 必须
    radis:标签圆角 选填 默认3
    color:标签颜色 选填 默认随机

示例：
    https://svg.hamm.cn/badge.svg?key=芬&value=必得&color=ff4500&radius=3
    https://svg.hamm.cn/badge.svg?key=博客&value=Hamm.cn
```

**2.码云特殊标签**

![SVG](https://svg.hamm.cn/gitee.svg?user=hamm&project=svg_badge_tool&type=star "SVG") ![SVG](https://svg.hamm.cn/gitee.svg?user=hamm&project=svg_badge_tool&type=watch "SVG") ![SVG](https://svg.hamm.cn/gitee.svg?user=hamm&project=svg_badge_tool&type=fork "SVG") ![SVG](https://svg.hamm.cn/gitee.svg?user=hamm&project=svg_badge_tool&type=commit "SVG")

```
接入地址：https://svg.hamm.cn/gitee.svg
参数说明：
    user:用户 必须
    project:项目 必须
    type:类型 必须 固定值为：
        - watch 项目watch人数
        - fork 项目fork人数
        - star 项目star数量
        - commit 项目提交次数
    radis:标签圆角 选填 默认3
    color:标签颜色 选填 默认随机

示例：
    https://svg.hamm.cn/gitee.svg?user=hamm&project=svg_badge_tool&type=star&color=ff4500&radius=3
```

**3.客户端特殊标签** 

![SVG](https://svg.hamm.cn/client.svg?type=os "SVG") ![SVG](https://svg.hamm.cn/client.svg?type=broswer "SVG") ![SVG](https://svg.hamm.cn/client.svg?type=ip "SVG")

```
接入地址：https://svg.hamm.cn/client.svg
参数说明：
    type:类型 必须 固定值为：
        - os 操作系统
        - ip IP地址
        - broswer 浏览器
    radis:标签圆角 选填 默认3
    color:标签颜色 选填 默认随机

示例：
    https://svg.hamm.cn/client.svg?type=os&color=ff4500&radius=3
```

<h3>三、私有部署</h3>

Clone代码到本地，配置站点后即可使用，但后缀是.php，你也可以配置好伪静态，这样可能看起来更舒服：

```
if($request_uri ~* ^(.*?)svg(.*?)$){
    rewrite  ^/(.*?)svg(.*?)$  /$1php$2 break;
}
```

<h3>四、Enjoy it!</h3>

















