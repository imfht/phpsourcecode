<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class InstallModelDatabase extends InstallAbstractModel
{
	/**
	 * Check database configuration and try a connection
	 *
	 * @param string $server
	 * @param string $database
	 * @param string $login
	 * @param string $password
	 * @param string $prefix
	 * @param string $engine
	 * @param bool $clear
	 * @return array List of errors
	 */
	public function testDatabaseSettings($server, $database, $login, $password, $prefix, $engine, $clear = false)
	{
		$errors = array();

		// Check if fields are correctly typed
		if (!$server || !Validate::isUrl($server))
			$errors[] = $this->language->l('Server name is not valid');

		if (!$database)
			$errors[] = $this->language->l('You must enter a database name');

		if (!$login)
			$errors[] = $this->language->l('You must enter a database login');

		if ($prefix && !Validate::isTablePrefix($prefix))
			$errors[] = $this->language->l('Tables prefix is invalid');

		if (!Validate::isMySQLEngine($engine))
			$errors[] = $this->language->l('Wrong engine chosen for MySQL');

		if (!$errors)
		{
			$dbtype = ' ('.Db::getClass().')';
			// Try to connect to database
			switch (Db::checkConnection($server, $login, $password, $database, true, $engine))
			{
				case 0:
					if (!Db::checkEncoding($server, $login, $password))
						$errors[] = $this->language->l('Cannot convert database data to utf-8').$dbtype;

					// Check if a table with same prefix already exists
					if (!$clear && Db::hasTableWithSamePrefix($server, $login, $password, $database, $prefix))
						$errors[] = $this->language->l('At least one table with same prefix was already found, please change your prefix or drop your database');
				break;

				case 1:
					$errors[] = $this->language->l('Database Server is not found. Please verify the login, password and server fields').$dbtype;
				break;

				case 2:
					$errors[] = $this->language->l('Connection to MySQL server succeeded, but database "%s" not found', $database).$dbtype;
				break;

				case 4:
					$errors[] = $this->language->l('Engine innoDB is not supported by your MySQL server, please use MyISAM').$dbtype;
				break;
			}
		}

		return $errors;
	}
}
