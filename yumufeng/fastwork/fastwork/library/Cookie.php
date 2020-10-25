<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:05
 */

namespace fastwork;


class Cookie
{

    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => false,
        // 是否使用 setcookie
        'setcookie' => true,
    ];
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * 构造方法
     * @access public
     * @param array $config
     * @param Request $request
     */
    public function __construct(array $config = [], Request $request, Response $response)
    {
        $this->request = &$request;
        $this->response = &$response;
        $this->init($config);
    }

    /**
     * Cookie初始化
     * @access public
     * @param  array $config
     * @return void
     */
    public function init(array $config = [])
    {
        $this->config = array_merge($this->config, array_change_key_case($config));
    }

    public static function __make(Config $config, Request $request, Response $response)
    {
        return new static($config->pull('cookie'), $request, $response);
    }

    /**
     * 设置或者获取cookie作用域（前缀）
     * @access public
     * @param  string $prefix
     * @return string|void
     */
    public function prefix($prefix = '')
    {
        if (empty($prefix)) {
            return $this->config['prefix'];
        }

        $this->config['prefix'] = $prefix;
    }

    /**
     * Cookie 设置、获取、删除
     *
     * @access public
     * @param  string $name cookie名称
     * @param  mixed $value cookie值
     * @param  mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public function set($name, $value = '', $option = null)
    {
        // 参数设置(会覆盖黙认设置)
        if (!is_null($option)) {
            if (is_numeric($option)) {
                $option = ['expire' => $option];
            } elseif (is_string($option)) {
                parse_str($option, $option);
            }

            $config = array_merge($this->config, array_change_key_case($option));
        } else {
            $config = $this->config;
        }

        $name = $config['prefix'] . $name;

        // 设置cookie
        if (is_array($value)) {
            array_walk_recursive($value, [$this, 'jsonFormatProtect'], 'encode');
            $value = 'fast:' . json_encode($value);
        }

        $expire = !empty($config['expire']) ? $this->request->time() + intval($config['expire']) : 0;

        if ($config['setcookie']) {
            $this->setCookie($name, $value, $expire, $config);
        }
        $this->request->cookie[$name] = $value;
    }

    /**
     * Cookie 设置保存
     *
     * @access public
     * @param  string $name cookie名称
     * @param  mixed $value cookie值
     * @param  array $option 可选参数
     * @return void
     */
    protected function setCookie($name, $value, $expire, $option = [])
    {
        $this->response->cookie($name, $value, $expire, $option['path'], $option['domain'], $option['secure'], $option['httponly']);
    }

    /**
     * 永久保存Cookie数据
     * @access public
     * @param  string $name cookie名称
     * @param  mixed $value cookie值
     * @param  mixed $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public function forever($name, $value = '', $option = null)
    {
        if (is_null($option) || is_numeric($option)) {
            $option = [];
        }

        $option['expire'] = 315360000;

        $this->set($name, $value, $option);
    }

    /**
     * 判断Cookie数据
     * @access public
     * @param  string $name cookie名称
     * @param  string|null $prefix cookie前缀
     * @return bool
     */
    public function has($name, $prefix = null)
    {
        $prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
        $name = $prefix . $name;

        return $this->getCookie($name);
    }

    /**
     * Cookie获取
     * @access public
     * @param  string $name cookie名称 留空获取全部
     * @param  string|null $prefix cookie前缀
     * @return mixed
     */
    public function get($name = '', $prefix = null)
    {
        $prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
        $key = $prefix . $name;
        $cookie = $this->getCookie();
        if ('' == $name) {
            if ($prefix) {
                $value = [];
                foreach ($cookie as $k => $val) {
                    if (0 === strpos($k, $prefix)) {
                        $value[$k] = $val;
                    }
                }
            } else {
                $value = $cookie;
            }
        } elseif (isset($cookie[$key])) {
            $value = $cookie[$key];

            if (0 === strpos($value, 'think:')) {
                $value = substr($value, 6);
                $value = json_decode($value, true);
                array_walk_recursive($value, [$this, 'jsonFormatProtect'], 'decode');
            }
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Cookie删除
     * @access public
     * @param  string $name cookie名称
     * @param  string|null $prefix cookie前缀
     * @return void
     */
    public function delete($name, $prefix = null)
    {
        $config = $this->config;
        $prefix = !is_null($prefix) ? $prefix : $config['prefix'];
        $name = $prefix . $name;

        if ($config['setcookie']) {
            $this->setcookie($name, '', $this->request->time() - 3600, $config);
        }

        // 删除指定cookie
        $this->delCookie($name);
    }

    /**
     * Cookie清空
     * @access public
     * @param  string|null $prefix cookie前缀
     * @return void
     */
    public function clear($prefix = null)
    {
        // 清除指定前缀的所有cookie
        $cookies = $this->getCookie();
        if (empty($cookies)) {
            return;
        }
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $config = $this->config;
        $prefix = !is_null($prefix) ? $prefix : $config['prefix'];

        if ($prefix) {
            // 如果前缀为空字符串将不作处理直接返回
            foreach ($cookies as $key => $val) {
                if (0 === strpos($key, $prefix)) {
                    if ($config['setcookie']) {
                        $this->setcookie($key, '', $this->request->time() - 3600, $config);
                    }
                    $this->delCookie($key);
                }
            }
        }
        return;
    }

    private function jsonFormatProtect(&$val, $key, $type = 'encode')
    {
        if (!empty($val) && true !== $val) {
            $val = 'decode' == $type ? urldecode($val) : urlencode($val);
        }
    }

    private function delCookie($name)
    {
        if (isset($this->request->cookie[$name])) {
            unset($this->request->cookie[$name]);
        }
    }

    private function getCookie($name = '', $default = null)
    {
        $cookies = $this->request->cookie() ?: [];
        if ('' === $name) {
            return $cookies;
        }

        return isset($cookies[$name]) ? $cookies[$name] : $default;
    }
}