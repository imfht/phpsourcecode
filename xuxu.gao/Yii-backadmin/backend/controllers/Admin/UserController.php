<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/18
 * Time: 11:07
 */

namespace backend\controllers\Admin;


use backend\controllers\BaseController;
use backend\tools\Flush;
use backend\tools\ResponseUtils;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
class UserController extends BaseController{

    private $userService;
    private $roleService;
    /**
     * 初始化
     */
    public function init()
    {
        $this->userService = Yii::createObject('userservice');
        $this->roleService = Yii::createObject('roleservice');
    }
    /**
     * 用户列表
     * @return string
     */
    public function actionUserlist(){

        $request = Yii::$app->request;
        if($request->isAjax){
            //查询条件
            $params['search']   = $request->post('searchPhrase','');
            $sort               = $request->post('sort');
            $params['sort']     = key($sort).' '.$sort[key($sort)];
            $params['pageIndex']= $request->post('current',1);
            $params['pageSize'] = $request->post('rowCount',10);
            $data               = $this->userService->userList($params);
            $totalCount         = $this->userService->userCount($params);
            $json_data = array(
                "current"        => intval( $params['pageIndex'] ),
                "rowCount"       => intval( $params['pageSize'] ),
                "total"          => intval( $totalCount ),
                "rows"           => $data
            );
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $json_data;
        }
        return $this->render('userlist');
    }
    /**
     * 用户添加页面
     */
    public function actionUseradd(){
        //查询角色列表
        $roleList = $this->roleService->queryAllRoleByWhere(['type'=>1]);

        if(Yii::$app->request->isGet)
        {
            return $this->render('useradd',['roleList'=>$roleList]);

        }elseif(Yii::$app->request->isPost){

            $model = $this->userService->addUser(Yii::$app->request->post());
            if(is_object($model)){

                return $this->render('useradd',['model'=>$model,'error'=>$model->errors,'roleList'=>$roleList]);
            }else{
                Flush::success('添加成功');
                return $this->render('useradd',['roleList'=>$roleList]);
            }

        }

    }
    /**
     * 用户更新界面
     */
    public function actionUserupdate($id){

        if(Yii::$app->request->isGet)
        {
            $model = $this->userService->getUserById($id);
            //查询角色列表
            $roleList = $this->roleService->queryAllRoleByWhere(['type'=>1]);
            //查询当前用户所拥有的角色
            $roles = ArrayHelper::toArray(Yii::$app->authManager->getAssignments($id));
            $roles = array_keys($roles);
            return $this->render('userupdate',['model'=>$model,'roleList'=>$roleList,'roles'=>$roles]);
        }elseif(Yii::$app->request->isPost){

            $model = $this->userService->updateUser((Yii::$app->request->post()));

            if($model->errors){

                return $this->render('userupdate',['model'=>$model,'error'=>$model->errors]);

            }else{
                Flush::success('更新成功');
                //查询角色列表
                $roleList = $this->roleService->queryAllRoleByWhere(['type'=>1]);
                //查询当前用户所拥有的角色
                $roles = ArrayHelper::toArray(Yii::$app->authManager->getAssignments((Yii::$app->request->post('id'))));
                $roles = array_keys($roles);
                return $this->render('userupdate',['model'=>$model,'roleList'=>$roleList,'roles'=>$roles]);
            }
        }

    }
    /**
     * 用户删除
     */
    public function actionUserdelete(){


        $id  = Yii::$app->request->post('id',0);

        $ret = $this->userService->deleteUserById($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ResponseUtils::response_data($ret,'删除');
    }
    /**
     * 退出登录
     */
    public function actionLogout(){

        Yii::$app->user->logout(true);
        return $this->redirect('/auth/login');
    }
}