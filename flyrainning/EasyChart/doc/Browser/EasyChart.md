# EasyChart 类






## opt设置

opt为EasyChart配置对象，默认配置如下

```
var opt=
{
  id:'',  // div id
  echarts_style:'macarons',  // echarts主题
  loading_text:'loading ...',  // 加载时显示内容
  uri:"/api/",  // api数据接口入口
  post:false,  // 请求数据时，post给服务器的数据，json
  width:"",  // 宽度
  height:"",  // 高度
  debug:false,  // 是否调试模式
  on:false,  // 事件绑定

  use_websocket:false //是否使用websocket通讯，预留配置，目前只能使用ajax，必须设置为false

}

```

可以实例化时传入opt对象`var chart=new EasyChart(opt);`

若使用autoload自动构建对象，可通过HTML标签data-opt指定json格式的opt

## 全局设置

为避免多个图表重复opt设置，EasyChart提供全局默认配置

设置全局变量 `EasyChart_config`

配置优先级：用户opt > EasyChart_config > opt默认配置

```
EasyChart_config={
	uri:"/api/index.php"
};
```


## 创建

### 直接创建EasyChart对象

```
<div id="chart1"></div>
<script>
var chart=new EasyChart({
  id:"chart1"
  });
</script>
```
> 直接创建的EasyChart对象不纳入EC管理
> 可以通过EC.addItem(chart)添加到EC

### 通过EC创建

```
<div id="chart1"></div>
<script>
var chart=EC.add({
  id:"chart1"
  });
</script>
```

### 通过autoload创建

```
<div id="chart1" EasyChart data-api="chart.chart1"></div>
```

## 加载数据

autoload创建的对象会根据api和post配置自动请求数据

EC和直接创建的对象，需要调用`load(data)`方法加载数据，其中data为post到服务器的数据

```
<div id="chart1"></div>
<script>
EC.add({
  id:"chart1"
  });
var chart=EC.getByID("chart1");
chart.load({
  date:"2017-01-01",
  count:900
  });  
</script>
```

## 事件绑定

可以通过`opt`的`on`进行事件绑定，支持所有Echarts支持的事件

```
var chart=EC.add({
  id:"tools",
  api:"tools",
  height:"360px",
  on:{
    'click':function(p){
      console.log(p);
    }
  }
});

```

如果绑定的事件执行函数是一个字符串，会自动转换为闭包函数执行，并且可用变量`data`接收参数

除了通过opt以外，还可以通过`on()`方法进行事件绑定

```
var chart=EC.add({
  id:"tools",
  api:"tools",
  height:"360px"
});

chart.on("click","console.log(data);");
//等同于
chart.on("click",function(data){console.log(data);});

```

通过autoload自动创建的对象，也可以通过`data-opt`属性实现事件绑定


```
<div
 EasyChart
 data-api="area.getall"
 data-opt='{"loading_text":"loading ...","height":"100px","on":{"click":"console.log(data);"}}'
 data-post='{"date":"some_msg","title":"这是一个图表"}'
></div>

```
