<?php
namespace app\common\model\sup;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 供应商分组
 * Class SupService
 * @package app\sup\model
 */
class SupService extends OitBase {
    public $table = 'sup_service';
    public $pk = 'service_id';
    public $pk_name = 'service_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $field_dict_need = [
        'sup_service'
    ];

    public $field_dict_def = [
        [
            'field_id' => 'parent_service_id',
            'dict_id' => 'sup_service',
        ],
    ];


}
