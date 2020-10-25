<?php
namespace app\common\model;

class Picture extends App
{
    public $display = 'title';
    protected $table = 'picture';


    public function __construct($data = [])
    {
        $this->table = \think\facade\Config::get('database.prefix') . $this->table;
        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

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
            'foreign_id' => array(
                'type' => 'integer',
                'name' => '关联ID',
                'elem' => 'hidden',
                'list' => 0,
                'foreign' => 'Album.title',
            ),
            'module' => array(
                'type' => 'string',
                'name' => '栏目模块',
                'elem' => 'hidden',
                'list' => 'assoc',
            ),
            'controller' => array(
                'type' => 'string',
                'name' => '栏目模块',
                'elem' => 'hidden',
                'list' => 'assoc',
            ),
            
            
            
            'title' => array(
                'type' => 'string',
                'name' => '名称',
                'elem' => 'text',
            ),
            'image' => array(
                'type' => 'string',
                'name' => '图片',
                'elem' => 'image',
                'list' => 'image',
                'image' => array(
                    'thumb' => array(
                        'field'=>'thumb'
                    )
                ),
                'upload' => array(
                    'maxSize' => 1024,
                    'validExt' => array('jpg', 'png', 'gif')
                )
            ),
            'thumb' => array(
                'type' => 'string',
                'name' => '缩略图',
                'elem' => 0,
                'list' => 'image',
            ),
            'is_verify' => array(
                'type' => 'integer',
                'name' => '启用',
                'elem' => 'checker',
                'list' => 'checker.confirm',
            ),
            'content' => array(
                'type' => 'text',
                'name' => '简介',
                'elem' => 'textarea',
                'list' => 0
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
                'list' => 'show'
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }


    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请填写标题'
        ),
        'foreign_id' => array(            
            'rule' => array('egt', 1),
            'message' => '请选择关联数据'
        ),
        /*
        'image'=>array(
            'rule'=>'require',
            'message'=>'请上传图片',
            'on'=>'add'
        ),
        */
    );
}
