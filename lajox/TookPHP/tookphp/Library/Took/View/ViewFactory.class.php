<?php
/**
 * 视图处理抽象工厂
 * @package View
 * @author lajox <lajox@19www.com>
 */
namespace Took\View;
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
    protected static $driverList = array();

    /**
     * 构造函数
     */
    private function __construct(){}

    /**
     * 返回工厂实例，单例模式
     */
    public static function factory($drivers = null)
    {
        //只实例化一个对象
        if (is_null(self::$viewFactory)) {
            self::$viewFactory = new ViewFactory();
        }
        if (empty($drivers)) {
            $drivers = C("TPL_ENGINE") ? C("TPL_ENGINE") : 'Tk';
        }
        if(!is_array($drivers) && strpos($drivers,',')!==false) {
            $drivers = explode(',', $drivers);
        }
        if(is_array($drivers)) {
            //将Smarty引擎置前
            $hasSmarty = false;
            foreach($drivers as $k=>$driver) {
                if(strtolower($driver)=='smarty') {
                    unset($drivers[$k]);
                    $hasSmarty = true;
                    break;
                }
            }
            if($hasSmarty) {
                array_unshift($drivers, 'Smarty');
            }
            foreach($drivers as $driver) {
                $driver = ucfirst(strtolower(trim($driver)));
                self::$driverList[$driver] = self::newDriver($driver);
            }
            return self::$driverList;
        }
        else {
            $drivers = ucfirst(strtolower(trim($drivers)));
            if (!isset(self::$driverList[$drivers]) || empty(self::$driverList[$drivers])) {
                self::$driverList[$drivers] = self::newDriver($drivers);
            }
            return self::$driverList[$drivers];
        }
    }

    public static function newDriver($driver) {
        $class = ucfirst($driver);
        //加载类文件
        if (!class_exists($class, false)) {
            $classFile = TOOK_LIB_PATH . 'Took/View/Driver/' . $class . EXT;
            if (!require_cache($classFile)) {
                DEBUG && halt($classFile . "不存在");
            }
        }
        //记录驱动类
        $class = '\\Took\\View\\Driver\\'.$class;
        return new $class();
    }

}
