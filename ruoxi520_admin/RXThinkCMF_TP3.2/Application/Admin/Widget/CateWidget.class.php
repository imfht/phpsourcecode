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
 * 分类-组件
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Widget;
use Think\Controller;
use Admin\Model\CateModel;
class CateWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 选择分类【多级选择】
     * 
     * @author 牧羊人
     * @date 2018-11-14
     */
    function select($cateId,$limit=2) {
    
        $cateList = array(
            1 => array('tname'=>'一级分类', 'code'=>'p_cate'),
            2 => array('tname'=>'二级分类', 'code'=>'cate'),
        );
        $cateMod = new CateModel();
        $info = $cateMod->getInfo($cateId);
        $level = $info['level'];
        $cateList[1]['list']  = $cateMod->getChilds(0);
        while ($level > 1) {
            $cateList[$level]['list'] = $cateMod->getChilds($info['parent_id']);
            $cateList[$level]['selected'] = $info['id'];
            $info = $cateMod->getInfo($info['parent_id']);
            $level--;
        }
        $cateList[1]['selected'] = $info['id'];
        $cateList = array_slice($cateList, 0, $limit);
        $this->assign('cateList', $cateList);
        $this->display("Cate:cate.select");
    
    }
    
    /**
     * 选择分类【层级结构】
     *
     * @author 牧羊人
     * @date 2018-10-16
     */
    function select2($param,$selectId) {
        $arr = explode('|', $param);
    
        //参数
        $idStr = $arr[0];
        $isV = $arr[1];
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
    
        //获取分类列表
        $cateMod = new CateModel();
        $cateList = $cateMod->where(['status'=>1,'mark'=>1])->select();
        $list = [];
        if($cateList) {
            foreach ($cateList as $val) {
                $str = '';
                for ($i=0;$i<$val['level']-1;$i++) {
                    $str .= "|--";
                }
                $str .= $val['name'];
                $list[] = $str;
            }
        }
    
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('cateList',$list);
        $this->assign("selectId",$selectId);
        $this->display("Cate:cate.select2");
    
    }
    
    /**
     * 分类多选
     * 
     * @author 牧羊人
     * @date 2018-10-19
     */
    function multipleSelect($param,$selectId,$map=[]) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $show_name = $arr[1];
        $show_value = $arr[2];
        
        $selectArr = explode(',', $selectId);
        
        $where = [
            'status'=>1,
            'mark'=>1
        ];
        $where = array_merge($where,$map);
        
        //获取分类列表
        $cateMod = new CateModel();
        $cateList = $cateMod->where($where)->select();
        if($cateList) {
            foreach ($cateList as &$val) {
                if(in_array($val['id'], $selectArr)) {
                    $val['selected'] = 1;
                }
            }
        }
        
        $this->assign('idStr',$idStr);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('cateList',$cateList);
        $this->display("Cate:cate.multipleSelect");
    }
    
}