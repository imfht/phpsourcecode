<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/21
 * Time: 上午1:45
 */

namespace Inhere\Queue\Driver;

use MyLib\ObjUtil\Obj;
use MyLib\SimpleEvent\LiteEventTrait;

/**
 * Class BaseQueue
 * @package Inhere\Queue\Driver
 */
abstract class BaseQueue extends StdObject implements QueueInterface
{
    use LiteEventTrait;

    /**
     * @var string
     */
    protected $driver;

    /**
     * The queue id(name)
     * @var string|int
     */
    protected $id;

    /**
     * @var int
     */
    protected $errCode = 0;

    /**
     * @var string
     */
    protected $errMsg;

    /**
     * whether serialize data
     * @var bool
     */
    protected $serialize = true;

    /**
     * data serializer like 'serialize' 'json_encode'
     * @var callable
     */
    protected $serializer = 'serialize';

    /**
     * data deserializer like 'unserialize' 'json_decode'
     * @var callable
     */
    protected $deserializer = 'unserialize';

    /**
     * @var callable
     */
    private $pushFailHandler;

    /**
     * @var array
     */
    protected $channels = [];

    /**
     * @var array
     */
    protected $intChannels = [];

    /**
     * MsgQueue constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($config);

        // init property
        $this->getChannels();
        $this->getIntChannels();
    }

    /**
     * {@inheritDoc}
     */
    public function push($data, $priority = self::PRIORITY_NORM): bool
    {
        $status = false;
        $this->fire(self::EVENT_BEFORE_PUSH, [$data, $priority]);

        try {
            $status = $this->doPush($this->encode($data), $priority);
            $this->fire(self::EVENT_AFTER_PUSH, [$data, $priority, $status]);
        } catch (\Exception $e) {
            $this->errCode = $e->getCode() !== 0 ? $e->getCode() : __LINE__;
            $this->errMsg = $e->getMessage();

            $this->fire(self::EVENT_ERROR_PUSH, [$e, $data, $priority, $this]);
        }

        return $status;
    }

    /**
     * @param string $data Encoded data string
     * @param int $priority
     * @return bool
     */
    abstract protected function doPush($data, $priority = self::PRIORITY_NORM);

    /**
     * {@inheritDoc}
     */
    public function pop($priority = null, $block = false)
    {
        $data = null;
        $this->fire(self::EVENT_BEFORE_POP, [$priority, $this]);

        try {
            if ($data = $this->doPop($priority, $block)) {
                $data = $this->decode($data);
            }

            $this->fire(self::EVENT_AFTER_POP, [$data, $priority, $this]);
        } catch (\Exception $e) {
            $this->errCode = $e->getCode() !== 0 ? $e->getCode() : __LINE__;
            $this->errMsg = $e->getMessage();

            $this->fire(self::EVENT_ERROR_POP, [$e, $priority, $this]);
        }

        return $data;
    }

    /**
     * @param null $priority
     * @param bool $block
     * @return string Raw data string
     */
    abstract protected function doPop($priority = null, $block = false);

//////////////////////////////////////////////////////////////////////
/// helper method
//////////////////////////////////////////////////////////////////////

    /**
     * get Priorities
     * @return array
     */
    public function getPriorities(): array
    {
        return [
            self::PRIORITY_HIGH,
            self::PRIORITY_NORM,
            self::PRIORITY_LOW,
        ];
    }

    /**
     * @param int $priority
     * @return bool
     */
    public function isPriority($priority)
    {
        if (null === $priority) {
            return false;
        }

        return in_array((int)$priority, $this->getPriorities(), true);
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        if (!$this->channels) {
            $this->channels = [
                self::PRIORITY_HIGH => $this->id . self::PRIORITY_HIGH_SUFFIX,
                self::PRIORITY_NORM => $this->id,
                self::PRIORITY_LOW => $this->id . self::PRIORITY_LOW_SUFFIX,
            ];
        }

        return $this->channels;
    }

    /**
     * @return array
     */
    public function getIntChannels()
    {
        if (!$this->intChannels) {
            $id = (int)$this->id;
            $this->intChannels = [
                self::PRIORITY_HIGH => $id + self::PRIORITY_HIGH,
                self::PRIORITY_NORM => $id + self::PRIORITY_NORM,
                self::PRIORITY_LOW => $id + self::PRIORITY_LOW,
            ];
        }

        return $this->intChannels;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function encode($data)
    {
        if (!$this->serialize || !($cb = $this->serializer)) {
            return $data;
        }

        // return base64_encode(serialize($data));
        return $cb($data);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function decode($data)
    {
        if (!$this->serialize || !($cb = $this->deserializer)) {
            return $data;
        }

        return $cb($data);
    }

    /**
     * close
     */
    public function close()
    {
        $this->clearEvents();
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        $this->close();
    }


//////////////////////////////////////////////////////////////////////
/// getter/setter method
//////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSerialize(): bool
    {
        return $this->serialize;
    }

    /**
     * @param bool $serialize
     */
    public function setSerialize($serialize = true)
    {
        $this->serialize = (bool)$serialize;
    }

    /**
     * @return callable
     */
    public function getSerializer(): callable
    {
        return $this->serializer;
    }

    /**
     * @param callable $serializer
     */
    public function setSerializer(callable $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return callable
     */
    public function getDeserializer(): callable
    {
        return $this->deserializer;
    }

    /**
     * @param callable $deserializer
     */
    public function setDeserializer(callable $deserializer)
    {
        $this->deserializer = $deserializer;
    }

    /**
     * @return callable
     */
    public function getPushFailHandler(): callable
    {
        return $this->pushFailHandler;
    }

    /**
     * @param callable $pushFailHandler
     */
    public function setPushFailHandler(callable $pushFailHandler)
    {
        $this->pushFailHandler = $pushFailHandler;
    }

    /**
     * @return int
     */
    public function getErrCode(): int
    {
        return $this->errCode;
    }

    /**
     * @return string
     */
    public function getErrMsg(): string
    {
        return $this->errMsg;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
