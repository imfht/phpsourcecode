<?php

namespace app\behaviors;

use yii\base\Behavior;
use app\models\AuthPermission;

class ManagePermissionBehavior extends Behavior
{
    public function events()
    {
        return [
            AuthPermission::EVENT_AFTER_INSERT => 'afterInsert',
            AuthPermission::EVENT_AFTER_UPDATE => 'afterUpdate',
            AuthPermission::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterInsert($event)
    {
        if ($this->owner->parent) {
            $this->addParent($this->owner->parent);
        }
    }

    public function afterUpdate($event)
    {
        if (isset($event->changedAttributes['parent'])) {
            if ($parentName = $event->changedAttributes['parent']) {
                $this->removeChildFromOldParent($parentName);
            }
            $this->addParent($this->owner->parent);
        }
    }

    public function afterDelete($event){
        $this->removeChildFromOldParent($this->owner->parent);
    }

    protected function addParent($parentName)
    {
        if ($parent = \Yii::$app->authManager->getPermission($parentName)) {
            $child = \Yii::$app->authManager->getPermission($this->owner->name);
            if (!\Yii::$app->authManager->hasChild($parent, $child)) {
                \Yii::$app->authManager->addChild($parent, $child);
            }
        }
    }

    protected function removeChildFromOldParent($parentName)
    {
        if ($parent = \Yii::$app->authManager->getPermission($parentName)) {
            $child = \Yii::$app->authManager->getPermission($this->owner->name);
            \Yii::$app->authManager->removeChild($parent, $child);
        }
    }
}
