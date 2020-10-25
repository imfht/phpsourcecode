<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/20 22:56
// +----------------------------------------------------------------------
// | TITLE: 权限控制器
// +----------------------------------------------------------------------

namespace backend\controllers;

use Yii;
use backend\models\AdminRule;
use yii\data\Pagination;
use yii\web\Response;

/**
 * Class AdminRuleController
 * @package backend\controllers
 */
class AdminRuleController extends BaseController
{
    /**
     * 列表
     * @return string
     */
    public function actionIndex()
    {
        $AdminRule = AdminRule::find();
        $pages = new Pagination(['totalCount' => $AdminRule->count(), 'pageSize' => '15']);
        $models = $AdminRule->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render(
            'index',
            [
                'model' => $models,
                'pages' => $pages
            ]);

    }

    /**
     * 创建
     * @return array|string
     */
    public function actionCreate()
    {
        $AdminRule = new AdminRule(['scenario' => AdminRule::SCENARIO_CREATE]);
        if ($AdminRule->load(Yii::$app->request->post()) && $AdminRule->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($AdminRule->save()) {
                return ['code' => 200, 'message' => '添加成功'];
            } else {
                return ['code' => 400, 'message' => '添加失败'];
            }
        } else {
            return $this->render('create', ['model' => $AdminRule]);
        }
    }

    /**
     * 更新
     * @return string
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = AdminRule::findOne($id);
        $model->scenarios(AdminRule::SCENARIO_UPDATE);
        $AdminRule = Yii::$app->request->post('AdminRule');
        if ($model->load(Yii::$app->request->post())) {
            $model->status = isset($AdminRule['status']) ? $AdminRule['status'] : 0;
            $model->is_show = isset($AdminRule['is_show']) ? $AdminRule['is_show'] : 0;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['code' => 200, 'message' => '修改成功'];
            } else {
                return ['code' => 400, 'message' => '修改失败'];
            }
        } else {
            return $this->render('update', ['model' => $model]);
        }

    }

}