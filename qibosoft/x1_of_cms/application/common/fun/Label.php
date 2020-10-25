<?php
namespace app\common\fun;

/**
 * 标签用到的相关函数
 */
class Label{
    
    /**
     * 获得标签动态变量参数
     * @param string $code 比如 fid=$fid&uid=$info[uid]&pid=$info.pid
     * @return void|string
     */
    public static function get_union($code=''){
        if($code==''){
            return ;
        }
        $array = [];
        $detail = strstr($code,'@') ? explode('@',$code) : explode('&',$code);
        foreach($detail AS $str){
            if( !strstr($str,'|') ){
                list($field,$value) = explode('|',preg_replace('/^([-\w\.]+)([<>=\*]+)([^<>=\*]+)/i','\\1|\\3',$str));
            }else{
                list($field,$mod,$value) = explode('|',$str);
            }
            $field = trim($field);
            $value = trim($value);
            if(substr($value,0,1)=='$'){    //存在变量的时候
                if(strstr($value,'.')){
                    $value = str_replace('.','[\'',$value).'\']';
                }
                $array[] = $field.'='.substr($value,1);
            }            
        }
        return implode(',',$array);
    }
    
    /**
     * 获取查询语句中的动态变量
     * @param unknown $field
     * @param unknown $value
     * @param unknown $cfg
     * @return string|unknown
     */
    private static function get_label_value($field,$value,$cfg){
        if( substr($value,0,1)=='$' ){            
            $value = isset($cfg[$field]) ? $cfg[$field] : null ;
//             $_v = substr($value,1);
//             $value = isset($cfg[$_v]) ? $cfg[$_v] : null ;
        }
        return $value;
    }
    
    /**
     * 查询数据库前, 格式化标签查询语句 
     * 比如 where="fid=$fid&uid=$info[uid]&pid=$info.pid" 多条件查询
     * @param string $code
     * @return unknown
     */
    public static function where($code='',$cfg=[]){
        if($code==''){
            return ;
        }
        if(strstr($code,'"')){
            $array = json_decode($code,true);
        }else{
            $detail = strstr($code,'@') ? explode('@',$code) : explode('&',$code);
            foreach($detail AS $str){
                if( !strstr($str,'|') ){
                    $str = str_replace('!=','<>',$str);
                    list($field,$value) = explode('|',preg_replace('/^([-\w\.]+)([<>=\*]+)([^<>=\*]+)/i','\\1|\\3',$str));
                    $mod = preg_replace('/^([-\w\.]+)([<>=\*]+)([^<>=\*]+)/i','\\2',$str);
                    if($value=="''"||$value=='NULL'){
                        $value='';
                    }
                    $field = trim($field);
                    if( substr($value,0,1)=='$' ){  //获取动态变量的具体值
                        $value = self::get_label_value($field,$value,$cfg);
                        if ($value===null) {
                            continue;
                        }
                    }
                    
                    if($mod=='*'){
                        $array[$field] = ['LIKE',"%$value%"];
                    }elseif( strstr($value,',') ){
                        $array[$field] = [$mod=='='?'in':'not in',explode(',',$value)];
                    }else{
                        $array[$field] = [$mod,$value];
                    }
                    self::check_where_range($array,$field);
                    continue;
                }
                //下面的计划要弃用
                list($field,$mod,$value) = explode('|',$str);
                $field = trim($field);
                $mod = trim($mod);
                $value = trim($value);
                if($value=="''"){
                    $value='';
                }
                if($mod=='='){
                    $array[$field] = $value;
                }else{
                    if( substr($value,0,1)=='$' ){
                        $array[trim($field)] = self::get_label_value(trim($field),$value,$cfg);
                    }elseif(strstr($value,',')){
                        $value = explode(',',$value);
                    }
                    $array[$field] = [$mod,$value];
                }
                //上面的计划要弃用                
            }
        }
        foreach($array AS $key=>$value){
            if(!preg_match("/^[\w\.]+$/", $key)){
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    /**
     * 范围搜索 
     * 区域范围比如 price_1>=0&price_2<=100 其中price才是真正的字段值
     * 时间范围处理
     * 第一种是:距离现在某段时间内的,比如7天内的信息 create_time<7 单位是天 比如最近3天或最近7天或最近30天
     * 第二种是:某年或某月或某周或某天的开始与结束时间,比如今天的数据或昨天的数据,或本周的数据或上周的数据 create_time=day2
     * @param array $array
     * @param string $field
     */
    private static function check_where_range(&$array=[],$field=''){
        if ( preg_match("/^([\w]+)_2$/", $field,$data) ) {    //范围搜索 比如 price_1>=0&price_2<=100 其中price才是真正的字段值
            if ($array["{$data[1]}_1"] && preg_match("/(<|>)/", $array["{$data[1]}_1"][0]) && preg_match("/(<|>)/", $array["{$data[1]}_2"][0]) ) {
//                 if( preg_match("/(time|date)$/", $data[1]) ){   //时间范围处理,比如只限昨天的信息 create_time>1&create_time<3 单位是天 今天内的信息就是 create_time>0&create_time<2
//                     if( is_numeric($array["{$data[1]}_2"][1]) && $array["{$data[1]}_2"][1]<3650 ){
//                         $array["{$data[1]}_1"][1] = time()-$array["{$data[1]}_1"][1]*3600*24;
//                         $array["{$data[1]}_2"][1] = time()-$array["{$data[1]}_2"][1]*3600*24;
//                         $array["{$data[1]}_1"][0] = str_replace('>', '<', $array["{$data[1]}_1"][0]);
//                         $array["{$data[1]}_2"][0] = str_replace('<', '>', $array["{$data[1]}_2"][0]);
//                     }
//                 }
                $array[$data[1]] = [
                        [ $array["{$data[1]}_1"][0] , $array["{$data[1]}_1"][1] ],
                        [ $array["{$data[1]}_2"][0] , $array["{$data[1]}_2"][1] ],
                        'and'
                ];
                unset($array["{$data[1]}_1"],$array["{$data[1]}_2"]);
            }
        }elseif( preg_match("/(time|date)$/", $field) ){        //时间范围选择,比如 create_time<3 这是距离当前某段时间内的, create_time=day3 这仅仅是前天的数据,不包含昨天今天的
            if (in_array($array[$field][0], ['<','<=']) && is_numeric($array[$field][1]) && $array[$field][1]<3650) {     //时间范围处理,距离现在某段时间内的,比如7天内的信息 create_time<7 单位是天
                $array[$field] = [
                        str_replace('<', '>', $array[$field][0]),
                        time()-$array[$field][1]*3600*24,
                ];
            }elseif($array[$field][0]=='=' && preg_match("/^(day|week|month|year)([\d]*)$/", $array[$field][1],$data)){     //某个周期内的时间段
//                 list($min,$max) = self::get_time_bynum($data[1],$data[2]);
//                 $array[$field] = [
//                         ['>',$min],
//                         ['<',$max],
//                         'and'
//                 ];
                $array[$field] = Time::only($data[1],$data[2]);
            }
        }
    }
    
    /**
     * 将弃用,建议使用 \app\common\fun\Time\only
     * 根据day今天 day2昨天 day3前天数据对应那天的开始与结束时间, 年月周同理,也是对应的那年或那月或那周的开始与结束时间
     * @param string $type
     * @param string $num 1可以不写,2就代表上一个周期,
     * @return number[]
     */
    public static function get_time_bynum($type='',$num=''){
        list($y,$m,$d,$w) = explode(' ',date('Y m d w'));
        if($type=='day'){
            $time = strtotime("{$y}-{$m}-{$d} 00:00:00");     //今天凌晨0点是分隔界
            if ($num>1) {                
                $min = $time - ($num-1)*3600*24;
                $max = $time - ($num-2)*3600*24;
            }else{
                $min = $time;
                $max = time();
            }
        }elseif($type=='week'){
            $w = $w==0 ? 7 : $w;
            $time = strtotime("{$y}-{$m}-{$d} 00:00:00") - ($w-1)*3600*24; //本周一凌晨0点的分隔界
            if ($num>1) {
                $min = $time - ($num-1)*3600*24*7;
                $max = $time - ($num-2)*3600*24*7;
            }else{
                $max = time();
                $min = $time;
            }
        }elseif($type=='year'){
            if ($num>1) {
                $y2 = $y - ($num-2);
                $y1 = $y - ($num-1);
                $max = strtotime("{$y2}-01-01 00:00:00");
                $min = strtotime("{$y1}-01-01 00:00:00");
            }else{
                $max = time();
                $min =  strtotime("{$y}-01-01 00:00:00");
            }
        }elseif($type=='month'){
            if ($num>1) {
                $y2 = $y1 = $y;
                $m2 = $m - ($num-2);
                if ($m2<1) {
                    $m2 +=12;
                    $y2--;
                }
                $m1 = $m - ($num-1);
                if ($m1<1) {
                    $m1 +=12;
                    $y1--;
                }
                $max = strtotime("{$y2}-{$m2}-01 00:00:00");
                $min = strtotime("{$y1}-{$m1}-01 00:00:00");
            }else{
                $max = time();
                $min =  strtotime("{$y}-{$m}-01 00:00:00");
            }
        }
        return [$min,$max];
    }
    
    /**
     * 通用标签用的
     * @param unknown $tag_name
     * @param unknown $cfg
     */
    public static function run_label($tag_name,$cfg){
        return controller('index/labelShow')->get_label($tag_name,$cfg);
    }
    
    /**
     * 圈子黄页店铺专用标签
     * @param unknown $tag_name
     * @param unknown $cfg
     */
    public static function run_hy($tag_name,$cfg){
        return controller('index/labelhyShow')->get_label($tag_name,$cfg);
    }
    
    /**
     * 表单标签
     * @param unknown $tag_name
     * @param unknown $cfg
     */
    public static function run_form_label($tag_name,$cfg){
        controller('index/labelShow')->get_form_label($tag_name,$cfg);
    }
    
    /**
     * 通用标签的分页AJAX地址
     * @param string $tag_name
     * @param unknown $dirname
     */
    public static function label_ajax_url($tag_name='',$dirname){
        controller('index/labelShow')->get_ajax_url($tag_name ,$dirname );
    }
    
    /**
     * 圈子黄页的分页AJAX地址
     * @param string $tag_name
     * @param unknown $dirname
     */
    public static function label_hy_ajax_url($tag_name='',$dirname){
        controller('index/labelhyShow')->get_ajax_url($tag_name ,$dirname );
    }
    
    /**
     * 列表页标签
     * @param unknown $tag_name
     * @param unknown $cfg
     * @return unknown
     */
    public static function run_listpage_label($tag_name,$cfg){
        return controller('index/labelShow')->listpage_label($tag_name,$cfg);  //返回分页代码
    }
    
    /**
     * 列表页显示分页
     * @param unknown $tag_name
     * @param unknown $info
     * @param unknown $cfg
     * @return unknown
     */
    public static function run_showpage_label($tag_name,$info,$cfg){
        return controller('index/labelShow')->showpage_label($tag_name,$info,$cfg);    //返回分页代码
    }
    
    /**
     * 列表页的分页AJAX地址
     * @param string $tag_name
     */
    public static function label_listpage_ajax_url($tag_name=''){
        controller('index/labelShow')->get_listpage_ajax_url($tag_name);
    }
    
    /**
     * 评论标签
     * @param unknown $tag_name
     * @param unknown $info
     * @param unknown $cfg
     */
    public static function run_comment_label($tag_name,$info,$cfg){
        controller('index/labelShow')->comment_label($tag_name,$info,$cfg);
    }
    
    /**
     * 论坛回复标签
     * @param unknown $tag_name
     * @param unknown $info
     * @param unknown $cfg
     */
    public static function reply_label($tag_name,$info,$cfg){
        controller('index/labelShow')->reply_label($tag_name,$info,$cfg);
    }
    
    /**
     * 各频道调用评论的接口
     * @param string $type 参数有三个，分别是 posturl 获取评论提交的地址，pageurl 获取评论的分页，list或留空即代表获取评论内容
     * @param number $aid 频道的内容ID
     * @param unknown $sysid 频道模块的ID，一般可以自动获取
     */
    public static function comment_api($type='',$aid=0,$sysid=0,$cfg=[]){
        static $data = null;
        $order = $cfg['order'];
        $by = $cfg['by'];
        $status = $cfg['status'];
        $page = $cfg['page'];
        $rows = $cfg['rows'];
        if(empty($sysid)){
            $array = modules_config(config('system_dirname'));
            $sysid = $array?$array['id']:0;
        }
        $parameter = ['name'=>$cfg['name'],'pagename'=>$cfg['pagename'],'sysid'=>$sysid,'aid'=>$aid,'rows'=>$rows,'order'=>$order,'by'=>$by,'status'=>$status];
        if($type=='posturl'){
            return purl('comment/api/add',$parameter);
        }elseif($type=='pageurl'){
            return purl('comment/api/ajax_get',$parameter);
        }elseif($type=='apiurl'){
            return purl('comment/api/act',$parameter);
        }else{
            $data = controller("plugins\\comment\\index\\Api")->get_list($sysid,$aid,$rows,$status,$order,$by,$page);
            //$data = $data ? getArray($data)['data'] : [];
            return $data;
        }
    }
    
    
    /**
     * 论坛回复
     * @param string $type 参数有三个，分别是 posturl 获取回复提交的地址，pageurl 获取回复的分页，list或留空即代表获取回复内容
     * @param number $aid 频道的内容ID
     */
    public static function reply_api($type='',$aid=0,$cfg=[]){
        static $data = null;
        $order = $cfg['order'];
        $by = $cfg['by'];
        $status = $cfg['status'];
        $page = $cfg['page'];
        $rows = $cfg['rows'];
        $parameter = ['name'=>$cfg['name'],'pagename'=>$cfg['pagename'],'aid'=>$aid,'rows'=>$rows,'order'=>$order,'by'=>$by,'status'=>$status];
        if($type=='posturl'){
            return auto_url('reply/add',$parameter);
        }elseif($type=='pageurl'){
            return auto_url('reply/ajax_get',$parameter);
            // if(is_object($data)){
            //return $data->render();   //分页代码
            //}
        }else{
            $data = controller('Reply','index')->get_list($aid,$rows,$status,$order,$by,$page);
            $listdb = $data ? getArray($data)['data'] : [];
            return $listdb;
        }
    }
   
   
}