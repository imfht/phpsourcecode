<?php
namespace app\common\fun;

class Sort{
    /**
     * 根据id获取栏目的名称 使用方法 fun('sort@name',$id)
     * @param number $id
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function name($id=0,$sys_type=''){
        return get_sort($id,'name','',$sys_type);
    }
    
    /**
     * 获取父辈及祖父,曾祖父,这样的上级栏目,一般用在面包屑导航,也包括自身,
     * @param number $id
     * @param string $sys_type
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function fathers($id=0,$sys_type=''){
        return get_sort($id,'','father',$sys_type);
    }
    
    /**
     * 只获取父栏目
     * @param number $id
     * @param string $sys_type
     */
    public function father($id=0,$sys_type=''){
        $array = sort_config($sys_type);
        $pid = $array[$id]['pid'];
        if($pid>0){
            return [
                    'id'=>$pid,
                    'name'=>$array[$pid]['name'],
            ];
        }
    }
    
    /**
     * 获取同级栏目,也包括本身
     * @param number $id
     * @param string $sys_type
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function brother($id=0,$sys_type=''){
        return get_sort($id,'name','brother',$sys_type);
    }
    
    /**
     * 获取所有子栏目,包括所有下下级
     * @param number $id
     * @param string $sys_type
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function sons($id=0,$sys_type=''){
        return get_sort($id,'name','sons',$sys_type);
    }
    
    /**
     * 只获取下一级的栏目
     * @param number $id
     * @param string $sys_type
     */
    public function son($id=0,$sys_type='',$moreCfg=null){
        $array = sort_config($sys_type);
        $s_array = [];
        foreach($array AS $key=>$rs){
            if($rs['pid']==$id){
                $s_array[$key] = empty($moreCfg) ? $array[$key]['name'] : $array[$key];
            }
        }
        return $s_array;
    }
    
    /**
     * 获取父级 同级 下一级 这三级的所有栏目
     * @param number $id
     * @param string $sys_type
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function family($id=0,$sys_type=''){
        return get_sort($id,'name','other',$sys_type);
    }
    
    /**
     * 只获取最顶级栏目
     * @param number $id
     * @param string $sys_type
     */
    public function top($sys_type='',$moreCfg=null){
        $array = sort_config($sys_type);
        $farray = [];
        foreach($array AS $key=>$rs){
            if($rs['pid']==0){
                $farray[$key] = empty($moreCfg) ? $rs['name'] : $rs;
            }
        }
        return $farray;
    }
}