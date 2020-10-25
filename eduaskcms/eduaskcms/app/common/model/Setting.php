<?php
namespace app\common\model;

use app\common\utility\Hash;

class Setting extends App
{

    public $display = 'title';
    public $assoc = array(
        'SettingGroup' => array(
            'type' => 'belongsTo'
        )
    );

    ##父模型名
    public $parentModel = 'SettingGroup';

    private $parseJsonTypes = array();

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
                'name' => '名称',
                'elem' => 'text.title',
            ),

            'setting_group_id' => array(
                'type' => 'integer',
                'name' => '分类ID',
                'elem' => 'select',
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'where' => array()
                    )
                ),
                'foreign' => 'SettingGroup.title',
                'list' => 'assoc',
            ),
            'vari' => array(
                'type' => 'string',
                'name' => '引用变量',
                'elem' => 'text',
                'info' => '非程序员请不要随意修改'
            ),
            'value' => array(
                'type' => 'string',
                'name' => '数据',
                'elem' => 0
            ),
            'type' => array(
                'type' => 'string',
                'name' => '输入类型',
                'elem' => 'select',
                'options' => array('text' => '文本框', 'password' => '密码框', 'textarea' => '文本域', 'file' => '文件', 'checkbox' => '多选框', 'radio' => '单选框', 'color'=> '取色器','select' => '下拉菜单', 'checker' => '是否', 'array' => '数组', 'keyvalue' => '键值对'),
            ),
            'options' => array(
                'type' => 'string',
                'name' => '选项',
                'elem' => 'keyvalue',
                'list' => 'json',
            ),

            'info' => array(
                'type' => 'string',
                'name' => '提示信息',
                'elem' => 'text'
            )

        );


        $this->parseJsonTypes = array('checkbox', 'array', 'keyvalue');
        call_user_func(array('parent', __FUNCTION__));
    }

    ##当前类验证规则
    protected $validate = array(
        'title' => array(
            'rule' => 'require'
        ),
        'setting_group_id' => array(
            'rule' => array('egt', 1)
        ),
        'vari' => array(
            'rule' => array('unique', 'setting')
        ),
        'type' => array(
            'rule' => 'require'
        )
    );


    public function before_write()
    {
        $rslt = call_user_func(array('parent', __FUNCTION__));
        if ($this['type'] == 'keyvalue' && is_array($this['value'])) {
            $this['value'] = Hash::combine($this['value'], '{n}.key', '{n}.value');
        }
        if (in_array($this['type'], $this->parseJsonTypes) && is_array($this['value'])) {
            $this['value'] = json_encode($this['value']);
        }
        return $rslt;
    }


    public function write_cache()
    {
        $listObj = $this->field(array('vari', 'value'))->select();
        $list = array();
        foreach ($listObj as $obj) {
            $list[] = $obj->toArray();
        }
        $list = Hash::combine($list, '{n}.vari', '{n}.value');
        write_file_cache('Setting', $list);
        return true;
    }
}
