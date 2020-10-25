<?php
/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/27
 * Time: 11:43
 */

namespace shiwolang\db;


class Statement
{
    /** @var null|\PDOStatement */
    protected $statement = null;

    /** @var null|DB */
    protected $db = null;

    protected $fetchMode = [];

    protected $jsonObjectContainerClassName = null;

    public function __construct($statement, $db)
    {
        $this->db        = $db;
        $this->statement = $statement;
        $this->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    public function bindParam()
    {
        $params = func_get_args();
        call_user_func_array([$this->statement, "bindParam"], $params);

        return $this;
    }

    public function getPdoType($data)
    {
        static $typeMap = [
            'boolean'  => \PDO::PARAM_BOOL,
            'integer'  => \PDO::PARAM_INT,
            'string'   => \PDO::PARAM_STR,
            'resource' => \PDO::PARAM_LOB,
            'NULL'     => \PDO::PARAM_NULL,
        ];
        $type = gettype($data);

        return isset($typeMap[$type]) ? $typeMap[$type] : \PDO::PARAM_STR;
    }

    public function bindValue($parameter, $value, $dataType = null)
    {
        if ($dataType === null) {
            $dataType = $this->getPdoType($value);
        }

        $this->statement->bindValue($parameter, $value, $dataType);

        return $this;
    }

    public function execute($params = [], &$result)
    {

        $isQMark = isset($params[0]);
        foreach ($params as $name => $param) {
            if ($isQMark) {
                $name = $name + 1;
            }
            $this->bindValue($name, $param);
        }
        $result = $this->statement->execute();

        $this->db->appendLog(new Log($this->db, $this->statement->queryString, $params));

        return $this;
    }

    public function setFetchMode($mode)
    {
        $params = func_get_args();

        $this->fetchMode = $params;
        call_user_func_array([$this->statement, "setFetchMode"], $params);

        return $this;
    }


    public function bindToClass($className, $constructArgs = [])
    {
        $this->setFetchMode(\PDO::FETCH_CLASS, $className, $constructArgs);

        return $this;
    }


    public function all($param = null, $args = [])
    {
        if (is_callable($param)) {
            return $this->statement->fetchAll(\PDO::FETCH_FUNC, $param);
        }
        if ($param !== null && is_string($param)) {
            $this->bindToClass($param, $args);
        }

        return $this->statement->fetchAll();
    }


    public function each($fn, $className = null, $args = [])
    {

        $className !== null && $this->bindToClass($className, $args);

        while ($row = $this->statement->fetch()) {
            $fn($row);
        }
    }

    /**
     * @param null $fetchResult
     * @param null $className
     * @param array $args
     * @return string
     */
    public function json(&$fetchResult = null, $className = null, $args = [])
    {
        $className !== null && $this->bindToClass($className, $args);
        $isContainer = false;
        if ($this->fetchMode[0] === \PDO::FETCH_CLASS) {
            $className       = $this->fetchMode[1];
            $constructArgs   = isset($this->fetchMode[2]) ? $this->fetchMode[2] : [];
            $reflectionClass = new \ReflectionClass($className);
            if (!in_array("JsonSerializable", $reflectionClass->getInterfaceNames())) {
                $jsonObjectContainerClassName = $this->jsonObjectContainerClassName === null ?
                    JsonObjectContainer::class :
                    $this->jsonObjectContainerClassName;
                $this->bindToClass($jsonObjectContainerClassName, [$className, $constructArgs]);
                $isContainer = in_array(ObjectContainerInterface::class, (new \ReflectionClass($jsonObjectContainerClassName))->getInterfaceNames());
            }
        }
        $_fetchResult = $this->all();
        $fetchResult  = $isContainer ? new Container($_fetchResult) : $_fetchResult;

        return json_encode($_fetchResult);
    }

    /**
     * @param null|string $jsonObjectContainerClassName
     * @return $this
     */
    public function setJsonObjectContainerClassName($jsonObjectContainerClassName)
    {
        $this->jsonObjectContainerClassName = $jsonObjectContainerClassName;

        return $this;
    }
}
