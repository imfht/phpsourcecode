<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\AdminRight;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\StringHelper;
use yii\helpers\Inflector;
use backend\services\AdminRightUrlService;
/**
/**
 * AdminRightController implements the CRUD actions for AdminRight model.
 */
class AdminRightController extends BaseController
{
	public $layout = "lte_main";

    /**
     * Lists all AdminRight models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $query = AdminRight::find()->andWhere(['menu_id' => $id]);
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
        //$models = $query->orderBy('display_order')
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
        $controllers = $this->getAllController();
        $controllerData = array();
        foreach($controllers as $c){
            $controllerData[$c['text']] = $c;
        }

        return $this->render('index', [
            'models'=>$models,
            'pages'=>$pagination,
            'controllerData'=>$controllerData,
            'query'=>$querys,
            'menu_id'=>$id
        ]);
    }

    /**
     * Displays a single AdminRight model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        //$id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $actions = $this->rightAction($model->id, $model->menu_id);
        $result = ['model'=>$model->getAttributes(), 'actions'=>$actions];
        return $this->asJson($result);
    }

    /**
     * Creates a new AdminRight model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminRight();
        if ($model->load(Yii::$app->request->post())) {
              $rightUrls = Yii::$app->request->post("rightUrls");
              $model->display_label = $model->right_name;
              if(empty($model->has_lef) == true){
                  $model->has_lef = 'n';
              }
              $model->create_user = Yii::$app->user->identity->uname;
              $model->create_date = date('Y-m-d H:i:s');
              $model->update_user = Yii::$app->user->identity->uname;
              $model->update_date = date('Y-m-d H:i:s');            
              if($model->validate() == true && $model->save()){
                  if(count($rightUrls) > 0){
                      $adminRightUrlService = new AdminRightUrlService();
                      $c = $adminRightUrlService->saveRightUrls($rightUrls, $model->id, Yii::$app->user->identity->uname);
                  }
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
     * Updates an existing AdminRight model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
              $rightUrls = Yii::$app->request->post("rightUrls");
              $model->has_lef = 'n';
              $model->update_user = Yii::$app->user->identity->uname;
              $model->update_date = date('Y-m-d H:i:s');        
        
            if($model->validate() == true && $model->save()){
                $adminRightUrlService = new AdminRightUrlService();
                $c = $adminRightUrlService->saveRightUrls($rightUrls, $id, Yii::$app->user->identity->uname);
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
     * Deletes an existing AdminRight model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = AdminRight::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    
  
    }

    /**
     * Finds the AdminRight model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminRight the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminRight::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    

    public function actionRightAction($rightId, $menu_id){
        $data = $this->rightAction($rightId, $menu_id);
        return $this->asJson($data);
    
    }
    
    private function rightAction($rightId, $menu_id){
        $systemRightUrls = AdminRightUrlService::findAll(['right_id'=>$rightId]);
        $rightUrls = [];
        $controller = '';
        foreach($systemRightUrls as $ru){
            $url = $ru->para_name . '/' . $ru->para_value;
            $rightUrls[$url] = true;
            $controller = 'backend\controllers\\'.Inflector::id2camel($ru->para_name, '-'). 'Controller';
        }
        $controllerDatas = [$controller];
        $rightActionData = array();
        foreach($controllerDatas as $c){
            if(StringHelper::startsWith($c, 'backend\controllers') == true && $c != 'backend\controllers\BaseController'){
                $controllerName = substr($c, 0, strlen($c) - 10);
                $cUrl = Inflector::camel2id(StringHelper::basename($controllerName));
                $methods = get_class_methods($c);
                $rightTree = ['text'=>$c, 'selectable'=>false, 'state'=>['checked'=>false], 'type'=>'r'];
                foreach($methods as $m){
                    if($m != 'actions' && StringHelper::startsWith($m, 'action') !== false){
                        $actionName = substr($m, 6, strlen($m));
                        $aUrl = Inflector::camel2id($actionName);
                        $actionTree = ['text'=>$aUrl . "&nbsp;&nbsp;($cUrl/$aUrl)", 'c'=>$cUrl, 'a'=>$aUrl, 'selectable'=>true, 'state'=>['checked'=>false], 'type'=>'a'];
                        if(isset($rightUrls[$cUrl.'/'.$aUrl]) == true){
                            $actionTree['state']['checked'] = true;
                            $rightTree['state']['checked'] = true;
                        }
                        $rightTree['nodes'][] = $actionTree;
                    }
                }
                $rightActionData[] = $rightTree;
            }
        }
        if(count($rightActionData) == 0){
            $rightActionData = array(['text'=>'请先选择控制器ID...', 'c'=>'', 'a'=>'', 'selectable'=>false, 'state'=>['checked'=>false], 'type'=>'a']);
        }
        //var_dump($rightActionData);
        return $rightActionData;
    }
}
