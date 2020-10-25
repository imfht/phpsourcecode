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
 * 城市-模型
 * 
 * @author 牧羊人
 * @date 2018-12-11
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class CityModel extends BaseModel
{
    // 设置数据表
    protected $name = 'city';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-11
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            //TODO...
        }
        return $info;
    }
    
    /**
     * 获取子级城市
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function getChilds($parentId, $flag=false)
    {
        $list = array();
        $result = $this->where([
            'parent_id' =>$parentId,
            'mark'      =>1
        ])->order("id asc")->select();
        if($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if($flag) {
                    $childList = $this->getChilds($id,$flag);
                    if(is_array($childList)) {
                        $info['children'] = $childList;
                    }
                }
                if($flag) {
                    $list[] = $info;
                }else{
                    $list[$id] = $info;
                }
            }
        }
        return $list;
    }
    
    /**
     * 获取城市名称
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function getCityName($cityId, $delimiter="", $isReplace=false)
    {
        do {
            $info = $this->getInfo($cityId);
            if ($isReplace){
                $names[] = str_replace(array("省","市","维吾尔","壮族","回族","自治区"), "", $info['name']);
            } else {
                $names[] = $info['name'];
            }
            $cityId = $info['parent_id'];
        } while($cityId>1);
        $names = array_reverse($names);
        if (strpos($names[1], $names[0])===0) {
            unset($names[0]);
        }
        return implode($delimiter, $names);
    }
    
    /**
     * 获取全部缓存
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function getAll()
    {
        return $this->getCacheFunc("all");
    }
    
    /**
     * 设置全部缓存
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function _cacheAll()
    {
        return $this->getChilds(1,true);
    }
    
}