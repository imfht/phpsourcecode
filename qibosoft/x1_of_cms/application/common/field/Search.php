<?php
namespace app\common\field;
use app\common\util\Field_filter;
/**
 * 自定义字段 搜索查找方式
 */
class Search
{
    /**
     * 字段查询方法
     * @param array $type
     * @param string $value
     * @param array $farray
     * @return string[]
     */
    public static function get_map($type=[],$value='',$farray=[]){
        if( $farray['range_opt'] && !in_array($farray['type'], ['select','radio','checkbox']) ){
//             $mod1 = '>=';
//             $mod2 = '<=';
            $array = Field_filter::format_range_opt($farray['range_opt']);
            foreach($array AS $vs){
                if($vs[0]==input("{$farray['name']}_1")){
                    $mod1 = $vs[2];
                    $mod2 = $vs[3];
                    break;
                }
            }
            $map = [
                    [$mod1,input("{$farray['name']}_1")],
                    [$mod2,input("{$farray['name']}_2")],
                    'and'
            ];
        }else{
            if(in_array($type, ['radio','select'])){
                $map = ['=',$value];
            }elseif($type=='checkbox'){
                $map = ['like',"%,$value,%"];
            }else{
                $map = ['like',"%$value%"];
            }
        }
        return $map;
    }
}
