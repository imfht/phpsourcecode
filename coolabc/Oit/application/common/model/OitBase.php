<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Log;

/**
 * 公共基础模型类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-30
 * Time: 16:56
 */
class OitBase extends Model {
    /**
     * 返回模型最新的 主id
     * 1 没有参数时，去掉客户编码前缀，转化为数值，再获取最大数值 + 1
     * 2 传递了区域时，获取该区域中的最大数值 + 1
     * 3 补足多少位，转字符
     * @param null   $where
     * @param string $type
     * @param string $front // 区域默认前缀
     * @param int    $bit_num
     * @return mixed
     */
    public function get_new_id($where = null, $type = 'pad', $front = '', $bit_num = 5) {
        $result = $this->field($this->pk)->where($where)->select()->toArray();
        if (empty($result)) {
            // 没有找到数据时
            // 编号从0开始
            $max_id = 0;
            $search = 0;  // 有没有找到前缀
        } else {
            $max_id = max($result)[$this->pk];
            $search = preg_match('/[A-Za-z]*/', $max_id, $str); // 有没有找到前缀
        }
        // 有无前缀
        if ($search == 0) {
            $str = $front;
            $int = (int)$max_id + 1;
        } else {
            $str = $str[0];
            if ($front != '') {
                $str = $front;
            }
            $int = str_replace($str, '', $max_id);
            $int = (int)$int + 1;
        }
        // 如何返回
        if ($type == 'int') {
            return $int;
        }
        return $str . str_pad($int, $bit_num, "0", STR_PAD_LEFT);
    }

    /**
     * 返回最大的排序号
     * @param string $order_id
     * @return mixed
     */
    public function get_new_order_id($order_id = '') {
        if ($order_id == '') {
            $order_id = $this->order_id;
        }
        $field = "max(" . $order_id . ") + 1 as max_order_id";
        $result = $this->field($field)->select()->toArray();
        return $result[0]['max_order_id'];
    }

}