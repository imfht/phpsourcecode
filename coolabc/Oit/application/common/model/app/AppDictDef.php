<?php
namespace app\common\model\app;

use think\Model;
use think\Db;
use think\Log;

/**
 * 静态字典选项
 * Class AppGridFmtDef
 * @package app\model\app
 */
class AppDictDef extends Model {
    public $pk = 'dict_id';
    protected $resultSetType = 'collection'; // 以数组返回



}
