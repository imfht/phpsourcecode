<?php
namespace app\common\model\app;

use think\Model;
use think\Db;
use think\Log;

/**
 * 动态字典语句
 * Class AppGridFmtDef
 * @package app\model\app
 */
class AppDictSql extends Model {
    public $pk = 'dict_id';
    protected $resultSetType = 'collection'; // 以数组返回



}
