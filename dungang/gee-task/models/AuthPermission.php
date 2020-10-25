<?php
namespace app\models;


/**
 * 系统权限
 *
 * @author dungang
 *        
 */
class AuthPermission extends AuthItem
{

    public $parent;

    public function init()
    {
        $this->type = parent::TYPE_PERMISSION;
        parent::init();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['parent', 'string'];
        return $rules;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['parent'] = '上级权限';
        return $labels;
    }
}
