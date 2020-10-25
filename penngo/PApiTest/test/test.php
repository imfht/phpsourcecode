<?php
 $url = 'http://localhost/papitest/test/api/upload.php';
$data = array(
    'id'=>1,
    'img'=>'@api/test.jpg'
 
		
);
$ch  =  curl_init ();
curl_setopt ( $ch ,  CURLOPT_URL ,  $url );
curl_setopt ( $ch ,  CURLOPT_POST ,  1 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ( $ch ,  CURLOPT_POSTFIELDS ,  $data );
$str = curl_exec ( $ch );
print_r($str);