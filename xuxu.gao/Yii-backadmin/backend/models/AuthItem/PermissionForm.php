<?php
/**
 * 权限model
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 15:15
 */

namespace backend\models\AuthItem;


use yii\base\Model;
use yii\rbac\Item;

class PermissionForm extends Model {

    public  $id;
    public  $name;
    public  $type;
    public  $description;
    public  $rule_name;
    public  $data;
    public  $typename;

    public function init() {

        parent::init();
        $this->type = Item::TYPE_PERMISSION;//常量值 2
    }
    /**
     * 验证规则
     */
    public function rules(){

        return [

            ['name','unique','targetClass'=>'\backend\models\AuthItem\AuthItem','message' => '权限名称不能重复','on'=>['create','update']],
            ['name','required','message'=>'权限名称不能为空'],
            ['description','required','message'=>'请简单描述权限'],
            ['typename','required','message'=>'权限类型名称'],
        ];

    }
    // 更新 ，添加，场景
    public function scenarios()
    {
        return [
            'create' => ['name','description','typename'],
            'update' => ['description','typename']
        ];
    }
    //验证权限名是否重复
    public function beforeValidate()
    {
        $ret = AuthItem::find()->where(['name'=>$this->name,'type'=>$this->type])->asArray()->one();
        if($ret) {
            if ($ret['name'] == $this->name) {
                return true;
            } else {

                $this->addError('name', '权限名称不能重复');
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * 添加权限
     */
    public function addPermission(){

        $authItem               = new AuthItem();
        $authItem->name         = $this->name;
        $authItem->type         = $this->type;
        $authItem->description  = $this->description;
        $authItem->typename     = $this->typename;
        $authItem->save();
        return $authItem;
    }
    /**
     * 更新权限
     */
    public function updatePermission($name){

        $authItem = AuthItem::find()->where(['name'=>$name,'type'=>$this->type])->one();
        $authItem->name         = $this->name;
        $authItem->type         = $this->type;
        $authItem->description  = $this->description;
        $authItem->typename     = $this->typename;
        $authItem->save();
        return $authItem;

    }
}