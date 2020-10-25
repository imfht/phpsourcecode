<?php

/**
 * 用户中心控制器
 */
class UserController extends \Our\Controller_AbstractApi {

    /**
     * 表单测试
     */
    public function loginAction() {
        //实例化表单对象，并传入需要验证的参数数组
        //其中键表示字段名
        $form = new \Forms\User\LoginModel($this->getRequest()->getParams());
        //调用表单对象的校验方法，该方法会根据字段设置校验所有字段
        if (!$form->validate()) {
            //校验失败，可以通过getMessages获取有错误字段的错误信息
            var_dump($form->getMessages());
            exit();
        }
        //表单校验通过，通过getFieldValue获取所有字段的值
        $params = $form->getFieldValue();
        var_dump($params);

        $v = $this->getRequest()->getParam("v", 1);
        if ($v == 1) {
            $bn = \Business\User\LoginModel::getInstance();
        } else if ($v == 2) {
            $bn = \Business\User\LoginV2Model::getInstance();
        }
        $result = $bn->login($params);
        var_dump($result);
    }

    /**
     * 表单demo
     */
    public function demoAction() {
        $form = new \Forms\User\DemoModel($this->getRequest()->getParams());
        if (!$form->validate()) {
            echo "表单校验没有通过，相关字段的错误信息：";
            var_dump($form->getMessages());
            exit();
        }
        echo "表单校验通过，所有字段的值：";
        var_dump($form->getFieldValue());
    }

}
