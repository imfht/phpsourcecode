<?php
namespace Cute\Contrib\Command;

use \Cute\Contrib\Command\ORMCommand;
use \Cute\Utility\Calendar;


/**
 * 添加用户/修改用户密码
 * 用法：
 * ./run.php user john 123456 apps/Blog/models --dbkey=wordpress --modns='Blog\Model' --singular=y
 */
class UserCommand extends ORMCommand
{
    protected $user = null;

    public function execute()
    {
        if (count($this->args) >= 3) {
            @list($username, $password, $model_dir) = $this->args;
            $this->setArgs();
        }
        $this->generate($model_dir);

        $query = $this->dbman->loadModel('users');
        $this->user = $query->findBy('user_login', $username)->get(false, '*', true);
        if ($this->user->isExists()) {
            $action = 'Set password for user %s.';
        } else {
            $action = 'Add user %s.';
            $this->user->user_login = $username;
            $this->user->user_nicename = $username;
            $this->user->display_name = $username;
            $cal = new Calendar();
            $this->user->user_registered = $cal->speak();
        }
        $this->user->setPassword($password);
        $query->save($this->user);
        $this->app->writeln($action, $username);
    }
}
