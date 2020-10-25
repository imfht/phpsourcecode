<?php
/**
 * 所有模块动作父类
 * Class FWAction
 */

class FWAction extends CAction {

    protected $db = null;
    protected $module = null;

    public function __construct($controller,$id)
    {
        parent::__construct($controller, $id);

        $this->module = &$controller;
        $this->db = Yii::app()->db;
    }

    /**
     * 父类包装函数，使用统一主题调用
     * @param $view
     * @param null $data
     * @param bool $return
     * @return mixed|string
     */
    public function render($view,$data=null,$return=false)
    {
        $data = $this->enhanceViewData($data);
        return $this->controller->render($view, $data, $return);
    }

    /**
     * 父类View包装方法，不使用统一主题调用
     * @param $view
     * @param null $data
     * @param bool $return
     * @param bool $processOutput
     * @return mixed|string
     */
    public function renderPartial($view,$data=null,$return=false,$processOutput=false)
    {
        $data = $this->enhanceViewData($data);
        return $this->controller->renderPartial($view, $data, $return, $processOutput);
    }

    /**
     * 提供跟模块相关的模板变量
     * @param $data
     * @return array
     */
    protected function enhanceViewData($data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $url = rtrim(Yii::app()->baseUrl,"/");
        $data['this'] = $this;
        $data['MODULE_BASE_URL'] = $url . "/m/" . $this->controller->id;
        $data['MODULE_STATIC_BASE_URL'] = $url . "/modules/" . $this->controller->id;

        return $data;
    }

    public function end()
    {
        Yii::app()->end();
    }


}