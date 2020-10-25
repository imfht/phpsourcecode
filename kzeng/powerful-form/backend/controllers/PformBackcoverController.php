<?php

namespace backend\controllers;

use Yii;
use backend\models\PformBackcover;
use backend\models\PformBackcoverSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * PformBackcoverController implements the CRUD actions for PformBackcover model.
 */
class PformBackcoverController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PformBackcover models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PformBackcoverSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PformBackcover model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    // 
    public function actionAdd()
    {
        $uid = $_GET['uid'];
        $pf_backcover = PformBackcover::findOne(['pform_uid' => $uid]);

        if(empty($pf_backcover)) //create
        {
            $model = new PformBackcover();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //return $this->redirect(['view', 'id' => $model->id]);
                return $this->redirect(['pform/index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'uid' => $uid,
                ]);
            }

        }
        else //update
        {
            //$model = $this->findModel($id);
            $model  = $pf_backcover;

            if ($model->load(Yii::$app->request->post())) {
                 $model->pform_uid = $uid;
                 $model->save(false);
                 //return $this->redirect(['view', 'id' => $model->id]);
                 return $this->redirect(['pform/index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }            
         
        }

    }


    /**
     * Creates a new PformBackcover model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PformBackcover();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PformBackcover model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PformBackcover model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PformBackcover model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PformBackcover the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PformBackcover::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
