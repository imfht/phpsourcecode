<?php
namespace Modules\Form;

use Modules\Form\Form;

class Manager
{
    public function create($formEntity = false, $data = array(), $options = array())
    {
        return new Form($formEntity, $data, $options);
    }
}