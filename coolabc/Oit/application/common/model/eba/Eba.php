<?php
namespace app\common\model\eba;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 客户
 * Class Eba
 * @package app\eba\model
 */
class Eba extends OitBase {
    public $table = 'eba';
    public $pk = 'eba_id';
    public $pk_name = 'eba_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = 'eba';
    public $fmt_id = 'eba';

    public $fmt_field_list = [
        ['field' => 'eba_name', 'width' => 80, 'title' => '客户名称'],
        ['field' => 'eba_id', 'width' => 65, 'title' => '客户编号'],
        ['field' => 'linkman', 'width' => 80, 'title' => '联系人'],
        ['field' => 'office_no', 'width' => 30, 'title' => '办公电话'],
        ['field' => 'mobile_no', 'width' => 50, 'title' => '移动电话'],
    ];

    public $field_dict_need = [
        'eba_grade','sex','eba_state', 'eba_service'
    ];

    public $field_dict_def = [
        [
            'field_id' => 'eba_grade',
            'dict_id' => 'eba_grade',
            'r_field_id' => 'eba_grade'
        ],
        [
            'field_id' => 'gender',
            'dict_id' => 'sex',
            'r_field_id' => 'gender'
        ],
        [
            'field_id' => 'state',
            'dict_id' => 'eba_state',
            'r_field_id' => 'state'
        ],

        [
            'field_id' => 'service_id',
            'dict_id' => 'eba_service',
            'r_field_id' => 'service_id'
        ],
    ];


}
