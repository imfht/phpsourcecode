<?php
namespace Core\Mvc;

use Core\Config;
use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;

class ModelQuery
{
    public static $modelsList = false;
    /*
     **$query array 检索条件
     *form array()
     *form@id table 短名
     *form @column 需要列举的列
     *join array()
     *join@id table 短名
     *join@conditions join筛选条件
     *where array()
     *where@conditions string
     *where@bind array 绑定参数
     *where@type array() 绑定参数类型
     *order string 排序规则
     */
    public static function find($query)
    {
        if (self::$modelsList === false) {
            self::$modelsList = Config::cache('modelsManager');
        }
        if (!isset($query['from']['id']) || !isset(self::$modelsList[$query['from']['id']])) {
            return false;
        }
        if(isset($query['columns'])){
            $columns = $query['columns'];
        }else{
            if (isset($query['from']['columns'])) {
                $columns = &$query['from']['columns'];
            } else {
                $columns = self::$modelsList[$query['from']['id']]['columns'];
            }
        }
        global $di;
        $sql = $di->getShared('modelsManager')->createBuilder()->from(array($query['from']['id'] => self::$modelsList[$query['from']['id']]['entity']));
        foreach (array('join', 'leftJoin', 'rightJoin','innerJoin') as $value) {
            if (isset($query[$value]) && !empty($query[$value]) && is_array($query[$value])) {
                foreach ($query[$value] as $vvalue) {
                    if (isset(self::$modelsList[$vvalue['id']])) {
                        $sql = $sql->{$value}(self::$modelsList[$vvalue['id']]['entity'], $vvalue['conditions'], $vvalue['id']);
                        if (isset($vvalue['columns']) && is_array($vvalue['columns'])) {
                            $columns = array_merge($columns, $vvalue['columns']);
                        } else {
                            $joinColumns = self::$modelsList[$vvalue['id']]['columns'];
                            if (isset($vvalue['exColumns']) && is_array($vvalue['exColumns'])) {
                                foreach ($vvalue['exColumns'] as $ec) {
                                    $ek = array_search($ec, $joinColumns);
                                    if ($ek !== false) {
                                        unset($joinColumns[$ek]);
                                    }
                                }
                            }
                            $columns = array_merge($columns, $joinColumns);
                        }
                    }
                }
                unset($joinColumns);
            }
        }
        foreach (array('where', 'andWhere', 'orWhere') as $value) {
            if (isset($query[$value]) && !empty($query[$value]) && is_array($query[$value])) {
                foreach ($query[$value] as $vvalue) {
                    if (!isset($vvalue['type'])) {
                        $vvalue['type'] = null;
                    }
                    if (isset($vvalue['conditions']) && isset($vvalue['bind'])) {
                        $sql = $sql->{$value}($vvalue['conditions'], $vvalue['bind'], $vvalue['type']);
                    }
                }
            }
        }
        foreach (array('inWhere','notInWhere') as $value) {
            if (isset($query[$value]) && !empty($query[$value]) && is_array($query[$value])) {
                foreach ($query[$value] as $vvalue) {
                    if (!isset($vvalue['type'])) {
                        $vvalue['type'] = null;
                    }
                    if (isset($vvalue['conditions']) && isset($vvalue['bind'])) {
                        $sql = $sql->{$value}($vvalue['conditions'], $vvalue['bind']);
                    }
                }
            }
        }
        $sql = $sql->columns($columns);
        if (isset($query['order'])) {
            $sql = $sql->orderBy($query['order']);
        }
        if (isset($query['group'])) {
            $sql = $sql->groupBy($query['group']);
        }
        if (isset($query['paginator']) && $query['paginator'] == true) {
            if (!isset($query['limit'])) {
                $query['limit'] = 20;
            }
            if (!isset($query['page'])) {
                $query['page'] = 1;
            }
            $output = new Paginator(array(
                'builder' => $sql,
                'limit' => $query['limit'],
                'page' => $query['page'],
            ));
            return $output->getPaginate();
        }
        if (isset($query['limit'])) {
            if ($query['limit'] == 1) {
                return $sql->getQuery()->getSingleResult();
            } else {
                $sql = $sql->limit(intval($query['limit']));
            }
        }
        return $sql->getQuery()->execute();
    }
}
