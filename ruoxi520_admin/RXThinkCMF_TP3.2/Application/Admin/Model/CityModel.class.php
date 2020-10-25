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
 * @date 2018-07-17
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class CityModel extends CBaseModel {
    function __construct() {
        parent::__construct('city');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '城市名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,50', '城市名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
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
     * @date 2018-07-17
     */
    function getChilds($parentId,$flag=false) {
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
     * @date 2018-07-17
     */
    function getCityName($cityId, $delimiter="", $isReplace=false) {
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
     * 获取所有城市缓存
     *
     * @author 牧羊人
     * @date 2018-03-07
     */
    function getAll(){
        return $this->getFuncCache("all");
    }
    
    /**
     * 查询所有城市缓存
     *
     * @author 牧羊人
     * @date 2018-07-17
     */
    public function _cacheAll(){
        return $this->getChilds(1,true);
    }
    
}