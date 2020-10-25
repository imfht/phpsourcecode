<?php
namespace Core;
/**
 * @author shooke
 * 模型控制
 * 对数据进行验证，和基本数据操作
 */
/*		说明：
 
		$_validate=array(
		    array(验证字段1,验证规则,错误提示,[附加规则]),
		    ......
		);
		$_auto=array(
		    array(验证字段1,填充规则,[附加规则]),
		    ......
		);
		$_field   =   array('允许字段1','允许字段2','允许字段3'......);

		用法：
		class indexModel extends Model{
		    // 定义自动验证
		    protected $_validate    =   array(
		        array('title','require','标题必须'),
		    	array('aa','require','标题3必须'),
		    );
		    // 定义自动完成
		    protected $_auto    =   array(
		        array('create_time','time','function'),
		    );
		    //规定字段
		    protected $_field   =   array('title','create_time','aa');
  			protected $tableName        =   '';       // 操作表
			protected $tableField       =   false;	  // 是开启表结构过滤

		}
		$m=model('index');
		$data = $m->checkData();
		$error = $db->getError();
		if ($error) {
			$this->error($error);
		}
		//自定义
		$m->validate($validate)->checkData();
 		$m->need($field)->checkData();
 		$m->clear($field)->checkData();
		$this->table('index')->data($data)->insert();
 
		若有一个验证函数返回false,则返回对应的错误返回值，若全部通过验证，则返回true。
		验证函数，可以是自定义的函数或类方法，返回true表示通过，返回false，表示没有通过
	*/

class Model extends Db {
	protected $error            =   '';       // 错误信息
	protected $_validate        =   array();  // 自动验证定义
	protected $_auto            =   array();  // 自动完成定义
	protected $_field           =   array();  // 数据返回字段，主要用于数据库操作
	protected $_clear           =   array();  // 数据排除字段，删除用不到的字段
	protected $patchValidate    =   false;	  // 是否批处理验证
	public $errorExit = false;
	public $tableName        =   '';       // 操作表
	protected $tableField       =   true;	  // 是开启表结构过滤


	/**
	 * 创建数据连接对象
	 * @access public
	 * @param mixed $db
	 */
	function __construct(){
	    $config = Config::$config['APP'];//取得设置后的配置信息
		parent::__construct($config);//调用cpModel构造方法
		$this->errorExit = Config::get('VALIDATE_EXIT');
		
		/*
		 * 设置表名
		 * 当模型文件名称与表名不一致，或需要指定操作表时使用
		 * 如表名one_page 模型文件名onePage就需要在模型中定义$tableName='one_page'
		 */
		$this->setTable();

	}
	/*
	 * 设置表名
	 * 根据模型文件名自动寻找表名
	 * 当模型文件名称与表名不一致，或需要指定操作表时
	 * 如表名one_page 模型文件名onePage就需要在模型中定义$tableName='one_page'
	 */
	private function setTable(){
		if($this->tableName){//设置表名后自动执行table方法，如果是数组请关闭表过滤		    
			$this->table($this->tableName);
		}else{
			$suffix = explode('.',Config::get('MODEL_SUFFIX'));//取得后缀
			$len = strlen($suffix[0]);//得到后缀长度
			$len = 0-$len;//得到从后向前截取长度
			$table = substr(get_class($this),0,$len);//得到表名
			if ($table && in_array($this->pre.$table,$this->getTables())) {//table属于数据库内的表时
				$this->table($table);//如果有表，设置模型名称为默认表
			}
		}
	}

	/**
     * 创建数据对象 但不保存到数据库
     * @access public
     * @param mixed $data 创建数据
     * $token 是否进行表单外部提交检验 默认true true是 false否
     * @return mixed
     */
     public function checkData($checkData='',$token=true) {
        // 如果没有传值默认取POST数据
        if(empty($checkData)) {
            $data   =   $_POST;
        }elseif(is_object($checkData)){
            $data   =   get_object_vars($checkData);
        }else{
            $data = $checkData;
        }
        // 验证数据
        if(empty($data) || !is_array($data)) {
            $this->error = '数据为空';
            return $this->errorExit($this->error);
        }

        // 自动表单令牌验证 当开启TOKEN_NAME时进行验证 单个表单不希望验证时，设置$toke参数为false
        if ($token && Config::$config['APP']['TOKEN_NAME']){
        	if(!$this->autoCheckToken($data)){ 
        	    $this->error = '禁止非法提交数据';
        	    return $this->errorExit($this->error);	
        	}
        }

        // 表单验证
		$this->autoValidation($data);
		// 自动完成
		$this->autoOperation($data);
        // 过滤非入库字段
        $this->autoField($data);        
        $this->options['data'] = $data;
        
        //错误终止处理
        if($this->error){ 
            $this->errorExit($this->error);
        }
        // 返回创建的数据以供其他调用
        return $this->options['data'];
     }
     
    // 自动表单令牌验证
    public function autoCheckToken($data) {       
        $token=self::Token_val();
        $name = Config::$config['APP']['TOKEN_NAME'];       
        if ($data[$name]==$token){            
        	return true;
        }else{
        	return false;
        }
    }
    // 生成表单令牌
    static public function Token_val() {
    	$authkey = Config::$config['APP']['TOKEN_NAME'];
    	return substr(md5(substr(time(), 0, -7).md5($authkey)), 8, 8);
    }
    //生成令牌表单
    static public function Token() {
    	$name = Config::$config['APP']['TOKEN_NAME'];
    	$value = self::Token_val();
    	return '<input type="hidden" name="'.$name.'" value="'.$value.'">';
    }
    /**
     * 使用正则验证数据
     * @access public
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     * @return boolean
     */
    public function regex($value,$rule) {
        $validate = array(
            'require'   =>  '/.+/',//必填
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',//邮箱
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',//网址
            'currency'  =>  '/^\d+(\.\d+)?$/',//货币金额
            'number'    =>  '/^\d+$/',//数字
            'zip'       =>  '/^\d{6}$/',//邮编
            'integer'   =>  '/^[-\+]?\d+$/',//int类型
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',//double类型
            'english'   =>  '/^[A-Za-z]+$/',//英文字符
			'ID'        =>  "/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",//身份证
			'mobile'    =>  '/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/',//手机号码
        );

        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)])){
            $rule       =   $validate[strtolower($rule)];
        }
        return preg_match($rule,$value)===1;
    }

    /**
     * 自动表单处理
     * @access public
     * @param array $data 创建数据
     * @return mixed
     */
    private function autoOperation(&$data) {
        $_auto   =   $this->_auto;
        // 自动填充
        if(is_array($_auto)) {
            foreach ($_auto as $auto){
                // 填充因子定义格式
                // array('field','填充内容','附加规则',[额外参数])
                switch(trim($auto[2])) {
                	case 'function':    //  使用函数进行填充 字段的值作为参数
                		$data[$auto[0]] = $auto[1]();
                		break;
                    case 'field':    // 用其它字段的值进行填充
                     	$data[$auto[0]] = $data[$auto[1]];
                        break;
                    case 'ignore': // 为空忽略
                     	if(''===$data[$auto[0]])
                        unset($data[$auto[0]]);
                        break;
                    case 'string':
                    default: // 默认作为字符串填充
                        $data[$auto[0]] = $auto[1];
                }
                if(false === $data[$auto[0]] )   unset($data[$auto[0]]);
            }
        }
        return $data;
    }


    /**
     * 自动表单验证
     * @access protected
     * @param array $data 创建数据
     * @return boolean
     */
    protected function autoValidation($data){
        $_validate   =   $this->_validate;
        // 属性验证
        if(isset($_validate)) { // 如果设置了数据自动验证则进行数据验证
            if($this->patchValidate) { // 重置验证错误信息
                $this->error = array();
            }
            foreach($_validate as $key=>$val) {
                // 验证因子定义格式
                // array(field,rule,message,condition,params)
                // 判断是否需要执行验证
                $val[3]  =  isset($val[3])?$val[3]:'regex';
                // 判断验证条件
                if(false === $this->_validationField($data,$val)){
                	if (!$this->patchValidate) return false;//单条验证 批量验证关闭
                }
            }
            // 批量验证的时候最后返回错误
            if(!empty($this->error)) return false;
        }
        return true;
    }
    /**
     * 验证表单字段 支持批量验证
     * 如果批量验证返回错误的数组信息
     * @access protected
     * @param array $data 创建数据
     * @param array $val 验证因子
     * @return boolean
     */
    protected function _validationField($data,$val) {
    	if(false === $this->_validationFieldItem($data,$val)){
    		if($this->patchValidate) {
    			$this->error[$val[0]]   =   $val[2];
    		}else{
    			$this->error            =   $val[2];
    			return false;
    		}
    	}
    	return ;
    }
    /**
     * 根据验证因子验证字段
     * @access protected
     * @param array $data 创建数据
     * @param array $val 验证因子
     * @return boolean
     */
    protected function _validationFieldItem($data,$val) {
    	switch(strtolower(trim($val[3]))) {
    		case 'function':    //  使用函数进行填充 字段的值作为参数
    			return $data[$val[0]] = $val[1]();
    		case 'confirm': // 验证两个字段是否相同
    			return $data[$val[0]] == $data[$val[1]];
			case 'unique': // 验证某个值是否唯一
				if(is_string($val[0]) && strpos($val[0],',')){//如果是array('field1,field2','','用户名已被注册','unique'),则多个字段先转换为数组
                    $val[0]  =  explode(',',$val[0]);
				}
                $where = array();
                if(is_array($val[0])) {
                    // 支持多个字段验证
                    foreach ($val[0] as $field){
                        $where[$field]   =  $data[$field];
                    }
                }else{
                    $where[$val[0]] = $data[$val[0]];
                }
                if($this->where($where)->find())   return false;//如果已有数据返回false
                return true; //无相同数据表示唯一 返回true
    		default:  // 检查附加规则
    			return $this->check($data[$val[0]],$val[1],$val[3]);
    	}
    }

    /**
     * 验证数据 支持 in between equal length regex expire ip_allow ip_deny
     * @access public
     * @param string $value 验证数据
     * @param mixed $rule 验证表达式
     * @param string $type 验证方式 默认为正则验证
     * @return boolean
     */
    public function check($value,$rule,$type='regex'){
    	$type   =   strtolower(trim($type));
    	switch($type) {
    		case 'in': // 验证是否在某个指定范围之内 逗号分隔字符串或者数组
    		case 'notin':
    			$range   = is_array($rule)? $rule : explode(',',$rule);
    			return $type == 'in' ? in_array($value ,$range) : !in_array($value ,$range);
    		case 'between': // 验证是否在某个范围
    		case 'notbetween': // 验证是否不在某个范围
    			if (is_array($rule)){
    				$min    =    $rule[0];
    				$max    =    $rule[1];
    			}else{
    				list($min,$max)   =  explode(',',$rule);
    			}
    			return $type == 'between' ? $value>=$min && $value<=$max : $value<$min || $value>$max;
    		case 'equal': // 验证是否等于某个值
    		case 'notequal': // 验证是否等于某个值
    			return $type == 'equal' ? $value == $rule : $value != $rule;
    		case 'length': // 验证长度
    			$length  =  mb_strlen($value,'utf-8'); // 当前数据长度
    			if(strpos($rule,',')) { // 长度区间
    				list($min,$max)   =  explode(',',$rule);
    				return $length >= $min && $length <= $max;
    			}else{// 指定长度
    				return $length == $rule;
    			}
    		case 'expire':
    			list($start,$end)   =  explode(',',$rule);
    			if(!is_numeric($start)) $start   =  strtotime($start);
    			if(!is_numeric($end)) $end   =  strtotime($end);
    			return NOW_TIME >= $start && NOW_TIME <= $end;
    		case 'regex':
    		default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
    			// 检查附加规则
    			return $this->regex($value,$rule);
    	}
    }

    /**
     * 返回筛选的字段使用方式如下
     * class indexModel extends Cmodel{
     * 		protected $_field   =   array('title','create_time','aa');
     * }
     * 或者
     * $obj->field('a,b')或$obj->field(arrary('a','b'))
     * @access public
     * @return string
     */
    protected function autoField(&$data){

    	/**
    	 * 没有拥有数据表模型并且未过滤字段，自动获取表字段
    	 */
    	if ($this->tableField && !$this->_field) {
    		$tables = $this->getFields();
    		if(is_array($tables)){
	    		foreach ($tables as $tab){
	    			$table[] = $tab['Field'];
	    		}
    		}
    		$this->_field= $table;
    	}
    	/**
    	 * 返回字段
    	 * 使用$obj->field('a,b')或者$obj->field(arrary('a','b'))时，覆盖子类中设置的protected $_field
    	 * 当未设置_field或者不是*时进行过滤
    	 */
    	if($this->_field){
    		//$obj->field(arrary('a','b'))数组时直接使用
    		if(is_array($this->_field)){
    			$_field = $this->_field;
    		}else{
    			//$obj->field('a,b')字符串时转换成数组使用
    			$_field = explode(',', $this->_field);
    		}
    		foreach ($data as $key=>$val){
    			if(!in_array($key, $_field)) unset($data[$key]);
    		}
    	}

    	/**
    	 * 排除字段
    	 * 使用$obj->clear('a,b')或者$obj->clear(arrary('a','b'))时，覆盖子类中设置的protected $_clear
    	 */
    	if($this->_clear){
    		//$obj->clear(arrary('a','b'))数组时直接使用
    		if(is_array($this->_clear)){
    			$_clear = $this->_clear;
    		}else{
    			//$obj->$_clear('a,b')字符串时转换成数组使用
    			$_clear = explode(',', $this->_clear);
    		}
    		foreach ($data as $key=>$val){
    			if(in_array($key, $_clear)) unset($data[$key]);
    		}
    	}
    }

    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError(){
    	return $this->error;
    }
    //保留表单字段
    public function need($field){
    	$this->_field=$field;
    	return $this;
    }
    //排除表单字段
    public function clear($field){
    	$this->_clear=$field;
    	return $this;
    }
    //验证规则
    public function validate($validate){
    	$this->_validate=$validate;
    	return $this;
    }
    //自动完成
    public function auto($auto){
    	$this->_auto=$auto;
    	return $this;
    }
    
    //错误输出，如果开启终止则终止允许，否则返回false
    public function errorExit($msg){
        if($this->errorExit){
            $msg = is_array($msg) ? explode('<br>', $msg) : $msg;
            Error::show($msg);
        }else{
            return false;
        }
    }
}
