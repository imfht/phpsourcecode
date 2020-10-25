<?php

namespace frontend\actions;

/**
 * Description of PageAction
 */
class PageAction extends \yii\web\ViewAction
{
    public $slug;
    public $page;
    public $view = 'page';
    public $layout = 'main';

    public function run()
    {
        $this->controller->action = $this;
        $this->controller->layout = "//{$this->layout}";
        return $this->controller->render($this->view, ['page' => $this->page]);
    }
}