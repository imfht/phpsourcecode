<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:43.
 */

namespace MiotApi\Contract\Specification;

/**
 * 有时候，一个有意义的操作需要对多个属性进行读写，可以用方法来实现，描述一个方法，需要说清楚
 * 是什么方法？
 * 输入参数是什么？
 * 方法执行完有没有输出值，如果有，输出值什么？
 *
 * Class ActionSpecification
 */
class ActionSpecification extends Specification
{
    /**
     * 输入参数列表
     * 可以是0到N个，每个参数都由属性组成.
     *
     * @var
     */
    protected $in;

    /**
     * 输出参数列表
     * 可以是0到N个，每个参数都由属性组成.
     *
     * @var
     */
    protected $out;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function init()
    {
        parent::init();

        if ($this->has('in')) {
            $ins = $this->__get('in');
            if (!empty($ins)) {
                foreach ($ins as $index => $property) {
                    $this->in[] = new PropertySpecification($property);
                }
            }
        }

        if ($this->has('out')) {
            $outs = $this->__get('out');
            if (!empty($outs)) {
                foreach ($outs as $index => $property) {
                    $this->out[] = new PropertySpecification($property);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIn()
    {
        return $this->in;
    }

    /**
     * @return mixed
     */
    public function getOut()
    {
        return $this->out;
    }
}
