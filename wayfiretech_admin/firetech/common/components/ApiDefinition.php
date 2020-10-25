<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-09-19 12:08:13
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-19 12:09:37
 */

namespace common\components;

 /**
 * @SWG\Definition()
 * @package app\index\controller
 */
class ApiDefinition{

    /**
     * @SWG\Property(example=0)
     */
    protected $code;
    /**
     * @SWG\Property(example={})
     */
    protected $data;

    /**
     * @SWG\Property(example="string")
     */
    protected $msg;
    public function index($result){

    }

}