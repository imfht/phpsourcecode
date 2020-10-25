<?php
namespace app\common\model\app;

use think\Model;
use think\Db;
use think\Log;

/**
 * 表格显示方案
 * Class AppGridFmt
 * @package app\model\app
 */
class AppGridFmt extends Model {
    protected $pk = 'fmt_id';
    protected $resultSetType = 'collection'; // 以数组返回



}
