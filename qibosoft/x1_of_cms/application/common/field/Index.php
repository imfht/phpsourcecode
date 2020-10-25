<?php
namespace app\common\field;

/**
 * 列表页自定义字段
 */
class Index extends Base
{
    protected static $pagetype = 'index';
    
    /**
     * 取得某个字段转义后的HTML代码
     * @param array $field 具体某个字段的配置参数
     * @param array $info 信息内容 这里使用&是方便修改其值
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @return string[]|unknown[]|mixed[]
     */
    public static function get_field($field=[],&$info=[],$pagetype='list'){        
        return parent::format_field($field,$info,$pagetype);        
    }
    
    
    
}
