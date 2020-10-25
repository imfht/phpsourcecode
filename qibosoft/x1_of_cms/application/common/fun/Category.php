<?php
namespace app\common\fun;

use think\Db;

/**
 * 辅栏目
 */
class Category{
    
    /**
     * 获取具体某个辅栏目信息 方便跨频道调用
     * @param number $id 辅栏目ID
     * @param string $dir 频道目录名
     * @param string $fomat 默认要转义字段
     * @return array|void|mixed|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function sort($id=0,$dir='',$fomat=true){
        $dir || $dir = config('system_dirname');
        
        static $array = [];
        
        $skey = $dir.$id;
        if($array[$skey]){
            return $array[$skey];
        }
        
        $info = Db::name($dir.'_category')->where('id',$id)->find();
        if (empty($info)) {
            return [];
        }
        $info['mid'] = -3;
        if ($fomat) {
            $info = fun('field@format',$info,'','show',$dir);
        }
        $array[$skey] = $info;
        return $info;
    }
    
    /**
     * 获取某个分类下的所有辅栏目信息 方便跨频道调用
     * @param number $fid 父ID
     * @param string $dir 频道目录名
     * @param string $fomat 默认要转义字段
     * @return array|void|mixed|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function sortlist($fid=0,$dir='',$fomat=true){
        $dir || $dir = config('system_dirname');
        $listdb = Db::name($dir.'_category')->where('pid',$fid)->column(true);
        if ($fomat) {
            foreach($listdb AS $key=>$rs){
                $rs['mid'] = -3;
                $listdb[$key] = fun('field@format',$rs,'','list',$dir);
            }
        }
        return $listdb;        
    }
    
    /**
     * 获取某个辅栏目或专题下面的所有内容数据
     * @param number $fid
     * @param string $dir
     * @param number $rows
     * @param string $fomat
     */
    public static function content($fid=0,$dir='',$rows=10,$order='A.list',$mid=1,$fomat=true){
        $dir || $dir = config('system_dirname');
        $listdb = Db::name($dir.'_info')->alias('A')->join($dir.'_content'.$mid.' B','A.aid=B.id','RIGHT')->where('A.cid',$fid)->order($order,'desc')->limit($rows)->select();
        if ($fomat) {
            foreach($listdb AS $key=>$rs){
                $listdb[$key] = fun('field@format',$rs,'','list',$dir);
            }            
        }
        return $listdb;
    }
    
}