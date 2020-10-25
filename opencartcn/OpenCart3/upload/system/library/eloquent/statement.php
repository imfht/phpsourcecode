<?php
/**
 * Statement.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-03-07 15:38
 * @modified   2019-03-07 15:38
 */

namespace Eloquent;

use PDO;

/**
 * A traceable PDO statement to use with \PDO
 */
class Statement extends \DebugBar\DataCollector\PDO\TraceablePDOStatement
{
    /** @var PDO */
    protected $pdo;

    /** @var array */
    protected $boundParameters = array();

    /**
     * Bind a column to a PHP variable
     *
     * @link   http://php.net/manual/en/pdostatement.bindcolumn.php
     * @param  mixed $column Number of the column (1-indexed) or name of the column in the result set
     * @param  mixed $param  Name of the PHP variable to which the column will be bound.
     * @param  int   $type [optional] Data type of the parameter, specified by the PDO::PARAM_*
     * constants.
     * @param  int   $maxlen [optional] A hint for pre-allocation.
     * @param  mixed $driverdata [optional] Optional parameter(s) for the driver.
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        $this->boundParameters[$column] = $param;
        $args = array_merge(array($column, &$param), array_slice(func_get_args(), 2));
        return call_user_func_array(array("parent", 'bindColumn'), $args);
    }

    /**
     * Binds a parameter to the specified variable name
     *
     * @link   http://php.net/manual/en/pdostatement.bindparam.php
     * @param  mixed $parameter Parameter identifier. For a prepared statement using named
     * placeholders, this will be a parameter name of the form :name. For a prepared statement using
     * question mark placeholders, this will be the 1-indexed position of the parameter.
     * @param  mixed $variable  Name of the PHP variable to bind to the SQL statement parameter.
     * @param  int $data_type [optional] Explicit data type for the parameter using the PDO::PARAM_*
     * constants.
     * @param  int $length [optional] Length of the data type. To indicate that a parameter is an OUT
     * parameter from a stored procedure, you must explicitly set the length.
     * @param  mixed $driver_options [optional]
     * @return bool TRUE on success or FALSE on failure.
     */
    public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = null, $driver_options = null)
    {
        $this->boundParameters[$parameter] = $variable;
        $args = array_merge(array($parameter, &$variable), array_slice(func_get_args(), 2));
        return call_user_func_array(array("parent", 'bindParam'), $args);
    }

    /**
     * Binds a value to a parameter
     *
     * @link   http://php.net/manual/en/pdostatement.bindvalue.php
     * @param  mixed $parameter Parameter identifier. For a prepared statement using named
     * placeholders, this will be a parameter name of the form :name. For a prepared statement using
     * question mark placeholders, this will be the 1-indexed position of the parameter.
     * @param  mixed $value The value to bind to the parameter.
     * @param  int   $data_type [optional] Explicit data type for the parameter using the PDO::PARAM_*
     * constants.
     * @return bool TRUE on success or FALSE on failure.
     */
    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        $this->boundParameters[$parameter] = $value;
        return call_user_func_array(array("parent", 'bindValue'), func_get_args());
    }

    /**
     * Executes a prepared statement
     *
     * @link   http://php.net/manual/en/pdostatement.execute.php
     * @param  array $input_parameters [optional] An array of values with as many elements as there
     * are bound parameters in the SQL statement being executed. All values are treated as
     * PDO::PARAM_STR.
     * @return bool TRUE on success or FALSE on failure.
     * @throws \Exception
     */
    public function execute($input_parameters = null)
    {
        /*
        $boundParameters = $this->boundParameters;
        if (is_array($input_parameters)) {
            $boundParameters = array_merge($boundParameters, $input_parameters);
        }

        $trace = new TracedStatement($this->queryString, $boundParameters, $preparedId);
        $sql = $trace->getSqlWithParams('');
        \Models\ActionLog::createActionLog($sql);
         */

        return parent::execute($input_parameters);
    }
}
