<meta charset="utf-8" />
<?php 

require('../PHPTree.class.php');

$data = array(
	array(
		'id' => 1,
		'name' => '用户管理',
		'parent_id' => 0
	),
	array(
		'id' => 2,
		'name' => '用户列表',
		'parent_id' => 1
	),
	array(
		'id' => 3,
		'name' => '权限管理',
		'parent_id' => 1
	),
	array(
		'id' => 4,
		'name' => '文章管理',
		'parent_id' => 0
	),
	array(
		'id' => 5,
		'name' => '新闻',
		'parent_id' => 4
	),
	array(
		'id' => 6,
		'name' => '国内新闻',
		'parent_id' => 5
	)
);


$r = PHPTree::makeTreeForHtml($data);

echo '<h1>PHPTree树形结构</h1>';
echo '<select  style="width:300px;">';
foreach($r as $item){
	echo '<option>';
	echo str_repeat('......',$item['level']);
	echo $item['name'];
	echo '</option>';
}
echo '</select>';
?>