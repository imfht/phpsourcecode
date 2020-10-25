<?php
namespace app\common\model;

class Feedback extends App
{
    public $display = 'title';
    public $parentModel = 'Menu';

    public $assoc = array(
        'Menu' => array(
            'type' => 'belongsTo'
        ),
        'User' => array(
            'type' => 'belongsTo'
        ),
        'ReplyUser' => array(
            'foreign' => 'User',
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
            'menu_id' => array(
                'type' => 'integer',
                'name' => '所属栏目',
                'elem' => 'nest_select.Menu',
                'foreign' => 'Menu.title',
                'list' => 'assoc'
            ),
            'user_id' => array(
                'type' => 'integer',
                'name' => '留言用户',
                'foreign' => 'User.username',
                'elem' => 0,
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'where' => array()
                    )
                ),
                'list' => 'assoc'
            ),
            'title' => array(
                'type' => 'string',
                'name' => '标题',
                'elem' => 'text',
                'list' => 'show',
            ),
            'truename' => array(
                'type' => 'string',
                'name' => '真实姓名',
                'elem' => 'text',
                'list' => 'show',
            ),
            'mobile' => array(
                'type' => 'string',
                'name' => '手机号码',
                'elem' => 'text',
                'list' => 'show',
            ),
            'email' => array(
                'type' => 'string',
                'name' => '邮箱帐号',
                'elem' => 0,
                'list' => 'show',
            ),
            'qq' => array(
                'type' => 'string',
                'name' => 'QQ号码',
                'elem' => 0,
                'list' => 'show',
            ),
            'wechat' => array(
                'type' => 'string',
                'name' => '微信号码',
                'elem' => 0,
                'list' => 'show',
            ),
            'address' => array(
                'type' => 'string',
                'name' => '地址',
                'elem' => 0,
                'list' => 'show',
            ),
            'ip' => array(
                'type' => 'string',
                'name' => '留言IP',
                'elem' => 'format',
                'list' => 'show',
            ),
            'content' => array(
                'type' => 'text',
                'name' => '内容',
                'elem' => 'textarea'
            ),
            'reply_user_id' => array(
                'type' => 'integer',
                'name' => '回复用户',
                'foreign' => 'ReplyUser.username',
                'elem' => 0,
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'where' => array()
                    )
                ),
                'list' => 'assoc'
            ),
            'reply_content' => array(
                'type' => 'text',
                'name' => '回复内容',
                'elem' => 'textarea'
            ),
            'is_verify' => array(
                'type' => 'boolean',
                'name' => '审核',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'is_finish' => array(
                'type' => 'boolean',
                'name' => '已处理',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'visit_count' => array(
                'type' => 'integer',
                'name' => '访问计数',
                'elem' => 0,
                'list' => 'show',
            ),
            'is_index' => array(
                'type' => 'boolean',
                'name' => '首页优先',
                'elem' => 'checker',
                'list' => 'checker',
                'elem_group' => 'advanced',
            ),
            'is_recommend' => array(
                'type' => 'boolean',
                'name' => '推荐',
                'elem' => 'checker',
                'list' => 'checker',
                'elem_group' => 'advanced',
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '添加时间',
                'elem' => 0,
                'list' => 'datetime',
                'elem_group' => 'advanced',
            ),
            'modified' => array(
                'type' => 'datetime',
                'name' => '修改时间',
                'elem' => 0,
                'list' => 'datetime',
                'elem_group' => 'advanced',
            ),
            'list_order' => array(
                'type' => 'integer',
                'name' => '排序权重',
                'elem' => 'number',
                'list' => 'show',
                'elem_group' => 'advanced',
            ),
            'keywords' => array(
                'type' => 'string',
                'name' => 'SEO关键字',
                'elem' => 'text',
                'elem_group' => 'advanced',
            ),
            'description' => array(
                'type' => 'string',
                'name' => 'SEO描述',
                'elem' => 'textarea',
                'elem_group' => 'advanced',
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

    public $formGroup = array(
        'advanced' => '高级选项'
    );

    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请填写留言标题'
        ),
        'menu_id' => array(
            array(
                'rule' => array('egt', 1),
                'message' => '请选择父级导航'
            ),
            array(
                'rule' => array('call', 'checkTypeOfMenu')
            )
        ),
        'mobile' => array(
            array(
                'rule' => 'require',
                'message' => '请填写手机号码'
            ),
            array(
                'rule' => array('regex', '/^1[3|4|5|6|7|8|9]\d{9}$/'),
                'message' => '请填写正确的手机号码'
            )
        ),
        'content' => array(
            'rule' => 'require',
            'message' => '请填写留言内容'
        ),
    );
}
