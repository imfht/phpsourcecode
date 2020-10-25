<?php
function smarty_modifier_date($num,$format){
	if($num=='now' || $num=='') $num=time();
	return @date($format,$num);
}
?>