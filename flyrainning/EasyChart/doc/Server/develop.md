# 附加图表

EasyChart默认支持的图表类型较少

但是，EasyChart提供了一个极简的插件扩展模式，可以快速开发所需图表

## 约定

图表插件必须遵守如下约定

1. 图表插件以单独一个类的形式提供，类名与文件名相同，存放到class目录
2. 命名规则：必须以`Chart_`为前缀，大小写敏感，名称首字母必须大写，例如Chart_My_new_bar.php
3. 图表插件必须继承ECData类，3D图表继承ECData3D类

## 实现接口

图表类需要实现以下函数

|   名称    | 参数  |                                      说明                                       |
| --------- | ----- | ------------------------------------------------------------------------------- |
| init      |       | 初始化函数，在图表被创建时被调用                                                |
| add       | $data | 添加数据，$data为用户调用EasyChart对象add()方法后传入的参数，转换为数组后的结果 |
| make_data |       | 数据打包，在输出前被调用，将数据打包成需要的格式                                |

> 若插件未实现相应接口，则使用ECData类对应默认接口

## 可用对象

图表类可直接使用以下对象

### option

等同于EasyChart的option对象，可对echarts选项进行配置

### data

数据打包后必须存入data变量

data变量会自动与option配置合并，并覆盖相同的配置

data变量必须包含series，承载具体数据

### d

多维数组，可作为add方法的数据缓存

d默认为20个元素的数组

## 实例

```

//这是一个按百分比显示的仪表盘

class Chart_Gauge extends ECData
{
  //单独定义一个本图表用的变量
  public $max;

  //初始化方法，在这里通过option设置图表的默认参数
  function init(){

    //如果之前默认配置中已经设置了dataZoom，清除配置，防止干扰
    $this->option->clean("dataZoom");

    //保险起见可以清除所有配置，config.php中的默认配置将无效
    //$this->option->clean();

    //设置图表需要的参数
    $this->option->set("tooltip",'
    {
        formatter: "{a} <br/>{b} : {c}%"
    }
    ');
    $this->option->set("series","
    [
        {
            name: '百分比',
            type: 'gauge',
            detail: {formatter:'{value}%'},
            data: [0]
        }
    ]
    ");

  }

  //数据添加函数
  function add($data){
    //用户调用add(p1,p2,p3),参数将自动转换为$data数组，此时$data=array(p1,p2,p3)

    if (isset($data[1])){  //  如果输入数据大于2个

      //将数据缓存到自带数组d中，当然，也可以使用其他自定义变量
      $this->d[0][]=array(
        'name'=>$data[0],
        'value'=>$data[1],

        // 这里的data可作为附加数据，可直接传递给echarts，在图表的点击事件中提取
        'data'=>(isset($data[2]))?$data[2]:'',

      );

      //计算显示的最大百分比
      $this->max=($data[1]<=100)?"100":ceil($data[1]/10)*10;
    }

  }

  //打包数据
  function make_data(){
    //若缓存没有数据，不进行处理
    if (empty($this->d[0])) return;

    //按照图表需要的格式填充data变量，具体格式参考echarts对应图表的配置信息
    $this->data=array(
      'series'=>array(
        array(
          "max"=>$this->max,
          "data"=>$this->d[0],
        )
      )
    );
  }
}

```
