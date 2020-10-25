<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;

class FormGeneratorService extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', ['name','label', 'value' => null, 'attributes' => []]);
        Form::component('bsPassword', 'components.form.password', ['name','label', 'attributes' => []]);
        Form::component('bsEmail', 'components.form.email', ['name','label', 'value' => null, 'attributes' => []]);
        Form::component('bsTextArea', 'components.form.textarea', ['name','label', 'value' => null, 'attributes' => []]);
        Form::component('bsButton', 'components.form.button', ['type','class', 'label','url'=>'']);
        Form::component('bsCheckbox', 'components.form.checkbox', ['items','attributes' => []]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
