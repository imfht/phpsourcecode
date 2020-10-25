<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Config;
use think\facade\Request;

class Setting extends Common
{
    public function index($act=null)
    {
        if ($act == 'update') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $data = input('post.');
            if (!isset($data['custom'])) {
                $data['custom'] = [];
            }
            $config_file='config/cy.php';
            if (!is_writable($config_file)) {
                return $this->error('请确保config/cy.php文件可读写');
            }

            $result = file_put_contents($config_file, "<?php\r\nreturn " . var_export($data, true) . ";");
            if ($result) {
                addlog('修改网站配置。', $this->user['username']);
                return $this->success('恭喜，网站配置成功！', url('index'));
            } else {
                return $this->error('参数错误，请重试！');
            }
        }
        return View::fetch('form');
    }
}
