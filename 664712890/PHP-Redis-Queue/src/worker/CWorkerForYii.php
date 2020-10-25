<?php

class CWorkerForYii extends CWorker{
	protected $model;
	
	protected function getRedisConnection() {
		if(empty($this->share))
			throw new CException('Please set share first! Like: $worker->setShare(1)');
		
		$this->redis == null && 
			$this->redis = CRedisQueue::instance()->setSharded($this->share, CRedisConfig::instance()->queueSherdedKey);
		
		return $this->redis;
	}
	
	protected function initData() {
		$table = explode("_", $this->data['table']);
		$modelName = implode("", array_map("ucfirst", $table));

		$this->model = isset($this->data['shardValue']) && is_subclass_of($modelName, 'CShardedActiveRecord')
			? $modelName::model($this->data['shardValue']) : $modelName::model();
	}
	
	/**
	 * 删除记录
	 */
	protected function deleteRecord() {
		$pk = $this->data['pk'];
		if(empty($pk)) return array(false, 'Missing primary key!');
		
		!is_array($pk) && $pk = array($pk);
		$criteria = new CDbCriteria;
		foreach($pk as $v) {
			$criteria->compare("`$v`", $this->data['data'][$v]);
		}
		
		$line = $this->model->deleteAll($criteria);
		return array($line, 'Nothing delete.');
	}
	
	/**
	 * 更新记录
	 */
	protected function updateRecord() {
		$pk = $this->data['pk'];
		if(empty($pk)) return array(false, 'Missing primary key!');
		
		$criteria = new CDbCriteria;

		!is_array($pk) && $pk = array($pk);
		foreach($pk as $v) {
			$criteria->compare($v, $this->data['data'][$v]);
		}

		CDbManager::beginMaster();
		$result = $this->model->find($criteria);
		CDbManager::finishMaster();
		
		if(empty($result)) return false;
		$this->setAttributes($result);
		
		return $this->saveModel($result);
	}
	
	/**
	 * 插入记录
	 */
	protected function insertRecord() {
		$this->model->unsetAttributes();
		
		$this->setAttributes();
		$this->model->isNewRecord = true;
		return $this->saveModel();
	}
	
	/**
	 * 设置字段
	 */
	protected function setAttributes($model = null) {
		!$model && $model = &$this->model;
		
		$model->attributes = array_intersect_key($this->data['data'], $model->attributes);
		$diff = array_diff_key($this->data['data'], $model->attributes);
		foreach($diff as $key => $val) {
			if(property_exists($model, $key))
				$model->{$key} = $val;
		}
	}
	
	/**
	 * 保存数据
	 * @param unknown_type $model
	 */
	protected function saveModel($model = null) {
		!$model && $model = $this->model;
		$res = array(false, '');
		try{
			$save = $model->save();
			$res = array($save, $model->getErrors());
		} catch(Exception $e) {
			$res = array(false, $e->getMessage());
		}
		return $res;
	}
	
	public function pop($key = null, $blockTime = 1) {
		return $this->getRedisConnection()->bpop();
	}
}