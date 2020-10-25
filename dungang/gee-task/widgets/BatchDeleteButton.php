<?php
namespace app\widgets;

use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class BatchDeleteButton extends Widget
{
    public $route;
    public $title = '批量删除';
    public $clientOptions  = [
        'modal'=> ''
    ];
    public function run()
    {
        $id = $this->getId();
        $btn = Html::a($this->title, $this->route, ['class' => 'btn btn-danger','id'=>$id]);
        $options = Json::encode($this->clientOptions);
        $this->view->registerJs("$('#$id').batchProcess($options);");
        return $btn;
    }
}
