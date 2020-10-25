<?php
namespace app\common\model\eba;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 客户区域
 * Class EbaService
 * @package app\eba\model
 */
class EbaService extends OitBase {
    public $table = 'eba_service';
    public $pk = 'service_id';
    public $pk_name = 'service_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $field_dict_need = [
        'eba_service'
    ];

    public $field_dict_def = [
        [
            'field_id' => 'parent_service_id',
            'dict_id' => 'eba_service',
        ],
    ];


}
