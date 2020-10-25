
<?php
if (!defined('EasyChart')){
	die('Access denied');
}

//创建对象，指定图表类型为 bar3D
$chart=new EasyChart("bar3D");

$title=EasyChart::getVar("title");
$subtitle=EasyChart::getVar("subtitle");
$chart->option->set("title","
{
        text: '$title',
        subtext: '$subtitle',
        right: '10'
}
");

//添加数据
$chart->add("apple","one",365);
$chart->add("apple","two",390);
$chart->add("banana","one",200);
$chart->add("banana","two",260);
$chart->add("orange","one",180);
$chart->add("orange","two",130);

//输出
$chart->out();


?>
