<?php
namespace app\common\model\mup;

use think\Model;
use think\Db;
use think\Log;

/**
 * 导航风格
 * Class MupFrame
 * @package app\common\model\mup
 */
class MupFrame extends Model {
    protected $pk = 'frame_id';
    protected $resultSetType = 'collection'; // 以数组返回


}
