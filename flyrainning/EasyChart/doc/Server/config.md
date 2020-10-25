# config

全局配置，作为默认配置，会被对象配置覆盖

文件`config.php`

返回一个配置数组，包含以下项目

```
array(
  'type'=>"bar", // 默认的图表类型
  'debug'=>false, // 是否开启调试
  'default'=>array(),// echarts的参数，将被应用于所有echarts作为默认设置
);

```

实例：

```
<?php return array(
  'type'=>"bar",
  'debug'=>true,
  'default'=>array(
    'dataZoom'=>array(
      'show'=>true,
      'start'=>0
    ),
    'toolbox'=>array(
      'show'=>true,
      'feature'=>array(
        'mark'=>array(
          'show'=>true,
          'readOnly'=>false,
        ),
        'dataView'=>array(
          'show'=>true,
        ),
        'restore'=>array(
          'show'=>true,
        ),
        'saveAsImage'=>array(
          'show'=>true,
        ),

      )
    ),
    'grid'=>array(
      'left'=>'60',
      'right'=>'10',
      'left'=>'10',
      'bottom'=>'60',
      'containLabel'=>true,
    ),
  ),

);
?>


```
