<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<?php
$cs=Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
?>

        <title></title>
    </head>
    <body>
		<form action='' method="post">
			昵称：<input name="name" id="nameid" type="text" value="" onblur="dami()">
			密码：<input name="password" type="password" value="" >
			重复密码：<input id="repasswordid" type="password" value="" >
			<?php
				//echo CHtml::activeTextField($modelLogin,'verifyCode',array('size'=>10,'maxlength'=>10,'autocomplete'=>'on'));
			$this->widget('CCaptcha',array('showRefreshButton'=>array('style'=>'padding-left:-20px;'),'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;')));
			?>
			 <input type="submit" value="确认" />
		</form>
		<a onclick="dami()" href="#">点击事件</a>
    </body>
	<script   language="javascript">  
		function dami(){
				var name= txt=$("#nameid").val();
				$.post('/games/register/ajax',{name:name,type:name}, function(data){
					if(data=='namefalse'){
						alert("此用户名已经注册！");
					}
				});
			
			
		}
		
	</script>
</html>
