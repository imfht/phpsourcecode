<?php
/**
 * 权限控制器
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 11:02
 */

namespace backend\controllers\Admin;



use backend\controllers\BaseController;
use backend\tools\Flush;
use backend\tools\ResponseUtils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class RoleController extends BaseController{

    private $roleservice;
    private $permissionservice;
    public function init(){

        $this->roleservice       = Yii::createObject('roleservice');
        $this->permissionservice = Yii::createObject('permissionservice');
    }
    /**
     * 角色列表
     */
    public function actionRolelist(){

        $request = Yii::$app->request;

        if($request->isAjax){

            //查询条件
            $params['search']   = $request->post('searchPhrase','');
            $sort               = $request->post('sort');
            $params['sort']     = key($sort).' '.$sort[key($sort)];
            $params['pageIndex']= $request->post('current',1);
            $params['pageSize'] = $request->post('rowCount',10);
            $params['type']     = '1';
            $data               = $this->roleservice->roleList($params);
            $totalCount         = $this->roleservice->roleCount($params);
            $json_data = array(
                "current"        => intval( $params['pageIndex'] ),
                "rowCount"       => intval( $params['pageSize'] ),
                "total"          => intval( $totalCount ),
                "rows"           => $data
            );
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $json_data;
        }
        return $this->render('rolelist');
    }
    /**
     * 添加角色
     */
    public function actionRoleadd(){

        if(Yii::$app->request->isGet)
        {
            return $this->render('roleadd');
        }elseif(Yii::$app->request->isPost){

            $model   = $this->roleservice->addRole(Yii::$app->request->post());

            if($model->errors){

                Flush::danger('添加失败');
                return $this->render('roleadd',['model'=>$model,'error'=>$model->errors]);

            }else{

                Flush::success('添加成功');
                return $this->render('roleadd');
            }
        }
    }
    /**
     * 更新角色数据页面
     */
    public function actionRoleupdate(){

        if(Yii::$app->request->isGet)
        {

            $roleName = Yii::$app->request->get('name','');
            $role     = $this->roleservice->queryRoleByWhere(['name'=>$roleName,'type'=>1]);
            return $this->render('roleupdate',['model'=>$role]);

        }elseif(Yii::$app->request->isPost){

            $ret = $this->roleservice->roleUpdate(Yii::$app->request->post());

            if($ret){

                Flush::success('角色更新成功');
            }else{

                Flush::danger('操作失败');
            }
            return $this->redirect('/role/roleupdate/'.Yii::$app->request->post('name'));

        }


    }
    /**
     * 更新角色权限
     */
    public function actionRolepermission(){

        if(Yii::$app->request->isGet)
        {
            $roleName = Yii::$app->request->get('name','');
            //获取当前角色的权限
            $roleList = array_keys(ArrayHelper::toArray(Yii::$app->authManager->getPermissionsByRole($roleName)));
            //查询权限列表
            $list = $this->permissionservice->permissionGroupByTypeName();
            return $this->render('role_permission',['permissionlist'=>$list,'name'=>$roleName,'roleList'=>$roleList]);

        }elseif(Yii::$app->request->isPost){

            $ret     = $this->roleservice->assignRole(Yii::$app->request->post());

            if($ret){

                Flush::success('权限分配成功');
            }else{

                Flush::danger('操作失败');
            }
            return $this->redirect('/role/rolepermission/'.Yii::$app->request->post('name'));
        }

    }
    /**
     * 删除角色
     */
    public function actionRoledelete(){

           $name = Yii::$app->request->post('name','');
           $ret  = $this->roleservice->deleteRole(['name'=>$name,'type'=>1]);
           Yii::$app->response->format = Response::FORMAT_JSON;
           return ResponseUtils::response_data($ret,'删除');
    }

}