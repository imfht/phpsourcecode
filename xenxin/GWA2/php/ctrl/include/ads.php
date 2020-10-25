<?php
# ads system

if(!isset($ads)){
	$ads = new ADS();
}
$hm_homepage_adlist = $ads->getBy('*', "adplace='".$adplace."' and state=1");
if($hm_homepage_adlist[0]){
	$data['hm_'.$adplace.'_adlist'] = $hm_homepage_adlist[1];
}else{
	$data['hm_'.$adplace.'_adlist'] = array();
}

?>
