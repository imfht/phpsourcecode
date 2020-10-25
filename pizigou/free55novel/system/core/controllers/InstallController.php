<?php
/**
 * Class InstallController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class InstallController extends FWFrontController
{
    public $layout = 'setup';

//    protected $lockFile = 'install.lock';
    protected $dbFile = 'db.sql';


    public function init()
    {
        parent::init();

        // 强制使用bootstrap 作为安装主题
        Yii::app()->theme = 'system';
    }

    public function beforeAction($action)
    {
        if ($action->id != 'finish' && H::checkIsInstall()) {
            Yii::app()->user->setFlash('actionInfo','已经成功安装了小说系统，不需要重新安装！');
            $this->redirect(array('install/finish'));
        }
        return true;
    }

    public function actionIndex()
    {
        $this->redirect('setup');
    }

    /**
     * 数据库安装
     */
    public function actionSetup()
    {
        $model = new SetupForm();

        if(isset($_POST['SetupForm']))
        {
            $model->attributes = $_POST['SetupForm'];

            if($model->validate()){

                $model->dbname = trim($model->dbname);

                // 数据配置校验
                $dsn = 'mysql:host='. $model->dbhost . ';dbname=' . $model->dbname;

                try {
                    $db = new CDbConnection($dsn, $model->username, $model->password);
                    $db->setActive(true);
                } catch (Exception $e) {

                    try {
                        $dsnTry =  'mysql:host='. $model->dbhost . ';dbname=';
                        $db = new CDbConnection($dsnTry, $model->username, $model->password);
                        $db->setActive(true);

                        $sql = "CREATE DATABASE IF NOT EXISTS `" . $model->dbname . "` default charset utf8";
                        $db->createCommand($sql)->execute();
                        $db->setActive(false);

                    } catch (Exception $ex) {
                        Yii::app()->user->setFlash('actionInfo','安装错误！数据库创建失败！请确认数据库账号是否有权限！');
                        $this->refresh();
                    }

                    try {
                        $db = new CDbConnection($dsn, $model->username, $model->password);
                        $db->setActive(true);
                    } catch (Exception $e) {
                        Yii::app()->user->setFlash('actionInfo','安装错误！数据库链接失败！请确认数据库账号是否有权限！');
                        $this->refresh();
                    }
                }


                if (false === $this->writeDbConfig($dsn, $model->username, $model->password)) {
                    Yii::app()->user->setFlash('actionInfo','安装错误！数据库配置写入失败！请给 runtime 目录777权限！');
                    $this->refresh();
                }

                // 导入数据库文件
                if (!$this->importDbFile($db)) {
                    Yii::app()->user->setFlash('actionInfo','安装错误！创建数据表失败！请联系作者！');
                    $this->refresh();
                }

                $db->setActive(false);

//                Yii::app()->user->setFlash('actionInfo','恭喜，安装成功！');
                $this->redirect(array('setupBaseConfig'));

            } else {
                $msg = "";
                foreach ($model->getErrors() as $err) {
                    $msg .= array_shift($err) . "<br />";
                }
                Yii::app()->user->setFlash('actionInfo', $msg);
                $this->refresh();
            }
        }
        //$this->render('login',array('model'=>$model));
        $this->render('setup',array('model'=>$model));
    }

    /**
     * 基础信息安装
     */
    public function actionSetupBaseConfig()
    {
        $cacheCategory =  'system';
        $model = new SystemBaseConfig();

        if(isset($_POST['SystemBaseConfig']))
        {
            $model->attributes = $_POST['SystemBaseConfig'];

            if(!$model->validate()){
                $msg = "";
                foreach ($model->getErrors() as $err) {
                    $msg .= array_shift($err) . "<br />";
                }
                Yii::app()->user->setFlash('actionInfo', $msg);
                $this->refresh();
            } else {
                Yii::app()->settings->set(get_class($model), $model, $cacheCategory);
                Yii::app()->settings->deleteCache($cacheCategory);
//                Yii::app()->user->setFlash('actionInfo','恭喜，安装成功！');
                $this->redirect('setupAdminUser');
            }
        }

        $this->render('baseconfig',array(
            'model'=> $model,
//			'categorys'=>Category::model()->showAllSelectCategory(),
        ));
    }

    /**
     * 后台用户安装
     */
    public function actionSetupAdminUser()
    {
        $cacheCategory =  'system';
        $model = new AdminUser();

        if(isset($_POST['AdminUser']))
        {
            $model->attributes = $_POST['AdminUser'];

            if(!$model->validate()){
                $msg = "";
                foreach ($model->getErrors() as $err) {
                    $msg .= array_shift($err) . "<br />";
                }
                Yii::app()->user->setFlash('actionInfo', $msg);
                $this->refresh();
            } else {

                $model->save();

                // 安装成功，创建安装锁定文件
                $this->createLockFile();
                Yii::app()->user->setFlash('actionInfo','恭喜，飞舞小说系统安装成功！');
                $this->redirect('finish');
            }
        }

        $this->render('adminuser',array(
            'model'=> $model,
//			'categorys'=>Category::model()->showAllSelectCategory(),
        ));
    }    

    /**
     * 安装完成提示
     */
    public function actionFinish()
    {
        $this->render('finish');
    }

    /**
     * @param $dsn
     * @param $username
     * @param $password
     * @return int|boolean
     */
    protected function writeDbConfig($dsn, $username, $password)
    {
        $dbConfigFile = dirname(dirname(dirname(__FILE__))) . '/runtime/front/db.config.php';

        $dbConfig = @include $dbConfigFile;

        $dbConfig['connectionString'] = $dsn;
        $dbConfig['username'] = $username;
        $dbConfig['password'] = $password;

        $s = "<?php \n return ";
        $s .= var_export($dbConfig, true);
        $s .= ";\n?>";



        return file_put_contents($dbConfigFile, $s, LOCK_EX);
    }



    /**
     * 创建安装锁定文件
     */
    protected function createLockFile()
    {
        $lockFile = Yii::app()->runtimePath . "/" . Yii::app()->params['lockFile'];
        file_put_contents($lockFile, date('Y-m-d H:i:s'), LOCK_EX);
    }

    /**
     * 导入数据库文件
     * @param $db CDbConnection
     * @return bool
     */
    protected function importDbFile($db)
    {
        $dbFile =  Yii::app()->runtimePath . "/" . $this->dbFile;

        try {
            $sqlText = file_get_contents($dbFile);

            $sqlList = explode(";", $sqlText);

            foreach ($sqlList as $sql) {
                $sql = trim($sql);
                if ($sql != "")
                    $db->createCommand($sql)->execute();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage(); exit;
            return false;
        }

        return true;
    }
}