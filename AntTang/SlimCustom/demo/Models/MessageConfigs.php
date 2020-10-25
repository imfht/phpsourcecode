<?php
/**
 * @package     MessageConfigs.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月5日
 */

namespace Demo\Models;

use SlimCustom\Libs\Model\Model;
use SlimCustom\Libs\Paginator\Paginator;

/**
 * MessageConfigs
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
class MessageConfigs extends Model
{
    /**
     * 表名
     * 
     * @var string
     */
    protected $table = 'configs';

    /**
     * 自定义获取全部方法
     */
    public function all()
    {
        return $this->bind(function (\SlimCustom\Libs\Support\Collection $row) {
            // $row['configs'] = unserialize($row['configs']);
            // $row['update_time'] = date('Y-m-d H:i:s', $row['update_time']);
            // return $row;
            $this->configs = unserialize($this->configs);
            $this->update_time = date('Y-m-d H:i:s', $this->update_time);
            return $this;
        })
            ->where('id', '<', 10)
            ->limit(request()->getParam('per_page', Paginator::COUNT), (intval(request()->getParam('page', 1)) - 1) * Paginator::COUNT)
            ->rows();
    }

    /**
     * 自定义创建方法
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Model\Model::create()
     */
    public function create($pairs = [], $insertId = true)
    {
        $this->rules([
            'key' => 'required|integer'
        ]);
        return parent::create($pairs, $insertId);
    }
}