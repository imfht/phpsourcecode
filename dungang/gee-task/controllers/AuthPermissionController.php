<?php
namespace app\controllers;


/**
 * AuthPermissionController implements the CRUD actions for AuthPermission model.
 */
class AuthPermissionController extends AdminController
{

    public function actions()
    {
        return [
            'index' => [
                'class' => 'app\core\ListModelsAction',
                'modelClass' => [
                    'class' => 'app\models\AuthPermissionSearch'
                ]
            ],
            'create' => [
                'class' => 'app\core\CreateModelAction',
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'batch-create' => [
                'class' => 'app\core\CreateModelsAction',
                'formName' => 'AuthPermission',
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'update' => [
                'class' => 'app\core\UpdateModelAction',
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'delete' => [
                'class' => 'app\core\DeleteModelAction',
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'batch-delete' => [
                'class' => 'app\core\DeleteModelsAction',
                'modelBehaviors' => [
                    'manage-perm' => 'app\behaviors\ManagePermissionBehavior',
                ],
                'modelClass' => [
                    'class' => 'app\models\AuthPermission'
                ]
            ],
            'excel-import'=>[
                'class' => 'app\core\ExcelImportAction',
                'formName' => 'AuthPermission',
                'cellMap'=>[
                    'A' => 'name',
                    'B' => 'parent',
                    'C' => 'description',
                    'D' => 'rule_name'
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
                'title' => '权限导入模板',
                'cells' => [
                    'A1' => ['val'=>'编码','comment'=>'权限编码,比如：blog/delete的路由对应的权限为blog-delete'],
                    'B1' => ['val'=>'上级编码','comment'=>'权限的父级编码,比如：blog-*'],
                    'C1' => ['val'=>'描述'],
                    'D1' => ['val'=>'规则名称'],
                ]
            ]
        ];
    }
}
