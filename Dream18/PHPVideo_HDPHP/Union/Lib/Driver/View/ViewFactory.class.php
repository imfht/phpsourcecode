<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 视图处理抽象工厂
 * @package View
 * @author 后盾向军 <2300071698@qq.com>
 */
final class ViewFactory
{

    /**
     * 静态工厂实例
     * @var string
     */
    public static $viewFactory = null;
    /**
     * 驱动链接组
     * @var array
     */
    protected $driverList = array();

    /**
     * 构造函数
     */
    private function __construct()
    {

    }

    /**
     * 返回工厂实例，单例模式
     */
    public static function factory($driver = null)
    {
        //只实例化一个对象
        if (is_null(self::$viewFactory)) {
            self::$viewFactory = new viewFactory();
        }
        if (is_null($driver)) {
            $driver = ucfirst(strtolower(C("TPL_ENGINE")));
        }
        if (isset(self::$viewFactory->driverList[$driver])) {
            return self::$viewFactory->driverList[$driver];
        }
        self::$viewFactory->getDriver($driver);
        return self::$viewFactory->driverList[$driver];
    }

    /**
     * 获得数据库驱动接口
     * @param string $driver 驱动
     * @return bool
     */
    public function getDriver($driver)
    {
        $class = "View" . ucfirst($driver);
        //加载类文件
        if (!class_exists($class, false)) {
            $classFile = HDPHP_DRIVER_PATH . 'View/' . $class . '.class.php';
            if (!require_cache($classFile)) {
                DEBUG && halt($classFile . "不存在");
            }
        }
        /**
         * 记录驱动类
         */
        $this->driverList[$driver] = new $class();
        //视图操作引擎对象
        return true;
    }

}
