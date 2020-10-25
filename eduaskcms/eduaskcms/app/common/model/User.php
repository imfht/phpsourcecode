<?php
namespace app\common\model;

use \app\common\helper\Auth;

class User extends App
{
    public $display = 'username';
    public $parentModel = 'UserGroup';
    
    public $assoc = array(
        'UserGroup' => array(
            'type' => 'belongsTo'
        ),
        'Member' => array(
            'type' => 'hasOne',
            'deleteWith' => true
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
            'username' => array(
                'type' => 'string',
                'name' => '用户名',
                'elem' => 'text'
            ),
            'password' => array(
                'type' => 'string',
                'name' => '密码',
                'elem' => 'password',
                'list' => 'password'
            ),
            'user_group_id' => array(
                'type' => 'integer',
                'name' => '用户组',
                'elem' => 'select',
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'foreign' => 'UserGroup',
                        'field' => array('id', 'title'),
                    )
                ),
                'list' => 'assoc',
                'foreign' => 'UserGroup.title',

            ),
            'status' => array(
                'type' => 'string',
                'name' => '状态',
                'elem' => 'radio',
                'options' => array(
                    'verified' => '正常',
                    'unverified' => '未激活',
                    'banned' => '已禁用'
                ),
                'list' => 'options',
            ),
            'email' => array(
                'type' => 'string',
                'name' => '认证邮箱',
                'elem' => 'text',
                'list' => 'show'
            ),

            'logined' => array(
                'type' => 'datetime',
                'name' => '最后登录时间',
                'elem' => 0,
                'list' => 'datetime'
            ),
            'logined_ip' => array(
                'type' => 'string',
                'name' => '最后登录IP',
                'elem' => 0
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '注册时间',
                'elem' => 0,
                'list' => 'datetime'
            )
        );
        call_user_func(array('parent', __FUNCTION__));
    }

    ##当前类验证规则
    protected $validate = array(
        'username' => array(
            'rule' => array('unique', 'user')
        ),
        'password' => array(
            array(
                'rule' => 'require',
                'on' => 'add'
            ),
            array(
                'rule' => array('length', 5, 16)
            )
        ),
        
        'email'=>array(
            'allowEmpty' => true,
            'rule' => 'email'
            
        ),
        'user_group_id' => array(
            'rule' => array('egt', 1),
            'message' => '请选择用户组'
        ),
        'status' => array(
            'rule' => 'require'
        ),

    );

    public function before_insert()
    {
        $rslt = call_user_func(array('parent', __FUNCTION__));
        if (isset($this['password']) && $this['password'] === '') unset($this['password']);
        if (isset($this['password'])) $this['password'] = Auth::password($this['password']);
        return $rslt;
    }

    public function before_update()
    {
        $rslt = call_user_func(array('parent', __FUNCTION__));
        if (isset($this['password']) && $this['password'] === '') unset($this['password']);
        if (isset($this['password'])) $this['password'] = Auth::password($this['password']);
        return $rslt;
    }

    public function after_insert()
    {
        $rslt = call_user_func(array('parent', __FUNCTION__));
        $data['user_id'] = $this['id'];
        $data['nickname'] = '';
        $memberModel = model('Member');
        $memberModel->isValidate(false)->isUpdate(false)->save($data);
        return $rslt;
    }

}
