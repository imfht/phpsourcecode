<?php

namespace modules\doc\controllers;

use app\controllers\AppController;
use app\helpers\MiscHelper;
use modules\doc\models\Doc;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for Doc model.
 */
class DefaultController extends AppController
{
    public function init(){
        parent::init();
        $this->userActions = [
            'index'
        ];
    }

	public function actions(){
        $project_id = MiscHelper::getProjectId();
		return [
            'index' => [
                'class' => 'app\core\ListModelsAction',
                'findParams'=>[
                    'project_id'=>$project_id,
                    'pid'=> 0
                ],
                'modelClass' => [
                    'class' => 'modules\doc\models\DocSearch'
                ]
            ],
            'create' => [
                'class' => 'app\core\CreateModelAction',
                'modelBehaviors'=>[
                    'gid-behavior' => 'modules\doc\behaviors\GidBehavior'
                ],
                'assignParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\doc\models\Doc'
                ]
            ],
            'create-page' => [
                'class' => 'app\core\CreateModelAction',
                'assignParams'=>[
                    'project_id'=>$project_id,
                    'gid' => \Yii::$app->request->get('gid')
                ],
                'modelClass' => [
                    'class' => 'modules\doc\models\Doc'
                ]
            ],
            'update' => [
                'class' => 'app\core\UpdateModelAction',
                'modelBehaviors'=>[
                    'gid-behavior' => 'modules\doc\behaviors\GidBehavior'
                ],
                'findParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\doc\models\Doc'
                ]
            ],
        
            'delete' => [
                'class' => 'app\core\DeleteModelAction',
                'findParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\doc\models\Doc'
                ]
            ],
		];
    }
    
    public function actionView($gid) {
        if($gmodel = Doc::findOne(['id'=>$gid])) {
            $id = \Yii::$app->request->get('id');
            if($id == null || $id == $gid) {
                $model = $gmodel;
            } else {
                $model = Doc::findOne(['id'=>$id]);
            }
            return $this->render('view',['gmodel'=>$gmodel,'model'=>$model]);
        }
        throw new NotFoundHttpException('不存在');
    }
}
