<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/21
 * Time: 上午1:26
 */

namespace Inhere\Queue;

/**
 * Interface QueueInterface
 * @package Inhere\Queue
 *
 * @property string $driver
 */
interface QueueInterface
{
    /**
     * Priorities
     */
    const PRIORITY_HIGH = 0;
    const PRIORITY_NORM = 1;
    const PRIORITY_LOW = 2;

    const PRIORITY_LOW_SUFFIX = '_low';
    const PRIORITY_HIGH_SUFFIX = '_high';

    /**
     * Events list
     */
    const EVENT_BEFORE_PUSH = 'beforePush';
    const EVENT_AFTER_PUSH = 'afterPush';
    const EVENT_ERROR_PUSH = 'errorPush';

    const EVENT_BEFORE_POP = 'beforePop';
    const EVENT_AFTER_POP = 'afterPop';
    const EVENT_ERROR_POP = 'errorPop';

    /**
     * push data
     * @param mixed $data
     * @param int $priority
     * @return bool
     */
    public function push($data, $priority = self::PRIORITY_NORM): bool;

    /**
     * pop data
     * @param null|int $priority If is Null, pop all queue data by priority.
     * @param bool $block Whether block pop
     * @return mixed
     */
    public function pop($priority = null, $block = false);

    /**
     * @param int $priority
     * @return int
     */
    public function count($priority = self::PRIORITY_NORM);

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return array
     */
    public function getChannels();

    /**
     * @return array
     */
    public function getIntChannels();

    /**
     * @return string
     */
    public function getDriver(): string;

    /**
     * @return int
     */
    public function getErrCode(): int;

    /**
     * @return string
     */
    public function getErrMsg(): string;
}
