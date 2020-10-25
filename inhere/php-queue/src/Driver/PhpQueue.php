<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/21
 * Time: 上午1:45
 */

namespace Inhere\Queue\Driver;

/**
 * Class PhpQueue
 * @package Inhere\Queue\Driver
 */
class PhpQueue extends BaseQueue
{
    /**
     * @var \SplQueue[]
     */
    private $queues = [];

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        parent::init();

        $this->driver = Queue::DRIVER_PHP;

        if (!$this->id) {
            $this->id = $this->driver;
        }

        // create queues
        $this->queues = new \SplFixedArray(count($this->getPriorities()));
    }

    /**
     * {@inheritDoc}
     */
    protected function doPush($data, $priority = self::PRIORITY_NORM)
    {
        if (!$this->isPriority($priority)) {
            $priority = self::PRIORITY_NORM;
        }

        $this->createQueue($priority)->enqueue($data); // can use push().

        return true;
    }

    /**
     * {@inheritDoc}
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

            return $this->queues[$priority]->dequeue();
        }

        $data = null;

        foreach ($this->queues as $pri => $queue) {
            // $queue = $queue ?: $this->createQueue($pri);
            if (!$queue) {
                continue;
            }

            // valid()
            if (!$queue->isEmpty()) {
                $data = $queue->dequeue();// can use shift().
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
     * create queue if it not exists.
     * @param int $priority
     * @return \SplQueue
     */
    protected function createQueue($priority)
    {
        if (!$this->queues[$priority]) {
            $this->queues[$priority] = new \SplQueue();
            $this->queues[$priority]->setIteratorMode(\SplQueue::IT_MODE_DELETE);
        }

        return $this->queues[$priority];
    }

    /**
     * @return \SplQueue[]
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @param int $priority
     * @return \SplQueue|false
     */
    public function getQueue($priority = self::PRIORITY_NORM)
    {
        if (!isset($this->getPriorities()[$priority])) {
            return false;
        }

        return $this->queues[$priority];
    }

    /**
     * @param int $priority
     * @return array|null
     */
    public function getStat($priority = self::PRIORITY_NORM)
    {
        if ($q = $this->getQueue($priority)) {
            return [
                'num' => $q->count(),
            ];
        }

        return null;
    }

    /**
     * close
     */
    public function close()
    {
        parent::close();

        foreach ($this->getPriorities() as $p) {
            $this->queues[$p] = null;
        }
    }
}
