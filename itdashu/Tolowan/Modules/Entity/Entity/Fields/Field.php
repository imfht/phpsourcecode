<?php
namespace Modules\Entity\Entity\Fields;

use Phalcon\Mvc\Model;

/**
 *
 */
class Field extends Model
{
    protected $_source = false;
    public static $source;
    protected $value;
    public $id;
    public $eid;
    protected $_options;

    public function getValue()
    {
        return $this->value;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getMessages()
    {
        $messages = array();
        foreach (parent::getMessages() as $message) {
            switch ($message->getType()) {
                case 'InvalidCreateAttempt':
                    $messages[] = 'The record cannot be created because it already exists';
                    break;
                case 'InvalidUpdateAttempt':
                    $messages[] = 'The record cannot be updated because it doesn\'t exist';
                    break;
                case 'PresenceOf':
                    $messages[] = 'The field ' . $this->_options['fieldName'] . ' is mandatory';
                    break;
            }
        }

        return $messages;
    }

    public static function filterValue($value, $option)
    {
        if ($value) {
            if (isset($option['maxNum']) && $option['maxNum'] > 1) {
                $fieldOutput = array();
                if ($value) {
                    foreach ($value as $m) {
                        $fieldOutput[] = $m->value;
                    }
                    if (isset($option['valueType']) && $option['valueType'] == 'string') {
                        return implode(',', $fieldOutput);
                    }
                    return $fieldOutput;
                } else {
                    return $fieldOutput;
                }
            } else {
                return $value->value;
            }
        }
    }

    public function classNameInfo()
    {
        $className = get_class($this);
        $className = explode('\\', $className);
        $className = exEntityNameInfo($className);
        return $className;
    }

    public function getFieldName()
    {

    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
