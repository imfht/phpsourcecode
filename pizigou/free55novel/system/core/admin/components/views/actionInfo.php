<?php if(Yii::app()->user->hasFlash('actionInfo'))
			 echo "<div class='flash-success' id='flash-success'><b>"."<img border='0' src='"
			 .BASEURL."/resources/icons/info.png' width='16px' height='16px'>提示："
			 .Yii::app()->user->getFlash('actionInfo')
			 ."</b><span style='float:right;margin-top:-11px'><a href='javascript:void(0)' onclick=\"$('#flash-success').slideToggle();\"><img border='0' src='"
			 .BASEURL."/resources/icons/cancel.png'></a></span></div>";
?>