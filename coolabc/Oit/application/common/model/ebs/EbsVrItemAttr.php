<?php
namespace app\common\model\ebs;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 单据明细扩展属性值
 * Class EbsVrItemAttr
 * @package app\eba\model
 */
class EbsVrItemAttr extends OitBase {
    public $table = 'ebs_vr_item_attr';
    public $pk = '';
    public $pk_name = '';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = '';
    public $fmt_id = '';

    public $fmt_field_list = [
        ['field' => 'eba_name', 'width' => 80, 'title' => '客户名称'],
        ['field' => 'eba_id', 'width' => 65, 'title' => '客户编号'],
    ];

    public $field_dict_need = [
        'eba_grade',
    ];

    public $field_dict_def = [
        [
            'field_id' => 'eba_grade',
            'dict_id' => 'eba_grade',
            'r_field_id' => 'eba_grade'
        ],
    ];



}
