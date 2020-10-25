<?php
namespace app\common\model\mup;

use think\Model;
use think\Db;
use think\Log;

/**
 * 系统用户
 * Class MupUser
 * @package app\common\model\mup
 */
class MupUser extends Model {
    protected $pk = 'user_id';

    protected $resultSetType = 'collection'; // 以数组返回


}
