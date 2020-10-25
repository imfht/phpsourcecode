<?php

//金额单位换算
function price_convert($type='yuan',$price){
	if($type=='yuan'){
		$price = sprintf("%.2f",$price/100);
	}
	if($type=='fen'){
		$price = sprintf("%.2f",$price*100);
	}
	return $price;
}



