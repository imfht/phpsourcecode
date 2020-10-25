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
 * API服务基类
 * 
 * @author 牧羊人
 * @date 2018-08-27
 */
namespace API\Service;
use Think\Model;
class APIServiceModel extends Model {
    public $mod;
    function __construct() {
        
    }
    
    /**
     * 分页初始化
     * 
     * @author 牧羊人
     * @date 2018-08-27
     * @param $page 页码
     * @param $perpage 每页数
     * @param $limit 分页
     */
    function initPage(&$page, &$perpage, &$limit) {
        $page = (int) $_REQUEST['page'];
        $perpage = (int) $_REQUEST['perpage'];
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : 10;
        $startIndex = ($page-1)*$perpage;
        $limit = "{$startIndex}, {$perpage}";
    }
    
    /**
     * 获取数据列表【带分页】
     *
     * @author 牧羊人
     * @date 2018-11-15
     */
    public function getList() {
    
        //获取参数
        $argList = func_get_args();
        //查询参数
        $map = isset($argList[0]['query']) ? $argList[0]['query'] : [];
        //排序
        $sort = isset($argList[0]['sort']) ? $argList[0]['sort'] : "id DESC";
        //回调方法名
        $func = isset($argList[1]) ? $argList[1] : "Short";
        //自定义MOD
        $mod = isset($argList[2]) ? $argList[2] : $this->mod;

        $map['mark'] = 1;
    
        //分页设置
        $page = $perpage = $limit = null;
        $this->initPage($page, $perpage, $limit);

        //获取数据总数
        $count = $mod->where($map)->count();
        
        //获取数据
        $result = $mod->where($map)->order($sort)->limit($limit)->getField("id",true);
    
        $list = [];
        if(is_array($result)) {
            foreach ($result as $val) {
                $info = $mod->getInfo($val);
                if(!$info) continue;
                if(is_object($func)) {
                    //方法函数
                    $data = $func($info);
                }else if(is_string($func)) {
                    //回调函数
                    $funcName = "get".ucfirst($func)."Info";
                    $data = call_user_func_array(array($this, $funcName),[$info]);
                }
                $list[] = $data;
            }
        }
    
        //结果
        $result = array(
            'count'     =>$count,
            'perpage'   =>$perpage,
            'page'      =>$page,
            'list'      =>$list ? $list : [],
        );
        return $result;
    }
    
    /**
     * 获取数据列表(不分页)
     *
     * 参数说明：1查询条件 2排序 3获取数据条数 4回调方法名
     *
     * @author 牧羊人
     * @date 2018-11-15
     */
    function getData() {
        
        //获取参数
        $argList = func_get_args();
        //查询参数
        $map = isset($argList[0]['query']) ? $argList[0]['query'] : [];
        //排序
        $sort = isset($argList[0]['sort']) ? $argList[0]['sort'] : "id DESC";
        //获取条数
        $limit = isset($argList[0]['limit']) ? $argList[0]['limit'] : '';
        //回调方法名
        $func = isset($argList[1]) ? $argList[1] : "Short";
        //自定义MOD
        $mod = isset($argList[2]) ? $argList[2] : $this->mod;
        
        $map['mark'] = 1;
        
        //获取数据
        if($limit) {
            $result = $mod->where($map)->order($sort)->limit($limit)->getField("id",true);
        }else{
            $result = $mod->where($map)->order($sort)->getField("id",true);
        }

        $list = [];
        if(is_array($result)) {
            foreach ($result as $val) {
                $info = $mod->getInfo($val);
                if(!$info) continue;
                if(is_object($func)) {
                    //方法函数
                    $data = $func($info);
                }else if(is_string($func)) {
                    //回调函数
                    $funcName = "get".ucfirst($func)."Info";
                    $data = call_user_func_array(array($this, $funcName),[$info]);
                }
                $list[] = $data;
            }
        }
        return $list;
    }
    
    /**
     * 回调函数
     *
     * @author 牧羊人
     * @date 2018-08-29
     */
    function getShortInfo($info) {
        //若子类不重写,直接返回
        return $info;
    }
    
}