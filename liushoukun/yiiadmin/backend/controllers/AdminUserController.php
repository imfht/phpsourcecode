<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/18 19:02
// +----------------------------------------------------------------------
// | TITLE: 用户管理
// +----------------------------------------------------------------------

namespace backend\controllers;

use Yii;
use backend\models\AdminUser;
use yii\data\Pagination;
use yii\web\Response;

/**
 * Class AdminUserController
 * @package backend\controllers
 */
class AdminUserController extends BaseController
{

    public function actionIndex()
    {
        $AdminUser = AdminUser::find();
        $pages = new Pagination(['totalCount'=>$AdminUser->count(),'pageSize'=>15]);
        $models = $AdminUser
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('index',
            [
                'model' => $models,
                'pages' => $pages
            ]);
    }

    /**
     * 创建用户
     * @return array|string
     */
    public function actionCreate()
    {
        $AdminUser = new AdminUser(['scenario' => AdminUser::SCENARIO_CREATE]);
        if ($AdminUser->load(Yii::$app->request->post()) && $AdminUser->validate()) {
            $AdminUserData = Yii::$app->request->post('AdminUser');
            $AdminUser->setPassword($AdminUserData['password']);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (  $AdminUser->save() ){
                return ['code' => 200, 'message' => '添加成功'];
            }else{
                return ['code' => 200, 'message' => '添加失败'];
            }

        } else {
            return $this->render('create', ['model' => $AdminUser]);
        }
    }


    /**
     * 更新角色
     * @return array|string
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = AdminUser::findOne($id);
        $model->scenarios( AdminUser::SCENARIO_UPDATE);
        $AdminUser= Yii::$app->request->post('AdminUser');
        if (!empty($AdminUser['password'])) {
            $model->setPassword($AdminUser['password']);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->status = isset( $AdminUser['status']) ? $AdminUser['status'] : 0;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (  $model->save() ){
                return ['code' => 200, 'message' => '修改成功'];
            }else{
                return ['code' => 400, 'message' => '修改失败'];
            }
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (AdminUser::deleteUser($id)) {
            return ['code' => 200, 'message' => '删除成功'];
        } else {
            return ['code' => 400, 'message' => '删除错误'];
        }
    }
}