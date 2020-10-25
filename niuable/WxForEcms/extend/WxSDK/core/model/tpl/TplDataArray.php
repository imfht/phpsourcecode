<?php
namespace WxSDK\core\model\tpl;

class TplDataArray
{
    public $data;
    function __construct(DataItem... $items) {
        $this->data = [];
        foreach ($items as $v){
            $this->data[$v->key] = [
                'value'=>$v->value,
                'color'=>$v->color
            ];
        }
    }
}

