<?php
namespace App\Model;
/**
 * 系统菜单模型
 * @package App\Model
 */
class SysMenu extends \App\Component\BaseModel
{
    public $primary = 'menuId';
    /**
     * 表名
     * @var string
     */
    public $table = 'sys_menu';

    /**
     * 通过url获取菜单数据
     * @param $url
     * @return array
     */
    public function getMenuDataByUrl($url)
    {
        $menuData = $this->getone([
            'select' => 'menuId,menuName,url,parentId',
            'where' => "`url`='$url' AND `isDel`=0",
        ]);
        return $menuData;
    }
    /**
     * 显示菜单列表
     * @param string $moduleType
     * @return array
     */
    public function getMenuList($moduleType = 'admin')
    {
        $menuList = $this->gets([
            'select' => 'menuId,menuName,url,iconClass,parentId',
            'where' => "`moduleType`='$moduleType' AND `isDel`=0",
            'order' => "orderNum ASC,menuId ASC",
        ]);
        $newMenuList = [];
        foreach ($menuList as $v){
            //$v['url'] = strtolower($v['url']);
            $newMenuList[(int)$v['menuId']] = $v;
        }
        return $newMenuList;
    }
}