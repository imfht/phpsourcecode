<?php
namespace app\common\model\ebs;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 单据明细
 * Class EbsVrItem
 * @package app\eba\model
 */
class EbsVrItem extends OitBase {
    public $table = 'ebs_vr_item';
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
        'res',
    ];

    public $field_dict_def = [
        [
            'field_id' => 'res_id',
            'dict_id' => 'res',
            'r_field_id' => 'res_name',
        ],
    ];

    public $footer_sum_field = [
        'discount_amount', 'draw_amount', 'mem_card_pay_amount',
        'bank_card_pay_amount', 'gift_ticket_pay_amount', 'io_amount',
        'pre_amount', 'main_res_total_amount'
    ];


}
