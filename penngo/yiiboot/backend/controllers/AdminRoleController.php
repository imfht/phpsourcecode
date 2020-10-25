<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\AdminRole;
use yii\web\NotFoundHttpException;
use backend\services\AdminRoleRightService;
use backend\services\AdminRightService;
/**
 * AdminRoleController implements the CRUD actions for AdminRole model.
 */
class AdminRoleController extends BaseController
{
	public $layout = "lte_main";

    /**
     * Lists all AdminRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = AdminRole::find();
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
        ]);
    }

    /**
     * Displays a single AdminRole model.
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
     * Creates a new AdminRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminRole();
        if ($model->load(Yii::$app->request->post())) {
        
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
     * Updates an existing AdminRole model.
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
     * Deletes an existing AdminRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = AdminRole::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    
  
    }

    /**
     * Finds the AdminRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdminRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetAllRights($roleId){
    
        $roleRights = AdminRoleRightService::findAll(['role_id'=>$roleId]);
        $roleRightsData = [];
        foreach($roleRights as $r){
            $roleRightsData[$r->right_id] = $r->right_id;
        }
        $adminRightService = new AdminRightService();
        $rights = $adminRightService->getAllRight();
        $datas = array();
        foreach($rights as $r){
            $mid = $r['mid'];
            $m_name = $r['m_name'];
            $fid = $r['fid'];
            $f_name = $r['f_name'];
            $rid = $r['rid'];
            $r_name = $r['r_name'];
    
            $rightData = ['rid'=>$rid, 'text'=>$r_name, 'type'=>'r', 'selectable'=>false, 'state'=>['checked'=>false]];
            if(isset($roleRightsData[$rid]) == true){
                $rightData['state']['checked'] = true;
            }
            if(isset($datas[$mid]) == false){
                $moduleData = ['mid'=>$mid, 'text'=>$m_name, 'type'=>'m', 'selectable'=>false, 'state'=>['checked'=>true]];
                $datas[$mid] = $moduleData;
            }
    
            if(isset($datas[$mid]['funs'][$fid]) == false){
                $funData = ['fid'=>$fid, 'text'=>$f_name, 'type'=>'f', 'selectable'=>false, 'state'=>['checked'=>true]];
                $datas[$mid]['funs'][$fid] = $funData;
            }
            $datas[$mid]['funs'][$fid]['rights'][$rid] = $rightData;
        }
        foreach($datas as $k=>$modules){
            $funs = $modules['funs'];
            foreach($funs as $f=>$fun){
                $rights = $funs[$f]['rights'];
                unset($funs[$f]['rights']);
                $rights = array_values($rights);
                $funs[$f]['nodes'] = $rights;
                // 检查当前功能下所有权限是否选中,
                foreach($rights as $r=>$right){
                    if($right['state']['checked'] == false){
                        $funs[$f]['state']['checked'] = false;
                        break;
                    }
                }
                // 判断当前模块下所有功能是否全选中
                if($datas[$k]['state']['checked'] == true && $funs[$f]['state']['checked'] == false){
                    $datas[$k]['state']['checked'] = false;
                }
            }
            unset($datas[$k]['funs']);
            $funs = array_values($funs);
            $datas[$k]['nodes']=$funs;
    
        }
        $datas = array_values($datas);
    
        return $this->asJson($datas);
    
    }
    
    public function actionSaveRights(array $rids, $roleId){
         
        if(count($rids) > 0){
            $adminRoleRightService = new AdminRoleRightService();
            $count = $adminRoleRightService->saveRights($rids, $roleId, Yii::$app->user->identity->uname);
            if($count > 0){
                return $this->asJson(array('errno'=>0, 'data'=>$count, 'msg'=>'保存成功'));
                return;
            }
        }
        return $this->asJson(array('errno'=>2, 'data'=>'', 'msg'=>'保存失败'));
    }
}
