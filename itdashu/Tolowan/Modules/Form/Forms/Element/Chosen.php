<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element\Select;
use Phalcon\Tag\Select as TagSelect;

class Chosen extends Select
{
    public function render($attributes = null)
    {
        global $di;
        $name = $this->getName();
        $options = $this->getUserOptions();
        if(isset($options['valueInit']) && $this->getValue()){
            $valueName = $options['valueInit']($this->getValue());
            $this->setOptions(array($this->getValue() => $valueName ? $valueName : $this->getValue()));
        }else{
            $this->setOptions(array('null' => '请选择一个选项'));
        }
        $source = isset($options['source']) ? $options['source'] : '/auto_source/username/';
        if (isset($options['scription'])) {
            $scription = $options['scription'];
        } else {
            $scription = <<<scription
		$("#{$name}").ajaxChosen({
                type: 'GET',
                url: '{$source}',
                dataType: 'json',
                jsonTermKey: "q"
            }, function(json) {
                var results = [];
                $.each(json.items, function(i, val) {
                    results.push({
                        id: val.id,
                        name: val.name
                    });
                });
                return results;
            });
scription;
        }
        $di->getShared('assets')
            ->addJs('chosen', '//cdn.bootcss.com/chosen/1.6.2/chosen.jquery.min.js', 'footer')
            ->addJs('ajax-chosen', 'http://cdn.itdashu.com/library/chosen-ajax-addition/ajax-chosen.js', 'footer')
            ->addCss('chosen', '//cdn.bootcss.com/chosen/1.6.2/chosen.min.css', 'footer')
            ->addInlineJs('chosen-init-' . $name, $scription, 'footer');
        if(is_null($attributes)){
            $attributes = array();
        }
        return parent::render($attributes);
    }
}
