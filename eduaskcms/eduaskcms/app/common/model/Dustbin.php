<?php
namespace app\common\model;

class Dustbin extends App
{
    public $is_dustbin = false;

    public function initialize()
    {
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
            ],
            'model' => array(
                'type' => 'string',
                'name' => '模型名称',
                'elem' => 0,
                'list' => 'options',
                'options' => $GLOBALS['Model_title']
            ),
            'model_id' => array(
                'type' => 'integer',
                'name' => '对象ID',
                'elem' => 0,
                'list' => 'text',
            ),
            'title' => array(
                'type' => 'string',
                'name' => '对象标题',
                'elem' => 0,
                'list' => 'text',
            ),
            'data' => array(
                'type' => 'string',
                'name' => '数据',
                'elem' => 0,
                'list' => 'blob',
            ),
            'status' => array(
                'type' => 'integer',
                'name' => '状态',
                'elem' => 'format',
                'list' => 'options',
                'options' => array(
                    0 => '回收站中',
                    1 => '已恢复',
                )
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
            //其他字段
        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }


    //数据验证    
    protected $validate = [];
}
