<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/30 23:02
// +----------------------------------------------------------------------
// | TITLE:个人中心
// +----------------------------------------------------------------------

namespace backend\controllers;

use Yii;
use yii\web\Response;
use backend\models\AdminUser;

class PersonalController extends BaseController
{
    /**
     * 个人资料
     * @return array|string
     */
    public function actionIndex()
    {
        $model = AdminUser::findOne(Yii::$app->user->id);
        $model->scenarios(AdminUser::SCENARIO_USER_UPDATE);
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            if (!empty($post['AdminUser']['password'])) {
                $model->setPassword($post['AdminUser']['password']);
            }
            $model->validate();
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['code' => 200, 'message' => '修改成功'];
            } else {
                return ['code' => 400, 'message' => '修改失败'];
            }
        } else {
            return $this->render('index', ['model' => $model]);
        }

    }

}