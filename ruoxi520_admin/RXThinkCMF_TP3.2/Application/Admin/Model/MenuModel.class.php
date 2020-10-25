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
 * @date 2018-07-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class MenuModel extends CBaseModel {
    public function __construct() {
        parent::__construct('menu');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '菜单名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,30', '菜单名称长度不合法!', self::EXISTS_VALIDATE, 'length',3),
//         array('icon', 'require', '请选择菜单ICON！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     *
     * @author 牧羊人
     * @date 2018-02-28
     */
    function getInfo($id,$flag=false) {
        $info = parent::getInfo($id);
        if($info) {
            
            //菜单类型
            if($info['type']) {
                $info['type_name'] = C('MENU_TYPE')[$info['type']];
            }
            
            //获取上级菜单
            if($info['parent_id']) {
                $pInfo = $this->getInfo($info['parent_id']);
                $info['parent_name'] = $pInfo['name'];
            }
    
            //URL设置
            if($flag) {
                if($info['type']==3) {
                    $result = $this->where([
                        'parent_id'=>$id,
                        'type'=>4,
                        'name'=>"查看",
                        'is_show'=>1,
                        'mark'=>1,
                    ])->find();
                    if($result) {
                        $info['to_url'] = MAIN_URL . $result['url'] . $result['param'];
                    }
                }
            }
    
        }
        return $info;
    }
    
    /**
     * 获取子级菜单
     *
     * @author 牧羊人
     * @date 2018-03-01
     */
    function getChilds($parentId,$isMenu=true) {
        $map = [
            'parent_id'=>$parentId,
            'mark'=>1,
        ];
        $result = $this->where($map)->order("sort_order asc")->select();
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
     * @date 2018-11-30
     */
    function getMenuName($menuId,$delimiter='/') {
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