<?php 

$chart=new EasyChart("Map_China");

$title=EasyChart::getVar("title");
$chart->title($title);


$chart->add("北京",365,"123");//北京有附加数据 123 通过js事件绑定实现点击北京弹出123
$chart->add("上海",200);
$chart->add("山东",300);
$chart->add("广东",5200);

$chart->setJS("

EasyChart.on('click',function(p){
  console.log(p);
  if (p.data.data) alert(p.data.data);
});

");

$chart->out();


 

 ?>