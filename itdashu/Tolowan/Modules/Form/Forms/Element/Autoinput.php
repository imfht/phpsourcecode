<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element\Text;

class Autoinput extends Text
{
    public function render($attributes = null)
    {
        global $di;
        $name = $this->getName();
        $options = $this->getUserOptions();
        $source = isset($options['source']) ? $options['source'] : '/auto_source/username/';
        if (isset($options['scription'])) {
            $scription = $options['scription'];
        } else {
            $scription = <<<scription
        
        \n$("#{$name}").autocomplete({
            source: function( request, response ) {
                $.get('{$source}'+request.term,function(data) {
                        response( $.map( data, function( item ) {
                            return {
                                label:item.label,
                                value: item.value
                            }
                        }));
                    });
            },
        });
scription;
        }
        $di->getShared('assets')
            ->addJs('autocomplete', '//cdn.bootcss.com/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js', 'footer')
            ->addCss('autocomplete', '//cdn.bootcss.com/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css', 'footer')
            ->addInlineJs('autocomplete-init-' . $name, $scription, 'footer');
        return parent::render($attributes);
    }
}
