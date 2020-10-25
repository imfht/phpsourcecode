<?php
namespace App\BaseController;

/**
 * 接口基类控制器
 * @package App\Controller\Api
 */
class ApiBaseController extends BaseController
{
    public function __construct($swoole)
    {
        parent::__construct($swoole);
        $this->is_ajax = true;
    }
}