<?php


namespace Bluehouseapp\Bundle\CoreBundle\Controller\Resource;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Configuration parameters parser.

 */
class ParametersParser
{


    /**
     * @param array   $parameters
     * @param Request $request
     *
     * @return array
     */
    public function parse(array $parameters, Request $request)
    {
        if (!isset($parameterNames)) {
            $parameterNames = array();
        }

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                list($parameters[$key] , $parameterNames[$key]) = $this->parse($value, $request);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameterName = substr($value, 1);
                $parameters[$key] = $request->get($parameterName);
                $parameterNames[$key] = $parameterName;
            }


        }

        return array($parameters, $parameterNames);
    }

    /**
     * @param array  $parameters
     * @param object $resource
     *
     * @return array
     */
    public function process(array &$parameters, $resource)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (empty($parameters)) {
            return array('id' => $accessor->getValue($resource, 'id'));
        }

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->process($value, $resource);
            }

            if (is_string($value) && 0 === strpos($value, 'resource.')) {
                $parameters[$key] = $accessor->getValue($resource, substr($value, 9));
            }
        }


        return $parameters;
    }
}
