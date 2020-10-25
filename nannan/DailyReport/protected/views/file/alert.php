<?php $this->Widget('ext.highcharts.HighchartsWidget',array(
	'options'=>'{
		"title":{"text":"fruit sales"},
		"xAxis":{
			"categories":["Apples","Bananas","Oraneges"]
		},
		"yAxis":{
			"title":{"text":"Fruit eaten"}
		},
		"series":[
			{"name":"Jane","data":[1,0,4]},
			{"name":"John","data":[5,7,3]}
		],
		"themes":{"themes":"grid"},
	}'
));?>
<?php $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
      'title' => array('text' =>$name),
      'xAxis' => array(
         'categories' =>$type,
      ),
      'yAxis' => array(
         'title' => array('text' => 'Fruit eaten')
      ),
      'series' => array(
         array('name' => 'Jane', 'data' => array(1, 0, 4)),
         array('name' => 'John', 'data' => array(5, 7, 3))
      )
   )
));?>