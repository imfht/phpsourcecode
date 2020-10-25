<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-02 10:29:02
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-23 18:14:51
 */
 
namespace common\widgets\inputs;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Json;

class ButtonInput extends InputWidget
{
    public $clientOptions = [];
    public $chooseButtonClass = ['class' => 'btn-default'];
    private $_hashVar;
    private $_view;
    private $_encOptions;
    public $tags;
    public $click;
    public $placeholder;
    
    
    public function init ()
    {
        parent::init();
        $this->_view = $this->getView();
        $this->initOptions();
    }

    public function run ()
    {
        if ($this->hasModel()) {
            $model = $this->model;
            $attribute = $this->attribute;

            $html = $this->renderInput($model, $attribute);
            
            echo $html;
        }
    }

    /**
     * init options
     */
    public function initOptions ()
    {
        // to do.
        $id = md5($this->options['id']);
        $this->hashClientOptions("inputbutton_config_{$id}");
    }


    /**
     * generate hash var by plugin options
     */
    protected function hashClientOptions($name)
    {
        $this->_encOptions = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
        $this->_hashVar = $name . '_' . hash('crc32', $this->_encOptions);
    }


    /**
     * render html body-input
     */
    public function renderInput ($model, $attribute)
    {
        Html::addCssClass($this->chooseButtonClass, "btn {$this->_hashVar}");
        $tags = $this->tags;
        $eles = [];
        $eles[] = Html::activeTextInput($model, $attribute, [
            'class' => 'form-control',
            'v-model'=>$attribute,
            'placeholder' =>$this->placeholder
            ]);
        $eles[] = Html::tag('span', Html::button($tags?$tags:'按钮名称', $this->chooseButtonClass), ['class' => 'input-group-btn','@click'=>$this->click]);

        return Html::tag('div', implode("\n", $eles), ['class' => 'input-group']);
    }
}