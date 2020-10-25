<?php declare(strict_types = 1);
namespace msqphp\main\view\component;
use msqphp\base;

final class Data
{
    // 二维数组，一维存放模版变量键，二维存放对应值，缓存，类型
    private $data     = [];
    private $pointer = [];
    /**
     * @param  string $key 键
     * @param  string|array  $tpl_var  变量名称或对应值
     * @param  miexd   $value 变量值
     * @param  boolen  $cache 是否缓存
     * @param  boolen  $html  是否仅仅为html文本
     * @throws ViewComponentException
     */

    // 抛出异常
    private function exception(string $message) : void
    {
        throw new ViewComponentException($message);
    }
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    public function cache(bool $cache) : self
    {
        $this->pointer['cache'] = $cache;
        return $this;
    }
    public function html(bool $html) : self
    {
        $this->pointer['html'] = $html;
        return $this;
    }
    private function getKey() : string
    {
        isset($this->pointer['key']) || $this->exception('视图数据操作键未设置');
        return $this->pointer['key'];
    }
    private function getValue()
    {
        isset($this->pointer['value']) || $this->exception('视图数据操作键对应值未设置');
        return $this->pointer['value'];
    }
    // 模版变量是否存在
    public function exists() : bool
    {
        return isset($this->data[$this->getKey()]);
    }
    // 取得模版变量的值
    public function get()
    {
        return $this->data[$this->getKey()];
    }
    public function getAll() : array
    {
        return $this->data;
    }
    // 模版变量赋值
    public function assign() : void
    {
        $value = $this->getValue();
        // 转义
        ($this->pointer['html'] ?? false) && $value = base\filter\Filter::html($value);

        // 赋值
        $this->data[$this->getKey()] = ['value'=>$value,'cache'=>($this->pointer['cache'] ?? false)];
    }

    // 模版变量赋值
    public function set() : void
    {
        $this->assign();
    }
    // 删除模版变量
    public function delete() : void
    {
        unset($this->data[$this->getKey()]);
    }
    public function deleteAll() : void
    {
        $this->data = [];
    }
    public function getKeyValueData() : array
    {
        $result = [];
        // 遍历赋值
        foreach ($this->data as $key => ['value'=>$value]) {
            $result[$key] = $value;
        }
        return $result;
    }
}