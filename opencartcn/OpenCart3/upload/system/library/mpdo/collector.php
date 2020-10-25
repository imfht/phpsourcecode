<?php
/**
 * mpdo/collector.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-03-05 09:22
 * @modified   2019-03-05 09:22
 */

namespace Mpdo;

class Collector extends \DebugBar\DataCollector\PDO\PDOCollector
{
    private $adaptor;

    public function __construct()
    {
        parent::__construct();

        $this->adaptor = registry()->get('db')->getAdaptor();
        if ($this->adaptor instanceof \DB\mPDO) {
            $this->addConnection($this->getTraceablePdo(), 'mPDO');
        }
    }

    /**
     * @return \DebugBar\DataCollector\PDO\TraceablePDO
     */
    protected function getTraceablePdo()
    {
        return new \DebugBar\DataCollector\PDO\TraceablePDO($this->getPdo());
    }

    /**
     * @return \PDO
     */
    protected function getPdo()
    {
        return $this->getAdaptor()->getConnection();
    }

    /**
     * @return \DB\mPDO;
     */
    protected function getAdaptor()
    {
        return $this->adaptor;
    }
}