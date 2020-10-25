<?php


$chart=new EasyChart("bar");

$title=EasyChart::getVar("title");
$chart->title($title);

$chart->add("apple",365);
$chart->add("banana",200);
$chart->add("orange",180);

$chart->out();


 ?>
