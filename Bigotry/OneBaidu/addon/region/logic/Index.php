<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace addon\region\logic;

use app\common\model\Addon;

/**
 * 省市县三级联动插件逻辑
 */
class Index extends Addon
{

    /**
     * 组合下拉框选项信息
     */
    public function combineOptions($id = 0, $list = [], $default_option_text = '')
    {
        
        $data = "<option value =''>$default_option_text</option>";
        
        foreach ($list as $vo)
        {
            $data .= "<option ";
            
            if ($id == $vo['id']) : $data .= " selected "; endif;
            
            $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
        }
        
        return $data;
    }
    
    /**
     * 获取区域列表
     */
    public function getRegionList($where = [])
    {
        
        $cache_key = 'cache_region_' . md5(serialize($where));
        
        $cache_list = cache($cache_key);
        
        if (!empty($cache_list)) : return $cache_list; endif;
        
        $list = $this->modelRegion->getList($where, true, 'id', false);
        
        !empty($list) && cache($cache_key, $list);
        
        return $list;
    }
}
