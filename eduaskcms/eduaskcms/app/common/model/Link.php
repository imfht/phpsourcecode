<?php
namespace app\common\model;

class Link extends App
{
    public $display = 'title';
    public $parentModel = 'Menu';

    public $assoc = array(
        'Menu' => array(
            'type' => 'belongsTo'
        ),
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
            'menu_id' => array(
                'type' => 'integer',
                'name' => '所属栏目',
                'elem' => 'nest_select.Menu',
                'foreign' => 'Menu.title',
                'list' => 'assoc'
            ),
            'user_id' => array(
                'type' => 'integer',
                'name' => '所属用户',
                'foreign' => 'User.username',
                'elem' => 0,
                'list' => 'assoc'
            ),
            'title' => array(
                'type' => 'string',
                'name' => '标题',
                'elem' => 'text.title',
                'list' => 'show',
            ),

            'link' => array(
                'type' => 'string',
                'name' => '外部链接',
                'elem' => 'text',
                'list' => 'show'
            ),
            'image' => array(
                'type' => 'string',
                'name' => '链接图片',
                'elem' => 'image.upload',
                'list' => 'image',
                'image' => array(
                    'thumb' => array(
                        'width' => 200,
                        'height' => 150,
                        'method' => 1,
                        'field' => 'thumb'
                    ),
                ),
                'upload' => array(
                    'maxSize' => 2048 * 1024,
                    'validExt' => array('jpg', 'png', 'gif')
                )
            ),
            'thumb' => array(
                'type' => 'string',
                'name' => '缩略图',
                'elem' => 0,
                'list' => 0,
            ),
            'is_verify' => array(
                'type' => 'boolean',
                'name' => '审核',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'content' => array(
                'type' => 'text',
                'name' => '内容',
                'elem' => 0,
                'list' => 0
            ),
            'summary' => array(
                'type' => 'text',
                'name' => '摘要说明',
                'elem' => 0,
                'list' => 'show',
                'elem_group' => 'advanced',
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
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请填写标题'
        ),
        'menu_id' => array(
            array(
                'rule' => array('egt', 1),
                'message' => '请选择父级导航'
            ),
            array(
                'rule' => array('call', 'checkTypeOfMenu')
            )
        )
    );
}
