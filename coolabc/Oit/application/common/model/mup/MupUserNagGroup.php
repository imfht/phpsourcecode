<?php
namespace app\common\model\mup;

use think\Model;
use think\Db;
use think\Log;

/**
 * 用户导航风格的分组
 * Class MupUserNagGroup
 * @package app\common\model\mup
 */
class MupUserNagGroup extends Model {
    protected $pk = 'frame_id'; // 流水号，与 MupFrame中的frame_id不一样
    protected $resultSetType = 'collection'; // 以数组返回


}
