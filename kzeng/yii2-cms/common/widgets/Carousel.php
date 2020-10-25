<?php
namespace common\widgets;
use yii\helpers\Html;

/**
 * @author Tommy <447569003@qq.com>
 */
class Carousel extends \yii\bootstrap\Widget
{
    /**
     * @var $data为传过来的幻灯片数组  img_url title url link
     */
    public $data;

    public function init()
    {
        parent::init();
        $data = $this->data;
        echo Html::beginTag('div',['id'=>'carousel-example-generic','class' => 'carousel slide','date-ride' => 'carousel']);

        // 中间的点
        echo Html::beginTag('ol',['class'=>'carousel-indicators']);
        foreach ($data as $key => $value) {
            echo Html::tag('li','',['data-target'=>'#carousel-example-generic','class'=>(($key == 0) ? 'active' : ''),'data-slide-to'=>$key]);
        }
        echo Html::endTag('ol');
        
        echo Html::beginTag('div',['role' =>'listbox','class'     => 'carousel-inner',]);
        // 轮播图item
        foreach ($data as $key => $value) {
            echo Html::beginTag('div',['role' =>'listbox','class' => ['item',(($key == 0) ? 'active' : '')],]);
            echo Html::beginTag('a',['class'=>'','href'=>$value->link,'target'=>'_new']);
            echo Html::img($value->img_url, ['alt' => $value->title,'width'=>'100%']);
            echo Html::beginTag('div',['class' => 'carousel-caption']);
            echo $value->title;
            echo Html::endTag('div');
            echo Html::endTag('a');
            echo Html::endTag('div');
        }
         echo Html::endTag('div');

        //  切换按钮
          echo Html::beginTag('a',['class'=>'left carousel-control','href'=>'#carousel-example-generic','role'=>'button','data-slide'=>'prev']);
          echo Html::tag('span','',['class'=>'glyphicon glyphicon-chevron-left']);
          echo Html::tag('span',Html::encode('上一个'),['class'=>'sr-only']);
          echo Html::endTag('a');

          echo Html::beginTag('a',['class'=>'right carousel-control','href'=>'#carousel-example-generic','role'=>'button','data-slide'=>'next']);
          echo Html::tag('span','',['class'=>['glyphicon','glyphicon-chevron-right'],'aria-hidden'=>'true']);
          echo Html::tag('span',Html::encode('下一个'),['class'=>'sr-only']);
          echo Html::endTag('a');

       echo Html::endTag('div');



    }
}
