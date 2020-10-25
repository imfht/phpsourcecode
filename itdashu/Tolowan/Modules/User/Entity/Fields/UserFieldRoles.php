<?php
namespace Modules\User\Entity\Fields;

use Modules\Entity\Entity\Fields\Field;

class UserFieldRoles extends Field
{
    public function beforeValidationOnCreate()
    {
        $this->created = time();
    }

    public function setValue($value)
    {
        if (isset($this->_options['stateDefaultValue'])) {
            $this->state = $this->_options['stateDefaultValue'];
        }
        $this->state = 1;
        $this->value = $value;
    }
}