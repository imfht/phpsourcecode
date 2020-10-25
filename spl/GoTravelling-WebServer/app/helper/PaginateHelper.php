<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-1
 * Time: 上午12:05
 */
namespace Helper;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginateHelper
{
    /**
     * 对数组数据进行分页
     *
     * @param array $data 待分页的数组数据
     * @param $total 数组数据的数量
     * @param $perPage 每页显示的记录条数
     * @param int $offset 记录的偏移量
     * @return array|LengthAwarePaginator
     */
    public static function paginateArrayData(array $data, $total, $perPage, $offset = 0)
    {
        // 根据per_page字段来判断是否应该分页
        if ( is_null($perPage) ) {
            return $data;
        }

        // 判断offset的值是否超过了数组长度
        if ( $offset >= $total ) {
            $desData =  [];
        } else {
            $desData = array_slice($data, $offset, $perPage);
        }

        return new LengthAwarePaginator($desData, $total, $perPage);
    }
}