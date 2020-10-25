<?php
namespace app\common\model;
class UserLogin extends App
{
    public $assoc = array(
        'User' => array(
            'type' => 'belongsTo'
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
            'user_id' => array(
                'type' => 'integer',
                'name' => '所属用户',
                'foreign' => 'User.username',
                'elem' => 0,
                'list' => 'assoc'
            ),
            'ip' => array(
                'type' => 'string',
                'name' => '登录IP',
                'elem' => 0
            ),
            'success' => array(
                'type' => 'integer',
                'name' => '是否登录成功',
                'elem' => 0
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '登录时间',
                'elem' => 0,
                'list' => 'datetime'
            )
        );
        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

}
