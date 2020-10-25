<?php

namespace Component\Orm\Model;



use Component\Orm\Query\AsyncMysql;
use Component\Orm\Query\Mongodb;
use Component\Orm\Query\IQuery;
use Component\Orm\Query\Mysql;
use Kernel\AgileCore as Core;
use Kernel\Core\Conf\Config;
use Kernel\Core\Exception\ErrorCode;

class Model implements IModel
{
	protected $driver = 'pdo';
	protected $database;
	protected $table;
	/** @var IQuery */
	protected $db;
	protected $fields = '*';
	protected $configName;
	public function __construct()
	{
		/** @var $config Config */
		$core = Core::getInstance();
		$config = $core->get('config');
		$dbConfig = $config->get($this->configName);
		$this->driver = $dbConfig['driver'] ??  $this->driver;
		$this->database = $dbConfig['database'];
		$this->table = $dbConfig['table'];

		switch ($this->driver) {
			case 'pdo':
				$this->db = new Mysql();
				break;
			case 'mongodb':
				$this->db = $core->get(Mongodb::class);
				break;
            case 'async':
                $this->db = new AsyncMysql();
                break;
			default:
				throw new \InvalidArgumentException('can\'t use '. $this->driver, ErrorCode::DB_DRIVER_ERROR);
		}
	}

	public function insert(array $data): IQuery
	{
		return $this->db->insert($data, $this->database.'.'.$this->table);
	}

	public function update(array $data): IQuery
	{
		return $this->db->update($data)->from($this->database.'.'.$this->table);
	}

	public function delete(array $data = []): IQuery
	{
		if(empty($data)) {
			return $this->db->delete()->from($this->database.'.'.$this->table);
		}
		return $this->db->delete($data)->from($this->database.'.'.$this->table);
	}

	public function select(string $fields = ''): IQuery
	{
		if(!empty($fields)) {
			$this->fields = $fields;
		}
		return $this->db->select($this->fields);
	}

}