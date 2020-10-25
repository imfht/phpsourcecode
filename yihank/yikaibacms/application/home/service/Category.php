<?php
namespace app\home\service;
use think\template\TagLib;
/**
 * 标签接口
 */
class Category extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        //'close'     => ['attr' => 'time,format', 'close' => 0], //闭合标签，默认为不闭合
        'categorylist'      => ['attr' => 'name,key', 'close' => 1],
    ];

	/**
	 * 栏目列表
	 */
	public function tagcategorylist($tag,$content){

        $name = $tag['name']; // name是必填项，这里不做判断了
        if (empty($name)){
            echo "name 不能为空";
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //上级栏目
        $parent_id = isset($tag['parent_id'])?$tag['parent_id']:0;
        //指定栏目
        $class_id = isset($tag['class_id'])?$tag['class_id']:0;
        //栏目属性
        if (isset($tag['type'])){
            $type=$tag['type'];
        }else{
            $type=3;
        }
        //其他条件
        $where_other = isset($tag['where'])?$tag['where']:0;
        $parse = '<?php ';
        $parse .= '$__WHERE__ = model(\'kbcms/Category\')->listMap('.$parent_id.','.$class_id.','.$type.','.$where_other.');';

        $parse .= '$__LIST__ = model(\'kbcms/Category\')->loadData($__WHERE__);';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
	}
    
}
