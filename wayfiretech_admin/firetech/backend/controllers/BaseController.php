<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-03 07:34:16
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-18 11:27:08
 */

namespace backend\controllers;

use common\helpers\CacheHelper;
use Yii;
use yii\web\Controller;
use diandi\admin\models\Menu;
use diandi\addons\modules\searchs\DdAddons;
use diandi\admin\models\AddonsUser;
use Smarty;

class BaseController extends Controller
{
    public $layout = '@backend/views/layouts/main';

    // 是否根据公司检索
    public $blocField = 'bloc_id';

    // 是否根据商户检索
    public $storeField = 'store_id';

    // 主要数据的模型
    public $modelName = '';

    // 检索的模型名称，区分大小写
    public $modelSearchName = '';

    public $smart;

    // 赋值额外变量
    
    private $_params;
    
    public $cache;
    
    public function beforeAction($action)
    {
        global $_GPC;
        //没有登录则跳转到登录界面
        if (Yii::$app->user->isGuest && \Yii::$app->controller->id != 'site') {
            return  $this->redirect(Yii::$app->urlManager->createUrl(Yii::$app->user->loginUrl));
        }
        $requestedRoute = '/'.\Yii::$app->controller->id.'/'.\Yii::$app->controller->action->id;
        $url = \Yii::$app->controller->module->id.$requestedRoute;

        $DdAddons = new DdAddons();
        $modules = $DdAddons::find()->select('identifie')->asArray()->column();
        if (in_array(Yii::$app->controller->module->id, $modules)) {
            Yii::$app->params['plugins'] = Yii::$app->controller->module->id;
        }
        
        $requestedRoute = '/'.\Yii::$app->controller->id;
        $nav = Yii::$app->service->backendNavService->getMenu();
        Yii::$app->params['addons'] = Yii::$app->service->commonGlobalsService->getAddons();
     
        $module = Yii::$app->params['addons'];

        $moduleName = $DdAddons->find()->where(['identifie' => Yii::$app->params['addons']])->asArray()->one();

        Yii::$app->params['moduleAll']  = [];
     

        $is_addons = $moduleName ? true : false;
        if($is_addons){
            $AddonsUser = new AddonsUser();
            $module_names = $AddonsUser->find()->where([
                'user_id' => Yii::$app->user->id,
            ])->with(['addons'])->asArray()->all();
            Yii::$app->params['moduleAll']  = $module_names?$module_names:[];
        }
        Yii::$app->params['is_addons'] = $is_addons; //  empty($menutypes['type']) ? $nav['top'][0]['mark'] : $menutypes['type'];
        Yii::$app->params['module'] = $moduleName; //  empty($menutypes['type']) ? $nav['top'][0]['mark'] : $menutypes['type'];

        Yii::$app->params['welcomeUrl'] = $is_addons ? '/'.Yii::$app->params['addons'] : '/addons/addons/index';
        // $menutypes = Menu::find()->where(['like', 'route', $requestedRoute])->select(['type'])->one();
        /*初始化当前菜单类别*/
        Yii::$app->params['plugins'] = $nav['top'][0]['mark']; //  empty($menutypes['type']) ? $nav['top'][0]['mark'] : $menutypes['type'];
        // 初始化菜单
        Yii::$app->params['topNav'] = Yii::$app->service->backendNavService->getMenu('top', $is_addons);
        Yii::$app->params['leftNav'] = Yii::$app->service->backendNavService->getMenu('left', $is_addons);
        Yii::$app->params['bloc_id'] = Yii::$app->service->commonGlobalsService->getBloc_id();
        Yii::$app->params['store_id'] = Yii::$app->service->commonGlobalsService->getStore_id();
        // p(Yii::$app->params['topNav'],Yii::$app->params['leftNav']);
        // 获取全局消息
        Yii::$app->service->commonGlobalsService->getMessage(Yii::$app->params['bloc_id']);
        /* 设置模板主题 */
        Yii::$app->params['Website']['themcolor'] = Yii::$app->settings->get('Website', 'themcolor');

        // 设置模板标题
        Yii::$app->params['Website']['title'] = $is_addons ? Yii::$app->params['module']['title'] : Yii::$app->settings->get('Website', 'name');

        Yii::$app->params['message']['total'] = 0;
        
        // 获取当前用户的公司
        Yii::$app->service->commonGlobalsService->getBlocByuserId(Yii::$app->user->id);

        $this->smart = new Smarty();
        $this->cache = new CacheHelper();
        
        return parent::beforeAction($action);
    }
    
 


    public function behaviors()
    {
        /* 添加行为 */
        $behaviors = parent::behaviors();

        // 跨域支持
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                // restrict access to
                'Origin' => ['http://www.ai.com', 'https://locahost:8080', 'http://webapi.amap.com'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Method' => ['POST', 'PUT'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Request-Headers' => ['X-Wsse'],
                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
        ];

        // 添加默认的公司与商户参数
        $behaviors['request'] = [
            'class' => \common\behaviors\HttpRequstMethod::className(),
        ];


        return $behaviors;
    }

    
    
    public function assign($key,$val){
        $this->_params[$key]  = $val;
    }
    
    

    public function renderVue($view,$param,$return=false)
    {   
        if(!empty($this->_params)){
            $param =  $param ===null?  $this->_params :array_merge($this->_params,$param);
        }
        
        return $this->render($view.'.vue', $param,$return);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
