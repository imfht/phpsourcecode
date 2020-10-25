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
 * 菜单管理-模型
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class MenuModel extends BaseModel
{
    //设置数据表
    protected $name = 'menu';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 菜单类型
            if($info['type']) {
                $info['type_name'] = Config::get('adminconfig.menu_type')[$info['type']];
            }
            
            // 获取上级菜单
            if($info['parent_id']) {
                $pInfo = $this->getInfo($info['parent_id']);
                $info['parent_name'] = $pInfo['name'];
            }
    
            // URL设置
            if($info['type']==3) {
                $map = [
                    'parent_id'=>$id,
                    'type'=>4,
                    'name'=>"查看",
                    'is_show'=>1,
                    'mark'=>1,
                ];
                $result = $this->where($map)->find();
                if($result) {
                    $info['to_url'] = MAIN_URL . $result['url'] . $result['param'];
                }
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级菜单
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function getChilds($parentId, $isMenu=true)
    {
        $map = [
            'parent_id'=>$parentId,
            'mark'=>1,
        ];
        $result = $this->where($map)->order("sort_order asc")->select();
        $list = [];
        if($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if(!$info) continue;
                $info['title'] = $info['name'];
                $info['font'] = "larry-icon";
                $childList = $this->getChilds($id,$isMenu);
                if($childList) {
                    if($info['type']==3) {
                        if($isMenu) {
                            $info['children'] = $childList;
                        }else{
                            $info['funcList'] = $childList;
                        }
                    }else{
                        $info['children'] = $childList;
                    }
        
                }
                $list[] = $info;
            }
        }
        return $list;
    }
    
    /**
     * 获取菜单名称
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function getMenuName($menuId, $delimiter='/')
    {
        do {
            $info = $this->getInfo($menuId);
            $names[] = $info['name'];
            $menuId = $info['parent_id'];
        } while($menuId>0);
        $names = array_reverse($names);
        if (strpos($names[1], $names[0])===0) {
            unset($names[0]);
        }
        return implode($delimiter, $names);
    }
    
}