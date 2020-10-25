<?php
namespace app\{$namespace}\model;

use think\Model;
/**
 * Class {$_controller}
 * @package app\{$controller}\validate
 */
class {$_controller} extends Model{
    protected $autoWriteTimestamp = true;

     /**
     * @param array $where
     * @param int $pageSize
     * @param string $field
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($where= [], $pageSize=10,$field='*'){
        $list = $this
            ->field($field)
            ->order('id desc')
            ->where($where)
            ->paginate($pageSize);
        return $list;
    }
    //@todo more
}