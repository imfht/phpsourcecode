# address API
---

## 选项

选项 | 类型 | 默认值 | 描述
--- | ---- | ------- | -----------
title | String | `'请选择地址'` | 窗口 title
prov | String | `'北京'` | 省级
city | String | `'北京市'` | 市级
district | String | `'东城区'` | 县区级
selectNumber | Int | `0` | 配置可选项(2只选省市，1只选省)
scrollToCenter | Boolean |  `false` | 打开选择窗口时已选项是否滚动到中央
autoOpen | Boolean |  `false` | 是否自动打开选择窗口
customOutput | Boolean |  `false` | 自定义选择完毕输出，不执行内部填充函数
selectEnd | Function|  `false` | 选择完毕回调事件 `return {prov,city,district,zip},address`
## 事件


```javascript
// 选择省级时触发
$("#address").on("provTap",function(event,activeli){
    console.log(activeli.text());
})
// 选择市级时触发
$("#address").on("cityTap",function(event,activeli){
    console.log(activeli.text());
})
```

事件          | 参数 | 描述
------------ | -------- | -----------
provTap| event, activeli | 选择省级时触发
cityTap| event, activeli, iscroll | 选择市级时触发

