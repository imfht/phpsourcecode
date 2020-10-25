<?php


namespace App\Library\Helper;


class BaseHelper
{
    protected $entity_manager;

    protected $container;

    public function __construct()
    {
        $this->entity_manager = GetterHelper::getEntityManager();

        $this->container = GetterHelper::getContainer();
    }

    /**
     * array
     *
     * @var array
     */
    protected static $instance = [];

    /**
     * 获取单例
     *
     * @param mixed ...$args
     * @return static
     */
    public static function getOneInstance(...$args)
    {
        if (!isset(static::$instance[static::class])) {
            static::$instance[static::class] = new static(...$args);
        }
        return static::$instance[static::class];
    }

    /**
     * 获取非静态类
     *
     * @param mixed ...$args
     * @return static
     */
    public static function getInstance(...$args)
    {
        return new static(...$args);
    }

}