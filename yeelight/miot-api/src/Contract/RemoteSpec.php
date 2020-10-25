<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午5:17.
 */

namespace MiotApi\Contract;

use MiotApi\Util\Jsoner\Jsoner;
use MiotApi\Util\Request;

class RemoteSpec extends Jsoner
{
    private static $host = 'miot-spec.org';

    private static $prot = 80;

    private static $namespaces = 'miot-spec-v2';

    private static $timeout = 10;

    const SPEC = 'spec';

    const INSTANCES = 'instances';

    const PROPERTIES = 'properties';

    const ACTIONS = 'actions';

    const EVENTS = 'events';

    const SERVICES = 'services';

    const DEVICES = 'devices';

    const INSTANCE = 'instance';

    const PROPERTY = 'property';

    const ACTION = 'action';

    const EVENT = 'event';

    const SERVICE = 'service';

    const DEVICE = 'device';

    /**
     * 读取所有设备实例列表.
     *
     * @return bool|Jsoner|null
     */
    public static function instances()
    {
        $file = self::INSTANCES;

        return self::__instances($file);
    }

    /**
     * 读取所有的PropertyType.
     *
     * @return bool|Jsoner|null
     */
    public static function properties()
    {
        $file = self::SPEC.DIRECTORY_SEPARATOR.self::PROPERTIES;

        return self::__instances($file);
    }

    /**
     * 读取所有的ActionType.
     *
     * @return bool|Jsoner|null
     */
    public static function actions()
    {
        $file = self::SPEC.DIRECTORY_SEPARATOR.self::ACTIONS;

        return self::__instances($file);
    }

    /**
     * 读取所有的EventType.
     *
     * @return bool|Jsoner|null
     */
    public static function events()
    {
        $file = self::SPEC.DIRECTORY_SEPARATOR.self::EVENTS;

        return self::__instances($file);
    }

    /**
     * 读取所有的ServiceType.
     *
     * @return bool|Jsoner|null
     */
    public static function services()
    {
        $file = self::SPEC.DIRECTORY_SEPARATOR.self::SERVICES;

        return self::__instances($file);
    }

    /**
     * 读取所有的DeviceType.
     *
     * @return bool|Jsoner|null
     */
    public static function devices()
    {
        $file = self::SPEC.DIRECTORY_SEPARATOR.self::DEVICES;

        return self::__instances($file);
    }

    /**
     * 读取某个实例的详细定义.
     *
     * @return bool|Jsoner|null
     */
    public static function instance($type)
    {
        $file = self::INSTANCE.DIRECTORY_SEPARATOR.$type;
        $uri = self::INSTANCE;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取一个PropertyType的具体定义.
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return bool|Jsoner|null
     */
    public static function property($type)
    {
        $type = self::getBaseType($type);
        $file = self::PROPERTY.DIRECTORY_SEPARATOR.$type;
        $uri = self::SPEC.DIRECTORY_SEPARATOR.self::PROPERTY;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取一个ActionType的具体定义.
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return bool|Jsoner|null
     */
    public static function action($type)
    {
        $type = self::getBaseType($type);
        $file = self::ACTION.DIRECTORY_SEPARATOR.$type;
        $uri = self::SPEC.DIRECTORY_SEPARATOR.self::ACTION;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取一个EventType的具体定义.
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return bool|Jsoner|null
     */
    public static function event($type)
    {
        $type = self::getBaseType($type);
        $file = self::EVENT.DIRECTORY_SEPARATOR.$type;
        $uri = self::SPEC.DIRECTORY_SEPARATOR.self::EVENT;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取一个ServiceType的具体定义.
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return bool|Jsoner|null
     */
    public static function service($type)
    {
        $type = self::getBaseType($type);
        $file = self::SERVICE.DIRECTORY_SEPARATOR.$type;
        $uri = self::SPEC.DIRECTORY_SEPARATOR.self::SERVICE;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取一个DeviceType的具体定义.
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return bool|Jsoner|null
     */
    public static function device($type)
    {
        $type = self::getBaseType($type);
        $file = self::DEVICE.DIRECTORY_SEPARATOR.$type;
        $uri = self::SPEC.DIRECTORY_SEPARATOR.self::DEVICE;
        $params = [
            'type' => $type,
        ];

        return self::__instance($file, $uri, $params);
    }

    /**
     * 读取所有实例列表.
     *
     * @return bool|Jsoner|null
     */
    private static function __instances($file)
    {
        $instances = Jsoner::load($file);
        if (!$instances) {
            $instances = self::fetch($file);

            if (!$instances) {
                return false;
            }

            Jsoner::fill($instances, $file);
        }

        return $instances;
    }

    /**
     * 读取某个实例的详细定义.
     *
     * @param $file
     * @param $uri
     * @param $params
     *
     * @return bool|Jsoner|null
     */
    private static function __instance($file, $uri, $params)
    {
        $instance = Jsoner::load($file);
        if (!$instance) {
            $instance = self::fetch($uri, $params);

            if (!$instance) {
                return false;
            }

            $instance = Jsoner::fill($instance, $file);
        }

        return $instance;
    }

    /**
     * @param $type
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return mixed
     */
    private static function getBaseType($type)
    {
        $urn = new Urn($type);

        return $urn->getBaseUrn();
    }

    /**
     * @param $uri
     * @param array $params
     *
     * @return bool
     */
    public static function fetch($uri, $params = [])
    {
        $http = new Request(
            self::$host,
            '/'.self::$namespaces.'/'.$uri,
            self::$prot,
            true,
            self::$timeout
        );

        $result = $http
            ->setQueryParams($params)
            ->execute()
            ->getResponseText();

        if ($result) {
            return $result;
        }

        return false;
    }
}
