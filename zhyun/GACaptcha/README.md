# GACaptcha(best,easy) #

## Installation ##

`composer require ga/captcha dev-master`

## Usage ##

1.Set up following code in `actions` method of `SiteController`.(Default)
You can also change the "url" route what you want, but you should change the `captchaAction` property for CaptchaWidget in the next step.
```PHP
Class SiteController extend yii\web\Controller
public function actions()
    {
        return [
            'captcha'=>[
                'class' => 'ga\captcha\CaptchaAction',
            ]
        ];
    }
```
2.Create `Widget` in the view file, and it's support the active mode.
```PHP
<?php
use app\models\CaptchaWidget;
use yii\helpers\Html;
?>
<?= Html::beginForm('site/test', 'post')?>
<?= CaptchaWidget::widget([
    'name' => 'captcha',
    'template' => '<label for="captcha">Captcha</label>&emsp;&emsp;{input}{image}',
    'options' => ['id' => 'captcha'],
]);?>
<?= Html::submitButton('Submit')?>
<?= Html::endForm()?>

```
**NOTE:** If you change the url route, and you need to set the `captchaAction` property.
```PHP
<?= CaptchaWidget::widget([
    ...,
    'captchaAction'=> 'controller/action'
]);?>
```
3.Config your method need to validate the verification code.
```PHP
public function actionTest(){

    $res = Yii::$app->request->post('captcha');
    $ca = new CaptchaValidator();
    
    if($ca->validate($res)){
        echo "success";
    }else{
        echo "fail";
    }
}
```
## Others ##

Thanks for the author of `HansKendrickV-Regular.ttf` to provide the nice free font. 


