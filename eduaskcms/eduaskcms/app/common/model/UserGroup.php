<?php
namespace app\common\model;

class UserGroup extends App
{
    public $display = 'title';
    public $assoc = array(
        'User' => array(
            'type' => 'hasMany'
        )
    );

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
                'name' => '组名',
                'elem' => 'text',
            ),
            'alias' => array(
                'type' => 'string',
                'name' => '别名',
                'elem' => 'text',
            ),
            'is_admin' => array(
                'type' => 'integer',
                'name' => '可登陆后台',
                'elem' => 'checker',
                'list' => 'checker.show',
            ),
        );

        call_user_func(array('parent', __FUNCTION__));
    }

    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请填写组名'
        )
    );
}
