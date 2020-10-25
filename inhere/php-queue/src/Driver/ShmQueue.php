<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/31
 * Time: 下午8:08
 */

namespace Inhere\Queue\Driver;

use Inhere\Library\Helpers\PhpHelper;
use Inhere\Shm\ShmMap;

/**
 * Class ShmQueue - shared memory queue
 * @package Inhere\Queue\Driver
 */
class ShmQueue extends BaseQueue
{
    /**
     * @var ShmMap[]
     */
    private $queues = [];

    /**
     * shm options
     * @var array
     */
    private $options = [
        'size' => 256000,
        'project' => 's', // shared memory, semaphore NOTICE: Length can be only one
        'tmpDir' => '/tmp', // tmp path
    ];

    /**
     * {@inheritDoc}
     * @throws \LogicException
     */
    protected function init()
    {
        parent::init();

        $this->driver = Queue::DRIVER_SHM;

        if ($this->id <= 0) {
            // 定义共享内存,信号量key
            $this->id = PhpHelper::ftok(__FILE__, $this->options['project']);
        }

        // create queues
        $this->queues = new \SplFixedArray(count($this->getPriorities()));
    }

    /**
     * @param $data
     * @param int $priority
     * @return bool
     * @throws \RuntimeException
     */
    protected function doPush($data, $priority = self::PRIORITY_NORM)
    {
        if (!$this->isPriority($priority)) {
            $priority = self::PRIORITY_NORM;
        }

        return $this->createQueue($priority)->rPush($data);
    }

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    protected function doPop($priority = null, $block = false)
    {
        // 只想取出一个 $priority 队列的数据
        if ($this->isPriority($priority)) {
            // $priority 级别的队列还未初始化。
            // $queue = $this->createQueue($priority);
            if (!$this->hasQueue($priority)) {
                return null;
            }

            return $this->queues[$priority]->lPop();
        }

        $data = null;

        foreach ($this->queues as $pri => $queue) {
            // $queue = $queue ?: $this->createQueue($pri);
            if (!$queue) {
                continue;
            }

            if (false !== ($data = $queue->lPop())) {
                break;
            }
        }

        // reset($this->queues);
        return $data;
    }

    public function count($priority = self::PRIORITY_NORM)
    {
        if ($queue = $this->queues[$priority]) {
            return $queue->count();
        }

        return 0;
    }

    /**
     * @param int $priority
     * @return bool
     */
    public function hasQueue($priority = self::PRIORITY_NORM)
    {
        return $this->queues[$priority] !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        parent::close();

        foreach ($this->queues as $key => $queue) {
            if ($queue) {
                $queue->close();
                $this->queues[$key] = null;
            }
        }
    }

    /**
     * create queue if it not exists.
     * @param int $priority
     * @return ShmMap
     * @throws \RuntimeException
     */
    protected function createQueue($priority)
    {
        if (!$this->queues[$priority]) {
            $config = $this->getOptions();
            $config['key'] = $this->intChannels[$priority];
            $this->queues[$priority] = new ShmMap($config);
        }

        return $this->queues[$priority];
    }

    /**
     * @return ShmMap[]
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @param int $priority
     * @return ShmMap|false
     */
    public function getQueue($priority = self::PRIORITY_NORM)
    {
        if (!isset($this->getPriorities()[$priority])) {
            return false;
        }

        return $this->queues[$priority];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }
}
