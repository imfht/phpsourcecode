<?php

namespace ethan\serialized;

use yii\db\BaseActiveRecord;

/**
 * Class DeserializeAttributeException
 * @package baibaratsky\yii\behaviors\model
 */
class DeserializeAttributeException extends \Exception
{
    public function __construct(BaseActiveRecord $model, $attribute)
    {
        parent::__construct('Can’t deserialize attribute "' . $attribute . '" of "' . $model::className() . '".');
    }
}