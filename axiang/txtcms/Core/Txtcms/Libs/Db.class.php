<?php
/**
 * TXTCMS 数据库操作类
 * @copyright			(C) 2013-2014 TXTCMS
 * @license			http://www.txtcms.com
 * @lastmodify			2014-8-29
 */
class Db extends txtSQL {
	public $options;
	public $data;
	public function __construct($db_path='',$db_name='',$db_table){
		parent::__construct();
		$this->options=array();
		$this->data=array();
		$this->_LIBPATH=$db_path;
		$this->connect();
		$this->selectdb($db_name);
		$this->table(strtolower($db_table));
		return $this;
	}
	//清除配置缓存
	private function clearOption(){
		$this->data=array();
		foreach($this->options as $k=>$vo){
			if(!in_array($k,array('table','hashtable'))) $this->options[$k]=null;
		}
	}
	//删除数据
	public function delete($options=array()){
		num('db_write_num',1);
		if(empty($options)) {
			$options=$this->options;
		}else{
			$options=array(
				'table'=>$this->options['table'],
				'where'=>$this->options['where'],
			);
			if($this->options['limit']) $options['limit']=$this->options['limit'];
		}
		$result=parent::delete($options);
		$this->clearOption();
		return $result;
	}
	//查询数据集
	public function select($options=array()) {
		num('db_query_num',1);
		if(empty($options)) {
			$options=$this->options;
		}
		$result=parent::select($options);
		$this->clearOption();
		return $result;
	}
	//查询一条数据
	public function find($options=array()){
		if(empty($options)) {
			$options=$this->options;
		}
		$options['limit']=array(1);
		$result=$this->select($options);
		if($result) return $result[0];
		return $result;
	}
	//统计表
	public function count(){
		if($this->options['where']){
			$options=array(
				'table'=>$this->options['table'],
				'where'=>$this->options['where'],
			);
			$result=$this->select($options);
			$count=count($result);
		}else{
			$count=parent::table_count($this->options['table']);
		}
		$this->clearOption();
		return $count;
	}
	//字段值增长
	public function setInc($field,$step=1){
		num('db_write_num',1);
		$options=array(
			'table'=>$this->options['table'],
			'where'=>$this->options['where'],
		);
		$data=$this->find($options);
		$step+=$data[$field];
		$options['values']=array($field=>$step);
		$result=parent::update($options);
		$this->clearOption();
		return $result;
	}
	//字段值减少
	public function setDec($field,$step=1){
		num('db_write_num',1);
		$options=array(
			'table'=>$this->options['table'],
			'where'=>$this->options['where'],
		);
		$data=$this->find($options);
		$step-=$data[$field];
		$options['values']=array($field=>$step);
		$result=parent::update($options);
		$this->clearOption();
		return $result;
	}
	//更新数据，返回影响条数
	public function save($data='') {
		num('db_write_num',1);
		if(empty($data)) {
            if(!empty($this->data)) {
                $data=$this->data;
                $this->data=array();
            }else{
                $this->error='数据为空！';
                return false;
            }
        }
		$options=array(
			'table'=>$this->options['table'],
			'where'=>$this->options['where'],
			'values'=>$data,
		);
		if($this->options['limit']) $options['limit']=$this->options['limit'];
		$result=parent::update($options);
		$this->clearOption();
		return $result;
	}
	//新增数据，返回最后ID
	public function add($data='') {
		num('db_write_num',1);
		if(empty($data)) {
            if(!empty($this->data)) {
                $data=$this->data;
                $this->data=array();
            }else{
                $this->error='数据为空！';
                return false;
            }
        }
		$result=parent::insert(array(
			'table'=>$this->options['table'],
			'values'=>$data,
		));
		if(false !== $result ) {
			$insertId=$this->last_insert_id($this->options['table']);
			if($insertId) return $insertId;
		}
		$this->clearOption();
		return $result;
	}
	//创建数据对象
	public function create($data='',$type='') {
        // 如果没有传值默认取POST数据
        if(empty($data)) {
            $data   =   $_POST;
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        // 验证数据
        if(empty($data) || !is_array($data)) {
            $this->error ='非法数据对象！';
            return false;
        }
		// 赋值当前数据对象
        $this->data =   $data;
        // 返回创建的数据以供其他调用
        return $data;
	}
	public function data($data=''){
		if('' === $data && !empty($this->data)) {
            return $this->data;
        }
        if(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            exception('非法数据对象！');
        }
        $this->data = $data;
        return $this;
	}
	//设置表名
	public function table($name=''){
		//重置配置
		$this->options=array();
		if(!empty($name)) $this->options['table']=$name;
		return $this;
	}
	//设置字段
	public function field($name=''){
		if(!empty($name)) $this->options['select']=$name;
		return $this;
	}
	public function limit($offset,$length=null){
        $this->options['limit'] =   is_null($length)?array('0',$offset):array($offset,$length);
        return $this;
    }
	//排序
	public function order($orderby=''){
		if(is_string($orderby)) {
			$order_arr=explode(',', $orderby);
			$order_arr=array_map('trim',$order_arr);
			foreach($order_arr as $k => $vo) {
				//判断是否为空防止错误
				if($vo=='') continue;
				$vo_arr = explode(' ', $vo);
				if($vo_arr[0]=='' || $vo_arr[1]=='' ) continue;
				$temp_order_arr[] = $vo_arr[0];
				$temp_order_arr[] = strtoupper($vo_arr[1]);
			}
		}
		$this->options['orderby']=$temp_order_arr;
		return $this;
	}
	//条件
	public function where($where=''){
		if(empty($where)) return $this; 
		if(is_object($where)){
            $where=get_object_vars($where);
        }elseif(is_string($where) && '' != $where){
            $where=array($where);
        }
		if(is_array($where)){
			foreach($where as $k=>$vo){
				$where[$k]=str_ireplace(array(' LIKE ',' NOT LIKE ',' IN ',' NOT IN '),array(' =~ ',' !~ ',' ? ',' !? '),$vo);
				if(!is_numeric($k)){
					$where[$k]="{$k}={$vo}";
				}
			}
			$this->options['where']=$where;
		}
		return $this;
	}
	//获取分表名并创建
	public function getHash($key,$ismake=false){
		if(!isset($this->options['hashtable'])) $this->options['hashtable']=$this->options['table'];
		$table=$this->options['hashtable'];
		$s=50;
		$hash = sprintf("%u", crc32($key));
		$hash1 = intval(fmod($hash, $s));
		$hash_table=$table.$hash1;
		$file=DB_PATH.$table.'/'.$hash_table.'.MYD';
		if($ismake && !is_file($file)) write($file,'a:0:{}');
		$this->options['table']=$hash_table;
		return $this;
	}
}