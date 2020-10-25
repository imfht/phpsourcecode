<?php
/**
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2018/3/14 14:16
 */

namespace Tanwencn\Cart;

class Cart
{
    private $instances;

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
        $this->instances = [];
    }

    public function scope($scope = null)
    {
        $scope = $scope ?: 'default';

        if (!isset($this->instances[$scope])) {
            $this->instances[$scope] = new CartInstance($scope, config("cart.{$scope}", []), $this->app['session'], $this->app['auth']);
        }

        return $this->instances[$scope];
    }

    public function sync()
    {
        if (!$this->app['auth']->check()) return false;

        $user_id = $this->app['auth']->id();

        $models = \Tanwencn\Cart\Models\Cart::where('user_id', $user_id)->get();

        foreach ($models as $model) {
            if(!$this->scope($model->scope)->has($model)){
                $this->scope($model->scope)->put($model);
            }
        }

        $scopes = array_keys($this->app['session']->get('cart', []));
        foreach ($scopes as $scope) {
            $this->scope($scope)->save(true);
        }
    }

    public function __call($method, $parameters)
    {
        return $this->scope()->$method(...$parameters);
    }

}