
<?php

# read from an external src
# $adoffer is a sub class extending WebApp
$myObj = $adoffer->getBy('url:', array(
	'target'=>HASOFFERS_API_URL, 'parameter'=>array(
		'NetworkId' => 'xxxxx',
		'Target' => 'Affiliate_Offer',
		'offer_id' => $k, //-
		'params' => array(
			'sub_id' => '{channel}',
			'info'=>'{info}'
			),
		'options' => array(
			'tiny_url' => 0
			)
		)
	));
if($myObj[0]){
	$myObj = $myObj[1]['content'];    
}

# save to file
# $adoffer is a sub class extending WebApp
$file = '/tmp/offer_'.$k.'/'.$k.'.txt';
print "\nsave to file:[$file]";
$writeResult = $adoffer->setBy("file:", array(
	'target'=>$file,
	'content'=> "click:[$click]\nname/title:[".$nameArr[0]."]\ncountry:[".$nameArr[3]."]\ndesc:[".$ioffer['description']."]",
	'islock'=>1,
	'isappend'=>1,
	''=>''
	));
print "\n";
print_r($writeResult);

# read from file
print "\nread from file:[$file]";
$readResult = $adoffer->getBy('file:', array(
	'target'=>$file
	));
print "\n";
print_r($readResult);

?>