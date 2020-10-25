<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 复选标签-组件
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Admin\Widget;
use Think\Controller;
class CheckboxWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 选择复选标签
     * 
     * @author 牧羊人
     * @date 2018-11-22
     */
    function select($param,$list=[],$selectId=[]) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $show_name = $arr[1];
        $show_value = $arr[2];
        $max = $arr[3] ? $arr[3] : 10;
        
        //数据处理
        $cols = [];
        if(is_array($list)) {
            $num = 0;
            foreach ($list as $val) {
                $num++;
                if($num>$max) {
                    break;
                }
                //标签ID
                $tagId = $val[$show_value];
                //标签名称
                $tagName = $val[$show_name];
                //是否选中
                $checked = in_array($tagId, $selectId);
                $cols[] = [
                    'id'=>$tagId,
                    'name'=>$tagName,
                    'on'=>$checked,
                    'elemId'=>$idStr,
                ];
            }
        }
        $this->assign('cols',json_encode($cols));
        $this->display("Widget:checkbox.select");
    }
    
}