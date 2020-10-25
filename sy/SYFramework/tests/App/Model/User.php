<?php
namespace SyApp\Model;

use Sy\ModelAbstract;

class User extends ModelAbstract {
	protected $_table_name = 'user';
	protected $_primary_key = 'id';
}