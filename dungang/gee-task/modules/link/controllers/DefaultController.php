<?php
namespace modules\link\controllers;

use app\controllers\AppController;
use app\helpers\MiscHelper;


/**
 * DefaultController implements the CRUD actions for Link model.
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
                ],
                'modelClass' => [
                    'class' => 'modules\link\models\LinkSearch',
                ]
            ],
            'create' => [
                'class' => 'app\core\CreateModelAction',
                'assignParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\link\models\Link',
                ]
            ],
            'update' => [
                'class' => 'app\core\UpdateModelAction',
                'findParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\link\models\Link',
                ]
            ],
            'view' => [
                'class' => 'app\core\ViewModelAction',
                'findParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\link\models\Link',
                ]
            ],
            'delete' => [
                'class' => 'app\core\DeleteModelAction',
                'findParams'=>[
                    'project_id'=>$project_id,
                ],
                'modelClass' => [
                    'class' => 'modules\link\models\Link',
                ]
            ],
		];
	}
}
