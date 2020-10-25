<?php
class validate
{
    //去除字符串空格
    static function strTrim($str)
    {
        return preg_replace("/\s/","",$str);
    }
    //验证用户名*
    static function username($min,$max,$str,$type)
    {	
    	$denied=array('test','admin','root','manage','kefu');
        $str=self::strTrim($str);
		foreach($denied as $v)
		{
		    if(preg_match("/{$v}/i",$str))return false;
		}
        if($max<=strlen($str)&&$min>=strlen($str)){
             return false;
        } else{
            switch($type)
            {
                case "EN"://纯英文
                  return preg_match("/^[a-zA-Z]+$/",$str);
                  break;
                case "ENNUM"://英文数字//必须以 字母开始
                    return preg_match("/^[a-zA-Z]+[a-zA-Z0-9]+$/",$str);
                    break;
                case "ALL":    //允许的符号(|-_字母数字)
                    return preg_match("/^[\|\-\_a-zA-Z0-9]+$/",$str);
                    break;
            }
         }
    }
     //验证密码长度*
    static function password($min,$max,$str)
    {
        $str=self::strTrim($str);
         if(strlen($str)>=$min && strlen($str)<=$max){
            return true;
        }else{
             return false;
        }
     }
     //验证Email*
    static function is_email($str)
    {
       $str=self::strTrim($str);
       return preg_match("/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.){1,2}[a-z]{2,4}$/i",$str); 
    }
     
    static function sqlStr($str){
    	return !preg_match('/(select)|(update)|(insert)|(create)|(delete)/',$str);
    }
    //验证utf8中文
    static function chineseUtf8($min,$max,$str){
    	$str=self::strTrim($str);
    	$len=mb_strlen($str,'utf-8');
    	if($len>$max || $len<$min)
    	return false;
    	return preg_match("/^[\x7f-\xff]+$/",$str); 
    }
    //验证身份证(中国)
     static function idCard($str)
     {
         $str=self::strTrim($str);
         if(preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",$str))
         {
             return true;
        }else{
            return false;
          }
     }

static function mobilePhone($str){
	$str=self::strTrim($str);
	if(!preg_match('/^[\d]{11}$/',$str)){
		return false;
	}
	else{
		$tmp=substr($str,0,3);
		$number=array('a'=>'130',
					  'b'=>'131',
					  'c'=>'132',
					  'd'=>'133',
					  'e'=>'134',
					  'f'=>'135',
					  'g'=>'136',
					  'h'=>'137',
					  'i'=>'138',
					  'j'=>'139',
					  'k'=>'188',
					  'l'=>'189',
					  'm'=>'150',
					  'n'=>'151',
					  'o'=>'152',
					  'p'=>'153',
					  'q'=>'154',
					  'r'=>'155',
					  's'=>'156',
					  't'=>'157',
					  'u'=>'158',
					  'v'=>'159');
		$tmp=array_search($tmp,$number);
		if(!tmp){
			return false;
		}
	}
	return true;
}
    //验证座机电话
     static function Phone($str,$type)
    {
         $str=self::strTrim($str);
        switch($type)
        {
            case "CHN":
                 if(preg_match("/(^([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$)|(^[0-9]{7,8}$)/",$str))
                {
                     return true;
                }else{
                     return false;
                  }
                break;
            case "INT":
                 if(preg_match("/^[0-9]{4}-([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str))
                {
                     return true;
                }else{
                    return false;
                 }
                break;
         }
     }
     //验证中国邮编
     static function postCode($str){
     	return preg_match('/^[0-9]{6}$/',$str);
     }
     
 }