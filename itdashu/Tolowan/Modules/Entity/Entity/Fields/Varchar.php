<?php
namespace Modules\Entity\Entity\Fields;

use Modules\Queue\Library\Queue;

class Varchar extends Field
{
    public function setValue($value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        $this->value = $value;
    }

    public function afterSave()
    {
        if (isset($this->_options['fullTextSearch']) && $this->_options['fullTextSearch'] === true && isset($this->_options['entityId']) && isset($this->_options['fieldName'])) {
            $params = array(
                'entity' => $this->_options['entityId'],
                'field' => $this->_options['fieldName'],
                'id' => $this->eid
            );
            Queue::add('entityFieldScsw', $params);
        }
    }
}