<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\Relation;
use \Cute\ORM\Database;
use \PDO;


/**
 * 嵌套集合/树状结构/无限分类
 */
class NestedSet extends Relation
{
    public function relative($name, array& $result)
    {
        $query = $this->queryResult()->orderBy('low_value');
        if (!empty($result)) {
            $root = array_pop($result);
            $args = [$root->getLow(), $root->getHigh()];
            $query->find('low_value BETWEEN ? AND ?', $args);
        }
        $db = $query->getQuery()->getDB();
        $table_name = $db->getTableName($query->getTable(), false);
        $columns = sprintf('low_value,%s.*', $table_name);
        $objects = $query->all($columns, PDO::FETCH_UNIQUE);
        if (count($objects) === 0) {
            return;
        }
        $i = 0;
        $parents = [];
        $result = reset($objects);
        foreach ($objects as $low => &$object) {
            $object->$name = [];
            if (!$object->isLeaf()) {
                $parents[$low + 1] = $low; //首个子节点
            }
            if (isset($parents[$low])) {
                $object->parent = &$objects[$parents[$low]];
                $object->depth = $object->parent->depth + 1;
                array_push($object->parent->$name, $object);
                $high = $object->getHigh();
                if ($high < $object->parent->getHigh() - 1) {
                    $parents[$high + 1] = $parents[$low]; //后续兄弟节点
                }
            }
        }
        return $result;
    }

    public function exchange(& $node, & $another)
    {
    }

    public function append(& $node)
    {
    }

    public function remove(& $node)
    {
    }
}
