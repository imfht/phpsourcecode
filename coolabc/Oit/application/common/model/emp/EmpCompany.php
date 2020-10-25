<?php
namespace app\common\model\emp;

use think\Model;
use think\Db;
use think\Log;

/**
 * 员工公司
 * Class EmpCompany
 * @package app\emp\model
 */
class EmpCompany extends Model {
    public $table = 'emp_company';
    public $pk = 'company_id';
    public $pk_name = 'company_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = '';
    public $fmt_id = '';

    public $fmt_field_list = [
        ['field' => 'company_name', 'width' => 80, 'title' => '公司名称'],
        ['field' => 'company_id', 'width' => 65, 'title' => '公司编号'],
        ['field' => 'note_info', 'width' => 80, 'title' => '备注'],
    ];

}
