<?php
namespace Modules\Taxonomy\Models;

use Phalcon\Mvc\Model;

class Field extends Model {
	// 获取制定术语下所有子术语
	public static $tableName = 'Field';
	public function getSource() {
		return self::$tableName;
	}
}