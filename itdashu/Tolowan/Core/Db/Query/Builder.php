<?php
namespace Core\Db\Query;

use Core\Config;
use Phalcon\Mvc\Model\Query\Builder as Pbuilder;

class Builder extends Pbuilder
{
    protected $_columns = '*';

    /**
     * @param array $placeholders
     * @return \Phalcon\Db\ResultInterface
     */
    public function _execute(array $placeholders = null)
    {
        $sqlString = $this->getPhql();

        $bindParams = (array)$placeholders + (array)$this->_bindParams;
        $bindTypes = $this->_bindTypes;
        $this->_bindTypes = array();
        $this->_bindParams = array();
        global $di;

        /** @var $mm \Phalcon\Mvc\Model\Manager */
        $mm = $di->getShared('modelsManager');

        /**
         * Replace model names to table names
         * [App\Models\Schemaname\Tablename] -> schemaname.tablename
         */
        $sqlString = preg_replace_callback('/\[([^\]]*)\]/m', function (array $matches) use ($mm) {
            if (strpos($matches[1], '\\') !== false) {
                $model = $mm->load($matches[1]);

                $schema = $model->getSchema();
                $table = $model->getSource();

                return $schema ? "$schema.$table" : $table;
            }

            return $matches[1];

        }, $sqlString);

        /**
         * Replace PHQL placeholders to PDO placeholders
         * :name: -> :name
         */
        $sqlString = preg_replace('/(:[\w]*)(:)/m', '$1', $sqlString);

        /**
         * Replace new PHQL placeholders to PDO placeholders
         * {name} -> :name
         * {id:int} -> :id
         * {ids:array} -> :ids0, :ids1
         */
        $sqlString = preg_replace_callback('/\{([^\}]*)\}/m', function (array $matches) use (&$bindParams) {
            if (strpos($matches[1], ':') !== false) {
                list($key, $type) = explode(':', $matches[1]);

                if ($type == 'array') {
                    $result = [];

                    foreach ($bindParams[$key] as $k => $bindParam) {
                        $newkey = $key . '_' . $k;
                        $bindParams[$newkey] = $bindParam;
                        $result[] = ':' . $newkey;
                    }

                    unset($bindParams[$key]);

                    return implode(', ', $result);

                } elseif ('int') {
                    $bindParams[$key] = intval($bindParams[$key]);
                }

                return ':' . $key;
            }

            return ':' . $matches[1];

        }, $sqlString);
        foreach ($bindParams as $key => $value) {
            $this->_bindParams[':' . $key] = $value;
        }
        if ($bindTypes) {
            foreach ($bindTypes as $key => $value) {
                $this->_bindTypes[':' . $key] = $value;
            }
        }
        //print_r($sqlString);
        return $sqlString;
    }

    public function execute(array $placeholders = null)
    {
        global $di;
        $db = $di->getShared('db');
        return $db->query($this->_execute($placeholders), $this->_bindParams, $this->_bindTypes);
    }

    public function fetchOne(array $placeholders = null)
    {
        global $di;
        $db = $di->getShared('db');
        return $db->fetchOne($this->_execute($placeholders), null, $this->_bindParams, $this->_bindTypes);
    }
}
