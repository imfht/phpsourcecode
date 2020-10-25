<?php
namespace app\common\model;

class SettingGroup extends App
{
    public $display = 'title';
    
    public function initialize()
    {

        $this->form = array(
            'id' => array(
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
            ),
            'title' => array(
                'type' => 'string',
                'name' => '分组组名',
                'elem' => 'text'
            ),
            'list_order' => array(
                'type' => 'integer',
                'name' => '排序依据',
                'elem' => 'number',
            ),
        );

        call_user_func(array('parent', __FUNCTION__));
    }

    protected $validate = array(
        'title' => array(
            'rule' => 'require'
        )
    );
}
