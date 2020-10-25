<?php
/**
 * Class SiteController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class SiteController extends FWFrontController
{
    public function filters() {
        $ret = array();
        if ($this->siteConfig && $this->siteConfig->SiteIsUsedCache) {
            $ret[] = array (
                'FWOutputCache + index',
                'duration' => 2592000,
//                'varyByParam' => '',
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey'=> 'novel-index',
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'novel-index',
//                )
            );
        }
        return $ret;
    }

    public function actions(){
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xf4f4f4,
                'padding' => 0,
                'height' => 30,
                'maxLength' => 4,
            ),
        );
    }

	/**
     * 首页
	 */
	public function actionIndex()
	{

        if (!H::checkIsInstall()) {
            $this->redirect(array('install/index'));
        }

        $this->render("index");
	}

    /**
     * 登陆
     */
    public function actionLogin()
    {
        if(!Yii::app()->user->isGuest){
            $this->redirect(array('site/index'));
        }
//        $this->layout = 'main-login';
        $model=new LoginForm;
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            $identity=new UserIdentity($model->username,$model->password);

            if(!$this->createAction('captcha')->validate($model->verifyCode, false)){
                Yii::app()->user->setFlash('actionInfo','验证码错误！');
                $this->refresh();
            }
            if($model->validate()){
                if($identity->authenticate()){
                    Yii::app()->user->login($identity);
                    $this->redirect(array('site/index'));
                }else{
                    Yii::app()->user->setFlash('actionInfo','用户名或密码错误！');
                    $this->refresh();
                }
            }

        }
        //$this->render('login',array('model'=>$model));
        $this->render('login',array('model'=>$model));
    }

    /**
     * 退出
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('site/login'));
    }

    /**
     * 注册
     */
    public function actionRegister()
    {
        $model = new RegisterForm();

        if(isset($_POST['RegisterForm']))
        {
            $model->attributes = $_POST['RegisterForm'];
//            $identity = new UserIdentity($model->username,$model->password);

            if(!$this->createAction('captcha')->validate($model->verifyCode, false)){
                Yii::app()->user->setFlash('actionInfo','验证码错误！');
                $this->refresh();
            }

            if($model->validate()){
                $model = new User();
                $model->attributes = $_POST['RegisterForm'];
                $model->save();
                Yii::app()->user->setFlash('actionInfo','恭喜，注册成功！请登陆！');
                $this->redirect(array('site/login'));
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
        $this->render('register',array('model'=>$model));
    }

    /**
     * 错误显示
     */
    public function actionError()
    {
        if($error = Yii::app()->errorHandler->error) {
            $m = Yii::app()->settings->get("SystemBaseConfig");
            $adminEmail = '';
            if ($m) {
                $adminEmail = $m->SiteAdminEmail;
            }
            Yii::app()->user->setFlash('actionInfo', '^_^ 发现臭虫，请联系站长：' . $adminEmail);
        }
        $this->render('error', $error);
    }
}