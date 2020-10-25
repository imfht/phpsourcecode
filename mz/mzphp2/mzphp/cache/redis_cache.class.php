<?php

class redis_cache
{

    /**
     * @var config of instance
     */
    public $conf;

    public $redis;

    /**
     * @param $conf
     *
     * @throws Exception
     */
    public function __construct(&$conf) {
        $this->redis = false;
        $this->conf  = &$conf;
        if (extension_loaded('Redis')) {
            $this->redis = new Redis;
        } else {
            throw new Exception('Redis Extension not loaded.');
        }
        if (!$this->redis) {
            throw new Exception('PHP.ini Error: Redis extension not loaded.');
        }
        if ($conf['pconnect']) {
            $res = $this->redis->pconnect($this->conf['host'], $this->conf['port']);
        } else {
            $res = $this->redis->connect($this->conf['host'], $this->conf['port']);
        }
        if ($res) {
            if (isset($this->conf['pass']) && $this->conf['pass']) {
                $this->redis->auth($this->conf['pass']);
            }
            // set time out
            $this->conf['timeout'] && $this->redis->setOption(Redis::OPT_READ_TIMEOUT, $this->conf['timeout']);
            // set with out Fserialize
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
            // set default prefix
            $this->redis->setOption(Redis::OPT_PREFIX, $this->conf['pre'] . ':');
            // set database
            isset($this->conf['table']) && $this->redis->select($this->conf['table']);
            return $this->redis;
        } else {
            $error       = $this->redis->getLastError();
            $this->redis = false;
            throw new Exception('Can not connect to Redis host.' . var_dump($res) . $error);
        }
    }

    /**
     * @return bool
     */
    public function init() {
        $bool = $this->redis === false ? false : true;
        return $bool;
    }


    public function __call($method, $params) {
        $redis = $this->redis;
        if (method_exists($redis, $method)) {
            switch (count($params)) {
                case 0:
                    return $redis->$method();
                case 1:
                    return $redis->$method($params[0]);
                case 2:
                    return $redis->$method($params[0], $params[1]);
                case 3:
                    return $redis->$method($params[0], $params[1], $params[2]);
                case 4:
                    return $redis->$method($params[0], $params[1], $params[2], $params[3]);
                case 5:
                    return $redis->$method($params[0], $params[1], $params[2], $params[3], $params[4]);
            }
        }
        throw new Exception('RedisMethodNotExists[Method=' . $method . ']');
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function key($key) {
        static $pre_len = -1;
        if ($pre_len == -1) {
            $pre_len = strlen($this->conf['pre']);
        }
        if (is_array($key)) {
            foreach ($key as $index => $k) {
                $key[$index] = $this->key($k);
            }
            return $key;
        } else {
            return $pre_len && strpos($key, $this->conf['pre']) === 0 ? substr($key, $pre_len) : $key;
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function decode($value) {
        if (substr($value, 0, 2) == chr(6) . chr(2)) {
            $value = gzinflate(substr($value, 12, -8));
        }
        return json_decode($value, 1);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function encode($value) {
        $value = core::json_encode($value);
        return strlen($value) >= 512 ? chr(6) . chr(2) . gzencode($value) : $value;
    }

    /**
     * @param $key
     *
     * @return array|bool|mixed|string
     */
    public function get($key) {
        $key = $this->key($key);
        if (is_string($key)) {
            $res = $this->redis->get($key);
            $res = $this->decode($res);
            return $res;
        } else {
            $datas = $this->redis->mget($key);
            foreach ($datas as &$data) {
                $data = $this->decode($data);
            }
            unset($data);
            return $datas;
        }
    }

    /**
     * @param     $key
     * @param     $val
     * @param int $life
     *
     * @return bool
     */
    public function set($key, $val, $life = 0) {
        $key = $this->key($key);
        if ($life > 0) {
            $ret = $this->redis->setex($key, $life, $this->encode($val));
        } else {
            $ret = $this->redis->set($key, $this->encode($val));
        }
        return $ret;
    }

    /**
     * @param $key
     * @param $val
     *
     * @return bool
     */
    public function update($key, $val) {
        $arr = $this->get($key);
        if ($arr !== FALSE) {
            is_array($arr) && is_array($val) && $arr = array_merge($arr, $val);
            return $this->set($key, $arr);
        }
        return FALSE;
    }

    /**
     * @param $key
     *
     * @return int
     */
    public function delete($key) {
        $key = $this->key($key);
        return $this->redis->del($key);
    }

    /**
     * @param string $pre
     *
     * @return bool
     */
    public function truncate($pre = '') {
        return $this->redis->flushdb();
    }
}

?>