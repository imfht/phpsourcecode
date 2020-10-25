<?php
if (!defined('EasyChart')){
	die('Access denied');
}

$chart=new EasyChart("bar");

$title=EasyChart::getVar("title");
$chart->title($title);


$chart->xl("2016年");

$chart->add("apple",365);
$chart->add("banana",200);
$chart->add("orange",180);

$chart->xl("2017年");

$chart->add("apple",335);
$chart->add("banana",400);
$chart->add("orange",280);

// print_r($chart->data);
// $chart->data->make_data();
// print_r($chart->data);
// die();
$chart->out();


?>
