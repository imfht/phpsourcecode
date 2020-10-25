<?php
namespace Scabish\Abyss;

use SCS;
use Exception;

/**
 * Scabish\Abyss\Server
 * Abyss服务端
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-11-27
 */
class Server {
    
    private static $_instance;
    
    private $params = [];
    private $binds = [];
    private $return = [];
    private $cache = 0;
    private $transaction = false;
    
    private function __construct() {}
    
    public function __clone() {}
    
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Abyss服务启动
     * @throws Exception
     */
    public function Run() {
        $this->CheckAuth() || $this->Response(false, 'Authentication failed');
        $query = $this->ParseQuery();
        $this->transaction && SCS::Db()->BeginTransaction();
        try {
            for($i = 0; $i < count($query); $i++) {
                list($control, $action) = explode('/', $query[$i]['api']);
                $this->SetParam(isset($query[$i]['param']) ? $query[$i]['param'] : []);
                if(isset($query[$i]['size'])) $this->Page()->size = intval($query[$i]['size']);
                if(isset($query[$i]['cache'])) {
                    $this->cache = intval($query[$i]['cache']);
                    unset($query[$i]['cache']);
                }
                if(!$this->cache) {
                    $return = $this->Invoke($control, $action);
                } else {
                    $md5 = md5(serialize($query[$i]));
                    $directory = SC_CACHE_PATH.'/'.substr($md5, 0, 2);
                    $file = $directory.'/'.$md5.'.db';
                    if(!(file_exists($file) && is_file($file))) {
                        file_exists($directory) || mkdir($directory, 0775, true);
                        if(false === ($result = touch($file))) {
                            throw new Exception('Unable create cache file: '.realpath($file));
                        }
                    }
                    $stream = unserialize(base64_decode(file_get_contents($file)));
                    if(is_array($stream) && isset($stream['time']) && ($stream['time'] + $this->cache) >= time()) {
                        $this->cache = 0;
                        $return = $stream['data'];
                    } else {
                        $return = $this->Invoke($control, $action);
                        $stream = base64_encode(serialize(['time' => time(), 'data' => $return]));
                        if(false === ($result = file_put_contents($file, $stream))) {
                            throw new Exception('Cache file write failed: '.realpath($file));
                        }
                        $this->_cache = 0;
                    }
                }
                
                if(isset($query[$i]['return'])) {
                    $_r = $return;
                    $properties = trim($query[$i]['return'], '.') ? explode('.', trim($query[$i]['return'], '.')) : [];
                    foreach($properties as $property) {
                        $this->HasProperty($property, $_r) || $this->Response(false, 'Return error: undefined index "'.$property.'" in api result: '.$control.'/'.$action);
                        $_r = $this->GetProperty($property, $_r);
                    }
                    $this->return[] = $_r;
                } else {
                    $this->return[] = $return;
                }
                
                if(isset($query[$i]['bind']) && is_array($query[$i]['bind'])) {
                    foreach($query[$i]['bind'] as $k=>$v) {
                        $properties = trim($v, '.') ? explode('.', trim($v, '.')) : [];
                        $_r = $return;
                        foreach($properties as $property) {
                            $this->HasProperty($property, $_r) || $this->Response(false, 'Bind error: undefined index "'.$property.'" in api result: '.$control.'/'.$action);
                            $_r = $this->GetProperty($property, $_r);
                        }
                        $this->binds[$k] = $_r;
                    }
                }
                
                $this->cache = 0;
            }
            
            $this->transaction && SCS::Db()->Commit();
            
            $this->Response(true, $this->return);
            
        } catch(Exception $e) {
            $this->transaction && SCS::Db()->RollBack();
            throw new Exception($e->getMessage().' in file '.$e->getFile().', '.$e->getLine());
        }
    }
    
    /**
     * 获取参数
     * @param string $param 参数名
     * @return boolean|mixed
     */
    public function Param($param = null) {
        if($param) {
            if(!isset($this->params[$param])) return false;
            $value = $this->params[$param];
            if(is_string($value) && preg_match('/^::\w+/', $value) && isset($this->binds[$value])) {
                return isset($this->binds[$value]) ? $this->binds[$value] : false;
            }
            return $value;
        } else {
            $params = [];
            foreach($this->params as $param=>$value) {
                if(is_string($value) && preg_match('/^::\w+/', $value)) {
                    $properties = explode('.', $value);
                    $b = $this->binds;
                    foreach($properties as $i=>$property) {
                        if($this->HasProperty($property, $b)) {
                            $b = $this->GetProperty($property, $b);
                        } else {
                            $b = null;
                        }
                    }
                    $params[$param] = $b;
                } else {
                    $params[$param] = $value;
                }
            }
            return $params;
        }
    }
    
    /**
     * API间直接互调
     * @param string $api api路由, 如: User/Read
     * @param string $params 参数
     * @throws Exception
     * @return mixed
     */
    public function Call($api, $params = []) {
        list($control, $action) = explode('/', $api);
        $_params = $this->params; // 暂存当前params信息
        $this->params = $params;
        $result = $this->Invoke($control, $action);
        $this->params = $_params; // 恢复当前params数据
        return $result;
    }
    
    /**
     * 验证参数数据是否满足条件
     * @param array $rules
     */
    public function Validate($rules = []) {
        $validate = new \Scabish\Tool\Validate($rules);
        $validate->Check($this->Param());
    }
    
    /**
     * 根据传递过来的参数自动绑定查询条件
     * @example
     * 单表
     * $rules = ['field1, field2:param']; // 参数param映射到字段field2
     * 多表：
     * $rules = [
     *     'field, field2', // 不带别名的字段
     *     'tableAlias' => 'field2:param, field3' // 带别名的字段，参数param映射到tableAlais.field2上
     * ];
     * $rules = [
     *     'tableAlias' => 'field1, field2',
     *     'tableAlias2' => 'field2:param, field3' // 参数param映射到字段tableAlias2.field2
     * ];
     * 
     * @param array $rules
     */
    public function Compare(array $maps) {
        $rules = [];
        foreach($maps as $tableAlias => $map) {
            $map = explode(',', $map);
            foreach($map as $m) {
                $m = trim($m);
                if(strpos($m, ':')) {
                    list($field, $param) = explode(':', $m);
                } else {
                    $param = $field = $m;
                }
                $rules[$param] = (is_numeric($tableAlias) ? '' : $tableAlias.'.').$field;
            }
        }
        $condition = '';
        foreach($rules as $param=>$field) {
            $value = $this->Param($param);
            if(false === $value) continue;
            if(is_array($value)) {
                foreach($value as $op=>$v) {
                    $condition .= $this->AddCondition($op, $field, $v);
                }
            } elseif(strlen($value)) {
                $condition .= ' AND '.$field.' = "'.$value.'"';
            }
        }
        
        return strlen($condition) ? substr($condition, strlen(' AND ')) : '';
    }
    
    private function AddCondition($operation, $field, $value) {
        if('' == $value || false === $value || is_null($value)) return '';
        $operation = strtoupper(preg_replace('/\s+/', ' ', trim($operation)));
        switch($operation) {
        	case '=':
        	case '!=':
        	case '>':
        	case '<':
        	case '>=':
        	case '<=':
    	    case 'REGEXP':
        	    return ' AND '.$field.' '.$operation.' "'.$value.'"';
    	    case 'IN':
    	    case 'NOT IN':
    	        if(!is_array($value)) throw new Exception('Type error: value should be an array');
    	        return ' AND '.$field.' '.$operation.' ("'.implode('","', $value).'")';
    	    case 'LIKE':
    	    case 'NOT LIKE':
    	        return ' AND '.$field.' '.$operation.' "%'.$value.'%"';
        	case 'IS NULL':
        	case 'IS NOT NULL':
        	    return ' AND '.$field.' '.$operation;
        	case 'BETWEEN':
        	case 'NOT BETWEEN':
        	    if(!is_array($value)) throw new Exception('Type error: value should be an array');
        	    list($min, $max) = $value;
        	    return ' AND '.$field.' '.$operation.' "'.$min.'" AND '.'"'.$max.'"';
        	default:
        	    return ' AND '.$field.' '.$operation.' "'.$value.'"';
        }
    }
    
    /**
     * 返回json信息
     * @param boolean $status 结果状态
     * @param mixed $data 返回数据
     */
    public function Response($status, $data = '') {
        header('Content-type: application/json; charset=utf-8');
        die(json_encode([
            'status' => $status ? 1 : 0, // 成功状态码
            'data' => $data, // 数据
        ]));
    }
    
    /**
     * 获取Page对象
     * 注意：应该在Control/Action中调用此方法
     * @return \Scabish\Core\Page Page对象
     */
    public function Page() {
        static $page = null;
        if(is_null($page)) {
            $page = SCS::Page($this->Param('page'));
        }
        return $page;
    }
    
    /**
     * 返回分页格式数据
     * @param array $data
     * @return array
     */
    public function PageData($data) {
        return [
            'data' => $data, // 分页数据
            'total' => $this->Page()->total, // 数据总数
        ];
    }
    
    private function Invoke($control, $action) {
        $rc = new \ReflectionClass('Control\\'.ucfirst($control));
        if(!$rc->hasMethod($action)) {
            throw new Exception('Undefined API：'.$control.'/'.$action.'');
        }
        $class = $rc->newInstance();
        $method = $rc->getMethod($action);
        return $method->invoke($class);
    }
    
    private function ParseQuery() {
        $query = json_decode(file_get_contents('php://input'), true); // 用此方式保证数据不会发送自动转换(传统post数据不能识别false,null)
        $this->transaction = isset($query['transaction']) ? $query['transaction'] : false; // 默认关闭事务
        if(isset($query['query'])) $query = $query['query'];
        if(isset($query['api'])) $query = [$query];
    
        return $query;
    }
    
    private function CheckAuth() {
        if(!(isset($_SERVER['HTTP_X_ABYSS_ID']) && isset($_SERVER['HTTP_X_ABYSS_KEY']))) return false;
        
        $credit = SCS::Instance()->abyss['server']['credit'];
        $id = $_SERVER['HTTP_X_ABYSS_ID'];
        if(!isset($credit[$id])) return false;
    
        $split = explode('.', $_SERVER['HTTP_X_ABYSS_KEY']);
        if(count($split) != 2) return false;
    
        $key = sha1($id.'ABYSS'.$credit[$id].'ABYSS'.$split[1]).'.'.$split[1];
        return 0 === strcasecmp($key, $_SERVER['HTTP_X_ABYSS_KEY']);
    }
    
    private function CheckParam($api, $param) {
        foreach($param as $v) {
            list($type, $name, $desc, $value, $require) = $v;
            if($require) {
                if(false === $this->Param($name)) {
                    throw new Exception('Param of "'.$name.'" should be required ('.$api.')');
                }
            }
        }
        return true;
    }
    
    private function HasProperty($property, $object) {
        if(is_object($object)) return isset($object->$property);
        if(is_array($object)) return isset($object[$property]);
        return false;
    }
    
    private function GetProperty($property, $object) {
        if(is_object($object)) return isset($object->$property) ? $object->$property : null;
        if(is_array($object)) return isset($object[$property]) ? $object[$property] : null;
        return null;
    }
    
    private function SetParam($params = []) {
        $this->params = $params;
    }
}