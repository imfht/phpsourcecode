<?php
namespace app\common\model;

class Email extends App
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
                'name' => '模板名称',
                'elem' => 'text',
            ),
            'vari' => array(
                'type' => 'string',
                'name' => '模板变量',
                'elem' => 'text',
                'info' => '非程序员请不要随意修改',
            ),
            'email_title' => array(
                'type' => 'string',
                'name' => '邮件标题',
                'elem' => 'text',
            ),
            'fromname' => array(
                'type' => 'string',
                'name' => '邮件发件人',
                'elem' => 'text',
                'info' => '如果不填，默认为系统设置中“发件人名称”'
            ),
            'file' => array(
                'type' => 'string',
                'name' => '邮件附件',
                'elem' => 'file',
            ),
            'content' => array(
                'type' => 'text',
                'name' => '邮件内容',
                'elem' => 'editor',
                'list' => 0
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
    protected $validate = [
        'title' => array(
            'rule' => 'require'
        ),
        'email_title' => array(
            'rule' => 'require'
        ),
        'vari' => array(
            'rule' => array('unique', 'email')
        ),
    ];
}
