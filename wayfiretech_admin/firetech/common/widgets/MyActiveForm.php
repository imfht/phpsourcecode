<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-02 09:21:54
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-04-14 10:24:59
 */

namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

class MyActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */

    /**
     * @var array HTML attributes for the form tag. Default is `[]`.
     */
    public $options = [];

    /**
     * @var string the form layout. Either 'default', 'horizontal' or 'inline'.
     * By choosing a layout, an appropriate default field configuration is applied. This will
     * render the form fields with slightly different markup for each layout. You can
     * override these defaults through [[fieldConfig]].
     * @see \yii\bootstrap\ActiveField for details on Bootstrap 3 field configuration
     */
    public $layout = 'default';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!in_array($this->layout, ['default', 'horizontal', 'inline'])) {
            throw new InvalidConfigException('Invalid layout type: ' . $this->layout);
        }

        if ($this->layout !== 'default') {
            Html::addCssClass($this->options, 'form-' . $this->layout);
        }

        $this->fieldConfig = [
            'template' => "<div class='row row-box'><div class='col-xs-2 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='help-block'>{error}</div></div></div>",
        ];
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        $hidden = '';
        if (isset($options['options']['hidden'])) {
            $hidden = ' hide';
        }
        if (isset($options['options']['whole_row'])) {
            $options = [
                'options' => ['class' => 'form-group' . $hidden],
                'template' => "<div class='row row-box'><label for=\"{label}\" class=\"col-sm-2 control-label\">{label}</label><div class='col-xs-10 col-sm-10'>{input}</div><div class='help-block'>{error}</div></div>",
            ];
        } else {
            $options = [
                'options' => ['class' => 'form-group' . $hidden]
            ];
        }
        return parent::field($model, $attribute, $options);
    }
}
