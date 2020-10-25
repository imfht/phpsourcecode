<?php declare(strict_types = 1);
namespace msqphp\main\model;

trait ModelParamsTrait
{
    protected $params = [];

    public function init() : self
    {
        $this->params = [];
        return $this;
    }

    private function getTableName(string $table) : string
    {
        return $table[0] === '`' ? $table : '`'.$this->getPrefix().$table.'`';
    }
    private function getPrefix() : string
    {
        return $this->params['prefix'] ?? static::$config['prefix'];
    }
    private function getQuoteField(string $field) : string
    {
        return $field !== '*' && strpos($field, '`') !== false
        ? '`'.$field.'`' : $field;
    }
    private function getPrepare() : array
    {
        return $this->params['prepare'] ?? [];
    }

    private function addPrepare($value, string $type) : string
    {
        switch (strtolower($type)) {
            case 'int':
                $type = \PDO::PARAM_INT;
                break;
            case 'str':
            case 'string':
                $type = \PDO::PARAM_STR;
                break;
            default:
                static::exception('未知类型');
        }
        $pre_name = ':prepare' . (string) count($this->params['prepare'] ?? []);
        $this->params['prepare'][$pre_name] = [$value, $type];
        return $pre_name;
    }


    public function field() : self
    {
        foreach (func_get_args() as $field) {
            $this->params['field'][] = $this->getQuoteField($field);
        }
        return $this;
    }
    public function value($value, ?string $type = null) : self
    {
        if (is_array($value)) {
            $this->params['value'][] = $this->addPrepare($value[0], $value[1]);
        } elseif (null !== $type) {
            $this->params['value'][] = $this->addPrepare($value, $type);
        } else {
            $this->params['value'][] = $value;
        }
        return $this;
    }
    public function prefix(string $prefix) : self
    {
        $this->params['prefix'] = $prefix;
        return $this;
    }

    public function table() : self
    {
        foreach (func_get_args() as $table) {
            $this->params['table'][] = $this->getTableName($table);
        }
        return $this;
    }
    public function join(string $type, string $table) : self
    {
        $this->params['join']['type'] = $type;
        $this->params['join']['table']= $this->getTableName($table);
        return $this;
    }
    public function innerJoin(string $table) : self
    {
        return $this->join('inner_join', $table);
    }
    public function leftJoin(string $table) : self
    {
        return $this->join('left_join', $table);
    }
    public function rightJoin(string $table) : self
    {
        return $this->join('right_join', $table);
    }
    public function fullJoin(string $table) : self
    {
        return $this->join('full_join', $table);
    }
    public function crossJoin(string $table) : self
    {
        return $this->join('cross_join', $table);
    }

    public function on(string $left, ?string $right = null) : self
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                $this->params['join']['on'][] = [$this->getQuoteField($args[0]), '=', $this->getQuoteField($args[0])];
                break;
            case 2;
                $this->params['join']['on'][] = [$this->getQuoteField($args[0]), '=', $this->getQuoteField($args[1])];
                break;
            case 3:
                $this->params['join']['on'][] = [$this->getQuoteField($args[0]), $args[1], $this->getQuoteField($args[2])];
                break;
            default:
                static::exception('错误的传递参数个数');
        }
        return $this;
    }
    public function where()
    {
        $this->params['where'][] = $this->getWhereOrHavingInfo(func_get_args());
        return $this;
    }
    private function getWhereOrHavingInfo(array $args) : array
    {
        $where = $this->getQuoteField(array_shift($args));
        switch (count($args)) {
            case 2:
                $condition = array_shift($args);
            case 1:
                $condition = $condition ?? '=';
                if ($condition === 'in') {
                    $value = is_string($args[0]) ? '(\''. implode('\',\'', $args[0]).'\')' : '('. implode(',', $args[0]).')';
                } else {
                    $value = is_array($args[0]) ? $this->addPrepare($args[0][0], $args[0][1]) : $args[0];
                }
                break;
            default:
                throw new ModelException('不合理的where|having查询');
        }
        return [$where, $condition, $value];
    }
    public function count() : self
    {
        foreach (func_get_args() as $filed) {
            $this->params['field'][] = 'count('.$this->getQuoteField($field).')';
        }
        return $this;
    }
    public function group(string $field)
    {
        $this->params['group'][] = $field;
        return $this;
    }
    public function having() : self
    {
        $this->params['having'][] = $this->getWhereOrHavingInfo(func_get_args());
        return $this;
    }
    public function order(string $field, string $type = 'ASC') : self
    {
        $this->params['order'][] = ['field'=>$this->getQuoteField($field), 'type'=>strtolower($type) === 'desc' ? 'DESC' : 'ASC'];
        return $this;
    }
    public function limit(int $num1, ?int $num2 = null) : self
    {
        $this->params['limit'] = $num2 === null ? [0, $num1] : [$num1, $num2];
        return $this;
    }
}