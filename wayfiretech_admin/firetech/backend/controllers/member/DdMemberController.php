<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-09-18 11:07:02
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-18 11:07:26
 */
 

namespace backend\controllers\member;

use Yii;
use common\models\DdMember;
use common\models\searchs\DdMemberSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\BaseController;
use common\models\DdAiGroups;

/**
 * DdMemberController implements the CRUD actions for DdMember model.
 */
class DdMemberController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        yii::$app->params['plugins']='member';
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCacVs()
    {
        echo '34534';
        die;
    }

    /**
     * Lists all DdMember models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DdMemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DdMember model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DdMember model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DdMember();
        $ai_groups = DdAiGroups::find()->asArray()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->member_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'ai_groups' => $ai_groups,
        ]);
    }

    /**
     * Updates an existing DdMember model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->member_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DdMember model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DdMember model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DdMember the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DdMember::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
