<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\AdminUserRole;
use yii\web\NotFoundHttpException;
use backend\models\AdminUser;

/**
 * AdminUserRoleController implements the CRUD actions for AdminUserRole model.
 */
class AdminUserRoleController extends BaseController
{
	public $layout = "lte_main";
 
    /**
     * Lists all AdminUserRole models.
     * @return mixed
     */
    public function actionIndex($roleId)
    {
        $query = AdminUserRole::find()->with('user')->with('role')->andWhere(['role_id'=>$roleId]);
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
        $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
        return $this->render('index', [
            'models'=>$models,
            'pages'=>$pagination,
            'query'=>$querys,
            'role_id'=>$roleId
        ]);
    }

    /**
     * Displays a single AdminUserRole model.
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
     * Creates a new AdminUserRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminUserRole();
        
        if ($model->load(Yii::$app->request->post())) {
              $user_name = Yii::$app->request->post('user_name', '');
              $condition = array();
              if(empty($user_name) == false){
                  $condition['uname'] = $user_name;
              }
              
              if(empty($model->user_id) == false){
                  $condition['id'] = $model->user_id;
              }
              
              if(count($condition) > 0){
                  $user = AdminUser::findOne($condition);
                  if(empty($user) == false){
                      $model->user_id = $user->id;
                  }
                  else{
                      $msg = array('error'=>2, 'data'=>array('user_id'=>'用户不存在'));
                      return $this->asJson($msg);
                      exit();
                  }
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
     * Updates an existing AdminUserRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
        
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
     * Deletes an existing AdminUserRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = AdminUserRole::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    
  
    }

    /**
     * Finds the AdminUserRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminUserRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminUserRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
