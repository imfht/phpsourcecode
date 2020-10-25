<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: TemplateRender.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\traits;


use tpvue\admin\App;

trait KeTemplateRender
{
    protected function ke_fetch(string $template_file)
    {
        return $this->view->fetch(App::$ROOT_PATH . 'view/' . $template_file . '.html');
    }

}