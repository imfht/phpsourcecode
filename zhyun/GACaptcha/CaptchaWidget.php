<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/12/22
 * Time: 下午2:25
 */

namespace ga\captcha;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;

class CaptchaWidget extends InputWidget
{

    public $captchaAction = 'site/captcha';

    public $template = '{input}{image}';

    public $imageOptions;

    private $imageID = 'ga-captcha';

    public function run()
    {

        $this->reloadID();

        $this->registerJS();

        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::textInput($this->name, $this->value, $this->options);
        }
        $route = Url::toRoute($this->captchaAction);

        $image = Html::img($route, $this->imageOptions);
        echo strtr($this->template, [
            '{input}' => $input,
            '{image}' => "<a href='#'>$image</a>",
        ]);
    }

    protected function reloadID()
    {

        if (empty($this->imageOptions)) {
            $this->imageOptions['id'] = $this->imageID;
        } else {
            $this->imageID = $this->imageOptions['id'];
        }

    }

    protected function registerJS()
    {
        $js = <<<JS
        jQuery(window).ready(function(){
            var dom = jQuery('#$this->imageID');
            var id = 1;
            dom.click(function(){
                dom.attr('src', '/' + '$this->captchaAction' + '?id=' + id);
                id++;
            });
        });
JS;
        $this->getView()->registerJs($js);
        return $js;
    }
}