<?php
namespace app\common\field;

/**
 * 内容页自定义字段
 */
class Show extends Base
{
    protected static $pagetype = 'show';
    
    /**
     * 取得某个字段转义后的HTML代码
     * @param array $field 具体某个字段的配置参数
     * @param array $info 信息内容
     * @return string[]|unknown[]|mixed[]
     */
    public static function get_field($field=[],$info=[]){        
        return [
                'value'     => parent::format_field($field,$info,'show'),
                'title'       => $field['title'],
        ];        
    }
}
