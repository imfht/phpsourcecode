<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Exception\CoreException;

class MongoDb
{
    protected $manager;

    protected $dbname;

    protected static $instances;

    protected $config = [
        'uri' => 'mongodb://localhost:27017',
        'dbname' => 'test'
    ];

    private function __construct(array $config)
    {
        $this->config = $config + $this->config;
        $this->dbname = $this->config['dbname'];
        $this->manager = new \MongoDB\Driver\Manager($this->config['uri']);
    }

    /**
     * @param array $config
     * @return MongoDb
     */
    public static function getInstance(array $config)
    {
        if (!isset(self::$instances[$config['uri']])) {
            self::$instances[$config['uri']] = new self($config);
        }

        return self::$instances[$config['uri']];
    }

    public function insert($collection, $document)
    {
        $bulk = new \MongoDB\Driver\BulkWrite;
        $_id = $bulk->insert($document);
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        try {
            $result = $this->manager->executeBulkWrite($this->dbname . '.' . $collection, $bulk, $writeConcern);
        } catch (\Exception $e) {
            throw new CoreException($e->getMessage(), $e->getCode());
        }

        if (!empty($result->getWriteErrors())) {
            return false;
        }
        return $_id;
    }

    public function find($collection, $filter, $fields = '*', $order = '', array &$page = ['p' => 1, 'limit' => 20])
    {
        $filter = $this->parseWhere($filter);
        $projection = $this->parseFields($fields);
        $sort = $this->parseSort($order);

        $options = [
            'projection' => $projection,
            'sort' => $sort,
            'limit' => $page['limit'],
            'skip' => ($page['p'] - 1) * $page['limit']
        ];

        $page['total'] = $this->count($collection, $filter);
        $page['total_page'] = ceil($page['total'] / $page['limit']);

        // 查询数据
        $query = new \MongoDB\Driver\Query($filter, $options);
        $cursor = $this->manager->executeQuery($this->dbname . '.' . $collection, $query);
        return json_decode(json_encode($cursor->toArray()), true);
    }

    /**
     * 统计条数
     *
     * @param $collection
     * @param $where
     * @return mixed
     */
    public function count($collection, $where)
    {
        $where = $this->parseWhere($where);
        $cmd = new \MongoDB\Driver\Command([
            'count' => $collection,
            'query' => $where
        ]);
        $cursor = $this->manager->executeCommand($this->dbname, $cmd);
        $cursor = current(json_decode(json_encode($cursor->toArray()), true));
        return $cursor['n'];
    }

    private function parseFields($fields)
    {
        if ($fields == '*') {
            return [];
        }
        $temp = explode(',', $fields);
        $projection = [];
        $has_id = false;
        foreach ($temp as $val) {
            $val = trim($val);
            if ($val == '_id') {
                $has_id = true;
            }
            $projection[$val] = 1;
        }
        if (!$has_id) {
            $projection['_id'] = 0;
        }
        return $projection;
    }

    private function parseSort($sort)
    {
        if (!$sort) {
            return [];
        }
        $sort_arr = [];
        $temp = explode(',', $sort);
        foreach ($temp as $val) {
            $arr = explode(' ', trim($val));
            if (trim($arr[1]) == 'asc') {
                $sort_arr[$arr[0]] = 1;
            } else {
                $sort_arr[$arr[0]] = -1;
            }
        }
        return $sort_arr;
    }

    private function parseWhere(array $where)
    {
        $filter = [];
        $where_opts = [
            '>' => '$gt',
            '<' => '$lt',
            '>=' => '$gte',
            '<=' => '$lte',
            '!=' => '$ne',
        ];
        foreach ($where as $key => $value) {
            if (!is_array($value)) {
                $filter[$key] = $value;
            } else {
                list($key2, $val2) = $value;
                switch ($key2) {
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                    case '!=':
                        $filter[$key] = [$where_opts[$key2] => $val2];
                        break;
                }
            }
        }
        return $filter;
    }
}
