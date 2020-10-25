<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-16 09:37:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-12 13:10:23
 */

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\web\Request;

/**
 * Class HttpRequstMethod.
 *
 * @$_REQUEST = GET + POST
 */
class HttpRequstMethod extends Behavior
{
    /**
     * 保存注入的 yii\web\Request 实例.
     *
     * @var yii\web\Request
     */
    private $request;
    public $bloc_id;
    public $store_id;

    private static $_data = [];

    private $_queryParams = [];

    /**
     * 运用传说中的依赖注入 注入 yii\web\Request.
     *
     * @param array           $config
     * @param yii\web\Request $request
     */
    public function __construct(Request $request, $config = [])
    {
        $this->request = $request;
        parent::__construct($config);
    }

    public function init()
    {
        $bloc_id = Yii::$app->service->commonGlobalsService->getBloc_id();
        $store_id = Yii::$app->service->commonGlobalsService->getStore_id();

        // 后台用户使用
        // if (Yii::$app->user->identity->bloc_id) {
        //     $bloc_id = Yii::$app->user->identity->bloc_id;
        // }

        // if (Yii::$app->user->identity->store_id) {
        //     $store_id = Yii::$app->user->identity->store_id;
        // }

        $this->bloc_id = $bloc_id;
        $this->store_id = $store_id;
    }

    //@see http://www.yiichina.com/doc/api/2.0/yii-base-behavior#events()-detail
    public function events()
    {
        $whereInit = [];
        $where = [];
        if ($this->owner->blocField) {
            $whereInit[$this->owner->blocField] = $this->bloc_id;
        }

        if ($this->owner->storeField) {
            $whereInit[$this->owner->storeField] = $this->store_id;
        }

        $store = Yii::$app->service->commonGlobalsService->getStoreDetail($this->store_id);
        // 以集团化管理且是顶级公司的需要查看所有数据的权利
        if(!empty($store)){
            if ($store['bloc']['pid'] == 0 && $store['bloc']['status'] == 1) {
                $blocs = Yii::$app->service->commonGlobalsService->getBlocChild($this->bloc_id);
                if(!empty($blocs)){
                    // 存在子公司
                    $whereInit[$this->owner->blocField] = array_column($blocs,'bloc_id');                    
                }
            }
        }
        
        if ($this->owner->modelSearchName && !empty($whereInit)) {
            if (key_exists($this->owner->modelSearchName, Yii::$app->request->queryParams)) {
                $whereInit = array_merge($whereInit, Yii::$app->request->queryParams[$this->owner->modelSearchName]);
            } else {
                $where[$this->owner->modelSearchName] = $whereInit;
            }
        }
        $where = array_merge(\Yii::$app->request->get(), \Yii::$app->request->post(), $where);
        
        Yii::$app->request->setQueryParams($where);

        return Yii::$app->request->queryParams;
    }

    public function request($name = null)
    {
        $request = \Yii::$app->request;
        if (!self::$_data) {
            self::$_data = ArrayHelper::merge($request->getBodyParams(),
               $request->getQueryParams());
        }

        return self::$_data[$name] ?? self::$_data;
    }
}
