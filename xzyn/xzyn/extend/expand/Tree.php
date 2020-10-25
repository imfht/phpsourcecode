<?php
namespace expand;

class Tree
{
    static public $treeList=array();   //存放无限分类结果如果一页面有多个无限分类可以使用 Tree::$treeList = array(); 清空

    public function __construct()
    {
        self::$treeList = array();   //为什么要重置为空数组，因为如果同一个文件，发生两次都调用 树 时，第二次调用会将第一次中的数据保存在 数组($treeList) 中，因此每次清空 数组($treeList)。
    }
    /**
     * 无限级分类
     * @access public
     * @param Array $data     //数据库里获取的结果集
     * @param Int $pid
     * @param Int $h_layer       //第几级分类
     * @return Array $treeList
     */
    public function create(&$data, $pid=0, $h_layer=1)
    {
            foreach($data as $key => $value){
                if($value['pid'] == $pid){
                    $value['h_layer'] = $h_layer;
                    self::$treeList[]=$value;
                    unset($data[$key]);
                    self::create($data,$value['id'],$h_layer+1);
                }
            }
        return self::$treeList;
    }
}

