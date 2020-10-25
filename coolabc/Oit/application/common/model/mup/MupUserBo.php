<?php
namespace app\common\model\mup;

use think\Model;
use think\Db;
use think\Log;

/**
 * 用户限制
 * 用于产品目录、销售区域、采购分组、仓库等
 * Class MupUserBo
 * @package app\common\model\mup
 */
class MupUserBo extends Model {

    protected $resultSetType = 'collection'; // 以数组返回

}
