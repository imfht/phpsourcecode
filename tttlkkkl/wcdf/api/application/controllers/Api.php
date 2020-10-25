<?php

    /**
     *
     * Date: 2016/9/24 0024
     * Time: 9:18
     * Author: 李华胜 lihuasheng@wapwei.com
     */
    class ApiController extends system\controllers\Api
    {
        public function init()
        {
            parent::init();
        }

        public function indexAction()
        {
            echo 1;
        }
        /**
         * 后台登录鉴权
         */
        public function authAction(){
            $fun = sprintf('%sAuth', $this->getRequest()->getMethod());
            $this->getResponse()->setBody($this->packing(0, 'success', call_user_func(
                [
                    'system\auth\Factory',
                    $fun
                ],
                self::getParams()
            ), null, null));
        }
        /**
         * 前端登oauth授权
         */
        public function oauthAction(){
            $fun = sprintf('%sOAuth', $this->getRequest()->getMethod());
            $this->getResponse()->setBody($this->packing(0, 'success', call_user_func(
                [
                    'system\auth\Factory',
                    $fun
                ],
                self::getParams()
            ), null, null));
        }
    }