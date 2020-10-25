<?php 
require('../PHPTree.class.php');
$data = array(
	array(
		'id' => 1,
		'text' => '用户管理',
		'parent_id' => 0
	),
	array(
		'id' => 2,
		'text' => '用户列表',
		'parent_id' => 1
	),
	array(
		'id' => 3,
		'text' => '权限管理',
		'parent_id' => 1
	),
	array(
		'id' => 4,
		'text' => '文章管理',
		'parent_id' => 0
	),
	array(
		'id' => 5,
		'text' => '新闻',
		'parent_id' => 4
	),
	array(
		'id' => 6,
		'text' => '国内新闻',
		'parent_id' => 5
	)
);

$r = PHPTree::makeTree($data,array(
	'expanded' => true
));

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<link type="text/css" rel="stylesheet" href="ext5/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css" />
		<script src="ext5/ext-all.js"></script>
		<script>
		
		var treeData = JSON.parse('<?php echo json_encode($r);?>');
				
		var store = Ext.create('Ext.data.TreeStore',{
			root:{
				expanded:true,
				children:treeData
			}
		});
		
		Ext.onReady(function(){
			Ext.create('Ext.tree.Panel', {
				title: '简单的树',
				height:500,
				store:store,
				rootVisible:false,
				renderTo:Ext.getBody()
			});
		});
		
		</script>
	</head>
	<body>
	
	</body>
</html>