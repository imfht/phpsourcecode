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
 * 下拉选择框组件【支持单选、多选】
 * 
 * @author 牧羊人
 * @date 2018-11-23
 */
namespace Admin\Widget;
use Think\Controller;
class SelectWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 下拉选择框($list可以是数组,也可以是URL地址)
     * tag_id|1|产品标签|name|id|1
     * 
     * @author 牧羊人
     * @date 2018-11-23
     */
    function select($param,$list=[],$selectId=[]) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $isV = $arr[1];
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        $type = $arr[5]==1 ? 'checkbox' : 'radio';
        
        //原始数据处理
        $data = [];
        if(is_array($list)) {
            foreach ($list as $val) {
                $data[] = [
                    'id'=>$val[$show_value],
                    'name'=>$val[$show_name],
                    'text'=>$val[$show_name],
                ];
            }
        }
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('type',$type);
        $this->assign('selectList',json_encode($data));
        $this->assign('selectId',json_encode($selectId));
        $this->assign('url',is_string($list) ? $list : '');
        $this->display("Widget:selectPlus.select");
    }
    
    /**
     * 选择搜索组件(支持搜索框、输入框)
     * 
     * @author 牧羊人
     * @date 2018-11-23
     */
    function selectSearch($param,$url,$cols=[],$selectStr,$selectId) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $isV = $arr[1];
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        $type = $arr[5]==1 ? 'sugTable' : 'sug';
        $limit = isset($arr[6]) ? $arr[6] : 20;
        
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
        $this->assign('type',$type);
        $this->assign('limit',$limit);
        $this->assign('searchUrl',$url);
        $this->assign('cols',json_encode($list));
        $this->assign('selectStr',$selectStr);
        $this->assign('selectId',$selectId);
        $this->display("Widget:selectSearch.select");
    }
    
}