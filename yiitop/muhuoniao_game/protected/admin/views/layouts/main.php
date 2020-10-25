<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/system/css/header.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/system/css/side_left.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/system/css/side_right.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/system/css/fotter.css" type="text/css" media="screen" />
<?php Yii::app()->clientScript->registerCoreScript('jquery');?>
<?php
	echo '<title>'; 
	$controller = Yii::app()->controller->id;
	switch ($controller)
	{
		case 'order':
			echo '订单管理';break;
		case 'user':
			echo '用户管理';break;
		case 'member':
			echo '会员管理';break;
		case 'article':
			echo '文章管理';break;
		case 'games':
			echo '游戏管理';break;
		case 'gamesApi':
			echo '游戏API管理';break;
		case 'articleType':
			echo '栏目管理';break;
		default:
			echo '未知管理';
	}
	echo '</title>';	
?>
</head>

<body>
<script type="text/javascript">
$(function(){
	var $deletButton = $("#side_right h3 .manage a");
	$deletButton.click(function(){
		firm();
	})
})

function firm()
{
	if(confirm('你确认删除数据吗！')){
		var data=new Array();
        $("input:checkbox[name='user-grid_c0[]']").each(function (){
            if($(this).attr("checked")=='checked'){
                    data.push($(this).val());
            }
        });
        if(data.length > 0){
			$.ajax({
				url:"<?php echo Yii::app()->request->baseUrl;?>/admin.php/<?php echo Yii::app()->controller->id;?>/deleteAll",
				type:"POST",
				data:{'selectdel[]':data,'class':'<?php echo Yii::app()->controller->id;?>'},
				success:function(data){
					if (data=='ok'){
						alert('删除成功！');
						window.location.href='<?php echo Yii::app()->request->baseUrl;?>/admin.php/<?php echo Yii::app()->controller->id;?>/admin';
					}else{
						alert(data);
					}
				},
				error:function(){alert('没有权限')},
			});
        }else{
            alert("请选择要删除的选项!");
        }
	}	
}
</script>
<div id="container">
<!---------------header---------------->
<div id="header">
<h1>918游戏后台管理系统</h1>
<p><?php if(empty(Yii::app()->user->id)){ echo '您还没有登录，请您先登录然后在进行操作';}else{ echo '<a target="_blank" href="/">查看前台</a>  你好<a href="#">'.Yii::app()->user->name.'</a>，欢迎登陆'.CHtml::encode(Yii::app()->name).'<a href="'.Yii::app()->baseUrl.'/admin.php/site/logout">注销登陆</a> ';} ?></p> 
<div class="nav">
<ul class="right_img">
<li style="background:url(<?php echo Yii::app()->request->baseUrl;?>/system/images/nav_left.png) no-repeat left; padding-left:10px;"><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/user/index"><span>用户管理</span></a></li>
<li><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/article/index"><span>文章管理</span></a></li>
<li><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/articleType/index"><span>栏目管理</span></a></li>
<li><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/games/index"><span>游戏管理</span></a></li>
<li><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/gamesApi/index"><span>游戏API</span></a></li>
<li><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/member/index"><span>会员管理</span></a></li>
<li style="background:url(<?php echo Yii::app()->request->baseUrl;?>/system/images/nav_right.png) no-repeat right;padding-right:10px;"><a href="<?php echo Yii::app()->baseUrl;?>/admin.php/order/index"><span>订单管理</span></a></li>
</ul>
</div>
</div>
<!---------------header end---------------->

<?php echo $content;?>

<!---------------fotter---------------->
<div id="fotter">
<p>918后台管理系统</p>
</div>
<!---------------fotter end---------------->
</div>
</body>
</html>