<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use common\models\TestUser;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TestUserController implements the CRUD actions for TestUser model.
 */
class TestUserController extends BaseController
{
	public $layout = "lte_main";

    /**
     * Lists all TestUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = TestUser::find();
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
        
        $orderby = Yii::$app->request->get('orderby', '');
        if(empty($orderby) == false){
            $query = $query->orderBy($orderby);
        }
        
        
        $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
        return $this->render('index', [
            'models'=>$models,
            'pages'=>$pagination,
            'query'=>$querys,
        ]);
    }

    /**
     * Displays a single TestUser model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $data = $model->getAttributes();
        
        
        return $this->asJson($data);

    }

    /**
     * Creates a new TestUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TestUser();
        if ($model->load(Yii::$app->request->post())) {
        
              if(empty($model->is_online) == true){
                  $model->is_online = 'n';
              }
              if(empty($model->status) == true){
                  $model->status = 10;
              }
              $model->create_user = Yii::$app->user->identity->uname;
              $model->create_date = date('Y-m-d H:i:s');
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
     * Updates an existing TestUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
        
             if(empty($model->is_online) == true){
                 $model->is_online = 'n';
             }
             if(empty($model->status) == true){
                 $model->status = 10;
             }
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
     * Deletes an existing TestUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = TestUser::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    }

	
	 

    /**
     * Finds the TestUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TestUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TestUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
