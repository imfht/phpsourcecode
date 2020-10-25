<?php
/**
 * @link http://www.yee-soft.com/
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Connection;
use yii\base\Security;

/**
 * Inits root user.
 *
 * Below are some common usages of this command:
 *
 * ~~~
 * # init root user
 * yii init-admin
 *
 * # init root user
 * yii init-admin --username=root --password=qwerty
 * ~~~
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class InitAdminController extends Controller
{
    /**
     * @var string the default command action.
     */
    public $defaultAction = 'init';

    /**
     * @var boolean indicate whether is root user initialization allowed 
     * more that one time.
     */
    public $allowOverwrite = false;

    /**
     * @var string username of root user.
     */
    public $username;

    /**
     * @var string password of root user.
     */
    public $password;

    /**
     * @var string email of root user.
     */
    public $email;

    /**
     * @var boolean whether to execute the migration in an interactive mode.
     */
    public $interactive = true;

    /**
     * @var Connection|string the DB connection object or the application
     * component ID of the DB connection.
     */
    public $db = 'db';

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        return array_merge(parent::options($actionId), ['allowOverwrite', 'interactive', 'username', 'password', 'email', 'db']);
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     *
     * @param \yii\base\Action $action the action to be executed.
     *
     * @throws Exception if db component isn't configured
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            if (is_string($this->db)) {
                $this->db = Yii::$app->get($this->db);
            }
            if (!$this->db instanceof Connection) {
                throw new Exception("The 'db' option must refer to the application component ID of a DB connection.");
            }

            echo "Yee CMS Root User Init Tool\n";
            if (isset($this->db->dsn)) {
                echo "Database Connection: ".$this->db->dsn."\n\n";
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Init root user.
     * For example,
     *
     * ~~~
     * yii init-admin
     * yii init-admin --username=admin --password=admin123
     * ~~~
     *
     */
    public function actionInit()
    {
        if (!$this->canUpdateRootUser()) {
            echo "Root user has already been initialized.\n";

            return;
        }

        if (!$this->username) {
            $this->username = $this->prompt('Enter root user name: ', [
                'required' => true,
                'default' => 'admin',
            ]);
        }

        if (!$this->validateUsername($this->username)) {
            echo "Username must contains only alphabets and numbers and be at least 4 characters long.\n";

            return;
        }

        if (!$this->password) {
            $this->password = $this->prompt('Enter password for root user: ', [
                'required' => true,
            ]);
        }

        if (!$this->validatePassword($this->password)) {
            echo "Password must contains only alphabets and numbers and be at least 6 characters long.\n";

            return;
        }

        if (!$this->email) {
            $this->email = $this->prompt('Enter email of root user: ', [
                'required' => true,
            ]);
        }

        if (!$this->validateEmail($this->email)) {
            echo "Invalid email.\n";

            return;
        }

        if ( ($this->interactive==false) or ($this->confirm("Create root user '{$this->username}' with password '{$this->password}' ?")) ) {

            if (!$this->createUser($this->username, $this->password, $this->email)) {
                echo "\nCreation failed.\n";

                return;
            }

            echo "\nRoot user created successfully.\n";
        }
    }

    private function validateUsername($username)
    {
        if (strlen($username) < 4) {
            return false;
        }

        if (preg_match('/[^a-z_\-0-9]/i', $username)) {
            return false;
        }

        return true;
    }

    private function validatePassword($password)
    {
        if (strlen($password) < 6) {
            return false;
        }

        if (preg_match('/[^a-z_\-0-9]/i', $password)) {
            return false;
        }

        return true;
    }

    private function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    private function canUpdateRootUser()
    {
        $user = $this->db->createCommand('SELECT * FROM {{%user}} WHERE id = 1')->queryOne(\PDO::FETCH_OBJ);

        return ($user && (empty($user->password_hash) || $this->allowOverwrite));
    }

    private function createUser($username, $password, $email)
    {

        if ($this->canUpdateRootUser()) {
            $security      = new Security();
            $password_hash = $security->generatePasswordHash($password);

            $result = $this->db->createCommand()->update('{{%user}}', [
                    'username' => $username,
                    'password_hash' => $password_hash,
                    'email' => $email,
                    ], [ 'id' => '1'])->execute();

            if ($result > 0) {
                return true;
            }
        }

        return false;
    }
}