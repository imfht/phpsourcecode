<?php

namespace ethan\serialized;

use yii\db\BaseActiveRecord;

/**
 * Class SerializeAttributeException
 * @package baibaratsky\yii\behaviors\model
 */
class SerializeAttributeException extends \Exception
{
    public function __construct(BaseActiveRecord $model, $attribute)
    {
        parent::__construct('Can’t serialize attribute "' . $attribute . '" of "' . $model::className() . '".');
    }
}