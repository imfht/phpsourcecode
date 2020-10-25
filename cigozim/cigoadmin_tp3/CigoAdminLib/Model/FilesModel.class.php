<?php

namespace CigoAdminLib\Model;

use Think\Model;

class FilesModel extends Model {
	protected $_auto = array(
		array('status', 1, Model::MODEL_INSERT, 'string'),
		array('create_time', 'time', Model::MODEL_INSERT, 'function')
	);

}
