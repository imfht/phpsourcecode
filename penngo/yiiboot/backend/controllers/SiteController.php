<?php
namespace backend\controllers;

use Yii;
use backend\models\AdminUser;
use backend\models\BackendUser;
use backend\models\AdminUserRole;
use common\utils\CommonFun;
/**
 * Site controller
 */
class SiteController extends BaseController
{
    public $layout = "lte_main";
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'maxLength' => 5,
                'minLength' => 5
            ],
        ];
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest){
            $this->layout = "lte_main_login";
            return $this->render('login');
        }
        else{
//             $this->layout = "lte_main";
            $menus = Yii::$app->user->identity->getSystemMenus();
            $sysInfo = [
                ['name'=> '操作系统', 'value'=>php_uname('s')],  //'value'=>php_uname('s').' '.php_uname('r').' '.php_uname('v')],
                ['name'=>'PHP版本', 'value'=>phpversion()],
                ['name'=>'Yii版本', 'value'=>Yii::getVersion()],
                ['name'=>'数据库', 'value'=>$this->getDbVersion()],
                ['name'=>'AdminLTE', 'value'=>'V2.3.6'],
                ['name'=>'建议和BUG', 'value'=>'http://git.oschina.net/penngo/chadmin/issues'],
            ];
            return $this->render('index', [
                'system_menus' => $menus,
                'sysInfo'=>$sysInfo
            ]);
        }
    }

    public function actionLogin()
    {
        
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        $captcha = Yii::$app->request->post('captcha');
        $valid = $this->createAction('captcha')->validate($captcha, false);
        $key = 'try_login_'.$username;
        $checkinfo = Yii::$app->cache->get($key);
        $valid = true;
        if(empty($checkinfo) == false){
            $valid = $this->createAction('captcha')->validate($captcha, false);
        }

        $rememberMe = Yii::$app->request->post('remember');
        $rememberMe = $rememberMe == 'y' ? true : false;
        if($valid === false){
            return $this->asJson(['errno'=>1, 'msg'=>'验证码不正确']);
        }
        else{
            try{
                $login = AdminUser::login($username, $password, $rememberMe);
                if($login == true){
                    AdminUser::updateAll(
                        ['last_ip' => CommonFun::getClientIp()],
                        ['uname' => $username]
                        );
                    return $this->asJson(['errno'=>0, 'msg'=>$captcha, 'valid'=>$valid]);
                }
                else{
                    Yii::$app->cache->set('try_login_'.$username, true, 60);
                    return $this->asJson(['errno'=>2, 'msg'=>'用户名或密码不正确']);
                }
            }
            catch (\Exception $e){
                return $this->asJson(['errno'=>2, 'msg'=>"网络或数据库错误！"]);
            }
        }
    }

    public function actionTest()
    {

          
    }
    
    public function actionLogout()
    {
        Yii::$app->user->identity->clearUserSession();
        Yii::$app->user->logout();
        return $this->goHome();
    }
    public function actionPsw()
    {
       $userRole = AdminUserRole::find()->with('role')->andWhere(['user_id'=>Yii::$app->user->identity->id])->one();
        return $this->render('psw',[
            'user_role' => $userRole->role->name
        ]);
    }
    public function actionPswSave()
    {
        $old_password = Yii::$app->request->post('old_password', '');
        $new_password = Yii::$app->request->post('new_password', '');
        $confirm_password = Yii::$app->request->post('confirm_password', '');
        if(empty($old_password) == true){
            $msg = array('error'=>2, 'data'=>array('old_password'=>'旧密码不正确'));
            return $this->asJson($msg);
            exit();
        }
        if(empty($new_password) == true){
            $msg = array('error'=>2, 'data'=>array('new_password'=>'新密码不能为空'));
            return $this->asJson($msg);
            exit();
        }
        if(empty($confirm_password) == true){
            $msg = array('error'=>2, 'data'=>array('confirm_password'=>'确认密码不能为空'));
            return $this->asJson($msg);
            exit();
        }
        if($new_password != $confirm_password){
            $msg = array('error'=>2, 'data'=>array('confirm_password'=>'两次新密码不相同'));
            return $this->asJson($msg);
            exit();
        }
        if(Yii::$app->user->isGuest == false){
            $user = AdminUser::findByUsername(Yii::$app->user->identity->uname);
            if(BackendUser::validatePassword($user, $old_password) == true){
                $user->password = Yii::$app->security->generatePasswordHash($new_password);
                $user->save();
                $msg = array('errno'=>0, 'msg'=>'保存成功');
                return $this->asJson($msg);
            }
            else{
                $msg = array('errno'=>2, 'data'=>array('old_password'=>'旧密码不正确'));
                return $this->asJson($msg);
            }
        }
        else{
            $msg = array('errno'=>2, 'msg'=>'请先登录');
            return $this->asJson($msg);
        }
    }
    private function getDbVersion(){
        $driverName = Yii::$app->db->driverName;
        if(strpos($driverName, 'mysql') !== false){
            $v = Yii::$app->db->createCommand('SELECT VERSION() AS v')->queryOne();
            $driverName = $driverName .'_' . $v['v'];
        }
        return $driverName;
    }
    public function actionCheckinfo() {
        $username = Yii::$app->request->get('username','');
        $result = ['try'=>false];
        $key = 'try_login_'.$username;
        if(empty($username) == false){
            $data = Yii::$app->cache->get($key);
            if(empty($data) == false){
                $result = ['try'=>true];
            }
        }
        return $this->asJson($result);
    }
    /**
     * 全局错误处理
     */
    public function actionError()
    {
        $exception = Yii::$app->getErrorHandler()->exception;
        $statusCode = $exception->statusCode;
//         return $this->render('error', ['name' => $statusCode, 'message'=>$exception->__toString()]);
        return $this->render('error', ['name' => $statusCode, 'message'=>"系统出错，具体错误信息请查看runtime\logs\app.log"]);
         
    }
    

}
