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
 * TABLE数据选择-组件
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Admin\Widget;
use Think\Controller;
class TableWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * TABLE数据选择(包括：多选、单选)
     * 
     * @author 牧羊人
     * @date 2018-11-22
     */
    function select($param,$url,$cols=[],$selectStr,$selectId=[]) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $isV = $arr[1]==1 ? 'required' : '';
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        //类型：1复选框 2单选框
        $type = isset($arr[5]) ? $arr[5] : 1;
        $limit = isset($arr[6]) ? $arr[6] : 20;
        
        //列数据处理
        $list[] = [
            'type'=>$type==1 ? 'checkbox' : 'radio',
        ];
        if(is_array($cols)) {
            foreach ($cols as $val) {
                $item = explode('|', $val);
                $list[] = [
                    'field'=>$item[0],
                    'title'=>$item[1],
                    'width'=>isset($item[2]) ? (int)$item[2] : 100,
                    'align'=>'center',
                ];
            }
        }
        
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('limit',$limit);
        $this->assign('tableUrl',$url);
        $this->assign('cols',json_encode($list));
        $this->assign('selectStr',$selectStr);
        $this->assign('selectId',implode(',', $selectId));
        $this->display("Widget:table.select");
    }
    
}