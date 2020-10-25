<?php
namespace common\widgets;
use yii\helpers\Html;
use Yii;

/**
 * @author Tommy <447569003@qq.com>
 */
class headerup extends \yii\bootstrap\Widget
{
    /**
     * @var $data为传过来的幻灯片数组  img_url title url link
     */
    public $data;

    public function init()
    {
        parent::init();
        $data = $this->data;


    }
}
