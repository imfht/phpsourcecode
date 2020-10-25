<?php
/**
 * @from https://github.com/matyhtf/framework/blob/master/libs/Swoole/Queue/MsgQ.php
 */

namespace Inhere\Queue\Driver;

/**
 * Class SysVQueue - by system v message queue
 * @package Inhere\Queue\Driver
 */
class SysVQueue extends BaseQueue
{
    /**
     * @var \SplFixedArray
     */
    private $queues = [];

    /**
     * @var int
     */
    private $msgType = 1;

    /**
     * project ID of the sys v msg.
     * NOTICE: Length can be only one
     * @var int|string
     */
    private $project = 0;

    /**
     * @var bool
     */
    private $blocking = true;

    /**
     * buffer Size 8192 65525
     * @var int
     */
    private $bufferSize = 2048;

    /**
     * @var bool
     */
    private $removeOnClose = true;

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    protected function init()
    {
        // php --rf msg_send
        if (!function_exists('msg_receive')) {
            throw new \RuntimeException(
                'To enable System V semaphore,shared-memory,messages support compile PHP with the option --enable-sysvsem --enable-sysvshm --enable-sysvmsg.',
                -500
            );
        }

        parent::init();

        $this->driver = Queue::DRIVER_SYSV;

        if ($this->id <= 0) {
            $this->id = ftok(__FILE__, $this->project);
        }

        // 初始化队列列表. 使用时再初始化需要的队列
        $this->queues = new \SplFixedArray(count($this->getPriorities()));
    }

    /**
     * {@inheritdoc}
     */
    protected function doPush($data, $priority = self::PRIORITY_NORM)
    {
        // $blocking = true 如果队列满了，这里会阻塞
        // bool msg_send(
        //      resource $queue, int $msgtype, mixed $message
        //      [, bool $serialize = true [, bool $blocking = true [, int &$errorcode ]]]
        // )

        if (!$this->isPriority($priority)) {
            $priority = self::PRIORITY_NORM;
        }

        // create queue if it not exists.
        return msg_send(
            $this->createQueue($priority),
            $this->msgType,
            $data,
            false,
            $this->blocking,
            $this->errCode
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function doPop($priority = null, $block = false)
    {
        // bool msg_receive(
        //      resource $queue, int $desiredmsgtype, int &$msgtype, int $maxsize,
        //      mixed &$message [, bool $unserialize = true [, int $flags = 0 [, int &$errorcode ]]]
        //  )

        // 只想取出一个 $priority 队列的数据
        if ($this->isPriority($priority)) {
            // $priority 级别的队列还未初始化。
            if (!$this->hasQueue($priority)) {
                return null;
            }

            $flags = $block ? 0 : (MSG_IPC_NOWAIT | MSG_NOERROR);

            $success = msg_receive(
                $this->queues[$priority],
                0,  // 0 $this->msgType,
                $this->msgType,   // $this->msgType,
                $this->bufferSize,
                $data,
                false,

                // 0: 默认值，无消息后会阻塞等待。(要取多个队列数据时，不能用它，不然无法读取后面两个队列的数据)
                // MSG_IPC_NOWAIT 无消息后不等待
                // MSG_EXCEPT
                // MSG_NOERROR 消息超过大小限制时，截断数据而不报错
                $flags,
                $this->errCode
            );

            if ($success) {
                return $data;
            }

            return null;
        }

        $data = null;

        foreach ($this->queues as $pri => $queue) {
            if (($data = $this->doPop($pri)) !== null) {
                break;
            }
        }

        return $data;
    }

    public function count($priority = self::PRIORITY_NORM)
    {
        if ($queue = $this->queues[$priority]) {
            $stat = msg_stat_queue($queue);

            return $stat['msg_qnum'];
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
     * @return resource
     */
    protected function createQueue($priority)
    {
        if (!$this->queues[$priority]) {
            $key = $this->getIntChannels()[$priority];
            $this->queues[$priority] = msg_get_queue($key);
        }

        return $this->queues[$priority];
    }

    /**
     * @return \SplFixedArray
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * @param int $priority
     * @return resource|false
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
    public function allQueues()
    {
        $aQueues = [];

        exec('ipcs -q', $aQueues);

        return $aQueues;
    }

    /**
     * Setting the queue option
     * @param array $options
     * @param int $queue
     */
    public function setQueueOptions(array $options = [], $queue = self::PRIORITY_NORM)
    {
        msg_set_queue($this->queues[$queue], $options);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function exist($id)
    {
        return msg_queue_exists($id);
    }

    /**
     * close
     */
    public function close()
    {
        parent::close();

        foreach ($this->queues as $key => $queue) {
            if ($queue) {
                if ($this->removeOnClose) {
                    msg_remove_queue($queue);
                }

                $this->queues[$key] = null;
            }
        }
    }

    /**
     * @return array
     */
    public function getStats()
    {
        $stats = [];

        foreach ($this->queues as $queue) {
            $stats[] = msg_stat_queue($queue);
        }

        return $stats;
    }

    /**
     * @param int $queue
     * @return array
     */
    public function getStat($queue = self::PRIORITY_NORM)
    {
        return msg_stat_queue($this->queues[$queue]);
    }

//////////////////////////////////////////////////////////////////////
/// getter/setter method
//////////////////////////////////////////////////////////////////////

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return int
     */
    public function getMsgType(): int
    {
        return $this->msgType;
    }

    /**
     * @param int $msgType
     */
    public function setMsgType(int $msgType)
    {
        $this->msgType = $msgType;
    }

    /**
     * @return int|string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param int|string $project
     */
    public function setProject($project)
    {
        if ($project = trim($project)) {
            $this->project = $project{0};
        }
    }

    /**
     * @return bool
     */
    public function isBlocking(): bool
    {
        return $this->blocking;
    }

    /**
     * @param bool $blocking
     */
    public function setBlocking($blocking = true)
    {
        $this->blocking = (bool)$blocking;
    }

    /**
     * @return int
     */
    public function getBufferSize(): int
    {
        return $this->bufferSize;
    }

    /**
     * @param int $bufferSize
     */
    public function setBufferSize(int $bufferSize)
    {
        $this->bufferSize = $bufferSize;
    }

    /**
     * @return bool
     */
    public function isRemoveOnClose(): bool
    {
        return $this->removeOnClose;
    }

    /**
     * @param bool $removeOnClose
     */
    public function setRemoveOnClose($removeOnClose = true)
    {
        $this->removeOnClose = (bool)$removeOnClose;
    }

}
