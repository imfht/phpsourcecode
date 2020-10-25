<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-7-23
 * Time: 下午2:50
 */
//纯随机生成方法
function random($length, $pool = '')
{
    $random = '';

    if (empty($pool)) {
        $pool  = 'abcdefghkmnpqrstuvwxyz';
        $pool  .= '23456789';
    }

    srand ((double)microtime()*1000000);

    for($i = 0; $i < $length; $i++)
    {
        $random .= substr($pool,(rand()%(strlen ($pool))), 1);
    }

    return $random;
}

 $a=random(6);
print_r($a);

// 枚举生成方法
function shorturl($input) {
    $base32 = array (
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z"
    );

    $hex = md5($input);
    $hexLen = strlen($hex);
    $subHexLen = $hexLen / 8;
    $output = array();

    for ($i = 0; $i < $subHexLen; $i++) {
        $subHex = substr ($hex, $i * 8, 8);
        $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
        $out = '';

        for ($j = 0; $j < 6; $j++) {
            $val = 0x0000001F & $int;
            $out .= $base32[$val];
            $int = $int >> 5;
        }

        $output[] = $out;
    }

    return $output;
}
$a=shorturl("http://www.jb51.net");
print_r($a);
//62 位生成方法

function base62($x)

{

    $show= '';

    while($x> 0) {

        $s= $x% 62;

        if($s> 35) {

            $s= chr($s+61);

        } elseif($s> 9 && $s<=35) {

            $s= chr($s+ 55);

        }

        $show.= $s;

        $x= floor($x/62);

    }

    return $show;

}

function urlShort($url)

{

    $url= crc32($url);

    $result= sprintf("%u", $url);

    return base62($result);

}
echo "<br />";
echo urlShort("http://www.jb51.net/");

?>