<?php
use \Cute\Web\Handler;
use \Cute\ORM\Mapper;


class DigestHandler extends Handler
{
    protected $db = null;
    protected $user = null;
    protected $logger = null;
    protected $sign = '';

    public function init($method)
    {
        $this->logger = $this->app->load('\\Cute\\Log\\FileLogger', 'auth');
        $post = $this->app->input('post')->all();
        $get = $this->app->input('get')->all();
        $data = array_merge($get, $post);
        $this->logger->info(json_encode($data));
        if (!$this->auth($data)) {
            $this->logger->error('Auth fail');
            return 'fail';
        }
    }

    public function getSecret($appid, $uid)
    {
        $this->db = $this->app->load('\\Cute\\ORM\\MySQL', 'default');
        $query = new Mapper($this->db, '\\API\\User');
        $this->user = $query->filter('app_id', $appid)->get($uid, 'username');
        if ($this->user->isExists() && $this->user->isActive()) {
            return $this->user->getSecret();
        }
    }

    public function auth(array& $data)
    {
        return true;
        if (!isset($data['appid']) || !isset($data['uid'])) {
            return false;
        }
        if (isset($data['sign']) && $sign = $data['sign']) {
            unset($data['sign']);
            ksort($data);
            if ($secret = $this->getSecret($data['appid'], $data['uid'])) {
                $message = http_build_query($data) . $secret;
                $this->sign = md5($message);
                return md5($message) === $sign;
            }
        }
        return false;
    }

    public function fail()
    {
        $data = ['result' => -1, 'error' => '数据校验失败', 'sign' => $this->sign];
        return json_encode($data);
    }
}
