<?php
/**
 * 角色model
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 15:14
 */

namespace backend\models\AuthItem;

use yii\base\Model;
use yii\rbac\Item;

class RoleFrom extends Model{

    public $name;
    public $type;
    public $description;
    public $rule_name;
    public $data;

    public function init() {

        parent::init();
        $this->type = Item::TYPE_ROLE;//yii-rbac-Role隐藏继承常量这里的值是1
    }
    /**
     * 验证规则
     */
    public function rules(){

        return [

            ['name','unique','targetClass'=>'\backend\models\AuthItem\AuthItem','message' => '角色名称不能重复','on'=>['create','update']],
            ['name','required','message'=>'角色名称不能为空']
        ];

    }
    // 更新 ，添加，场景
    public function scenarios()
    {
        return [
            'create' => ['name'],
            'update' => ['']
        ];
    }
    //验证角色名是否重复
    public function beforeValidate()
    {
        $ret = AuthItem::find()->where(['name'=>$this->name,'type'=>$this->type])->asArray()->one();
        if($ret) {
            if ($ret['name'] == $this->name) {
                return true;
            } else {

                $this->addError('name', '角色名称不能重复');
                return false;
            }
        }else{
            return true;
        }
    }
    //角色添加
    public function addRole(){

        $authItem               = new AuthItem();
        $authItem->name         = $this->name;
        $authItem->type         = $this->type;
        $authItem->description  = $this->description;
        $authItem->save();
        return $authItem;
    }
    //角色更新
    public function updateRole($name){

        $authItem               = AuthItem::find()->where(['name'=>$name,'type'=>$this->type])->one();
        $authItem->name         = $this->name;
        $authItem->description  = $this->description;
        $authItem->save();
        return $authItem;
    }

}