<?php


namespace Kernel\Core\Conf;


use Igorw\Silex\JsonConfigDriver;
use Igorw\Silex\PhpConfigDriver;
use Igorw\Silex\TomlConfigDriver;
use Igorw\Silex\YamlConfigDriver;
use Kernel\Core\Conf\Type\JsonConfig;
use Kernel\Core\Conf\Type\PhpConfig;
use Kernel\Core\Conf\Type\TomlConfig;
use Kernel\Core\Conf\Type\YafConfig;
use Kernel\Core\Conf\Type\YamlConfig;
use Kernel\Core\Exception\ErrorCode;
use Swoole\Mysql\Exception;

class Config
{
        protected static $instant = null;
        protected $driver = null;
        protected $paths = [ '../conf'];
        protected $type = 'php';
        protected $configs = [];
        public function __construct($paths = [], $type = 'php')
        {
                $this->paths = array_merge($this->paths, $paths);
                $this->type = $type;
        }

        public function setDriverType(string $type)
        {
                $this->type = $type;
        }

        public function setLoadPath(array $paths, bool $cover = false)
        {
                if($cover) {
                        $this->paths = $paths;
                }else{
                        $this->paths = array_merge($this->paths, $paths);
                }
        }

        public function init()
        {
                if($this->driver == null) {
                        $type = strtolower($this->type);
                        switch ($type) {
                                case 'php':
                                        $this->driver = new PhpConfig();
                                        break;
                                case  'yaml':
                                        $this->driver = new YamlConfig();
                                        break;
                                case 'json':
                                        $this->driver = new JsonConfig();
                                        break;
                                case 'toml':
                                        $this->driver = new TomlConfig();
                                        break;
                                case 'yaf':
                                        $this->driver = new YafConfig();
                                        break;
                                default:
                                        throw new \Exception('Config Driver not found with name :'.$type, 1);
                        }
                }
        }

        public function load()
        {
                $this->init();
                $type = $this->type;
                if($this->type == 'yaf') {
                    $type = 'ini';
                }
                foreach ($this->paths as $path) {

                        $iterator = new \GlobIterator($path.DIRECTORY_SEPARATOR.'*.'.$type, \FilesystemIterator::KEY_AS_FILENAME);
                        if($iterator->count()>0) {
                                foreach ($iterator as $item) {
                                        $this->configs = array_merge($this->configs, $this->driver->load($item->getPathname()));
                                }
                        }
                }
        }

        public function get(string $name, bool $throw = true)
        {

                if(!isset($this->configs[$name])){
                        $this->load();
                }

                if(!isset($this->configs[$name])) {
                        if($throw) {
                                throw new ConfigNotFoundException(ErrorCode::CONFIG_NOT_FOUND, $name);
                        }else{
                                return [];
                        }
                }
                return $this->configs[$name];
        }

        public function __get($name)
        {
                return $this->get($name);
        }

}