<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/4/28
 * Time: 下午8:51
 */

namespace inhere\gearman\tools;

use Exception;

/**
 * Class Monitor
 * @package inhere\gearman\tools
 */
class Monitor
{
    const SORT_SERVER = 'server';
    const SORT_NAME = 'name';
    const SORT_JOBS_IN_QUEUE = 'in_queue';
    const SORT_JOBS_RUNNING = 'in_running';
    const SORT_WORKERS = 'capable_workers';
    const SORT_IP = 'ip';
    const SORT_FD = 'fd';
    const SORT_ID = 'id';

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    const GROUP_NONE = 'none';
    const GROUP_SERVER = 'server';
    const GROUP_NAME = 'name';

    /**
     * Server list
     *
     * @var array
     * [
     *     'index' => [
     *         'name' => 'default',
     *         'address' => '127.0.0.1:4730'
     *     ],
     *     [
     *          'name' => 'test',
     *          'address' => '10.0.0.2:4730',
     *      ],
     *      [
     *          'name' => 'product',
     *          'address' => '10.0.0.1:4730',
     *      ]
     *     ... ...
     * ]
     */
    protected $servers = [];

    /**
     * Filter: server IDs
     *
     * @var array
     */
    protected $filterServers = [];

    /**
     * Filter: server name substring
     *
     * @var string
     */
    protected $filterName = '';

    /**
     * Sort column name
     *
     * @var string
     */
    protected $sort = '';

    /**
     * Group column name
     *
     * @var string
     */
    protected $groupBy = self::GROUP_NONE;

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Class constructor
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set class options
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['filterServers']) && is_array($options['filterServers'])) {
            $this->filterServers = $options['filterServers'];
        }

        if (isset($options['filterName']) && strlen($options['filterName']) > 0) {
            $this->filterName = (string)$options['filterName'];
        }

        if (isset($options['sort'])) {
            $this->sort = (string)$options['sort'];
        }

        if (isset($options['groupby']) && in_array($options['groupby'], $this->_getGroupAvailableFunctions())) {
            $this->groupBy = (string)$options['groupby'];
        }

        if (isset($options['servers'])) {
            $this->addServers($options['servers']);
        }
    }

    /**
     * Clear server list
     * @return self
     */
    public function clearServers()
    {
        $this->servers = [];

        return $this;
    }

    /**
     * Add a server to the list
     * @param int $index
     * @param array $serverValues
     * @return self
     */
    public function addServer($index, $serverValues)
    {
        $this->servers[$index] = (array)$serverValues;

        return $this;
    }

    /**
     * Add several servers to the list
     * @param array $servers
     * @return self
     */
    public function addServers(array $servers)
    {
        foreach ($servers as $key => $value) {
            $this->addServer($key, $value);
        }

        return $this;
    }

    /**
     * Set server list
     * @param array $servers
     * @return self
     */
    public function setServers($servers)
    {
        return $this->clearServers()->addServers($servers);
    }

    /**
     * Returns server list
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * Return error messages
     * @param boolean $duplicates Fetch duplicate messages
     * @return array
     */
    public function getErrors($duplicates = false)
    {
        if ($duplicates) {
            return $this->_errors;
        } else {
            return array_unique($this->_errors);
        }
    }

    /**
     * Add error message to error list
     * @param string $message
     * @return self
     */
    protected function _addError($message)
    {
        $this->_errors[] = $message;

        return $this;
    }

    /**
     * Returns information about Gearman servers data
     * @return array
     */
    public function getServersData()
    {
        $data = [];

        foreach ($this->servers as $index => $server) {
            if ($this->filterServers && !in_array($index, $this->filterServers)) {
                continue;
            }

            try {
                $gmd = new TelnetGmdServer($server['address']);

                $data[$index] = array(
                    'index' => $index,
                    'name' => $server['name'],
                    'version' => $gmd->version(),
                    'address' => $server['address']
                );

                $gmd->close();
                unset($gmd);
            } catch (\Exception $e) {
                $this->_addError($e->getMessage());
            }
        }

        return $data;
    }

    /**
     * Returns information about Gearman registered functions
     * @return array
     */
    public function getFunctionData()
    {
        $i = 0;
        $data = $groupDataTmp = [];

        foreach ($this->servers as $index => $server) {
            if ($this->filterServers && !in_array($index, $this->filterServers)) {
                continue;
            }

            try {
                $gmd = new TelnetGmdServer($server['address']);
                $status = $gmd->statusInfo();
                $gmd->close();
                unset($gmd);

                foreach ((array)$status as $row) {
                    if (strlen($this->filterName) == 0 || stripos($row['job_name'], $this->filterName) !== false) {
                        $row['name'] = $this->groupBy === self::GROUP_SERVER ? '+' : $row['job_name'];
                        $row['server'] = $this->groupBy === self::GROUP_NAME ? '+' : $server['name'];

                        if ($this->groupBy !== self::GROUP_NONE) {
                            if (isset($groupDataTmp[$row[$this->groupBy]])) {
                                $k = $groupDataTmp[$row[$this->groupBy]];
                                foreach (['in_queue', 'in_running', 'capable_workers'] as $key) {
                                    $data[$k][$key] += $row[$key];
                                }
                            } else {
                                $groupDataTmp[$row[$this->groupBy]] = $i;
                                $data[] = $row;
                                $i++;
                            }
                        } else {
                            $data[] = $row;
                            $i++;
                        }
                    }
                }
            } catch (Exception $e) {
                $this->_addError($e->getMessage());
            }
        }

        $data = $this->_sortData($data, $this->_getSortAvailableFunctions());

        return $data;
    }

    /**
     * Returns information about workers connected to Gearman server
     *
     * @return array
     */
    public function getWorkersData()
    {
        $data = [];

        foreach ($this->servers as $index => $server) {
            if ($this->filterServers && !in_array($index, $this->filterServers)) {
                continue;
            }

            try {
                $gmd = new TelnetGmdServer($server['address']);
                $workers = $gmd->workersInfo();
                $gmd->close();
                unset($gmd);

                foreach ($workers as $worker) {
                    if (
                        strlen($this->filterName) == 0 ||
                        stripos($worker['ip'], $this->filterName) !== false ||
                        stripos(implode('$#!', $worker['job_names']), $this->filterName) !== false
                    ) {

                        if ($worker['job_names']) {
                            sort($worker['job_names'], SORT_STRING);
                        }

                        $worker['server'] = $server['name'];
                        $data[] = $worker;
                    }
                }
            } catch (Exception $e) {
                $this->_addError($e->getMessage());
            }
        }

        $data = $this->_sortData($data, $this->_getSortAvailableWorkers());

        return $data;
    }

    /**
     * Returns available sort column
     *
     * @return array
     */
    protected function _getSortAvailableFunctions()
    {
        return [
            self::SORT_SERVER,
            self::SORT_NAME,
            self::SORT_JOBS_IN_QUEUE,
            self::SORT_JOBS_RUNNING,
            self::SORT_WORKERS
        ];
    }

    /**
     * Returns available sort column
     *
     * @return array
     */
    protected function _getSortAvailableWorkers()
    {
        return [
            self::SORT_SERVER,
            self::SORT_IP,
            self::SORT_FD,
            self::SORT_ID
        ];
    }

    protected function _getGroupAvailableFunctions()
    {
        return [
            self::GROUP_SERVER,
            self::GROUP_NAME
        ];
    }

    /**
     * Sort Gearman functions data
     *
     * @param array $data
     * @param array $colsAvailable
     * @return array
     */
    protected function _sortData(array $data, $colsAvailable)
    {
        if (in_array($this->sort, $colsAvailable)) {
            $sortCol = [];

            foreach ($data as $key => $values) {
                $sortCol[$key] = $values[$this->sort];
            }

            array_multisort($sortCol, $this->_getCurrentSortDir(), $data);
        }

        return $data;
    }

    /**
     * Returns requested sort direction
     *
     * @return string
     */
    protected function _getCurrentSortDir()
    {
        $result = SORT_ASC;

        if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == self::SORT_DESC) {
            $result = SORT_DESC;
        }

        return $result;
    }
}
