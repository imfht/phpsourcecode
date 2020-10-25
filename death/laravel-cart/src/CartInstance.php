<?php
/**
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2018/3/14 14:16
 */

namespace Tanwencn\Cart;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Session\SessionManager;
use Tanwencn\Cart\Models\Cart;

class CartInstance
{
    private $session;

    private $auth;

    private $old;

    private $name;

    private $config;

    public function __construct($name, $config, SessionManager $sessionManager, AuthManager $auth)
    {
        $this->name = $name;
        $this->config = $config;
        $this->session = $sessionManager;
        $this->auth = $auth;
        $this->old = $this->all()->toArray();
    }

    protected function getKey()
    {
        return "cart." . $this->name;
    }

    public function update($itemKey, $qty)
    {
        if (!$itemKey) return false;
        $item = $this->get()->get($itemKey);
        if (!is_null($item)) {
            $item->qty = $qty > 0 ? $qty : 1;
        }
    }

    public function has(Model $model)
    {
        $item = $this->formatItem($model);

        return $this->all()->has($item->getItemKey());
    }

    public function formatItem($model, $qty = 1)
    {
        if ($model instanceof Cart) {
            return $model;
        } else {
            if (!$model->getKey()) abort(500, 'model call getKey() is not exist.');
            return new Cart([
                'cartable_type' => get_class($model),
                'cartable_id' => $model->getKey(),
                'qty' => $qty
            ]);
        }
    }

    public function put(Model $model, $qty = 1)
    {
        $item = $this->formatItem($model, $qty);
        $item->scope = $this->name;
        $item->qty = $item->qty > 0 ? $item->qty : 1;

        $items = $this->get();

        if ($items->has($item->getItemKey())) {
            $item->qty += $items->get($item->getItemKey())->qty;
        }

        $items->put($item->getItemKey(), $item);

        return $item;
    }

    protected function get()
    {
        $items = $this->session->get($this->getKey());
        if (is_null($items)) {
            $items = new Items();
            $this->session->put($this->getKey(), $items);
        }

        return $items;
    }

    public function all()
    {
        $items = $this->get();
        $results = new Items();
        foreach ($items as $key => $item) {
            $copy = $item->replicate();
            $copy->load('cartable');
            if (is_null($copy->cartable)) {
                $items->forget($key);
            } else {
                $results->put($key, $copy);
            }
        }
        return $results;
    }

    private function config($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    public function save($force = false)
    {
        if ($this->config('persistent') == false || !$this->auth->check()) return false;

        if ($force || $this->isChange()) {

            $user_id = $this->auth->id();

            if (is_null($user_id)) {
                abort(500);
            }

            Cart::where('user_id', $user_id)->where('scope', $this->name)->delete();

            foreach ($this->get() as $item) {
                $item->user_id = $user_id;
                Cart::create($item->only(['user_id', 'qty', 'scope', 'cartable_type', 'cartable_id']));
            }

            $this->setOld($this->all());
        }
    }

    protected function setOld($items)
    {
        $this->old = $items;
    }

    protected function isChange()
    {
        return json_encode($this->all()->toArray()) !== json_encode($this->old);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  string|array $keys
     */
    public function forget($keys)
    {
        $items = $this->get();
        foreach ($keys as $key) {

        }
        $items->forget($keys);
    }

    public function forgetByModel($models)
    {
        if ($models instanceof Model) {
            $arr[] = $models;
        } else {
            $arr = $models;
        }
        $keys = [];
        foreach ($arr as $model) {
            $keys[] = $this->formatItem($model)->getItemKey();
        }

        $this->forget($keys);
    }

    public function flush()
    {
        $this->session->forget($this->getKey());
    }

    public function __destruct()
    {
        $this->save();
    }

}