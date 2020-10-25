<?php
namespace Kernel;

use Kernel\Config;
use Kernel\Request;

class Route
{
    protected $debug=true;
    protected $configFile='';
    protected $cacheFile='';

    public function __construct()
    {
        $this->debug = Config::instance()->get('app_debug');

        $this->configFile = BOOT_PATH.'route.php';
        $this->cacheFile  = RUNTIME_PATH.'route.php';
    }
    public function dispatch()
    {
        $dispatchData = $this->getDispatchData();
        $dispatcher = new \FastRoute\Dispatcher\GroupCountBased($dispatchData);

        $request = Request::instance();
        $method = $request->method();
        //后缀
        $requestUrl = parse_url($request->url());
        $pathinfo = isset($requestUrl['path']) ? $requestUrl['path']:'/';
        if(strpos($pathinfo,'.html')){
            $pathinfo = str_replace('.html','',$pathinfo);
        }

        $dispatch = $dispatcher->dispatch($method, $pathinfo);
        switch ($dispatch[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                header('HTTP/1.1 404 Not Found');
                exit( '404 Not Found' );
                break;
        }
        
        return $dispatch;
    }
    public function getDispatch()
    {
        return $this->dispatch;
    }

    protected function getDispatchData()
    {
        if(!$this->debug && $dispatchData=self::getCache()){
            return $dispatchData;
        }

        $configRoute = $this->configFile;
        $routeCollector = new \FastRoute\RouteCollector(
            new \FastRoute\RouteParser\Std(),
            new \FastRoute\DataGenerator\GroupCountBased()
        );
        $routes = require $configRoute;
        for ($i=0; $i < count($routes); $i++) {
            call_user_func_array([$routeCollector,'addRoute'], $routes[$i]);
        }
        $dispatchData=$routeCollector->getData();

        if(!$this->debug && $dispatchData){
            $this->setCache($dispatchData);
        }

        return $dispatchData;
    }
    protected function getCache()
    {
        if(is_file($this->cacheFile)){
            return require $this->cacheFile;
        }
        return false;
    }
    protected function setCache($dispatchData=[])
    {
        $cacheFile = $this->cacheFile;
        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export($dispatchData, true) . ';'
        );
    }
}