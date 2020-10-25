<?php
use Modules\Form\Forms\FormInit;

FormInit::init();
$di->setShared('form', '\Modules\Form\Manager');
function formRender($form)
{
    global $di;
    $formObject = $di->getShared('form')->create($form);
    return $formObject->renderForm();
}
