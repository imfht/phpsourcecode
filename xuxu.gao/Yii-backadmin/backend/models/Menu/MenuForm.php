<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/20
 * Time: 15:29
 */

namespace backend\models\Menu;


use yii\base\Event;
use yii\base\Model;

class MenuForm extends Model{

    public $id;
    public $name;
    public $url;
    public $parent_id;
    public $slug;
    public $description;

    /**
     * 验证规则
     */
    public function rules(){

        return [

            ['name','unique','targetClass'=>'\backend\models\Menu\Menu','message' => '菜单名称不能重复','on'=>['create','update']],
            ['name','required','message'=>'菜单名称不能为空'],
            ['url','required','message'=>'访问地址不能为空'],
            ['slug','required','message'=>'请选择对应的菜单权限']
        ];

    }
    // 更新 ，添加，场景
    public function scenarios()
    {
        return [
            'create' => ['name','url','slug'],
            'update' => ['url','slug']
        ];
    }
    //验证用户名是否重复
    public function beforeValidate()
    {
        $ret = Menu::find()->where(['name'=>$this->name])->asArray()->one();
        if($ret) {
            if ($ret['id'] == $this->id) {
                return true;
            } else {

                $this->addError('name', '菜单名称不能重复');
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * 添加菜单
     */
    public function addMenus(){

        $menu = new Menu();
        $menu->name         = $this->name;
        $menu->parent_id    = $this->parent_id;
        $menu->url          = $this->url;
        $menu->description  = $this->description;
        $menu->slug         = $this->slug;
        return $menu->save();
    }
    /**
     * 更新菜单
     */
    public function updateMenu(){

        $menu = Menu::findOne($this->id);
        $menu->name         = $this->name;
        $menu->parent_id    = $this->parent_id;
        $menu->url          = $this->url;
        $menu->description  = $this->description;
        $menu->slug         = $this->slug;
        $menu->save();
        return $menu;
    }
}