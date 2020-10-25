<?php

/**
 * Arrays 类提供了一组简化数组操作的方法
 */
class Arrays
{

    /**
     * 对字符串或数组进行格式化，返回格式化后的数组
     *
     * $input 参数如果是字符串，则首先以“,”为分隔符，将字符串转换为一个数组。
     * 接下来对数组中每一个项目使用 trim() 方法去掉首尾的空白字符。最后过滤掉空字符串项目。
     *
     * 该方法的主要用途是将诸如：“item1, item2, item3” 这样的字符串转换为数组。
     *
     * @code php
     * $input = 'item1, item2, item3';
     * $output = Arrays::normalize($input);
     * // $output 现在是一个数组，结果如下：
     * // $output = [
     * //   'item1',
     * //   'item2',
     * //   'item3',
     * // ];
     *
     * $input = 'item1|item2|item3';
     * // 指定使用什么字符作为分割符
     * $output = Arrays::normalize($input, '|');
     * @endcode
     *
     * @param array|string $input 要格式化的字符串或数组
     * @param string $delimiter 按照什么字符进行分割
     *
     * @return array 格式化结果
     */
    public static function normalize($input, $delimiter = ',')
    {
        if (!is_array($input))
        {
            $input = explode($delimiter, $input);
        }
        $input = array_map('trim', $input);
        return array_filter($input, 'strlen');
    }

    /**
     * 从数组中删除空白的元素（包括只有空白字符的元素）
     *
     * 用法：
     * @code php
     * $arr = ['', 'test', '   '];
     * Arrays::remove_empty($arr);
     *
     * dump($arr);
     *   // 输出结果中将只有 'test'
     * @endcode
     *
     * @param array $arr 要处理的数组
     * @param boolean $trim 是否对数组元素调用 trim 函数
     */
    public static function remove_empty(& $arr, $trim = true)
    {
        foreach ($arr as $key => $value) 
        {
            if (is_array($value)) 
            {
                self::remove_empty($arr[$key]);
            }
            else 
            {
                $value = trim($value);
                if ($value == '') 
                {
                    unset($arr[$key]);
                } 
                elseif ($trim) 
                {
                    $arr[$key] = $value;
                }
            }
        }
    }

    /**
     * 从一个二维数组中返回指定键的所有值
     *
     * 用法：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1'],
     *     ['id' => 2, 'value' => '2-1'],
     * ];
     * $values = Arrays::cols($rows, 'value');
     *
     * dump($values);
     *   // 输出结果为
     *   // [
     *   //   '1-1',
     *   //   '2-1',
     *   // ]
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $col 要查询的键
     *
     * @return array 包含指定键所有值的数组
     */
    public static function cols($arr, $col)
    {
        $ret = [];
        foreach ($arr as $row) 
        {
            if (isset($row[$col])) { $ret[] = $row[$col]; }
        }
        return $ret;
    }

    /**
     * 将一个二维数组转换为 HashMap，并返回结果
     *
     * 用法1：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1'],
     *     ['id' => 2, 'value' => '2-1'],
     * ];
     * $hashmap = Arrays::hashmap($rows, 'id', 'value');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // [
     *   //   1 => '1-1',
     *   //   2 => '2-1',
     *   // ]
     * @endcode
     *
     * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
     *
     * 用法2：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1'],
     *     ['id' => 2, 'value' => '2-1'],
     * ];
     * $hashmap = Arrays::hashmap($rows, 'id');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // [
     *   //   1 => ['id' => 1, 'value' => '1-1'],
     *   //   2 => ['id' => 2, 'value' => '2-1'],
     *   // ]
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 按照什么键的值进行转换
     * @param string $value_field 对应的键值
     *
     * @return array 转换后的 HashMap 样式数组
     */
    public static function hashmap($arr, $key_field, $value_field = null)
    {
        $ret = [];
        if ($value_field) 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row[$value_field];
            }
        } 
        else 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row;
            }
        }
        return $ret;
    }

    /**
     * 将一个二维数组按照指定字段的值分组
     *
     * 用法：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1', 'parent' => 1],
     *     ['id' => 2, 'value' => '2-1', 'parent' => 1],
     *     ['id' => 3, 'value' => '3-1', 'parent' => 1],
     *     ['id' => 4, 'value' => '4-1', 'parent' => 2],
     *     ['id' => 5, 'value' => '5-1', 'parent' => 2],
     *     ['id' => 6, 'value' => '6-1', 'parent' => 3],
     * ];
     * $values = Arrays::group_by($rows, 'parent');
     *
     * dump($values);
     *   // 按照 parent 分组的输出结果为
     *   // array(
     *   //   1 => [
     *   //        ['id' => 1, 'value' => '1-1', 'parent' => 1],
     *   //        ['id' => 2, 'value' => '2-1', 'parent' => 1],
     *   //        ['id' => 3, 'value' => '3-1', 'parent' => 1],
     *   //   ),
     *   //   2 => [
     *   //        ['id' => 4, 'value' => '4-1', 'parent' => 2],
     *   //        ['id' => 5, 'value' => '5-1', 'parent' => 2],
     *   //   ),
     *   //   3 => [
     *   //        ['id' => 6, 'value' => '6-1', 'parent' => 3],
     *   //   ],
     *   // ]
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 作为分组依据的键名
     *
     * @return array 分组后的结果
     */
    public static function group_by($arr, $key_field)
    {
        $ret = [];
        foreach ($arr as $row) 
        {
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }
        return $ret;
    }

    /**
     * 将一个平面的二维数组按照指定的字段转换为树状结构
     *
     * 用法：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1', 'parent' => 0],
     *     ['id' => 2, 'value' => '2-1', 'parent' => 0],
     *     ['id' => 3, 'value' => '3-1', 'parent' => 0],
     *
     *     ['id' => 7, 'value' => '2-1-1', 'parent' => 2],
     *     ['id' => 8, 'value' => '2-1-2', 'parent' => 2],
     *     ['id' => 9, 'value' => '3-1-1', 'parent' => 3],
     *     ['id' => 10, 'value' => '3-1-1-1', 'parent' => 9],
     * ];
     *
     * $tree = Arrays::tree($rows, 'id', 'parent', 'nodes');
     *
     * dump($tree);
     *   // 输出结果为：
     *   // [
     *   //   ['id' => 1, ..., 'nodes' => []],
     *   //   ['id' => 2, ..., 'nodes' => [
     *   //        [..., 'parent' => 2, 'nodes' => []],
     *   //        [..., 'parent' => 2, 'nodes' => []],
     *   //   ),
     *   //   ['id' => 3, ..., 'nodes' => [
     *   //        ['id' => 9, ..., 'parent' => 3, 'nodes' => [
     *   //             [..., , 'parent' => 9, 'nodes' => [],
     *   //        ],
     *   //   ],
     *   // ]
     * @endcode
     *
     * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
     * @code php
     * $refs = null;
     * $tree = Arrays::tree($rows, 'id', 'parent', 'nodes', $refs);
     * 
     * // 输出 id 为 3 的节点及其所有子节点
     * $id = 3;
     * dump($refs[$id]);
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_node_id 节点ID字段名
     * @param string $key_parent_id 节点父ID字段名
     * @param string $key_childrens 保存子节点的字段名
     * @param boolean $refs 是否在返回结果中包含节点引用
     *
     * return array 树形结构的数组
     */
    public static function tree($arr, $key_node_id, $key_parent_id = 'parent_id', $key_childrens = 'childrens', & $refs = null)
    {
        $refs = [];
        foreach ($arr as $offset => $row) 
        {
            $arr[$offset][$key_childrens] = [];
            $refs[$row[$key_node_id]] =& $arr[$offset];
        }

        $tree = [];
        foreach ($arr as $offset => $row) 
        {
            $parent_id = $row[$key_parent_id];
            if ($parent_id)
            {
                if (!isset($refs[$parent_id]))
                {
                    $tree[] =& $arr[$offset];
                    continue;
                }
                $parent =& $refs[$parent_id];
                $parent[$key_childrens][] =& $arr[$offset];
            }
            else
            {
                $tree[] =& $arr[$offset];
            }
        }

        return $tree;
    }

    /**
     * 将树形数组展开为平面的数组
     *
     * @param array $tree 树形数组
     * @param string $key_childrens 包含子节点的键名
     *
     * @return array 展开后的数组
     */
    public static function tree_to($tree, $key_childrens = 'childrens')
    {
        $ret = [];
        if (isset($tree[$key_childrens]) && is_array($tree[$key_childrens]))
        {
            $childrens = $tree[$key_childrens];
            unset($tree[$key_childrens]);
            $ret[] = $tree;
            foreach ($childrens as $node)
            {
                $ret = array_merge($ret, self::tree_to($node, $key_childrens));
            }
        }
        else
        {
            unset($tree[$key_childrens]);
            $ret[] = $tree;
        }
        return $ret;
    }

    /**
     * 根据指定的键对数组排序
     *
     * 用法：
     * @code php
     * $rows = [
     *     ['id' => 1, 'value' => '1-1', 'parent' => 1],
     *     ['id' => 2, 'value' => '2-1', 'parent' => 1],
     *     ['id' => 3, 'value' => '3-1', 'parent' => 1],
     *     ['id' => 4, 'value' => '4-1', 'parent' => 2],
     *     ['id' => 5, 'value' => '5-1', 'parent' => 2],
     *     ['id' => 6, 'value' => '6-1', 'parent' => 3],
     * ];
     *
     * $rows = Arrays::sort_by_col($rows, 'id', SORT_DESC);
     * dump($rows);
     * // 输出结果为：
     * // [
     * //   ['id' => 6, 'value' => '6-1', 'parent' => 3],
     * //   ['id' => 5, 'value' => '5-1', 'parent' => 2],
     * //   ['id' => 4, 'value' => '4-1', 'parent' => 2],
     * //   ['id' => 3, 'value' => '3-1', 'parent' => 1],
     * //   ['id' => 2, 'value' => '2-1', 'parent' => 1],
     * //   ['id' => 1, 'value' => '1-1', 'parent' => 1],
     * // ]
     * @endcode
     *
     * @param array $arr 要排序的数组
     * @param string $keyname 排序的键
     * @param int $dir 排序方向
     *
     * @return array 排序后的数组
     */
    public static function sort_by_col($arr, $keyname, $dir = SORT_ASC)
    {
        return self::sort_by_cols($arr, [$keyname => $dir]);
    }

    /**
     * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
     *
     * 用法：
     * @code php
     * $rows = Arrays::sort_by_cols($rows, [
     *     'parent' => SORT_ASC, 
     *     'name' => SORT_DESC,
     * ]);
     * @endcode
     *
     * @param array $rowset 要排序的数组
     * @param array $args 排序的键
     *
     * @return array 排序后的数组
     */
    public static function sort_by_cols($rowset, $args)
    {
        $sortArray = [];
        $sortRule = '';
        foreach ($args as $sortField => $sortDir) 
        {
            foreach ($rowset as $offset => $row) 
            {
                $sortArray[$sortField][$offset] = $row[$sortField];
            }
            $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
        }
        if (empty($sortArray) || empty($sortRule)) { return $rowset; }
        eval('array_multisort(' . $sortRule . '$rowset);');
        return $rowset;
    }
}

