<?php
/**
 * Author: dungang
 * Date: 2017/4/12
 * Time: 14:27
 */

namespace dungang\luosimao;


use yii\bootstrap\InputWidget;
use yii\helpers\Html;

class CaptchaWidget extends InputWidget
{

    /**
     * app site key
     * @var string
     */
    public $siteKey;

    /**
     * @var integer 验证码的宽度
     */
    public $width = 400;

    /**
     * @var string 出来相应的回调函数
     */
    public $callback;

    public function run()
    {
        $jsCode = "
            (function(){
                var c = document.createElement('script');c.type = 'text/javascript';c.async = true;
                c.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'captcha.luosimao.com/static/dist/captcha.js?v=201610101436';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(c, s);
            })();
        ";

        $this->view->registerJs($jsCode);

        if (empty($this->siteKey)) {
            if (isset(\Yii::$app->params['luosimao']) &&
                isset(\Yii::$app->params['luosimao']['siteKey'])) {
                $this->siteKey = \Yii::$app->params['luosimao']['siteKey'];
            }
        }
        $options = [
            'data-site-key'=>$this->siteKey,
            'data-width'=>$this->width,
            'class'=>'l-captcha'
        ];
        if ($this->callback) {
            $options['data-callback'] = $this->callback;
        }
        if ($this->hasModel()) {
            $attr = $this->attribute;
            $this->model->$attr = 'captcha';
            $input = Html::activeHiddenInput($this->model,$this->attribute,$this->options);
        } else {
            $input = Html::hiddenInput($this->name,'captcha',$this->options);
        }
        $captcha = $input . Html::tag('div','',$options);

        return $captcha;
    }
}