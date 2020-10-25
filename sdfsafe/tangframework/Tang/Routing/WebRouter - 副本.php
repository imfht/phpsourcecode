<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Routing;
/**
 * Web路由
 * Class WebRouter
 * @package Tang\Routing
 */
class WebRouter extends BaseRouter implements IRouter
{
	/**
	 * 配置
	 * @var array
	 */
	protected $config = array();
	/**
	 * 分隔符
	 * @var string
	 */
	protected $delimiter;
	/**
	 * 脚本名称
	 * @var string
	 */
	protected $scripteName;
	/**
	 * 是否域名绑定
	 * @var bool
	 */
	protected $domainBind = false;
	/**
	 * 生成网址的缓存
	 * @var array
	 */
	protected static $urls =array();
	/**
	 * 请求后缀
	 * @var string
	 */
	protected $extension = '';

	/**
	 * 设置
	 * @param array $config
	 * @return mixed|void
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;
	}

	/**
	 * 获取网页后缀
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * 解析路由
	 * @return mixed|void
	 */
	public function router()
	{
		$model = $this->config['model'];
		$this->scripteName = $_SERVER['SCRIPT_NAME'];
		if($model > 0)
		{
			$pathInfoParmName = $this->config['pathInfo']['parmName'];
			if(!isset($_SERVER['PATH_INFO']) || !$_SERVER['PATH_INFO'])
			{
				if(isset($_GET[$pathInfoParmName]))
				{
					$_SERVER['PATH_INFO'] = $_GET[$pathInfoParmName];
					unset($_GET[$pathInfoParmName]);
				} else
				{
					$_SERVER['PATH_INFO'] = '';
				}
			}
			if($model == 3)
			{
				$this->scripteName = $_SERVER['SCRIPT_NAME'].'?'.$pathInfoParmName.'=';
			} else if($model == 2)
			{
				$this->scripteName =  dirname($_SERVER['SCRIPT_NAME']);
				if($this->scripteName == '/' || $this->scripteName == '\\')
					$this->scripteName =  '';
			}
			$this->routeByPathInfo();
		}
		$this->checkDefaultValue($this->config['moduleName'], ucfirst($this->config['defaultModule']), $this->moduleValue);
		$this->checkDefaultValue($this->config['controllerName'], ucfirst($this->config['defaultController']), $this->controllerValue);
		$this->checkDefaultValue($this->config['actionName'], $this->config['defaultAction'], $this->actionValue,false);
		if(!$this->domainBind)
		{
			$alias = $this->config['moduleAlias'];
			if(in_array($this->moduleValue, $alias))
			{
				$this->request->getResponse()->message()->notFound('alias');
			} else if(isset($alias[$this->moduleValue]) && $alias[$this->moduleValue])
			{
				$this->moduleValue = ucfirst($alias[$this->moduleValue]);
			}
		}
	}

	/**
	 * 创建URL
	 * @param string $actionPath //操作路径 /模型/控制器/操作 有绑定子域名需要在后面加@域名
	 * @param string $parameters
	 * @param mixed $suffix 为空的话使用默认的配置后缀
	 * @param bool $CSXF
	 * @return string
	 */
	public function createUrl($actionPath='/',$parameters = '', $suffix='',$CSXF =false)
	{
		$index = strpos($actionPath,'@');
		if($index)
		{
			$subDomain = substr($actionPath,$index+1);
			$actionPath = substr($actionPath,0,$index);
		}
		if($actionPath)
		{
			$parseUrl = parse_url($actionPath);
			$actionPath = $parseUrl['path'];
		}
		if($actionPath)
		{
			$array = explode('/',$actionPath,3);
			$array = array_map('lcfirst',$array);
			$count = count($array);
			if($count ==2)
			{
				array_unshift($array,$this->moduleValue);
			} else if($count == 1)
			{
				array_unshift($array,$this->moduleValue,$this->controllerValue);
			}
		} else
		{
			$array = array(lcfirst($this->moduleValue),lcfirst($this->controllerValue),lcfirst($this->actionValue));
		}
		$alias = array_search(ucfirst($array[0]),$this->config['moduleAlias']);
		if($alias)
		{
			$array[0] = lcfirst($alias);
		}
		$actionPath = implode('/',$array);
		// 解析参数
		if(is_string($parameters) && $parameters)  // aaa=1&bbb=2 转换成数组
		{
			parse_str($parameters,$parameters);
		}elseif(!is_array($parameters))
		{
			$parameters = array();
		}

		if(isset($parseUrl['query']) && $parseUrl['query'])
		{
			$queryParameters = array();
			parse_str($parseUrl['query'],$queryParameters);
			$parameters = array_merge($parameters,$queryParameters);
		}

		//判断伪静态
		$isRewrite = false;
		if(!$this->rootDomain)
		{
			$domain = $_SERVER['HTTP_HOST'];
		} else
		{
			$domain = 'www.'.$this->rootDomain;
		}
		if($CSXF)
		{
			$CSXFInstance = $this->request->CSRF();
			$parameters[$CSXFInstance->getName()] = $CSXFInstance->getValue();
		}

		if($this->config['model'] == 0) // 普通模式URL转换
		{
			$url = $this->scripteName.'?';
			foreach(array('moduleName','controllerName','actionName') as $key => $configName)
			{
				$parameters[$this->config[$configName]] = $array[$key];
			}
			$url .= http_build_query($parameters);
		} else
		{
			$path = '';
			if($this->config['rewrite']['support'] && isset($this->config['rewrite']['rules']) && $this->config['rewrite']['rules'])
			{
				$ruleName = 'public';
				if($this->rootDomain && $subDomain)
				{
					if(isset($this->config['rewrite']['rules'][$subDomain]))
					{
						$ruleName = $subDomain;
					} else
					{
						$index = strpos($subDomain,'.');
						if(!$index)
						{
							if(isset($this->config['rewrite']['rules']['*']))
							{
								$ruleName = '*';
							} else
							{
								$ruleName = '';
							}
						} else
						{
							$thirdDomain = substr($subDomain,0,$index);
							$ruleName = '*'.substr($subDomain,$index);
							if(!isset($this->config['rewrite']['rules'][$ruleName]))
							{
								$ruleName = '';
							}
						}
					}
					$domain = $subDomain.'.'.$this->rootDomain;
				}

				$rules = $ruleName ? $this->config['rewrite']['rules'][$ruleName] : array();
				foreach($rules as $key => $value)
				{
					if($isRewrite = preg_match('%'.$key.'$%i',$actionPath,$matches))
					{
						if(!is_array($value) || !isset($value['regex']) || !$value['regex'])
						{
							break;
						}
						$rewriteRules = $value;
						$count = count($matches);
						for($i = 1; $i < count($matches);$i++)
						{
							$value['regex'] = preg_replace('%\((.+?)\)%',$matches[$i],$value['regex'],1);
						}
						$count = $count - 1;
						if($parameters && is_array($value['parameters']) && $value['parameters'])
						{
							$parametersCount = count($value['parameters']);
							for($count;$count<$parametersCount;$count++)
							{
								$parameterName = $value['parameters'][$count];
								if(isset($parameters[$parameterName]))
								{
									$replaceValue = $parameters[$parameterName];
									unset($parameters[$parameterName]);
								} else
								{
									$replaceValue = $parameterName;
								}
								$value['regex'] = preg_replace('%\((.+?)\)%',$replaceValue,$value['regex'],1);
							}
						}
						$path = stripcslashes($value['regex']);
						//进行参数绑定
						break;
					}
				}
			}
			$url = $this->scripteName.'/'.(!$path ? implode($this->delimiter,$array):ltrim($path,'/'));
			if($isRewrite && !isset($rewriteRules['appendParameters']) && !$rewriteRules['appendParameters'])
			{
				$parameters && $url .= '?'.http_build_query($parameters);
			} else
			{
				if($parameters)
				{
					$url = rtrim($url,$this->delimiter).$this->delimiter;
					foreach ($parameters as $name => $value)
					{
						if('' !== trim($value))
						{
							$url .= $name.$this->config['parametersDelimiter'].urlencode($value).$this->config['parametersDelimiter'];
						}
					}
					$url = substr($url,0,-(strlen($this->config['parametersDelimiter'])));
				}
			}
			if($suffix !== false && !$suffix)
			{
				$suffix = $this->config['defaultSuffix'];
			}

			if($suffix && substr($url,-1) != '/')
			{
				if(!is_bool($suffix))
				{
					$url .= '.'.$suffix;
				} else if($this->extension)
				{
					$url  .=  '.'.$this->extension;
				} else if($this->config['suffixs'])
				{
					$suffix = $this->config['suffixs'];
					$index = strpos($suffix, '|');
					if($index)
					{
						$suffix = substr($suffix,0, $index);
					}
					$url  .=  '.'.ltrim($suffix,'.');
				}
			}
		}
		return  ($this->request->isSsl() ? 'https://':'http://').$domain.$url;
	}

	/**
	 * 获取类型
	 * @return string
	 */
	public function getType()
	{
		return 'Web';
	}

	/**
	 * PathInfo模式解析
	 */
	protected function routeByPathInfo()
	{
		$this->delimiter = $this->config['delimiter'];
		$domainString = $this->getSubDomain();
		$this->checkPathInfo();
		//判断有没带参数
		$index = strpos($_SERVER['PATH_INFO'],'?');
		$parameters = array();
		if($index !== false)
		{
			$parametersString = substr($_SERVER['PATH_INFO'], $index+1);
			$_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 0,$index);
			parse_str($parametersString,$parameters);
			$_GET = array_merge($parameters,$_GET);
			$_REQUEST = array_merge($_REQUEST,$_GET);
			if($index === 0)
			{
				return ;
			} else
			{
				$parameters = array();
			}
		}
		//判断后缀名
		$this->extension = strtolower(pathinfo($_SERVER['PATH_INFO'],PATHINFO_EXTENSION));
		$suffixs = isset($this->config['suffixs']) ? $this->config['suffixs']:'';
		$notfound = function($request)
		{
			$request->getResponse()->message()->notFound('not extension');
		};
		if($suffixs && $this->extension)
		{
			$suffixs = trim($suffixs,'.');
			if(preg_match('/\.('.$suffixs.')$/i',$_SERVER['PATH_INFO']))
			{
				$_SERVER['PATH_INFO'] = preg_replace('/\.('.$suffixs.')$/i', '',$_SERVER['PATH_INFO']);
			} else if($this->extension)
			{
				$notfound($this->request);
			}
		} else if($this->extension)
		{
			$notfound($this->request);
		}
		if($this->config['rewrite']['support'])
		{
			$rewriteRules = $this->config['rewrite']['rules'];
			$rules = $domainString && isset($rewriteRules[$domainString]) ? $rewriteRules[$domainString] : $rewriteRules['public'];
			$isPregMatch = false;
			if($rules) foreach ($rules as $rule => $value)
			{
				if(!isset($value['regex']) || !$value['regex'])
				{
					continue;
				}
				$appendParameters = isset($value['appendParameters']) && $value['appendParameters'];
				if($appendParameters)
				{
					$regex = '%^'.$value['regex'].'(.*)%';
				} else
				{
					$regex = '%^'.$value['regex'].'$%';
				}
				if($isPregMatch = preg_match($regex,$_SERVER['PATH_INFO'],$matches))
				{
					unset($matches[0]);
					if(isset($value['parameters']) && is_array($value['parameters']) && $value['parameters'])
					{
						$key = 0;
						foreach($matches as  $matchValue)
						{
							$rule = preg_replace('%\((.+?)\)%',$matchValue,$rule,1);
							if(isset($value['parameters'][$key]) && $value['parameters'][$key])
							{
								$_GET[$value['parameters'][$key]] = $matchValue;
							}
							$key++;
						}
					} else
					{
						foreach($matches as  $value)
						{
							$rule = preg_replace('%\((.+?)\)%',$value,$rule,1);
						}
					}
					$_SERVER['PATH_INFO'] = $rule;
					if($appendParameters)
					{
						$_SERVER['PATH_INFO'] .= $this->delimiter.ltrim(end($matches),$this->delimiter);
					}
					break;
				}
			}
		}
		if($_SERVER['PATH_INFO'] == '/')
		{
			return;
		}
		if(!$isPregMatch)
		{
			$delimiter = $this->delimiter;
		} else
		{
			$delimiter = '/';//使用正则表达式的分隔符为/
		}
		$paths  = explode($this->delimiter,trim($_SERVER['PATH_INFO'],$delimiter));
		foreach(array('setModuleValue','setControllerValue','setActionValue') as $method)
		{
			if($paths && $this->$method($paths[0]))
			{
				array_shift($paths);
			}
		}
		if($paths)
		{
			// 解析剩余的URL参数
			$parametersDelimiter = $this->config['parametersDelimiter'];
			$parameters = explode($this->config['paramsBindType'],$paths[0]);
			if($paramsBindType && 2 == $paramsBindType)
			{
				$parameters = explode($parametersDelimiter,$paths[0]);
			}else
			{
				preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$parameters){$parameters[$match[1]]=strip_tags($match[2]);},str_replace($parametersDelimiter,'/',$paths[0]));
			}
		}
		$_GET = array_merge($parameters,$_GET);
		$_REQUEST = array_merge($_REQUEST,$_GET);
	}

	/**
	 * @param $name
	 * @param $defaultValue
	 * @param $value
	 * @param bool $isUcfirst
	 */
	protected function checkDefaultValue($name,$defaultValue,&$value,$isUcfirst = true)
	{
		if($value)
		{
			return;
		}
		if(isset($_GET[$name]) && $_GET[$name])
		{
			$value = $isUcfirst ? ucfirst($_GET[$name]):$_GET[$name];
		} else
		{
			$value = $defaultValue;
		}
	}

	/**
	 * 设置模块值
	 * @param $value
	 * @return bool
	 */
	protected function setModuleValue($value)
	{
		if(!$this->moduleValue && $this->checkVarname($value))
		{
			$this->moduleValue = ucfirst($value);
			return true;
		} else
		{
			return false;
		}
	}

	/**
	 * 设置控制器值
	 * @param $value
	 * @return bool
	 */
	protected function setControllerValue($value)
	{
		if($this->moduleValue && !$this->controllerValue && $this->checkVarname($value))
		{
			$this->controllerValue = ucfirst($value);
			return true;
		} else
		{
			return false;
		}
	}

	/**
	 * 设置动作值
	 * @param $value
	 * @return bool
	 */
	protected function setActionValue($value)
	{
		if($this->moduleValue && $this->controllerValue && !$this->actionValue && $this->checkVarname($value))
		{
			$this->actionValue = $value;
			return true;
		} else
		{
			return false;
		}
	}

	/**
	 * 检查变量名
	 * @param $value
	 * @return bool
	 */
	public function checkVarname($value)
	{
		return $value && !is_numeric($value) && preg_match('%^[A-Za-z_][A-Za-z0-9|_|]*$%', $value);
	}

	/**
	 * 检查PathInfo
	 */
	protected function checkPathInfo()
	{
		if(!isset($_SERVER['PATH_INFO']) && is_array($this->config['pathInfo']['otherPathInfo']))
		{
			foreach ($this->config['pathInfo']['otherPathInfo'] as $name)
			{
				if(!empty($_SERVER[$name]))
				{
					$_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$name],$_SERVER['SCRIPT_NAME']))?substr($_SERVER[$name], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$name];
					break;
				}
			}
		}
		$_SERVER['PATH_INFO'] = '/'.ltrim($_SERVER['PATH_INFO'],'/');
	}

	/**
	 * 获取子域支持
	 */
	protected function getSubDomain()
	{
		$subDomainConfig = $this->config['subDomain'];
		if(!$subDomainConfig['support'] || !is_array($subDomainConfig['rules']) || !$subDomainConfig['rules'] || !preg_match('%(.+?)'.$subDomainConfig['suffix'].'$%',$_SERVER['HTTP_HOST'],$matches))
		{
			return;
		}

		$rules = $subDomainConfig['rules'];
		$domain = explode('.',$matches[1]);
		$domainParameters = '';
		$this->rootDomain = array_pop($domain).$subDomainConfig['suffix'];//网站的根域名
		$domainString = implode('.',$domain);
		$domain && $subDomain  = array_pop($domain); // 二级域名
		$domain && $threeDomain = array_pop($domain);
		if(isset($rules[$domainString])) //判断该域名是否有规则
		{
			$parametersString = $rules[$domainString];
			$domainParameters = $domainString;
		}elseif($subDomain && isset($rules['*.'.$subDomain]) && !empty($threeDomain))// 泛三级域名
		{
			$domainString = '*.'. $subDomain;
			$parametersString = $rules[$domainString];
			$domainParameters = $threeDomain;
		}elseif(isset($rules['*']) && !empty($subDomain) && 'www' != $subDomain )// 泛二级域名
		{
			$domainString = '*';
			$parametersString= $rules[$domainString];
			$domainParameters = $subDomain;
		} else
		{
			return;
		}
		if(isset($parametersString))  // 传入参数
		{
			if($domainParameters)
			{
				$parametersString = str_replace('[domain]',$domainParameters,$parametersString);
			}
			parse_str($parametersString,$parameters);
			$_GET = array_merge($_GET,$parameters);
			$_REQUEST = array_merge($_REQUEST,$parameters);
		}
		return $domainString;
	}
}