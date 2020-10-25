<?php
/**
 * form 验证 处理类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 * 
 * 字段验证类 字段处理
 * $check = Formcheck::checking($data, $message=null,array(
 *         'manager_name'=>array('func'=>array('isnotnull','is_username'),'note'=>array('账号不能为空！','账号格式错误')),
 *         'manager_password'=>array('func'=>'isnotnull','note'=>'密码不能为空！'),
 *         'manager_password2'=>array('func'=>'isnotnull','note'=>'重复密码不能为空！'),
 * 		'manager_ruleid'=>array('func'=>array('isnotnull','isequal'),'param'=>array(array('max'=>100,'min'=>20),22),'note'=>array('请选择角色！','不相等'))
 *     ));
 */
//templi::include_file();
namespace framework\libraries;
use framework\core\Abnormal,
    framework\Templi;

class FormCheck
{
    /**
     * 批量验证表单字段
     */
     public static function checking(&$data,&$message,$rule=array()){
        foreach($rule as $key=>$val){
            if(!isset($data[$key]))
                throw new Abnormal($key.'不存在', 500);
            if(is_array($val['func'])){
                $val['param'] =isset($val['param'])?$val['param']:NULL;
                for($i=0;$i<count($val['func']);$i++){
                   $param =isset($val['param'][$i])?$val['param'][$i]:NULL;
                   if(!self::$val['func'][$i]($data[$key],$param)){
                        $message = $val['note'][$i];
                        return false;
                        break;
                   } 
                }
            }else{
               $param =isset($val['param'])?$val['param']:NULL;
               if(!self::$val['func']($data[$key],$param)){
                    $message = $val['note'];
                    return false;
                    break;
               }
            }
             
        }
        return true;
     }
     public static function isUnique($data,$param=null){
        return !(Templi::getApp()->load->model($param['table'])->fetchOne(array($param['field']=>$data),$param['field']));
     }

    /**
     * 数字大小 在木范围内
     * @param $data
     * @param null $param
     * @return bool
     * @internal param array $params $param['max'] 最大值* $param['max'] 最大值
     * $param['min'] 最小值
     */
     public static function numRange($data,$param=null){
         return $data<=$param['max'] && $data>=$param['min'];
     }

    /**
     * @param $data
     * @param array $param
     * $param['encode'] 编码
     * $param['max']    最大长度
     * $param['min']    最小长度
     * @return bool
     */
     public static function strLength($data,$param=null){
         $c = mb_strlen($data, $param['encode']);
         return $c<=$param['max'] && $c>=$param['min'];
     }
     /**
      * 两个值是否相等
      */
     public static function isEqual($data,$param=null){
        return $data == $param;
     }
     /**
      *是否为手机号
      */
     public static function isMobile($data,$param=null){
        return preg_match("/^\d{11}$/i",$data);
     }
     /**
      * 是否为数字
      */
     public static function isNumber($data,$param=null){
        return is_numeric($data);
     }
     /**
      * 是否不为数字
      */
     public static function isNotNumber($data,$param=null){
        return !self::isnumber($data,$param=null);
     }
     /**
      * 验证是否为空
      */
     public static function isNotNull(&$data,$param=null){
        $data = trim($data);
        return $data!='';
     }
     /**
      * 验证邮箱
      */
     public static function isEmail($data,$param=null){
        return preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/",$data);
     }
     /**
      * 验证是否为数组
      */
     public static function isArray($data,$param=null){
        return is_array($data);
     }
     /**
      * 返回 转换html 标签为html 实体 后的文本
      */
     public static function new_htmlspecialchars(&$data){
        $data = new_htmlspecialchars($data);
        return true;
     }
     /**
      * 返回经过 安全过滤后的文本
      */
     public static function safeReplace(&$data){
        $data =safe_replace($data);
        return true;
     }
     /**
      * 返回经过 strip_tags 处理过的 文本
      */
     public static function new_strip_tags(&$data,$param=null){
        if(is_null($param)){
            $data = strip_tags($data);
        }else{
            $data = strip_tags($data,$param);
        }
        return true;
     }

    /**
     * 检查密码长度是否符合规定
     *
     * @param STRING $password
     * @param null $param
     * @return    TRUE or FALSE
     */
    public static function isPassword($password, $param=null) {
    	$strlen = strlen($password);
    	if($strlen >= 6 && $strlen <= 20) return true;
    	return false;
    }

    /**
     * 检查用户名是否符合规定
     *
     * @param string $username 要检查的用户名
     * @param null $param
     * @return    TRUE or FALSE
     */
    public static function isUsername($username, $param=null) {
    	$strlen = strlen($username);
    	if(self::isBadWord($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
    		return false;
    	} elseif ( 20 < $strlen || $strlen < 3 ) {
    		return false;
    	}
    	return true;
    }

    /**
     * 检测输入中是否含有 错误字符/敏感字符
     *
     * @param string $string 要检查的字符串名称
     * @param null $param
     * @return TRUE or FALSE
     */
    public static function isBadWord($string, $param=null) {
    	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
    	foreach($badwords as $value){
    		if(strpos($string, $value) !== FALSE) {
    			return TRUE;
    		}
    	}
    	return FALSE;
    }
}