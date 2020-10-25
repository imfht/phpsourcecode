<?php
$di->getShared('eventsManager')->attach('formRenderElement:Textarea', function ($event, $element) {
    if (isset($element->options['wordsmith']) && $element->options['wordsmith'] == true) {
        global $di;
        if (!isset($element->options['configInit'])) {
            $element->options['configInit'] = '';
        }
        $di->getShared('assets')->addJs('ckeditor', 'http://cdn.itdashu.com/modules/ckeditor/ckeditor.js', 'footer')->addInlineJs('ckeditorInit' . ucfirst($element->name), 'CKEDITOR.replace( \'' . $element->name . '\'' . $element->options['configInit'] . ');', 'footer');
        $element->{'#templates'}['widget'] = 'formElement-ckeditor';
        $element->{'#templates'}['widgetLayout'] = 'formElement-ckeditor-' . $element->layout;
    }
    return $element;
});
