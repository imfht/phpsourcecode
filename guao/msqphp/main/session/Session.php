<?php declare (strict_types = 1);
namespace msqphp\main\session;

use msqphp\core\config\Config;

final class Session
{
    use SessionStaticTrait, SessionParamsTrait, SessionOperateTrait;

    // 抛出异常
    private static function exception(string $message): void
    {
        throw new SessionException('[session错误]' . $message);
    }
}

trait SessionStaticTrait
{
    private static $config = [
        // 处理器
        'handler'         => 'File',
        // 范围
        'scope'           => false,
        // session名
        'name'            => 'MSQ_SESSION',
        // 配置
        'handlers_config' => [],
    ];

    // 保存所有session
    private static $sessions       = [];
    private static $scope_info     = [];
    private static $scope_instance = [];

    private static $started = false;

    // 处世化静态类
    private static function initStatic(): void
    {
        // 初始化过直接返回
        static $inited = false;

        if ($inited) {
            return;
        }
        $inited = true;

        static::$config = $config = array_merge(static::$config, Config::get('session'));
        // ini设置session过期时间
        ini_set('session.cache_expire', (string) $config['expire']);
        // 获取默认的驱动

        $handler = $config['handler'];
        // 当前目录下的Handlers下处理类名称.php
        $file = \msqphp\Environment::getVenderFilePath(__CLASS__, $handler, 'handlers');
        $file === null && static::exception($handler . ' 未知的session处理器');
        // 加载文件
        require $file;

        // 拼接函数类名, 例:\msqphp\core\session\session\handlers\File
        $class_name = __NAMESPACE__ . '\\handlers\\' . $handler;
        // 注册并传参配置config
        session_set_save_handler(new $class_name($config['handlers_config'][$handler]), true);

        static::sessionStart();
    }
    private static function sessionStart(): void
    {
        // session名设置
        session_name(static::$config['name']);
        // session开始
        session_start();

        if (static::$config['scope']) {
            static::$scope_info = &$_SESSION;
        } else {
            static::$sessions = &$_SESSION;
        }

        static::$started = true;
    }
    private static function sessionClose(): void
    {
        static::$started && session_write_close();
        static::$sessions = $_SESSION = null;
        foreach (static::$scope_instance as $instance) {
            unset($instance);
        }
        static::$scope_instance = [];
        static::$scope_info     = [];
        static::$started        = false;
    }
    private static function loadScpoeHandler(string $type): void
    {
        // 一个数组,包括所有载入过的处理器文件
        static $files = [];

        // 处理类文件是否载入
        if (!isset($files[$type])) {
            $file = \msqphp\Environment::getVenderFilePath(__CLASS__, $type, 'scopeHandlers');
            $file === null && static::exception($type . '缓存处理类不存在');
            // 载入文件
            require $file;
            // 文件载入过
            $files[$type] = true;
        }
    }
}

trait SessionParamsTrait
{
    // 当前操作session(所有操作型函数以此为基础)
    private $params = [];

    //构造函数
    public function __construct()
    {
        $this->init();
        static::$started || static::sessionStart();
    }

    // 初始化
    public function init(): self
    {
        $this->params = [];
        static::initStatic();
        return $this;
    }
    // 添加一个params值
    private function setParamValue(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }
    public function scope(string $scope): self
    {
        return $this->setParamValue('scope', $scope);
    }
    public function scpoeType(string $scope_type): self
    {
        return $this->setParamValue('scope_type', $scope_type);
    }
    public function scpoeConfig(string $scope_config): self
    {
        return $this->setParamValue('scope_config', $scope_config);
    }
    public function scopeHandler(scopeHandlers\ScopeHandlerInterface $handler): self
    {
        isset($this->params['scope']) || static::exception('请先指定session中scope范围,然后分配对应handler');
        isset(static::$scope_instance[$this->params['scope']]) || static::exception('当前scope处理器已存在,请不要反复分配');
        return $this->setParamValue('scope_handler', $handler);
    }
    public function key(string $key)
    {
        return $this->setParamValue('key', $key);
    }

    public function value($value)
    {
        return $this->setParamValue('value', $value);
    }
}

trait SessionOperateTrait
{
    // 存在
    public function exists()
    {
        if (static::$config['scope']) {
            return $this->getScopeHandle()->exists($this->getKey());
        } else {
            return isset(static::$sessions[$this->getKey()]);
        }
    }
    // 获取
    public function get()
    {
        if (static::$config['scope']) {
            return $this->getScopeHandle()->get($this->getKey());
        } else {
            return static::$sessions[$this->getKey()];
        }
    }
    // 设置
    public function set()
    {
        isset($this->params['value']) || static::exception('未设置对应session值');
        if (static::$config['scope']) {
            $this->getScopeHandle()->set($this->getKey(), $this->params['value']);
        } else {
            static::$sessions[$this->getKey()] = $this->params['value'];
        }
    }
    // 删除
    public function delete()
    {
        if (static::$config['scope']) {
            $this->getScopeHandle()->delete($this->getKey());
        } else {
            unset(static::$sessions[$this->getKey()]);
        }
    }
    // 关闭
    public function close()
    {
        static::sessionClose();
    }
    // 获得真是键
    private function getKey(): string
    {
        isset($this->params['key']) || static::exception('未设置对应session键');
        return $this->params['key'];
    }
    private function getScopeHandle(): scopeHandlers\ScopeHandlerInterface
    {
        if (!isset($this->params['handler'])) {
            $scope = $this->params['scope'] ?? 'default';
            if (!isset(static::$scope_instance[$scope])) {
                $scope_type                 = $this->params['scope_type'] ?? static::$config['scope_handler'];
                $scope_config               = array_merge(static::$config['scopes_config'][$scope_type], $this->params['scopes_config'] ?? []);
                static::$scope_info[$scope] = $scope_type;
                static::loadScpoeHandler($scope_type);
                $class                          = __NAMESPACE__ . '\\scopeHandlers\\' . $scope_type;
                static::$scope_instance[$scope] = new $class(session_id(), $scope, $scope_config);
            }

            // 拼接类名

            $this->params['handler'] = static::$scope_instance[$scope];
        }
        return $this->params['handler'];
    }
}
