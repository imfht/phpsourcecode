<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/25
 * Time: 下午9:58
 */

namespace Partini;


use Inject\Injector;

define('APP_PATH',dirname($_SERVER['SCRIPT_FILENAME']).'/');
class Application extends Injector implements ApplicationInterface
{

    const VERSION = '1.0.0';

    protected static $instance;
    protected $is_debug = true;

    public function __construct()
    {
        self::$instance = $this;

        $this->mapData(ApplicationInterface::class,$this);

        $this->mapData('config',new Config());

        $this->is_debug = $this->getConfig('APP_DEBUG') ? true : false;

        spl_autoload_register('Partini\Application::autoload');
    }

    public function version(){
        return self::VERSION;
    }

    public function getConfig($key = null){
        if($key === null){
            return $this->getData('config');
        }else{
            return $this->getData('config')->get($key);
        }
    }

    public function addConfig($data){
        $this->getData('config')->add($data);
    }

    public function isDebug(){
        return $this->is_debug;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public static function autoload($class) {
        if(false !== strpos($class,'\\')){
            $name           =   strstr($class, '\\', true);
            if(in_array($name,array('Vendor')) || is_dir('Lib/'.$name)){
                $path       =   'Lib/';
            }else{
                $path       =   APP_PATH;
            }
            $filename       =   $path . str_replace('\\', '/', $class) . '.php';
            if(is_file($filename)) {
                include $filename;
            }
        }
    }
}