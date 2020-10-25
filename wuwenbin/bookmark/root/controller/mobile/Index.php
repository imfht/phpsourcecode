<?php
/**
 * 移动版首页
 */
class ControllerIndex extends ControllerBaseMobile
{
    /**
     * 默认首页
     */
    public function actionIndex()
    {
        if ($this->mid) {
            redirect(Router::buildUrl("User_Index", array("uid" => $this->mid)));
        }

        Template::display("Index_Index", array(
            "title" => "网址书签 - 首页",
        ));
    }
}
