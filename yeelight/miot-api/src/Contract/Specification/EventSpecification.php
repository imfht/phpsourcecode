<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:43.
 */

namespace MiotApi\Contract\Specification;

/**
 * 简单的事件，用属性的变化来通知用户。复杂的事件，需要用Event来表达:
 * 发生了什么事情?
 * 哪些属性发生了变化？
 *
 * Class EventSpecification
 */
class EventSpecification extends Specification
{
    /**
     * 参数列表
     * 可以是0到N个，每个参数都由属性组成.
     *
     * @var
     */
    protected $arguments;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function init()
    {
        parent::init();

        if ($this->has('arguments')) {
            $arguments = $this->__get('arguments');
            if (!empty($arguments)) {
                foreach ($arguments as $argument) {
                    $this->arguments[] = new PropertySpecification($argument);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
