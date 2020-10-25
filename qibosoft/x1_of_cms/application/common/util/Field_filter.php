<?php

namespace app\common\util;

class Field_filter{
    
    /**
     * 生成其它字段网址,不包含当前字段
     * @param string $field 字段变量名
     * @param number $mid 模型ID
     * @param string $dirname 频道目录名
     * @return string
     */
    public static function make_url($field='',$mid=0,$dirname=''){
        //$url = request()->url(true) . '?';
        $url = '';
        $input = input();
        $input['province_id'] && $url .= 'province_id=' . $input['province_id'] . '&';
        $input['city_id'] && $url .= 'city_id=' . $input['city_id'] . '&';
        $input['zone_id'] && $url .= 'zone_id=' . $input['zone_id'] . '&';
        $input['street_id'] && $url .= 'street_id=' . $input['street_id'] . '&';
        $array = self::get_field($mid,$dirname);
        foreach ($array AS $name=>$rs){            
            if($field==$name){
                 continue;             
            }
            if($rs['range_opt'] && !in_array($rs['type'], ['select','radio','checkbox'])){
                if($input["{$name}_1"]!=='' && $input["{$name}_1"]!==null && $input["{$name}_2"]!=='' && $input["{$name}_2"]!==null){
                    $url .= "{$name}_1" . '=' . $input["{$name}_1"] . '&' . "{$name}_2" . '=' . $input["{$name}_2"] . '&' . "{$name}" . '=' . $input["{$name}"] . '&';
                }
            }else{
                if($input[$name]!==''&&$input[$name]!==null){
                    $url .= $name . '=' . $input[$name] . '&';
                }
            }
        }
        return $url;
    }
    
    /**
     * 取出筛选字段,参数已转为数组
     * @param number $mid 模型ID
     * @param string $dirname 频道目录名
     * @return array[]|string[]
     */
    public static function get_field($mid=0,$dirname=''){
        $data = [];
        $array = get_field($mid,$dirname);
        foreach ($array AS $rs){
            if(!$rs['ifsearch']){
                continue;
            }
            if(!in_array($rs['type'], ['select','radio','checkbox']) && empty($rs['range_opt'])){
                continue ;      //只有下拉框,单选框 复选框才能有列表筛选
            }
            if ($rs['range_opt'] && !in_array($rs['type'], ['select','radio','checkbox'])) {
                $rs['options'] = self::format_range_opt($rs['range_opt']);
            }else{
                $rs['options'] = str_array($rs['options']);    //转义成数组
            }            
            $data[$rs['name']] = $rs;
        }
        return $data;
    }
    
    /**
     * 格式化范围筛选字段
     * @param string $str
     * @return unknown[]|string[]|array[]
     */
    public static function format_range_opt($str=''){
        $str =  trim($str, " ,;\r\n|");
        $str = str_replace("\r","",$str);
        $detail = explode("\n",$str);
        foreach($detail AS $str2){
            list($v,$title,$mod) = explode('|', $str2);
            if($mod){
                list($mod1,$mod2) = explode(',',$mod);
            }else{
                $mod1='>=';
                $mod2='<=';
            }
            list($v1,$v2) = explode(',',$v);
            $array[$title] = [$v1,$v2,$mod1,$mod2];
        }
        return $array;
    }
}