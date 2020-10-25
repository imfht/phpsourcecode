---
title: 容器和依赖注入
lang: zh-CN
---

# 容器

容器是一个巨大的工厂，管理着大部分类的创建。所有控制器默认都由容器来创建。

## 容器方法

不建议直接使用Container，建议使用自动的依赖注入

### get

获取实例

```php
use Yesf\DI\Container;

Container::getInstance()->get('FileCache');
```

### has

检查是否存在此名称的类

```php
use Yesf\DI\Container;

Container::getInstance()->has('FileCache');
```

### setMulti

见下方《设置依赖注入》

# 依赖注入

为了方便使用，Yesf支持依赖注入。所有使用容器创建的类，均会自动进行依赖注入。

## 设置依赖注入

依赖注入默认工作于类名上，但用户可以自定义创建行为。

用户可以为类创建别名：

```php
use Yesf\DI\Container;

Container::getInstance()->set('FileCache', \YesfApp\Library\Cache\FileCache::class);
```

用户可以替换掉自动创建的类，例如编写代码时依赖于接口，而运行时实例化实际类：

```php
use Yesf\DI\Container;

Container::getInstance()->set(CacheInterface::class, \YesfApp\Library\Cache\FileCache::class);
```

可以使用闭包创建：

```php
use Yesf\DI\Container;

Container::getInstance()->set(CacheInterface::class, function() use ($argument) {
	return new \YesfApp\Library\Cache\FileCache($argument);
});
```

除非使用闭包，容器创建的类默认都是单例存在。但可以自行设置：

```php
use Yesf\DI\Container;

// 通过clone，创建多个实例，推荐
Container::getInstance()->setMulti(FileCache::class, Container::MULTI_CLONE);
// 每个实例都单独创建，消耗资源稍大一些
Container::getInstance()->setMulti(FileCache::class, Container::MULTI_NEW);
```

## 使用依赖注入

### 在构造函数上

在构造函数上指定类型，即可自动进行依赖注入：

```php
use Psr\SimpleCache\CacheInterface;

class Test {
	public function __construct(CacheInterface $cache) {
		echo $cache->get('key');
	}
}
```

为了遵循“依赖倒置原则”，我们可以通过注释，指定具体类：

```php
class Test {
	/**
	 * @Autowired obj YesfApp\Library\Impl\SomeClass
	 * @Autowired obj2 YesfApp\Library\Impl\OtherClass
	 */
	public function __construct(SomeInterface $obj, OtherInterface $obj2) {
	}
}
```

### 直接注入至属性

Yesf也支持直接注入至单个属性，注意，Yesf会忽略静态属性和默认不为null的属性：

```php
class Test {
	/** @Autowired YesfApp\Library\SomeClass */
	private $obj;

	/** @Autowired YesfApp\Library\SomeClass */
	public $obj2;
}
```

### 通过Setter和Getter

```php
class Test {
	private $obj;
	private $obj2;

	public function setObj(SomeInterface $obj) {
		$this->obj = $obj;
	}

	public function getObj() {
		return $this->obj;
	}

	/** @Autowired YesfApp\Library\SomeClass */
	public function setObj2(SomeInterface $obj) {
		$this->obj2 = $obj;
	}

	public function getObj2() {
		return $this->obj2;
	}
}
```
