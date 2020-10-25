<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

use backend\models\Pform;
use backend\models\PformSearch;

use backend\models\PformField;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use  yii\web\UploadedFile;

/**
 * PformController implements the CRUD actions for Pform model.
 */
class PformController extends Controller
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
                    'delete' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Pform models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PformSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionStatistics()
    {
        $searchModel = new PformSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('statistics', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pform model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Pform model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pform();

        if ($model->load(Yii::$app->request->post()) ) 
        {
             //上传列表小图片， 单文件上传
            $model->file = UploadedFile::getInstance($model, 'file');
            if(!empty($model->file))
            {
                $targetFileId = date("YmdHis").'-'.uniqid();
                $ext = pathinfo($model->file->name, PATHINFO_EXTENSION);
                $targetFileName = "{$targetFileId}.{$ext}";
                $targetFile = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $targetFileName;

                $targetFileUrl = Yii::getAlias('@web') . "/" . "uploads" . "/" . $targetFileName;

                $model->file->saveAs($targetFile);

                $model->form_img_url = $targetFileUrl;
            }

            $model->user_id = Yii::$app->user->id;
            $model->uid = uniqid();
            $model->save(false);

            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Pform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {

            //上传列表小图片， 单文件上传
            $model->file = UploadedFile::getInstance($model, 'file');
            if(!empty($model->file))
            {
                $targetFileId = date("YmdHis").'-'.uniqid();
                $ext = pathinfo($model->file->name, PATHINFO_EXTENSION);
                $targetFileName = "{$targetFileId}.{$ext}";
                $targetFile = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $targetFileName;

                $targetFileUrl = Yii::getAlias('@web') . "/" . "uploads" . "/" . $targetFileName;

                $model->file->saveAs($targetFile);

                $model->form_img_url = $targetFileUrl;
            }

            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Pform model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    

    public function actionDelformfield($view_id, $formfield_id)
    {
        //$this->findModel($id)->delete();
        PformField::findOne(['id' => $formfield_id])->delete();

        return $this->redirect(['view', 'id' => $view_id]);
    }


    /**
     * Finds the Pform model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pform the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pform::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
