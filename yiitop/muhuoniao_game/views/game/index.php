<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<?php
		/*$gamesServerId=array_reverse($gamesServerId);
		//reset($gamesServerId);
		foreach ($gamesServerId as $key => $value) {
			//echo $value.'<br>';
			echo '<a target=_blanck href=http://localhost/games/gameslogin/index?gametype=mszj&serverid='.++$key.'>918双线'.$value.'区</a><br>';
		}*/
		?>
		<?php
		//$gamesServerId=array_reverse($gamesServerId);
		//echo count($gamesServerId);
		//var_dump($gamesServerId);
		$gamesIdCount=count($gamesServerId);
		for($i=1;$i<=count($gamesServerId);$i++){
			echo '<a target=_blanck href='.Yii::app()->params['returnHost'].'gameslogin/index?gametype='.$gamesAlias.'&serverid='.$gamesIdCount.'>918双线'.$gamesServerId[$gamesIdCount-1].'区</a><br>';
			$gamesIdCount--;
		}
		
		?>
    </body>
</html>
