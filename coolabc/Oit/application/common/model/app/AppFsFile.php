<?php
namespace app\common\model\app;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * oit 文件存储信息
 * Class AppFsFile
 * @package app\eba\model
 */
class AppFsFile extends OitBase {
    public $table = 'app_fs_file';
    public $pk = 'file_id';
    public $pk_name = '';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = '';
    public $fmt_id = '';

    public $fmt_field_list = [
        ['field' => 'eba_name', 'width' => 80, 'title' => '客户名称'],
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
    ];



}
