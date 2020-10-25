<?php
namespace app\common\model;

class AdPosition extends App
{
    public $display = 'title';
    public $assoc = array(
        'Ad' => array(
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
                'name' => '标题',
                'elem' => 'text',
                'list' => 'show',
            ),
            'ex_title' => array(
                'type' => 'string',
                'name' => '展示标题',
                'elem' => 'text',
                'list' => 'show',
            ),
            'vari' => array(
                'type' => 'string',
                'name' => '变量',
                'elem' => 'text',
                'info' => '开发人员使用，请不要随意更改',
            ),
            'width' => array(
                'type' => 'integer',
                'name' => 'PC端宽度',
                'elem' => 'number'
            ),
            'height' => array(
                'type' => 'integer',
                'name' => 'PC端高度',
                'elem' => 'number'
            ),
            'mobile_width' => array(
                'type' => 'integer',
                'name' => '移动端宽度',
                'elem' => 'number'
            ),
            'mobile_height' => array(
                'type' => 'integer',
                'name' => '移动端高度',
                'elem' => 'number'
            ),
            'limit' => array(
                'type' => 'integer',
                'name' => '广告限制',
                'elem' => 'text'
            ),
            'is_thumb' => array(
                'type' => 'integer',
                'name' => '使用缩略图',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'is_text' => array(
                'type' => 'integer',
                'name' => '文字描述',
                'elem' => 'checker',
                'list' => 'checker',
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
            'ad_count' => array(
                'type' => 'integer',
                'name' => '广告数量',
                'elem' => 0,
                'list' => 'counter',
                'counter' => 'Ad'
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请填写标题'
        ),
        'vari' => array(
            array(
                'rule' => 'require',
                'message' => '请填写变量'
            ),
            array(
                'rule' => array('unique', 'ad_position'),
                'message' => '该变量已经存在'
            )
        )
    );
}
