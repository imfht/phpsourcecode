<?php
/**
 * tranceablepdo.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-03-07 16:30
 * @modified   2019-03-07 16:30
 */

namespace Eloquent;

class TraceablePDO extends \DebugBar\DataCollector\PDO\TraceablePDO
{
    /**
     * TraceablePDO constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array(Statement::class, array($this)));
    }
}
