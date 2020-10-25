<?php
return array(
	'api'=>array(
        'title'=>'选择接口',
        'type'=>'radio',
        'options'=>array(
            'pullword'=>'pullword在线分词',
            'discuz'=>'discuz(推荐，无需配置)',
        ),
    ),
    'num'=>array(
        'title'=>'关键词数量',
        'type'=>'text',
        'value'=>10
    ),

    'group'=>array(
        'type'=>'group',
        'options'=>array(
            'pullword'=>array(
                'title'=>'pullword配置',
                'options'=>array(
                    'appkey'=>array(
                        'title'=>'Appkey：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：http://apistore.baidu.com/apiworks/servicedetail/143.html',
                    ),                   
                ),
             ),
            
          ),       
    )
);
					