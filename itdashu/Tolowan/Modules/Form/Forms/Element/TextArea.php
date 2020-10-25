<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element;
use Phalcon\Forms\ElementInterface;
use Phalcon\Tag;

class TextArea extends Element implements ElementInterface
{
    public function render($attributes = null)
    {
        $attributes = $this->prepareAttributes($attributes);
        $attributes['value'] = htmlspecialchars($this->getValue());
        return Tag::textArea($attributes);
    }
}
