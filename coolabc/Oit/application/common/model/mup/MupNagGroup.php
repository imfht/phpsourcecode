<?php
namespace app\common\model\mup;

use think\Model;
use think\Db;
use think\Log;

/**
 * 导航分组的信息
 * Class MupNagGroup
 * @package app\mup\model
 */
class MupNagGroup extends Model {
    protected $pk = 'nag_group_id';
    protected $resultSetType = 'collection'; // 以数组返回


}
