<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/20
 * Time: 9:32
 */

namespace backend\services\menu\Impl;


use backend\models\Menu\Menu;
use backend\models\Menu\MenuForm;
use backend\services\menu\IMenuService;
use yii\helpers\ArrayHelper;
use Yii;
class MenuServiceImpl implements IMenuService{

    /**
     * 查询菜单
     * $where 条件数组
     * @return mixed
     */
    public function queryMenus($where = [])
    {
        $query = Menu::find();
        if(!empty($where)){

            $query = $query->where($where);
        }
        $list = $query->asArray()->all();
        $is_delete  = false;
        $is_edit = false;
        //检查权限
        if(Yii::$app->user->can('admin.menu.menudelete')){

            $is_delete = true;
        }
        if(Yii::$app->user->can('admin.menu.menuupdate')){

            $is_edit = true;
        }
        foreach ($list as &$item){

            $item['is_delete'] = $is_delete;
            $item['is_edit']   = $is_edit;
        }
        return $list;
    }

    /**
     * 添加菜单
     * @param array $params
     * @return mixed
     */
    public function menuAdd($params = [])
    {
        $menu = new MenuForm();
        $menu->setScenario('create');
        $menu->name         = $params['name'];
        $menu->parent_id    = $params['parent_id'];
        $menu->url          = $params['url'];
        $menu->description  = $params['description'];
        $menu->slug         = $params['slug'];
        if($menu->validate()){

            return $menu->addMenus();
        }else{
            return $menu;
        }
    }

    /**
     * 菜单列表
     * @param array $params
     * @return mixed
     */
    public function menuList($params = [])
    {
        $list    = Menu::dataPage($params);
        $is_delete  = false;
        $is_edit = false;
        //检查权限
        if(Yii::$app->user->can('admin.menu.menudelete')){

            $is_delete = true;
        }
        if(Yii::$app->user->can('admin.menu.menuupdate')){

            $is_edit = true;
        }
        foreach ($list as &$item){

            $item['is_delete'] = $is_delete;
            $item['is_edit']   = $is_edit;
        }
        return $list;
    }

    /**
     * 数量查询
     * @param array $params
     * @return mixed
     */
    public function menuCount($params = [])
    {
        return Menu::dataCount($params);
    }

    /**
     * 根据id查询数据
     * @param $id
     * @return mixed
     */
    public function menuById($id)
    {
        return Menu::findOne($id);
    }

    /**
     * 菜单更新
     * @param array $params
     * @return mixed
     */
    public function menuUpdate($params = [])
    {
        $menu = new MenuForm();
        $menu->setScenario('update');
        $menu->id           = $params['id'];
        $menu->name         = $params['name'];
        $menu->parent_id    = $params['parent_id'];
        $menu->url          = $params['url'];
        $menu->description  = $params['description'];
        $menu->slug         = $params['slug'];
        if($menu->validate()){

            return $menu->updateMenu();

        }else{

            return $menu;
        }
    }

    /**
     * 菜单列表
     * @param $list
     * @param int $pid
     * @return mixed
     */
    public function menuGroup($items)
    {
        $tree = [];
        $uid = Yii::$app->user->getId();
        $permissions = ArrayHelper::toArray(Yii::$app->authManager->getPermissionsByUser($uid));
        $permissions = array_keys($permissions);
        foreach ($items as $item){

            if(in_array($item['slug'],$permissions)) {

                $tree[$item['id']] = $item;
                $childreds = Menu::find()->where(['parent_id' => $item['id']])->asArray()->all();
                foreach ($childreds as $child){

                    if(in_array($child['slug'],$permissions)) {

                        $tree[$item['id']]['son'][] = $child;
                    }
                }

            }
        }
        return $tree;
    }

    /**
     * 根据id删除菜单
     * @param $id
     * @return mixed
     */
    public function menuDelete($id)
    {
        $menu = Menu::findOne($id);
        if($menu->parent_id == 0){

            return $menu->delete();
        }else{

            $menu->delete();
            return  Menu::deleteAll('parent_id = :id',[':id' =>$id]);
        }
    }
}