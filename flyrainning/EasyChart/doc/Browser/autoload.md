# 自动加载

EasyChart提供自动加载功能，根据HTML标签的附加属性生成EasyChart对象

自动加载创建的对象会自动加入到全局[EC](EC.md)对象中

>EasyChart需要一个div作为容器，采用自动加载，若容器没有id，会自动生成id

## 可用标签

| 标签        | 说明                                          |
| ----------- | --------------------------------------------- |
| EasyChart   | 必选 在此标签上创建EasyChart对象              |
| data-opt    | 可选 传递给EasyChart的参数集                  |
| data-api    | 可选 指定api                                  |
| data-onload | 可选 指定对象创建后的回调函数，必须为全局函数 |
| data-delay  | 可选 设置延时加载时间                         |
| data-post   | 可选 设置默认向服务器发送的请求 json格式      |
| data-debug  | 可选 开启调试模式，调试信息通过控制台输出     |
| data-width  | 可选 图表宽度                                 |
| data-height | 可选 图表高度                                 |


** 注意 **

图表一般需要设置高度才能正常显示

> data-opt中的属性与其他标签属性相冲突以标签属性为准
> 不设置delay的情况下，多个实例会以300ms为间隔依次向服务器请求数据


### 实例

HTML

```
<div
 EasyChart
 data-delay="1000"
 data-debug="true"
 data-onload="init"
 data-api="area.getall"
 data-opt='{"echarts_style":"macarons","loading_text":"loading ...","height":"100px"}'
 data-post='{"date":"some_msg","title":"这是一个图表"}'
></div>

```
