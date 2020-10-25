# EasyChart
Easy way to use echarts with php


## Dependencies

Built on top of

[ECharts](https://github.com/ecomfe/echarts)

[jQuery](http://jquery.com/)


## Installing

load it by script tag

we need jquery

```
<script src="jquery.min.js"></script>
```

we need echarts , echarts-gl for 3D

```
<script src="echarts.min.js"></script>
<script src="echarts-gl.min.js"></script>
```

EasyChart

```
<script src="EasyChart.js"></script>
```


## Basic Usage

Browser

```
<!-- jquery and echarts -->
<script src="jquery.min.js"></script>
<script src="echarts.min.js"></script>

<script src="EasyChart.min.js"></script>


<div EasyChart data-api="chart.sum"></div>

```

Server

/api/index.php

```
<?php
require "EasyChart/dist/Server/loader.php";

EasyChart::server();
?>
```

/api/chart/sum.php  ( for api `chart.sum` )

```
<?php
if (!defined('EasyChart')){
	die('Access denied');
}

$chart=new EasyChart("bar");
$chart->title("This is a chart");

$chart->add("apple",365);
$chart->add("banana",200);
$chart->add("orange",180);

$chart->out();
?>

```

## Document

[EasyChart Doc](doc/README.md)

## Example

copy `example` and `dist` to your php server WWWROOT and run

Or just run `test/run.sh` for docker

## License

EasyChart is available under the BSD license.
