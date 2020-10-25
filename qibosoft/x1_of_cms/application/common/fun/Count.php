<?php
namespace app\common\fun;

use think\Db;

/**
 * 数据统计使用
 */
class Count{
    /**
     * 统计内容条数
     * @param string $table 数据表名,不要加前缀
     * @param number|string|array $uid 为数字的话,就指定为用户UID, 也可以设置为查询数组
     * @param array $map 附加查询数组
     * @return number|string
     */
    public static function Info($table='',$uid=0,$map=[]){
        if (preg_match('/^qb_/i', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (is_array($uid)) {
            $map = $uid;
        }elseif(is_numeric($uid) && $uid>0){
            $map['uid'] = intval($uid);
        }elseif(strstr($uid,'=')){
            $map = Label::where($uid);
            if (isset($map['uid']) && ($map['uid']==0||$map['uid']=='my')) {
                $map['uid'] = login_user('uid');
            }
        }
        return Db::name($table)->where($map)->count('*');
    }
    
    /**
     * 标签统计数据使用
     * @param array|string $str
     * @return string[]|number[]|string[]
     */
    public static function label($str=[]){
        if (is_array($str)) {
            $cfg = $str;            
        }else{
            //若有where=条件的话,必须放在最后面
            $str =  preg_replace_callback("/&where=(.*?)$/is", function($array){
                return '&where='.urlencode($array[1]);
            }, $str); 
            parse_str($str, $cfg);
        }
        $table = $cfg['table_name'];
        if (empty($table)) {
            return [$table.'无数据来源选项'];
        }
        if ($cfg['count_type'] && $cfg['count_type']!=1) {
            $cfg['sum_field'] = $cfg['count_type'];
        }
        if ($cfg['count_type'] && empty($cfg['sum_field'])) {
            return ['求和字段为空'];
        }
        if (!is_table($table)){
            return [$table.'数据表不存在'];
        }
        $fields = table_field($table);
        $time_field = $cfg['time_field'];
        
        if ($time_field && !in_array($time_field, $fields)) {
            return ['时间字段有误'];
        }
        if ($cfg['count_type'] && !in_array($cfg['sum_field'], $fields)) {
            return ['求和字段有误'];
        }
        
        if ($cfg['showtime'] && empty($time_field)){            
            if (in_array('create_time', $fields)) {
                $time_field = 'create_time';
            }elseif (in_array('posttime', $fields)) {
                $time_field = 'posttime';
            }else{
                foreach($fields AS $v){
                    if (strstr($v,'time')) {
                        $time_field = $v;
                        break;
                    }
                }                
            }
            if (empty($time_field) && in_array('list', $fields)) {
                $time_field = 'list';
            }
        }
        $data = [];
        $map = $cfg['where'] ? Label::where($cfg['where']) : [];
        if (isset($map['uid']) && ($map['uid']==0||$map['uid']=='my')) {
            $map['uid'] = login_user('uid');
        }
        if ($cfg['showtime'] && $time_field) {
            $detail = is_array($cfg['showtime']) ? $cfg['showtime'] : explode(',',$cfg['showtime']);
            foreach ($detail AS $v){
                if ($v=='all' || empty($v)) {
                    unset($map[$time_field]);
                }else{
                    preg_match("/^([a-z]+)([\d]*)$/i", $v,$ar);
                    $map[$time_field] = Time::only($ar[1],$ar[2]);
                }
                $data[] = $cfg['count_type'] ? self::sum($table,$map,$cfg['sum_field']) : self::Info($table,$map);
            }
        }else{
            $data = [ $cfg['count_type'] ? self::sum($table,$map,$cfg['sum_field']) : self::Info($table,$map) ];
        }
        return $data;
    }
    

    /**
     * 求和
     * @param string $table 数据表名,不要加前缀
     * @param number $uid 为数字的话,就指定为用户UID, 也可以设置为查询数组
     * @param string $field 求和的具体字段
     * @param array $map 附加查询数组
     * @return number
     */
    public static function sum($table='',$uid=0,$field='uid',$map=[]){
        if (preg_match('/^qb_/i', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (is_array($uid)) {
            $map = $uid;
        }elseif(is_numeric($uid) && $uid>0){
            $map['uid'] = intval($uid);
        }elseif(strstr($uid,'=')){
            $map = Label::where($uid);
            if (isset($map['uid']) && ($map['uid']==0||$map['uid']=='my')) {
                $map['uid'] = login_user('uid');
            }
        }
        return Db::name($table)->where($map)->sum($field);
    }
    
    /**
     * 统计用户消费的金额
     * @param number $uid
     * @param unknown $time
     * @return number|mixed|\think\cache\Driver|boolean
     */
    public static function rmb($uid=0,$time=10800){
        $uid = intval($uid);
        $map = [
                'uid'=>$uid,
                'ifpay'=>1,
        ];
        $num = cache('user_rmb_total_'.$uid);
        if ($num=='') {
            $num = Db::name('rmb_infull')->where($map)->sum('money');
            cache('user_rmb_total_'.$uid,$num,$time);
        }        
        return $num;
    }
    
}