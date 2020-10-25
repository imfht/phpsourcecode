<?php
/**
 * 容器基本类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category DI
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\DI;

use ReflectionClass;
use Psr\Container\ContainerInterface;
use Yesf\Exception\NotFoundException;
use Yesf\Exception\InvalidClassException;
use Yesf\Exception\CyclicDependencyException;

class Container implements ContainerInterface {
	const MULTI_CLONE = 1;
	const MULTI_NEW = 2;

	/** @var array $instance Storage all created beans */
	private $instance = [];

	/** @var array $getter Alias names and Closure creaters */
	private $getter = [];

	/** @var array $multi Multi beans */
	private $multi = [];

	/** @var array $creating Creating beans, for cyclic dependency check */
	private $creating = [];

	private static $_instance = null;
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	private function __construct() {
		$this->set(ContainerInterface::class, self::class);
		$this->instance[Container::class] = $this;
	}
	/**
	 * Set alias or closure creaters
	 * 
	 * @access public
	 * @param string $id1 ID
	 * @param mixed $id2
	 */
	public function set($id1, $id2) {
		$this->getter[$id1] = $id2;
	}
	/**
	 * Set alias or closure creaters
	 * 
	 * @access public
	 * @param string $id ID
	 * @param int $type
	 */
	public function setMulti($id, $type = self::MULTI_CLONE) {
		$this->multi[$id] = $type;
	}
	/**
	 * Has
	 * 
	 * @access public
	 * @param string $id
	 * @return bool
	 */
	public function has($id) {
		$source = $id;
		while (is_string($source) && isset($this->getter[$source])) {
			$source = $this->getter[$source];
		}
		if (!is_string($source)) {
			return $source instanceof \Closure;
		} else {
			if (isset($this->instance[$source]) || class_exists($source)) {
				return true;
			}
			return false;
		}
	}
	/**
	 * Get
	 * 
	 * @access public
	 * @param string $id
	 * @return object
	 */
	public function get($id) {
		$source = $id;
		while (is_string($source) && isset($this->getter[$source])) {
			$source = $this->getter[$source];
		}
		if (!is_string($source)) {
			if ($source instanceof \Closure) {
				$source->bindTo($this);
				return $source();
			} elseif (is_object($source)) {
				return $source;
			} else {
				throw new NotFoundException("Class $id not found");
			}
		}
		if (isset($this->instance[$source])) {
			if (!isset($this->multi[$source])) {
				return $this->instance[$source];
			} elseif ($this->multi[$source] === self::MULTI_CLONE) {
				return clone $this->instance[$source];
			}
		}
		if (!class_exists($source)) {
			throw new NotFoundException("Class $id not found");
		}
		// Check cyclic dependency
		if (isset($this->creating[$source])) {
			throw new CyclicDependencyException("Found cyclic dependency of $id");
		}
		$this->creating[$source] = true;
		$ref = new ReflectionClass($source);
		if (!$ref->isInstantiable()) {
			throw new InvalidClassException("Can not create instance of $id");
		}
		// constructor
		$constructor = $ref->getConstructor();
		if ($constructor !== null) {
			// Read comment for getter
			$comment = $constructor->getDocComment();
			$is_autowire = preg_match_all('/@Autowired\s+([\w\\\\]+)\s+([\w\\\\]+)\s+/', $comment, $autowire_matches);
			$getter = [];
			if ($is_autowire && count($autowire_matches[1]) > 0) {
				foreach ($autowire_matches[1] as $k => $v) {
					$getter[$v] = $autowire_matches[2][$k];
				}
			}
			$params = $constructor->getParameters();
			$init_params = [];
			foreach ($params as $param) {
				if ($param->isOptional()) {
					$init_params[] = $param->getDefaultValue();
				} elseif (isset($getter[$param->getName()])) {
					$typeName = $getter[$param->getName()];
					$init_params[] = $this->get($typeName);
				} elseif ($param->hasType()) {
					$type = $param->getType();
					if (class_exists('ReflectionNamedType') && $type instanceof \ReflectionNamedType) {
						$typeName = $type->getName();
					} else {
						$typeName = $type->__toString();
					}
					if ($type->isBuiltin()) {
						$value = null;
						settype($value, $typeName);
						$init_params[] = $value;
					} else {
						$init_params[] = $this->get($typeName);
					}
				} else {
					$init_params[] = null;
				}
			}
			$instance = $ref->newInstance(...$init_params);
		} else {
			$instance = $ref->newInstance();
		}
		// properties
		$properties = $ref->getProperties();
		foreach ($properties as $property) {
			if ($property->isStatic()) {
				continue;
			}
			// Using getter and setter
			$propertyName = $property->getName();
			$setter = 'set' . ucfirst($propertyName);
			$getter = 'get' . ucfirst($propertyName);
			if (method_exists($instance, $setter) && method_exists($instance, $getter)) {
				if ($instance->$getter() === null) {
					$setter_ref = $ref->getMethod($setter);
					$is_autowire = preg_match('/@Autowired\s+([\w\\\\]+)\s+/', $setter_ref->getDocComment(), $autowire);
					if ($is_autowire) {
						$instance->$setter($this->get($autowire[1]));
					} else {
						$params = $setter_ref->getParameters();
						$param = $params[0];
						if ($param->hasType()) {
							$type = $param->getType();
							if (class_exists('ReflectionNamedType') && $type instanceof \ReflectionNamedType) {
								$typeName = $type->getName();
							} else {
								$typeName = $type->__toString();
							}
							$instance->$setter($this->get($typeName));
						}
					}
				}
			} else {
				$comment = $property->getDocComment();
				$is_autowire = preg_match('/@Autowired\s+([\w\\\\]+)\s+/', $comment, $autowire);
				if ($is_autowire) {
					$is_public = $property->isPublic();
					if (!$is_public) {
						$property->setAccessible(true);
					}
					if ($property->getValue($instance) === null) {
						$property->setValue($instance, $this->get($autowire[1]));
					}
					if (!$is_public) {
						$property->setAccessible(false);
					}
				}
			}
		}
		// put into instance
		if (!isset($this->multi[$source]) || $this->multi[$source] === self::MULTI_CLONE) {
			$this->instance[$source] = $instance;
		}
		unset($this->creating[$source]);
		return $instance;
	}
}