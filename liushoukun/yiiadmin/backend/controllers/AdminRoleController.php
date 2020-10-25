<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:10
// +----------------------------------------------------------------------
// | TITLE: 角色
// +----------------------------------------------------------------------

namespace backend\controllers;

use backend\helps\Tree;
use backend\models\AdminRule;
use Yii;
use backend\models\AdminRole;
use yii\web\Response;

class AdminRoleController extends BaseController
{


    /**
     * 角色列表
     * @return string
     */
    public function actionIndex()
    {
        $AdminRole = AdminRole::find();
        $models = $AdminRole->all();
        return $this->render('index', ['model' => $models]);
    }

    /**
     * 创建角色
     * @return array|string
     */
    public function actionCreate()
    {
        $AdminRole = new AdminRole(['scenario' => 'create']);
        if ($AdminRole->load(Yii::$app->request->post()) && $AdminRole->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($AdminRole->save()) {
                return ['code' => 200, 'message' => '添加成功'];
            } else {
                return ['code' => 400, 'message' => '添加失败'];
            }

        } else {
            return $this->render('create', ['model' => $AdminRole]);
        }

    }

    /**
     * 更新角色
     * @return array|string
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = AdminRole::findOne($id);
        $model->scenarios('update');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 200, 'message' => '修改成功'];
        } else {
            return $this->render('update', ['model' => $model]);
        }


    }

    /**
     * 删除 角色
     * @return array
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (AdminRole::deleteRole($id)) {
            return ['code' => 200, 'message' => '成功'];
        } else {
            return ['code' => 400, 'message' => '错误'];
        }
    }

    /**
     * 设置权限
     * @return array|string
     */
    public function actionSetRule()
    {

        $roleId = Yii::$app->request->get('id');
        if (empty($roleId)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 400, 'message' => '参数错误'];
        }
        $model = AdminRole::findOne($roleId);
        $model->rule = explode(',', $model->rule);
        if (Yii::$app->request->post()) {
            $rule = Yii::$app->request->post('rule');
            $rule = array_filter($rule);
            krsort($rule);
            $rule = implode(',', $rule);
            $model->scenario = 'update';
            $model->rule = $rule;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$model->save()) {
                return ['code' => 400, 'message' => '修改失败'];
            } else {
                return ['code' => 200, 'message' => '修改成功'];
            }

        } else {
            $ruleAll = AdminRule::find()->where(['status' => 1])->asArray()->all();
            $ruleAll = array_map(function ($item) use ($model) {
                (in_array($item['id'], $model->rule)) ?
                    $item['state'] = ['checked' => true] : '';
                $item['text'] = $item['title'];
                return $item;
            }, $ruleAll);
            $ruleAll = Tree::makeTree($ruleAll, ['children_key' => 'nodes']);
            return $this->render('setRule', ['ruleAll' => $ruleAll, 'model' => $model]);
        }

    }


}