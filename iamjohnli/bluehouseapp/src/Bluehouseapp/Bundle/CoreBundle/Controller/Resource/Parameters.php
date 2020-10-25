<?php


namespace Bluehouseapp\Bundle\CoreBundle\Controller\Resource;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 */
class Parameters extends ParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function get($path, $default = null, $deep = false)
    {
        $result = parent::get($path, $default, $deep);

        if ($this->has($path) && null === $result && $default !== null) {
            $result = $default;
        }

        return $result;
    }
}
