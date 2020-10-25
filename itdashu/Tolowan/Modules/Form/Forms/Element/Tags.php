<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element\Text;

class Tags extends Text
{
    public function getValue()
    {
        $value = parent::getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }

    public function render($attributes = null)
    {
        global $di;
        $name = $this->getName();
        $options = $this->getUserOptions();
        $scription = "$('#$name').tagsinput({";
        if (isset($options['maxTags'])) {
            $scription .= 'maxTags: ' . $options['maxTags'] . ',';
        }
        if (isset($options['freeInput'])) {
            $scription .= 'freeInput: false,';
        }
        if (isset($options['trimValue'])) {
            $scription .= 'trimValue: true,';
        }
        if (isset($options['source'])) {
            $scription .= 'typeahead: {
                source: function(query) {
                    return $.get("' . $options['source'] . '");
                }
            }';
        }
        $scription .= '});';
        $di->getShared('assets')
            ->addJs('bootstrap-tags', '//cdn.bootcss.com/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js', 'footer')
            ->addCss('bootstrap-tags', '//cdn.bootcss.com/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css', 'footer')
            ->addInlineJs('bootstrap-datetimepicker-init-' . $name, $scription, 'footer');
        return parent::render($attributes);
    }
}
