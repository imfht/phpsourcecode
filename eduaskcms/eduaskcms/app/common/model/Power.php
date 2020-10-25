<?php
namespace app\common\model;

class Power extends App
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
            'type'=>array(
				'type'=>'string',
				'name'=>'权限类型',
				'elem'=>0,
			),
            'foreign_id'=>array(
				'type'=>'integer',
				'name'=>'关联ID',
				'elem'=>0,
			),
            'user_id'=>array(
				'type'=>'integer',
				'name'=>'操作人',
				'elem'=>0,
			),
            'content'=>array(
				'type'=>'blob.array',
				'name'=>'授权规则',
				'elem'=>0,
			),
			'created'=>array(
				'type'=>'datetime',
				'name'=>'添加时间',
				'elem'=>0,
				'list'=>'datetime'
			),
			'modified'=>array(
				'type'=>'datetime',
				'name'=>'修改时间',
				'elem'=>0,
				'list'=>'datetime'
			)
        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }
    
    /*
    //表单分组
    public $formGroup = [
        'advanced' => '高级选项'
    ];
    */
    
    //数据验证    
    protected $validate = [];
}
