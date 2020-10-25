<?php
namespace app\common\model\ebs;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 单据配置参数
 * Class EbsVrCtl
 * @package app\eba\model
 */
class EbsVrCtl extends OitBase {
    public $table = 'ebs_vr_ctl';
    public $pk = 'voucher_type';
    public $pk_name = 'default_name';  // 默认单据名称
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = '';
    public $fmt_id = '';

    public $fmt_field_list = [
        ['field' => 'eba_name', 'width' => 80, 'title' => '客户名称'],
        ['field' => 'eba_id', 'width' => 65, 'title' => '客户编号'],
    ];

    // 所需字典
    public $field_dict_need = [
        'eba_grade',
    ];

    // 字典对应列名
    public $field_dict_def = [
        [
            'field_id' => 'eba_grade',
            'dict_id' => 'eba_grade',
            'r_field_id' => 'eba_grade'
        ],
    ];

    /**
     * 初试关联属性 一对一
     */
    public function ebsVr(){
        return $this->hasOne('EbsVr', 'voucher_id');
    }



}
