<?php

namespace modules\doc\behaviors;

use yii\base\Behavior;
use modules\doc\models\Doc;

class GidBehavior extends Behavior
{
    public function events()
    {
        return [
            Doc::EVENT_AFTER_INSERT => 'afterInsert',
            Doc::EVENT_BEFORE_UPDATE => 'beforeUpdate'
        ];
    }

    public function afterInsert($event) {
        $this->owner->gid = $this->owner->id;
        $this->owner->save(false);
    }

    public function beforeUpdate($event) {
        $this->owner->gid = $this->owner->id;
    }
}