<?php

namespace modules\backlog\controllers;

use app\controllers\AppController;
use app\helpers\MiscHelper;


/**
 * StoryController implements the CRUD actions for Story model.
 */
class DefaultController extends AppController
{
    public function actions(){
        $project_id = MiscHelper::getProjectId();
		return [
            'index' => [
                'class' => 'app\core\ListModelsAction',
                'modelClass' => [
                    'class' => 'modules\sprint\models\StorySearch',
                    'project_id'=>$project_id,
                    'sprint_id'=>0,
                ]
            ],
            'create' => [
                'class' => 'app\core\CreateModelAction',
                'modelClass' => [
                    'class' => 'modules\sprint\models\Story',
                    'project_id'=>$project_id,
                    'sprint_id'=>0,
                    'points'=>100,
                    'scenario'=>'backlog',
                ]
            ],
		    'update' => [
		        'class' => 'app\core\UpdateModelAction',
		        'modelClass' => [
		            'class' => 'modules\sprint\models\Story',
		            'project_id'=>$project_id,
		            'sprint_id'=>0,
		            'scenario'=>'backlog',
		        ]
		    ],
		    'trans' => [
		        'class' => 'app\core\UpdateModelAction',
		        'modelClass' => [
		            'class' => 'modules\sprint\models\Story',
		            'project_id'=>$project_id,
		        ]
		    ],
            'view' => [
                'class' => 'app\core\ViewModelAction',
                'modelClass' => [
                    'class' => 'modules\sprint\models\Story',
                    'project_id'=>$project_id,
                    'sprint_id'=>0,
                ],
                'viewName'=>'@modules/sprint/views/story/view'
            ],
            'delete' => [
                'class' => 'app\core\DeleteModelAction',
                'modelClass' => [
                    'class' => 'modules\sprint\models\Story',
                    'project_id'=>$project_id,
                    'sprint_id'=>0,
                ]
            ],
            'excel-import'=>[
                'class' => 'app\core\ExcelImportAction',
                'formName' => 'AuthPermission',
                'cellMap'=>[
                    'A' => 'story_type',
                    'B' => 'status',
                    'C' => 'description',
                    'D' => 'version',
                    'E' => 'important',
                    'F' => 'points',
                ],
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'download-template' => [
                'class' => 'app\core\ExcelTemplateAction',
                'title' => '产品Backlog导入模板',
                'cells' => [
                    'A1' => ['val'=>'类型','comment'=>'bug,requirement:需求,spike:探针,maintenance:运维'],
                    'B1' => ['val'=>'状态','comment'=>'1:待评估，2:已评估，3:作废'],
                    'C1' => ['val'=>'描述','comment'=>'必须填写，不要超过128个字符'],
                    'D1' => ['val'=>'版本','comment'=>'可选'],
                    'E1' => ['val'=>'优先级','comment'=>'请用较多的数字描述，比如100，200，300'],
                    'D1' => ['val'=>'故事点','comment'=>'暂时用工作日描述'],
                ]
            ]
		];
	}
}
