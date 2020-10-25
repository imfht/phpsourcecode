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

//将内容进行UNICODE编码
function unicode_encode($str, $encoding = 'utf-8', $prefix = '&#', $postfix = ';') {
    //将字符串拆分
    $str = iconv("UTF-8", "gb2312", $str);
    $cind = 0;
    $arr_cont = array();
 
    for ($i = 0; $i < strlen($str); $i++) {
        if (strlen(substr($str, $cind, 1)) > 0) {
            if (ord(substr($str, $cind, 1)) < 0xA1) { //如果为英文则取1个字节
                array_push($arr_cont, substr($str, $cind, 1));
                $cind++;
            } else {
                array_push($arr_cont, substr($str, $cind, 2));
                $cind+=2;
            }
        }
    }
    foreach ($arr_cont as &$row) {
        $row = iconv("gb2312", "UTF-8", $row);
    }
 	$unicodestr = '';
    //转换Unicode码
    foreach ($arr_cont as $key => $value) {
        $unicodestr.= $prefix . base_convert(bin2hex(iconv('utf-8', 'UCS-4', $value)), 16, 10) .$postfix;
    }
 
    return $unicodestr;
}

/**
 * $str 原始中文字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码后的前缀，默认"&#"
 * $postfix 编码后的后缀，默认";"
 */
function unicode_decode($unistr, $encoding = 'utf-8', $prefix = '&#', $postfix = ';') {
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for ($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        }
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    }
    return iconv('UCS-2', $encoding, $unistr);
}

   
/**
 * $str Unicode编码后的字符串
 * $decoding 原始字符串的编码，默认GBK
 * $prefix 编码字符串的前缀，默认"&#"
 * $postfix 编码字符串的后缀，默认";"
 */
function unicodeDecode($unicode_str){
    $json = '{"str":"'.$unicode_str.'"}';
    $arr = json_decode($json,true);
    if(empty($arr)) return '';
    return $arr['str'];
}
/**
 * 判断物流插件是否安装
 * @return [type] [description]
 */
function delivery_addons()
{
    $delivery = '';
    if(modC('MUUSHOP_EXPRESS_ADDON','muushop') == 'kdniao') {
        $delivery = model('addons\kdniao\model\Kdniao')->shipper;
    }
    return $delivery;
}
