<?php

namespace App\Model;

use Swoole;

class Interfaces extends Swoole\Model {

	/**
	 * 表名
	 * @var string
	 */
	public $table = 'interface';

	public function search($param) {
		return $this->gets($param);
	}

	public function create($data) {
		return $this->put($data);
	}

	public function update($id, $data) {
		return $this->set($id, $data);
	}

	public function delete($param) {
		return $this->dels($param);
	}

}
