<?php
class model{
	public $model = NULL;
	protected $db = NULL;
	protected $table = "";
	protected $ignoreTablePrefix = false;
	
	public function __construct( $database= 'DB', $force = false){
		$this->model = self::connect( config($database), $force);
		$this->db = $this->model->db;
	}
	
	static public function connect($config, $force=false){
		static $model = NULL;
		if( $force==true || empty($model) ){
			$model = new cpModel($config);
		}
		return $model;
	}
	
	public function query($sql){
		return $this->model->query($sql);
	}
	
	public function find($condition = '', $field = '', $order = ''){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->field($field)->where($condition)->order($order)->find();
	}
	
	public function select($condition = '', $field = '', $order = '', $limit = ''){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->field($field)->where($condition)->order($order)->limit($limit)->select();
	}
	
	public function count($condition = ''){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->where($condition)->count();
	}
	
	public function insert($data = array() ){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->data($data)->insert();
	}
	
	public function update($condition, $data = array() ){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->data($data)->where($condition)->update();
	}
	
	public function delete($condition){
		return $this->model->table($this->table, $this->ignoreTablePrefix)->where($condition)->delete();
	}
	
	public function getSql(){
		return $this->model->getSql();
	}
	
	public function escape($value){
		return $this->model->escape($value);
	}
	
	public function cache($time = 0){
		$this->model->cache($time);
		return $this;
	}
	
}