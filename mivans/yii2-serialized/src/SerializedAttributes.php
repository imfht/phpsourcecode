<?php

namespace ethan\serialized;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class SerializedAttributes
 * @package baibaratsky\yii\behaviors\model
 *
 * @property BaseActiveRecord $owner
 */
class SerializedAttributes extends Behavior
{
    /**
     * @var string[] Attributes you want to be serialized
     */
    public $attributes = [];

    /**
     * @var bool Encode serialized data to protect them from corruption (when your DB is not in UTF-8)
     * @see http://www.jackreichert.com/2014/02/02/handling-a-php-unserialize-offset-error/
     */
    public $encode = false;

    private $oldAttributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'serializeAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'serializeAttributes',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'deserializeAttributes',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'deserializeAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'deserializeAttributes',
        ];
    }

    public function serializeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if($this->owner->isNewRecord == false) {
                $this->owner->setOldAttribute($attribute, $this->oldAttributes[$attribute]);
            }

            if (is_array($this->owner->$attribute) && count($this->owner->$attribute) > 0) {
                $this->owner->$attribute = serialize($this->owner->$attribute);
                if ($this->encode) {
                    $this->owner->$attribute = base64_encode($this->owner->$attribute);
                }
            } elseif (empty($this->owner->$attribute)) {
                $this->owner->$attribute = null;
            } else {
                throw new SerializeAttributeException($this->owner, $attribute);
            }
        }
    }

    public function deserializeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);

            if (empty($this->owner->$attribute)) {
                $this->owner->setAttribute($attribute, []);
                $this->owner->setOldAttribute($attribute, []);
            } elseif (is_scalar($this->owner->$attribute)) {
                if ($this->encode) {
                    $this->owner->$attribute = base64_decode($this->owner->$attribute);
                }
                $value = @unserialize($this->owner->$attribute);
                if ($value !== false) {
                    $this->owner->setAttribute($attribute, $value);
                    $this->owner->setOldAttribute($attribute, $value);
                }
            }
        }
    }
}