<?php
namespace Component\Orm\Connection;

use Kernel\Core\Conf\Config;
use Kernel\Core\IComponent\IConnection;

class Mysql extends \PDO implements IConnection
{
    use HashCode;
    use Free;
	public function __construct(Config $config)
	{
	    $config = $config->get('mysql');
		$dsn = 'mysql:host='.$config['host'].';';
		$user = $config['user'];
		$password = $config['password'];
		$options = $config['options'] ?? [];
		if(strpos(strtolower($dsn), 'charset=')!==false) {
			preg_match('/charset=([a-z0-9-]+)/i', $dsn, $match);
			$charset = isset($match[1]) ? $match[1] : 'utf8';
		} else {
			$charset = isset($options['charset']) ? $options['charset'] : 'utf8';
			$dsn    .= (substr($dsn, -1)===';' ? '' : ';')."charset={$charset}";
		}
        //var_dump($dsn, $user, $options);
		try {
			//PDO::ATTR_PERSISTENT  长连接
			parent::__construct($dsn, $user, $password, array(\PDO::ATTR_PERSISTENT => true));
		} catch (\Exception $e) {
			throw new \InvalidArgumentException('Connection failed: '.$e->getMessage(), $e->getCode());
		}

		$this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

		$timezone = isset($options['timezone']) ? $options['timezone'] : '+00:00';
		$this->exec("SET time_zone='{$timezone}'");
		$this->exec("SET NAMES '{$charset}'");
        $this->HashCode();
	}

}