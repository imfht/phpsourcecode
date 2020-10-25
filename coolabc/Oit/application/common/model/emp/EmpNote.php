<?php
namespace app\common\model\emp;

use think\Model;
use think\Db;
use think\Log;

/**
 * 员工备注
 * Class EmpNote
 * @package app\common\model\emp
 */
class EmpNote extends Model {
    public $table = 'emp_note';
    protected $pk = 'emp_id';
    protected $resultSetType = 'collection'; // 以数组返回

    public $fmt_field_list = [
        ['field' => 'create_date', 'width' => 80, 'title' => '创建日期'],
        ['field' => 'create_user_id', 'width' => 65, 'title' => '操作员'],
        ['field' => 'note_info', 'width' => 80, 'title' => '备注信息'],
    ];

}
