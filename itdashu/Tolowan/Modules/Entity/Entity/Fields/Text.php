<?php
namespace Modules\Entity\Entity\Fields;

use Library\Scsw\Scsw;
use Modules\Entity\Library\Toc;

class Text extends Field
{
    public $full_text;
    protected $toc;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        $this->value = $value;
        if (isset($this->_options['fullTextSearch']) && $this->_options['fullTextSearch'] === true && isset($this->_options['entityId']) && isset($this->_options['fieldName'])) {
            $this->full_text = Scsw::toString($this->value);
        }

        if (isset($this->_options['toc']) && $this->_options['toc'] === true) {
            $toc = Toc::toc($this->value);
            $this->value = $toc['body'];
            $this->toc = json_encode($toc['toc']);
        }
    }

    public function getToc()
    {
        return json_decode($this->toc);
    }
}