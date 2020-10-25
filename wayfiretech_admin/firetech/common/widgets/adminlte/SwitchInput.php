<?php

namespace common\widgets\adminlte;

use Yii;
use yii\bootstrap\InputWidget;
use yii\helpers\Html;

 /**
  * @Author: Wang chunsheng  email:2192138785@qq.com
  * @Date:   2020-09-01 08:49:08
  * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
  * @Last Modified time: 2020-09-01 08:49:20
  */
 class SwitchInput extends InputWidget
 {
     public $css = [
        '/dist/css/bootstrap-switch.min.css',
    ];

     public $js = [
        '/dist/js/bootstrap-switch.min.js',
    ];

     public function run()
     {
         parent::run();
         $attribute = $this->attribute;
         $value = $this->model->$attribute;
         $inputname = Html::getInputName($this->model, $attribute);
         $inputid = Html::getInputId($this->model, $attribute);

         $content = "<div class='custom-control custom-switch' data-on= 'success' data-off= 'warning'>";
         if ($value) {
             $content .= "<input type='checkbox' id='{$inputid}'  class='custom-control-input' checked name='{$inputname}' value='1' checked/>";
         } else {
             $content .= "<input type='hidden'   class='custom-control-input' name='{$inputname}' value='0'/>";
             $content .= "<input type='checkbox' id='{$inputid}'  class='custom-control-input' name='{$inputname}' value='0'/>";
         }
         $content .= '</div>';
         echo $content;

         // 注册js
         $this->registerViewJs();
     }

     /**
      * @throws \yii\base\InvalidConfigException
      */
     public function registerViewJs()
     {
         $view = $this->view;
         $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common/widgets/adminlte/asset');

         $js = [];
         $css = [];
         $js = $this->js;
         $css = $this->css;
         foreach ($js as $key => $value) {
             $view->registerJsFile($directoryAsset.$value);
         }

         $attribute = $this->attribute;

         $inputid = Html::getInputId($this->model, $attribute);

         $js = <<< JS
            $("#{$inputid}").bootstrapSwitch({
                onText:"开启",  
                offText:"关闭",  
                size:'small',
                onSwitchChange: function (event, state) {
                    var ProductId = event.target.defaultValue;
                    if (state == true) {
                        $(this).val(1)
                         console.log(1)
                    } else {
                        $(this).val(0)

                        console.log(0)
                    }

                }
            });
JS;

         $view->registerJs($js);

         foreach ($css as $key => $value) {
             $view->registerCssFile($directoryAsset.$value);
         }
     }
 }
