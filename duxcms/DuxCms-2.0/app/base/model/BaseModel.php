<?php
namespace app\base\model;
/**
 * 底层模型
 */
class BaseModel extends \framework\base\Model{

	protected $table = '';
    protected $primary = '';
    protected $error = '';
    protected $data = array();
    public function __construct()
    {
        $this->setTable();
        parent::__construct();
    }

    /**
	 * 设置表名
	 */
    public function setTable($table = null){
        if($table){
            $this->table = $this->config['DB_PREFIX'].$table;
            $this->table($table);
            return;
        }
        if(!empty($this->table)){
            return;
        }
        $class = get_called_class();
        $class = str_replace('\\', '/', $class);
        $class = basename($class);
        $class = substr($class, 0, -5);
        $class = preg_replace("/(?=[A-Z])/","_\$1",$class);
        $class = substr($class, 1);
        $class = strtolower($class);
        $this->table = $class;
    }

    /**
     * 创建数据
     */
    public function create($data = array(), $time = null){
        if(empty($data)){
            $data = request('post.');
        }
        if(empty($data)){
            $this->error = 'Insert data not found';
            return false;
        }
        //过滤多余字段
        $data = $this->format_data_by_fill($data);
        if(empty($data)){
            $this->error = 'Insert data not found';
            return false;
        }
        //获取限制字段
        if(!empty($this->intoData)){
            $newData = array();
            foreach ($this->intoData as $value) {
                if(isset($data[$value])){
                    $newData[$value] = $data[$value];
                }
            }
            $data = $newData;
        }
        //获取验证
        if(!$time){
            if(empty($this->primary)||empty($data[$this->primary])){
                $time = 1;
            }else{
                $time = 2;
            }
        }
        $this->data = $data;
        //验证处理数据
        if(!$this->validateData($this->_validate, $time)){
            return false;
        }
        if(!$this->autoData($this->_auto, $time)){
            return false;
        }
        return $this->data;
    }

    /**
     * 规定写入字段
     */
    public function into($field){
        if(empty($field)){
            $this->intoData = array();
            return $this;
        }
        $this->intoData = explode(',', $field);
        return $this;
    }

    /**
     * 验证数据
     */
    public function validateData($validateData = array(), $time = null){
        if(empty($validateData)){
            return true;
        }
        $data = $this->data;
        foreach ($validateData as $v) {
            //设置处理条件
            list($field,$rule,$msg,$condition,$type,$opportunity) = $v;
            if(empty($opportunity)){
                $opportunity = 3;
            }
            $value = $data[$field];
            if($opportunity == $time ||$opportunity == 3){
                if($condition == 0) {
                    //不存在字段跳过
                    if(!isset($data[$field])){
                        continue;
                    }
                }elseif($condition == 2) {
                    //值为空跳过
                    if(empty($value)){
                        continue;
                    }
                }elseif($condition == 1) {
                    //继续验证
                }
                $error = false;
                switch (strtolower($type)) {
                    case 'function':
                        //函数
                        if(call_user_func($rule, $value)){
                            $error = true;
                        }
                        break;
                    case 'callback':
                        //回调方法
                        if(call_user_func(array(&$this,$rule), $value)){
                            $error = true;
                        }
                        break;
                    case 'confirm':
                        //字段相等
                        if($value == $data[$rule]){
                            $error = true;
                        }
                        break;
                    case 'length':
                        //验证长度
                        $length  =  mb_strlen($value,'utf-8');
                       if(strpos($rule,',')) {
                            list($min,$max)   =  explode(',',$rule);
                            if($length >= $min && $length <= $max){
                                $error = true;
                            }
                        }else{
                            if($length == $rule){
                                $error = true;
                            }
                        }
                        break;
                    case 'unique':
                        //判断唯一值
                        $where = array();
                        $where[$field] = $value;
                        if($time == 2){
                            $where[] = "`{$this->primary}` <> {$data[$this->primary]}";
                        }
                        $info = $this->where($where)->find();
                        if(empty($info)){
                            $error = true;
                        }
                        break;
                    case 'regex':
                    default:
                        $validate = array(
                            'require'   =>  '/\S+/',
                            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
                            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
                            'currency'  =>  '/^\d+(\.\d+)?$/',
                            'number'    =>  '/^\d+$/',
                            'zip'       =>  '/^\d{6}$/',
                            'integer'   =>  '/^[-\+]?\d+$/',
                            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
                            'english'   =>  '/^[A-Za-z]+$/',
                        );
                        if($validate[strtolower($rule)]){
                            $rule = $validate[strtolower($rule)];
                        }
                        if(preg_match($rule,$value) === 1){
                            $error = true;
                        }
                        break;
                }
                if(!$error){
                    $this->error = $msg;
                    return false;
                }
            }
        }
        return true;
    }

    /**
	 * 自动处理录入数据
	 */
    public function autoData($autoData, $time = null){
        //获取自动处理
        $data = $this->data;
        if(empty($autoData)){
            return $data;
        }
        foreach ($autoData as $v) {            
            if($v[2] == $time || $v[2] == 3){
                switch (strtolower($v[3])) {
                    case 'function':
                        $data[$v[0]] =  call_user_func($v[1], $data[$v[0]]);
                        break;
                    case 'callback':
                        if($v[4]){
                            $data[$v[0]] =  call_user_func_array(array(&$this,$v[1]), $v[4]);
                        }else{
                            $data[$v[0]] =  call_user_func(array(&$this,$v[1]), $data[$v[0]]);
                        }
                        break;
                    case 'field':
                        $data[$v[0]] = $data[$v[1]];
                        break;
                    case 'ignore':
                        if(empty($data[$v[0]])){
                            unset($data[$v[0]]);
                        }
                        break;
                    case 'string':
                    default:
                        if(empty($data[$v[0]])){
                            $data[$v[0]] = $v[1];
                        }
                        break;
                }
            }
        }
        if(empty($data)){
            $this->error = 'Insert data not found';
            return false;
        }
        $this->data = $data;
        return true;
    }

    /**
     * 设置自动处理
     */
    public function auto($data = array()){
        $this->_auto = $data;
        return $this;
    }

    /**
     * 设置验证状态
     */
    public function validate($data = array()){
        $this->_validate = $data;
        return $this;
    }

    /**
     * 添加数据
     */
    public function add($data = array()){
        if(empty($data)){
            $data = $this->options['data'];
            if(empty($data)){
                $data = $this->data;
            }
        }
        return $this->data($data)->insert();
    }

    /**
     * 保存数据
     */
    public function save($data = array()){
        if(empty($data)){
            $data = $this->options['data'];
            if(empty($data)){
                $data = $this->data;
            }
        }
        $where = array();
        if(!empty($this->options['where'])){
            $where = $this->options['where'];
        }else{
            if(empty($this->primary)){
                $data = $this->format_data_by_fill($data);
            }
            $where[$this->primary] = $data[$this->primary];
        }
        if(empty($where)){
            throw new \Exception("Save where not found'", 500);
        }
        return $this->data($data)->where($where)->update();
    }

    /**
     * 递增字段
     */
    public function setInc($key, $value = 1){
        $where = $this->options['where'];
        $info = $this->where($where)->find();
        $data = array();
        $data[$key] =  intval($info[$key]) + intval($value);
        $this->where($where)->data($data)->update();
    }

    /**
     * 递减字段
     */
    public function setDec($key, $value = 1){
        $where = $this->options['where'];
        $info = $this->where($where)->find();
        $data = array();
        $data[$key] =  intval($info[$key]) - intval($value);
        $this->where($where)->data($data)->update();
    }

    /**
     * 统计查询
     */
    public function sum($name){

        $info = $this->field('SUM(`'.$name.'`) as num')->select();
        $num = $info[0]['num'];
        if(empty($num)){
            $num = 0;
        }
        return $num;
    }

    /**
     * 设置分页
     */
    public function page($pageSize = 10, $scope = 5){
        return $this->pager(request('request.page'),$pageSize,$scope);
    }

    /**
     * 获取错误信息
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 过滤多余写入字段
     */
    public function format_data_by_fill($data = array())
    {
        $defaultData = $this->fields_default();
        $array = array();
        if (empty($data)){
            return $array;
        }
        foreach ($defaultData as $key => $value) {
            if (isset($data[$key])) {
                $array[$key] = $data[$key];
            }
        }
        return $array;
    }
    public function fields_default()
    {
        $data = $this->getFields();
        foreach ($data as $field) {
            if($field['Key'] == 'PRI'){
                $this->primary = $field['Field'];
            }
            $fields_default[$field['Field']] = $field['Default'];
        }
        return $fields_default;
    }

}