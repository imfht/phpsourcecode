<?php
namespace app\common\model\sup;

use app\common\model\OitBase;
use think\Model;
use think\Db;
use think\Log;

/**
 * 供应商
 * Class Sup
 * @package app\eba\model
 */
class Sup extends OitBase {
    public $table = 'sup';
    public $pk = 'sup_id';
    public $pk_name = 'sup_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = 'sup';
    public $fmt_id = 'sup';

    public $fmt_field_list = [
        ['field' => 'sup_name', 'width' => 80, 'title' => '供应商名称'],
        ['field' => 'sup_id', 'width' => 65, 'title' => '供应商编号'],
        ['field' => 'linkman', 'width' => 80, 'title' => '联系人'],
        ['field' => 'office_no', 'width' => 30, 'title' => '办公电话'],
        ['field' => 'mobile_no', 'width' => 50, 'title' => '移动电话'],
    ];

    public $field_dict_need = [
        'sup_state', 'sup_service',
    ];

    public $field_dict_def = [
        [
            'field_id' => 'state',
            'dict_id' => 'eba_state',
            'r_field_id' => 'state',
        ],
        [
            'field_id' => 'service_id',
            'dict_id' => 'eba_service',
            'r_field_id' => 'service_id',
        ],
    ];

}
