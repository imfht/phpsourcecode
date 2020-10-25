<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\AdminMenu;
use yii\web\NotFoundHttpException;
use yii\helpers\StringHelper;
use yii\helpers\Inflector;
use backend\models\AdminRight;
use backend\models\AdminRightUrl;
use yii\log\Logger;


/**
 * AdminMenuController implements the CRUD actions for AdminMenu model.
 */
class AdminMenuController extends BaseController
{
	public $layout = "lte_main";

    /**
     * Lists all AdminMenu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $mid = Yii::$app->request->get('mid');
        $controllers = $this->getAllController();
        $controllerData = array();
        foreach($controllers as $c){
            $controllerData[$c['text']] = $c;
        
        }
        
        $query = AdminMenu::find()->andWhere(['module_id'=>$mid]);
         $querys = Yii::$app->request->get('query');
         if(empty($querys)== false && count($querys) > 0){
            $condition = "";
            $parame = array();
            foreach($querys as $key=>$value){
                $value = trim($value);
                if(empty($value) == false){
                    $parame[":{$key}"]=$value;
                    if(empty($condition) == true){
                        $condition = " {$key}=:{$key} ";
                    }
                    else{
                        $condition = $condition . " AND {$key}=:{$key} ";
                    }
                }
            }
            if(count($parame) > 0){
                $query = $query->where($condition, $parame);
            }
        }
        
        $pagination = new Pagination([
            'totalCount' =>$query->count(), 
            'pageSize' => '10', 
            'pageParam'=>'page', 
            'pageSizeParam'=>'per-page']
        );
        
        $orderby = ['display_order'=>SORT_ASC];
        $query = $query->orderBy($orderby);
        
        $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
        return $this->render('index', [
            'models'=>$models,
            'pages'=>$pagination,
            'query'=>$querys,
            'module_id'=>$mid,
            'controllerData'=>$controllerData,
        ]);
    }

    /**
     * Displays a single AdminMenu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        //$id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        return $this->asJson($model->getAttributes());

    }

    /**
     * Creates a new AdminMenu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminMenu();
        if ($model->load(Yii::$app->request->post())) {
        
              if(empty($model->has_lef) == true){
                  $model->has_lef = 'n';
              }
              $model->display_label = $model->menu_name;
              $model->entry_right_name = $model->menu_name;
              $controllerName = substr($model->controller, 0, strlen($model->controller) - 10);
              $model->entry_url = Inflector::camel2id(StringHelper::basename($controllerName)) . '/' .$model->action;
              $model->create_user = Yii::$app->user->identity->uname;
              $model->create_date = date('Y-m-d H:i:s');
              $model->update_user = Yii::$app->user->identity->uname;
              $model->update_date = date('Y-m-d H:i:s');
              if($model->validate() == true && $model->save()){
                $msg = array('errno'=>0, 'msg'=>'保存成功');
                // 添加基本权限
                $this->saveDefaultRight($model->id, $model->controller);
                return $this->asJson($msg);
            }
            else{
                $msg = array('errno'=>2, 'data'=>$model->getErrors());
                return $this->asJson($msg);
            }
        } else {
            $msg = array('errno'=>2, 'msg'=>'数据出错');
            return $this->asJson($msg);
        }
    }

    /**
     * Updates an existing AdminMenu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
              $model->display_label = $model->menu_name;
              $model->entry_right_name = $model->menu_name;
              $controllerName = substr($model->controller, 0, strlen($model->controller) - 10);
              $model->entry_url = Inflector::camel2id(StringHelper::basename($controllerName)) . '/' .$model->action;
              $model->has_lef = 'n';
              $model->update_user = Yii::$app->user->identity->uname;
              $model->update_date = date('Y-m-d H:i:s');        
        
            if($model->validate() == true && $model->save()){
                $msg = array('errno'=>0, 'msg'=>'保存成功');
                return $this->asJson($msg);
            }
            else{
                $msg = array('errno'=>2, 'data'=>$model->getErrors());
                return $this->asJson($msg);
            }
        } else {
            $msg = array('errno'=>2, 'msg'=>'数据出错');
            return $this->asJson($msg);
        }
    
    }

    /**
     * Deletes an existing AdminMenu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = AdminMenu::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    
  
    }

    /**
     * Finds the AdminMenu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminMenu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminMenu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function saveDefaultRight($menu_id, $controller){
        //Yii::getLogger()->log("1===========$menu_id, $controller", Logger::LEVEL_ERROR, 'application');
        $controllerName = substr($controller, 0, strlen($controller) - 10);
        $controllerUrl = Inflector::camel2id(StringHelper::basename($controllerName));
        $actionArray = array();
        $methods = get_class_methods($controller);
        foreach($methods as $m){
            if($m != 'actions' && StringHelper::startsWith($m, 'action') !== false){
                $actionName = substr($m, 6, strlen($m));
                $actionName = Inflector::camel2id($actionName);
                $actionArray[$actionName] = $actionName;
            }
        }
        $rightName = array('添加'=>['create'],'查看'=>['index', 'view'],'修改'=>['update'],'删除'=>['delete']);
        //$actionName = array('index','view','update','create','delete');
        $order = 1;
        //Yii::getLogger()->log("1===========".json_encode($actionArray), Logger::LEVEL_ERROR, 'application');
        foreach($rightName as $right=>$actions){
            $adminRight = new AdminRight();
            $adminRight->menu_id = $menu_id;
            $adminRight->right_name = $right;
            $adminRight->display_label = $right;
            $adminRight->des = $right;
            $adminRight->display_order = $order++;
            $adminRight->has_lef = 'y';
            $adminRight->create_user = Yii::$app->user->identity->uname;
            $adminRight->create_date = date('Y-m-d H:i:s');
            $adminRight->update_user = Yii::$app->user->identity->uname;
            $adminRight->update_date = date('Y-m-d H:i:s');
            if($adminRight->save() == true){
                //Yii::getLogger()->log("2===========".json_encode($actions), Logger::LEVEL_ERROR, 'application');
                foreach ($actions as $action){
                    if(empty($actionArray[$action]) == false){

                        $adminRightUrl = new AdminRightUrl();
                        $adminRightUrl->right_id = $adminRight->id;
                        $adminRightUrl->url = "$controllerUrl/$action";
                        $adminRightUrl->para_name = $controllerUrl;
                        $adminRightUrl->para_value = $action;
                        $adminRightUrl->create_user = Yii::$app->user->identity->uname;
                        $adminRightUrl->create_date = date('Y-m-d H:i:s');
                        $adminRightUrl->update_user = Yii::$app->user->identity->uname;
                        $adminRightUrl->update_date = date('Y-m-d H:i:s');
                        $b = $adminRightUrl->save();
                        Yii::getLogger()->log("3===========".json_encode($actions) . " b=" . $b, Logger::LEVEL_ERROR, 'application');
                    }
                }
            }
        }


    }
}
