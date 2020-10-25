<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/26
 * Time: 11:05
 */

namespace backend\controllers\Admin;


use backend\controllers\BaseController;
use backend\tools\Flush;
use backend\tools\ResponseUtils;
use Yii;
use yii\web\Response;

class PermissionController extends BaseController{

    private $permissionservice;

    public function init(){

        $this->permissionservice = Yii::createObject('permissionservice');
    }
    /**
     * 权限列表
     */
    public function actionPermissionlist(){

        $request = Yii::$app->request;

        if($request->isAjax){

            //查询条件
            $params['search']   = $request->post('searchPhrase','');
            $sort               = $request->post('sort');
            $params['sort']     = key($sort).' '.$sort[key($sort)];
            $params['pageIndex']= $request->post('current',1);
            $params['pageSize'] = $request->post('rowCount',10);
            $params['type']     = '2';
            $data               = $this->permissionservice->permissionList($params);
            $totalCount         = $this->permissionservice->permissionCount($params);
            $json_data = array(
                "current"        => intval( $params['pageIndex'] ),
                "rowCount"       => intval( $params['pageSize'] ),
                "total"          => intval( $totalCount ),
                "rows"           => $data
            );
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $json_data;
        }
        return $this->render('permissionlist');
    }
    /**
     * 添加权限
     */
    public function actionPermissionadd(){

        if(Yii::$app->request->isGet)
        {
            return $this->render('permissionadd');
        }elseif(Yii::$app->request->isPost){

            $model   =  $this->permissionservice->addPermission(Yii::$app->request->post());

            if($model->errors){

                return $this->render('permissionadd',['model'=>$model,'error'=>$model->errors]);

            }else{
                Flush::success('添加成功');
                return $this->render('permissionadd');
            }
        }

    }
    /**
     * 更新权限数据
     */
    public function actionPermissionupdate(){

        if(Yii::$app->request->isGet)
        {
            $name  = Yii::$app->request->get('name','');

            $model = $this->permissionservice->queryPermission(['name'=>$name,'type'=>2]);

            return $this->render('permissionupdate',['model'=>$model]);

        }elseif(Yii::$app->request->isPost){

            $model   = $this->permissionservice->updatePermission(Yii::$app->request->post());
            if($model->errors){

                return $this->render('permissionupdate',['model'=>$model,'error'=>$model->errors]);

            }else{
                Flush::success('更新成功');
                return $this->render('permissionupdate',['model'=>$model]);
            }
        }
    }
    /**
     * 删除权限数据
     */
    public function actionPermissiondelete(){

        $name = Yii::$app->request->post('name','');
        $ret = $this->permissionservice->deletePermission(['name'=>$name,'type'=>2]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ResponseUtils::response_data($ret,'删除');
    }
}