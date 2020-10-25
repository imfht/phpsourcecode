<?php

return [
    //表单的类型：text、textarea、checkbox、radio、select等
    'radio'=> [
        'title'=>'是否允许显示',
        'type'=>'radio',
        'options'=> [
            '0'=>'不允许',
            '1'=>'允许'
        ],
        'value'=>'0',
        'tip'=>'单选描述文本'
    ],

    'checkbox'=> [
        'title'=>'单选',//表单的文字
        'type'=>'checkbox',         
        'options'=>[
            '1'=>'选项一',
            '0'=>'选项二',
        ],
        'value'=>'1',
        'tip'=>'多选描述文本'
    ],

    'text'=> [
        'title'=>'文本框',
        'type'=>'text',
        'value'=>'0',
        'tip'=>'文本框描述文本',
    ],

    'select'=> [
        'title'=>'下拉框',
        'type'=>'select',
        'options'=> [
            '0'=>'演示选项1',
            '1'=>'演示选项2'
        ],
        'value'=>'0',
        'tip'=>'下拉框描述文本'
    ],

    'textarea'=> [
        'title'=>'文本域',
        'type'=>'textarea',
        'value'=>'0',
        'tip'=>'文本域描述文本'
    ],

    'group'=>[
        'type'=>'group',
        'options'=>[
            'opt1'=>[
                'title'=>'配置1',
                'options'=>[

                    'text'=>[
                        'title'=>'配置1TEXT',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'文本框描述文本',
                    ],
                    'text2'=>[
                        'title'=>'配置1TEXT',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'文本框描述文本',
                    ]
                ],
            ],
            'opt2'=>[
                'title'=>'配置2',
                'options'=>[

                    'text'=>[
                        'title'=>'配置2TEXT',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'文本框描述文本',
                    ],
                    'text2'=>[
                        'title'=>'配置2TEXT',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'文本框描述文本',
                    ]
                ],
            ],
            
        ],
    ],

    
];