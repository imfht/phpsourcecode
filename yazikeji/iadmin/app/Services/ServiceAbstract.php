<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/21
 * Time: 下午2:38
 */

namespace Services;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;

abstract class ServiceAbstract
{
    protected $app;

    protected $model;

    abstract function model();

    public function _init()
    {

    }

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
        $this->_init();
    }

    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Model");
        }

        return $this->model = $model;
    }

}