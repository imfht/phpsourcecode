<?php
namespace app\common\model;

class Ad extends App
{
    public $display = 'title';
    public $parentModel = 'AdPosition';
    public $assoc = array(
        'AdPosition' => array(
            'type' => 'belongsTo',
            'counterCache' => true
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
            /*
            'ad_position_id' => array(
                'type' => 'integer',
                'name' => '所属广告位',
                'elem' => 'format',
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'where' => array()
                    )
                ),
                'foreign' => 'AdPosition.title',
                'list' => 'assoc',
            ),
            */
            'ad_position_id' => array(
                'type' => 'integer',
                'name' => '所属广告位',
                'elem' => 'assoc_select',
                'foreign' => 'AdPosition.title',
                'list' => 'assoc',
            ),
            'title' => array(
                'type' => 'string',
                'name' => '名称',
                'elem' => 'text',
            ),
            'ex_title' => array(
                'type' => 'string',
                'name' => '副标题',
                'elem' => 'text',
                'list' => 'show',
            ),
            'image' => array(
                'type' => 'string',
                'name' => 'PC端图片',
                'elem' => 'image',
                'list' => 'image',
            ),
            'thumb' => array(
                'type' => 'string',
                'name' => 'PC端缩略图',
                'elem' => 0,
                'list' => 0,
            ),
            'mobile_image' => array(
                'type' => 'string',
                'name' => '移动端图片',
                'elem' => 'image',
                'list' => 'image',
            ),
            'mobile_thumb' => array(
                'type' => 'string',
                'name' => '移动端缩略图',
                'elem' => 0,
                'list' => 0,
            ),
            'link' => array(
                'type' => 'string',
                'name' => '广告链接',
                'elem' => 'text',
            ),
            'content' => array(
                'type' => 'text',
                'name' => '文字内容',
                'elem' => 0,
            ),
            'is_verify' => array(
                'type' => 'integer',
                'name' => '启用',
                'elem' => 'checker',
                'list' => 'checker',
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '添加时间',
                'elem' => 0,
                'list' => 'datetime'
            ),
            'modified' => array(
                'type' => 'datetime',
                'name' => '修改时间',
                'elem' => 0,
                'list' => 'datetime'
            ),
            'list_order' => array(
                'type' => 'integer',
                'name' => '排序依据',
                'elem' => 'number',
                'list' => 'edit.text',
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }
    
    protected $validate = array(
        'ad_position_id' => array(
            'rule' => array('egt', 1),
            'message' => '缺少广告位参数'
        )
    ); 
}
