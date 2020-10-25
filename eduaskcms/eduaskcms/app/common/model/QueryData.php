<?php
namespace app\common\model;

class QueryData extends App
{
    //关联模型
    public $assoc = [];
    
    public function initialize()
    {        
        $this->form = [
            'id' => [
            	'type' => 'integer',
            	'name' => 'ID',
            	'elem' => 'hidden',
            ],
            'title' => array(
                'type' => 'string',
                'name' => '数据标题',
                'elem' => 'text',
                'list' => 'show'
            ),
            'query' => array(
                'type' => 'string',
                'name' => '查询位置',
                'elem' => 'select',
                'list' => 'options',
                'options' => ['all' => '所有页面', 'index' => '首页', 'insider' => '非首页', 'controller' => '指定控制器']
            ),
            'controller' => array(
                'type' => 'string',
                'name' => '控制器',
                'elem' => 'select',
                'options' => $GLOBALS['Model_map']
            ),
            'menu_id' => array(
                'type' => 'integer',
                'name' => '查询栏目',
                'elem' => 'nest_select.Menu',
            ),
            
            'is_family' => array(
                'type' => 'boolean',
                'name' => '后代栏目',
                'elem' => 'checker',
                'list' => 'checker.show',
                'info' => '如选择，将从其本身和其后代同类型栏目中查询数据'
            ),
            'is_verify' => array(
                'type' => 'boolean',
                'name' => '是否启用',
                'elem' => 'checker',
                'list' => 'checker'
            ),
            'type' => array(
                'type' => 'string',
                'name' => '查询方式',
                'elem' => 'select',
                'list' => 'show',
                'options' => ['select' => 'select', 'find' => 'find']
            ),
            
            'list_count' => array(
                'type' => 'integer',
                'name' => '查询条数',
                'elem' => 'number'
            ),
            'contain' => array(
                'type' => 'string',
                'name' => '关联模型',
                'elem' => 'textarea',
                'list' => 'show'
            ),
            'where' => array(
                'type' => 'string',
                'name' => '查询条件',
                'elem' => 'textarea',
                'list' => 'show'
            ),
            'field' => array(
                'type' => 'string',
                'name' => '查询字段',
                'elem' => 'textarea',
                'list' => 'show'
            ),
            'order' => array(
                'type' => 'string',
                'name' => '查询排序',
                'elem' => 'textarea',
                'list' => 'show'
            ),
        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }
    
    public $fieldRespond = array(
        'query' => array(
            'RespondField' => array('controller'),
            'controller' => array('controller')
        ),
        'type' => array(
            'RespondField' => array('list_count'),
            'select' => array('list_count')
        )
    );
    
    /*
    //表单分组
    public $formGroup = [
        'advanced' => '高级选项'
    ];
    */
    
    //数据验证    
    protected $validate = [
        'query' => array(
            'rule' => 'require',
            'message' => '请选择查询位置'
        ),
        'menu_id' => array(
            'rule' => array('egt', 1),
            'message' => '请选择查询栏目'
        ),
        'type' => array(
            'rule' => 'require',
            'message' => '请选择查询方式'
        ),
        'list_count' => array(
            'rule' => array('egt', 1)
        ),
    ];
}
