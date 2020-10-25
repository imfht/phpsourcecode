<?php
/**
 * TXTCMS txt数据库类
 * 对txtsql原版进行了多处修正和优化
 * @copyright (C) 2013-2014 TXTCMS
 * @license http://www.txtcms.com
 * @lastmodify 2014-8-8
 */
abstract class txtSQL {
	public $_STRICT = true;
	public $_LIBPATH = null;
	public $_USER = null;
	public $_PASS = null;
	public $_CACHE = array();
	public $_SELECTEDDB = null;
	public $_QUERYCOUNT = 0;
	public $hash_db = array();
	function __construct($path = '') {
		$this -> _LIBPATH = $path;
		$this->hash_db=explode(',',config('DB_HASH_LIST'));
		return true;
	}
	function _isselect() {
		if (empty($this -> _SELECTEDDB)) {
			$this -> _error(E_USER_NOTICE, 'No database selected');
			return false;
		} 
	} 
	function _checkdb($db) {
		if (!empty($db)) {
			if (!$this -> selectdb($db)) {
				return false;
			} 
		} 
	} 
	function _isemptytable($table) {
		if (empty($table)) {
			$this -> _error(E_USER_NOTICE, 'No table specified');
			return false;
		} 
	} 
	function _check_table_file($table) {
		if (is_array($table)) {
			foreach($table as $vo) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$vo";
				$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $vo);
				if (in_array($table_frm, $this->hash_db)) {
					$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$vo";
				}
				if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
					$this -> _error(E_USER_NOTICE, 'Table ' . $vo . ' doesn\'t exist');
					return false;
				} 
			} 
		} else {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
			$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table);
			if (in_array($table_frm, $this->hash_db)) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$table";
			} 
			if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
				$this -> _error(E_USER_NOTICE, 'Table ' . $table . ' doesn\'t exist');
				return false;
			} 
		} 
	} 
	function _checkdblock() {
		if ($this -> isLocked($this -> _SELECTEDDB)) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $this -> _SELECTEDDB . ' is locked');
			return false;
		} 
	} 
	/**
	 * Connects a user to the txtSQL service
	 * 
	 * @param string $user The username of the user
	 * @param string $pass The corressponding password of the user
	 * @return void 
	 * @access public 
	 */
	function connect ($user = '', $pass = '') {
		/**
		 * Check to see if our data exists
		 */
		if (!is_dir($this -> _LIBPATH)) {
			$this -> _error(E_USER_ERROR, '数据库路径不存在');
		} 
		return true;
	} 

	/**
	 * Disconnects a user from the txtSQL Service
	 * 
	 * @return void 
	 * @access public 
	 */
	function disconnect () {
		return false;
	} 

	/**
	 * Selects rows of information from a selected database and a table
	 * that fits the given 'where' clause
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'select', 'where', 'limit'
	 *                          and 'orderby'
	 * @return mixed $results An array that txtSQL returns that matches the given criteria
	 * @access public 
	 */
	function select ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _select($arguments);
	} 

	/**
	 * Inserts a new row into a table with the given information
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'values'
	 * @return int $inserted The number of rows inserted into the table
	 * @access public 
	 */
	function insert ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this ->_insert($arguments);
	} 

	/**
	 * Updates a row that matches a 'where' clause, with new information
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'where', 'limit',
	 *                          and 'values'
	 * @return int $inserted The number of rows updated
	 * @access public 
	 */
	function update ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _update($arguments);
	} 

	/**
	 * Deletes a row from a table that matches a 'where' clause
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'where', 'limit'
	 * @return int $inserted The number of rows deleted
	 * @access public 
	 */
	function delete ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _delete($arguments);
	} 

	/**
	 * Returns a list containing the current valid txtSQL databases
	 * 
	 * @return mixed $databases A list containing the databases
	 * @access public 
	 */
	function showdbs () {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate(array());
		$this -> _QUERYCOUNT++;

		return $this -> _showdatabases();
	} 

	/**
	 * Creates a new database
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db'
	 * @return void 
	 * @access public 
	 */
	function createdb ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _createdatabase($arguments);
	} 

	/**
	 * Drops a database
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db'
	 * @return void 
	 * @access public 
	 */
	function dropdb ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _dropdatabase($arguments);
	} 

	/**
	 * Renames a database
	 * 
	 * @param mixed $arguments The arguments in form of "[old db name], [new db name]"
	 * @return void 
	 * @access public 
	 */
	function renamedb ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _renamedatabase($arguments);
	} 

	/**
	 * Returns an array containing a list of tables inside of a database
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db'
	 * @return mixed $tables   An array with a list of tables
	 * @access public 
	 */
	function showtables ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _showtables($arguments);
	} 

	function getFields($table, $name = '') {
		$this -> _isemptytable($table);
		$col = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table}.FRM");
		unset($col['primary']);
		return array_keys($col);
	} 
	/**
	 * Creates a new table with the given criteria inside a database
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'columns'
	 * @return int $deleted The number of rows deleted
	 * @access public 
	 */
	function createtable ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _createtable($arguments);
	} 

	/**
	 * Drops a table from a database
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *   where $key can be 'db', 'table'
	 * @return void 
	 * @access public 
	 */
	function droptable ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _droptable($arguments);
	} 

	/**
	 * Alters a database by working with its columns
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table', 'action',
	 *                          'name', and 'values'
	 * @return void 
	 * @access public 
	 */
	function altertable ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _altertable($arguments);
	} 

	/**
	 * Returns a description of a table using an array
	 * 
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                          where $key can be 'db', 'table'
	 * @return int $columns An array with the description of a table
	 * @access public 
	 */
	function describe ($arguments) {
		/**
		 * Check for a connection, and valid arguments
		 */
		$this -> _validate($arguments);
		$this -> _QUERYCOUNT++;

		return $this -> _describe($arguments);
	} 

	/**
	 * Checks for a connection, and valid arguments
	 * 
	 * @param mixed $arguments The arguments to validify
	 * @return void 
	 * @access private 
	 */
	function _validate ($arguments) {
		/**
		 * Check to see user is connected
		 */
		if (!$this -> _isconnected()) {
			$this -> _error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return false;
		} 

		/**
		 * Arguments have to be inside of an array
		 */
		if (!empty($arguments) && !is_array($arguments)) {
			$this -> _error(E_USER_ERROR, 'can only accept arguments in an array');
		} 

		return true;
	} 

	/**
	 * Evaluates a query with manually inputted arguments.
	 * The $action can be either 'show databases', 'create databases', 'drop database', 'rename database'
	 * 'show tables', 'create table', 'drop table', 'alter table', 'describe', 'select', 'insert', 'delete',
	 * and 'insert'. See the readme for more information.
	 * 
	 * @param string $action The command txtSQL is to perform
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 * @return mixed $results The results that txtSQL returned
	 * @access public 
	 */
	function execute ($action, $arguments = null) {
		/**
		 * Check to see user is connected
		 */
		if (!$this -> _isconnected()) {
			$this -> _error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return false;
		} 

		/**
		 * If there is no action
		 */
		if (empty($action)) {
			$this -> _error(E_USER_NOTICE, 'You have an error in your SQL query');
			return false;
		} 

		/**
		 * Arguments have to be inside of an array
		 */
		if (!empty($arguments) && !is_array($arguments)) {
			$this -> _error(E_USER_ERROR, 'SQL Can only accept arguments in an array');
		} 

		/**
		 * Depending on what type of action it is, then perform right query
		 */
		switch (strtolower($action)) {
			/**
			 * ----- Database Related -----
			 */
			case 'show databases':
				$results = $this -> _showdatabases();
				break;
			case 'create database':
				$results = $this -> _createdatabase($arguments);
				break;
			case 'drop database':
				$results = $this -> _dropdatabase($arguments);
				break;
			case 'rename database':
				$results = $this -> _renamedatabase($arguments);
				break;

			/**
			 * ----- Table Related -----
			 */
			case 'show tables':
				$results = $this -> _showtables($arguments);
				break;
			case 'create table':
				$results = $this -> _createtable($arguments);
				break;
			case 'drop table':
				$results = $this -> _droptable($arguments);
				break;
			case 'alter table':
				$results = $this -> _altertable($arguments);
				break;
			case 'describe':
				$results = $this -> _describe($arguments);
				break;

			/**
			 * ----- Main functions -----
			 */
			case 'select':
				$results = $this -> _select($arguments);
				break;
			case 'insert':
				$results = $this -> _insert($arguments);
				break;
			case 'update':
				$results = $this -> _update($arguments);
				break;
			case 'delete':
				$results = $this -> _delete($arguments);
				break;

			default:
				$this -> _error(E_USER_NOTICE, 'Unknown action: ' . $action);
				return false;
		} 

		/**
		 * Return whatever results we got back
		 */
		$this -> _QUERYCOUNT++;
		return isset($results) ? $results : '';
	} 

	/**
	 * Turns strict property of txtSQL off/on
	 * 
	 * @param bool $strict The value of the strict property
	 * @return void 
	 * @access public 
	 */
	function strict ($strict = false) {
		$strict = (bool) $strict;
		$this -> _STRICT = $strict;

		if ($this -> _isconnected()) {
			$this -> _strict($strict);
		} 
		return true;
	} 

	/**
	 * To set username and/or passwords, or create/delete users
	 * 
	 * @param string $action The action to perform (add, drop, edit)
	 * @param string $user The username to be added/modified
	 * @param string $pass The password of the username
	 * @param string $pass1 The new password of the username (optional if editing)
	 * @return void 
	 * @access public 
	 */
	function grant_permissions($action, $user, $pass = null, $pass1 = null) {
		/**
		 * Are we connected?
		 */
		if (!$this -> _isconnected()) {
			$this -> _error(E_USER_NOTICE, 'Not connected');
			return false;
		} 

		/**
		 * Can only work with strings
		 */
		if (!is_string($action) || !is_string($user) || (!empty($pass) && !is_string($pass)) || (!empty($pass1) && !is_string($pass1))) {
			$this -> _error(E_USER_NOTICE, 'The arguments must be a string');
			return false;
		} 

		/**
		 * Read in user database
		 */
		if (($DATA = $this -> _readFile("$this->_LIBPATH/txtsql/user.MYI")) === false) {
			$this -> _error(E_USER_WARNING, 'Database file is corrupted!');
			return false;
		} 

		/**
		 * Need a username
		 */
		if (empty($user)) {
			$this -> _error(E_USER_NOTICE, 'Forgot to input username');
			return false;
		} 

		/**
		 * Perform the correct operation
		 */
		switch (strtolower($action)) {
			case 'add':
				if (isset($DATA[strtolower($user)])) {
					$this -> _error(E_USER_NOTICE, 'User already exists');
					return false;
				} 
				$DATA[strtolower($user)] = md5($pass);
				break;
			case 'drop':
				if (strtolower($user) == strtolower($this -> _USER)) {
					$this -> _error(E_USER_NOTICE, 'Can\'t drop yourself');
					return false;
				} elseif (strtolower($user) == 'root') {
					$this -> _error(E_USER_NOTICE, 'Can\'t drop user root');
					return false;
				} elseif (!isset($DATA[strtolower($user)])) {
					$this -> _error(E_USER_NOTICE, 'User doesn\'t exist');
					return false;
				} elseif (md5($pass) != $DATA[strtolower($user)]) {
					$this -> _error(E_USER_NOTICE, 'Incorrect password');
					return false;
				} 
				unset($DATA[strtolower($user)]);
				break;
			case 'edit':
				if (!isset($DATA[strtolower($user)])) {
					$this -> _error(E_USER_NOTICE, 'User doesn\'t exist');
					return false;
				} 
				if (md5($pass) != $DATA[strtolower($user)]) {
					$this -> _error(E_USER_NOTICE, 'Incorrect password');
					return false;
				} 
				$DATA[strtolower($user)] = md5($pass1);
				break;
			default: $this -> _error(E_USER_NOTICE, 'Invalid action specified');
				return false;
		} 

		/**
		 * Save the new information
		 */
		$fp = @fopen("$this->_LIBPATH/txtsql/user.MYI", 'w') or $this -> _error(E_USER_FATAL, "Couldn't open $this->_LIBPATH/txtsql/user.MYI for writing");
		@flock($fp, LOCK_EX);
		@fwrite($fp, serialize($DATA)) or $this -> _error(E_USER_FATAL, "Couldn't write to $this->_LIBPATH/txtsql/user.MYI");
		@flock($fp, LOCK_UN);
		@fclose($fp) or $this -> _error(E_USER_NOTICE, "Error closing $this->_LIBPATH/txtsql/user.MYI");

		/**
		 * Save it in the cache
		 */
		$this -> _CACHE["$this->_LIBPATH/txtsql/user.MYI"] = $DATA;
		return true;
	} 

	/**
	 * Returns an array filled with a list of current txtSQL users
	 * 
	 * @return mixed $users
	 * @access public 
	 */
	function getUsers () {
		/**
		 * Are we connected?
		 */
		if (!$this -> _isconnected()) {
			$this -> _error(E_USER_NOTICE, 'Not connected');
			return false;
		} 

		/**
		 * Read in user database
		 */
		if (($DATA = $this -> _readFile("$this->_LIBPATH/txtsql/user.MYI")) === false) {
			$this -> _error(E_USER_WARNING, 'Database file is corrupted!');
			return false;
		} 

		$users = array();
		foreach ($DATA as $key => $value) {
			$users[] = $key;
		} 
		return $users;
	} 

	/**
	 * Check whether a database is locked or not
	 * 
	 * @param string $db The database to check
	 * @return bool $locked Whether it is locked or not
	 * @access public 
	 */
	function isLocked ($db) {
		if (!$this -> _dbexist($db)) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $db . ' doesn\'t exist');
			return false;
		} 
		return is_file("$this->_LIBPATH/$db/txtsql.lock") ? true : false;
	} 

	/**
	 * To put a file lock on the database
	 * 
	 * @param string $db The database to have a file lock placed on
	 * @return void 
	 * @access public 
	 */
	function lockdb ($db) {
		/**
		 * Make sure that the user is connected
		 */
		if (!$this -> _isConnected()) {
			$this -> _error(E_USER_NOTICE, 'You must be connected');
			return false;
		} elseif ($this -> isLocked($db)) {
			$this -> _error(E_USER_NOTICE, 'Lock for database ' . $db . ' already exists');
			return false;
		} 

		$fp = fopen("$this->_LIBPATH/$db/txtsql.lock", 'a') or $this -> _error(E_USER_ERROR, 'Err1or creating a lock for database ' . $db);
		fclose($fp) or $this -> _error(E_USER_ERROR, 'Error creating a lock for database ' . $db);

		return true;
	} 

	/**
	 * To remove a file lock from the database
	 * 
	 * @param string $db The database to have a file lock removed from
	 * @return void 
	 * @access public 
	 */
	function unlockdb ($db) {
		/**
		 * Make sure that the user is connected
		 */
		if (!$this -> _isConnected()) {
			$this -> _error(E_USER_NOTICE, 'You must be connected');
			return false;
		} elseif (!$this -> isLocked($db)) {
			$this -> _error(E_USER_NOTICE, 'Lock for database ' . $db . ' doesn\'t exist');
			return false;
		} 

		if (!@unlink("$this->_LIBPATH/$db/txtsql.lock")) {
			$this -> _error(E_USER_ERROR, 'Error removing lock for database ' . $db);
		} 
		return true;
	} 

	/**
	 * To select a database for txtsql to use as a default
	 * 
	 * @param string $db The name of the database that is to be selected
	 * @return void 
	 * @access public 
	 */
	function selectdb ($db) {
		/**
		 * Valid db name?
		 */
		if (empty($db)) {
			$this -> _error(E_USER_NOTICE, 'Cannot select database ' . $db);
			return false;
		} 

		/**
		 * Does it exist?
		 */
		if (!$this -> _dbexist($db)) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $db . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Select the database
		 */
		$this -> _SELECTEDDB = $db;
		return true;
	} 

	/**
	 * An alias (but public) of the private function _tableexist()
	 * 
	 * @param  $table Table to be checked for existence
	 * @param  $db The database the table is in
	 * @return bool Whether it exists or not
	 */
	function table_exists ($table, $db) {
		return $this -> _tableexist($table, $db);
	} 

	/**
	 * An alias (public) of the private function _dbexist()
	 * 
	 * @param  $table DB to be checked for existence
	 * @return bool Whether it exists or not
	 */
	function db_exists ($db) {
		return $this -> _dbexist($db);
	} 

	/**
	 * To retrieve the number of records inside of a table
	 * 
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return int $count The number of records in the table
	 * @access public 
	 */
	function table_count ($table, $database = null) {
		/**
		 * Inside of another database?
		 */
		if (!empty($database)) {
			if (!$this -> selectdb($database)) {
				return false;
			} 
		} 

		/**
		 * No database or no table specified means that we stop here
		 */
		if (empty($this -> _SELECTEDDB) || empty($table)) {
			$this -> _error(E_USER_NOTICE, 'No database selected');
			return false;
		} 

		/**
		 * Does table exist?
		 */

		$this -> _check_table_file($table);

		/**
		 * Read in the table's records
		 */
		$count = 0;
		if (is_array($table)) {
			foreach($table as $vo) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$vo";
				$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $vo);
				if (in_array($table_frm, $this->hash_db)) {
					$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$vo";
				} 
				if (($rows = @file_get_contents($filename . '.MYD')) === false) {
					$this -> _error(E_USER_NOTICE, 'Table ' . $vo . ' doesn\'t exist');
					return false;
				} 
				$count += substr($rows, 2, strpos($rows, '{') - 3);
			} 
		} else {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
			$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table);
			if (in_array($table_frm, $this->hash_db)) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$table";
			} 
			if (($rows = @file_get_contents($filename . '.MYD')) === false) {
				$this -> _error(E_USER_NOTICE, 'Table ' . $table . ' doesn\'t exist');
				return false;
			} 
			$count += substr($rows, 2, strpos($rows, '{') - 3);
		} 
		/**
		 * Return the count
		 */
		return $count;
	} 

	/**
	 * To retrieve the last ID generated by an auto_increment field in a table
	 * 
	 * @param string $table The name of the table
	 * @param string $db The database the table is inside of (optional)
	 * @return string $column Get the last ID generated by this column instead of the priamry key (optional)
	 * @access public 
	 */
	function last_insert_id($table, $db = '', $column = '') {
		/**
		 * Select a database if one is given
		 */
		if (!empty($db)) {
			if (!$this -> selectdb($db)) {
				return false;
			} 
		} 

		/**
		 * Check for a selected database
		 */
		$this -> _isselect();

		/**
		 * Read in the column definitions
		 */
		if (is_array($table)) $table = $table[0];
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table);
		if (($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", false)) === false) {
			$this -> _error(E_USER_NOTICE, 'Table "' . $table . '" doesn\'t exist');
			return false;
		} 

		/**
		 * Check for a valid column that is auto_increment
		 */
		if (!empty($column)) {
			if ($this -> _getColPos($column, $cols) === false) {
				$this -> _error(E_USER_NOTICE, 'Column ' . $column . ' doesn\'t exist');
				return false;
			} elseif ($cols[$column]['auto_increment'] != 1) {
				$this -> _error(E_USER_NOTICE, 'Column ' . $column . ' is not an auto_increment field');
				return false;
			} 

			$cols['primary'] = $column;
		} 

		/**
		 * If we are using the primary key, make sure it exists
		 */
		elseif (empty($cols['primary']) && empty($column)) {
			$this -> _error(E_USER_NOTICE, 'There is no primary key defined for table "' . $table . '"');
			return false;
		} 

		return $cols[$cols['primary']]['autocount'];
	} 

	/**
	 * To return the number of queries sent to txtSQL
	 * 
	 * @return int $_QUERYCOUNT
	 * @access public 
	 */
	function query_count() {
		return $this -> _QUERYCOUNT;
	} 

	/**
	 * To print the last error that occurred
	 * 
	 * @return void 
	 * @access public 
	 */
	function last_error() {
		if (!empty($this -> _ERRORS)) {
			print '<pre>' . $this -> _ERRORSPLAIN[count($this -> _ERRORS)-1] . '</pre>';
		} 
	} 

	/**
	 * To return the last error that occurred
	 * 
	 * @return string $error The last error
	 * @access public 
	 */
	function get_last_error() {
		if (!empty($this -> _ERRORS)) {
			return $this -> _ERRORSPLAIN[count($this -> _ERRORS)-1];
		} 
	} 

	/**
	 * To print any errors that occurred during script execution so far
	 * 
	 * @return void 
	 * @access public 
	 */
	function errordump() {
		/**
		 * No errors?
		 */
		if (empty($this -> _ERRORS)) {
			echo 'No errors occurred during script execution';
			return true;
		} 

		/**
		 * Errors during this part of script
		 */
		if (!empty($this -> _ERRORS)) {
			foreach ($this -> _ERRORS as $key => $value) {
				echo 'ERROR #[' . $key . '] ' . $value;
			} 
		}

		return true;
	} 

	/**
	 * Removes any cache that is being stored
	 * 
	 * @return void 
	 * @access public 
	 */
	function emptyCache() {
		$this -> _CACHE = array();
		return true;
	} 
	// PRIVATE FUNCTIONS //////////////////////////////////////////////////////////////////////////////////////
	/**
	 * To retrieve the number of records inside of a table
	 * 
	 * @param int $errno The error type (number form)
	 * @param string $errstr The error message that will be shown
	 * @param string $errtype Prints this string before the message
	 * @return void 
	 * @access private 
	 */
	function _error ($errno, $errstr, $errtype = null) {
		/**
		 * If this error is not an internal error, then generate a backtrace
		 * to the line that originally caused the error
		 */
		$backtrace = array_reverse(@debug_backtrace());
		$errfile = $backtrace[0]['file'];
		$errline = $backtrace[0]['line'];

		/**
		 * Determine what kind of error this is, so we can display it.
		 */
		switch ($errno) {
			case E_USER_ERROR:
				$type = '致命错误';
				break;
			case E_USER_NOTICE:
				$type = "警告";
				break;
			default:
				$type = "错误";
				break;
		} 
		$type = isset($errtype) ? $errtype : $type;

		/**
		 * Print the message to the screen, if strict is on
		 */
		$this -> _ERRORSPLAIN[] = $errstr;
		$errfile = $errfile;
		$errormsg = "<style>.message{font-family: 'Microsoft Yahei', Verdana, arial, sans-serif;font-size:14px;padding:1em;border:solid 1px #000;margin:10px 0;background:#FFD;line-height:150%;background:#FFD;color:#2E2E2E;border:1px solid #E0E0E0;}.red{color:red;font-weight:bold;}</style><p class='message'><strong>$type: </strong>$errstr<!-- <br>FILE: <span class='red'>$errfile</span>	LINE: <span class='red'>$errline</span> --></p>";
		$this -> _ERRORS[] = $errormsg;
		if ($this -> _STRICT === true) {
			echo $errormsg;
		} 

		/**
		 * If this is a fatal error, then we are forced to exit and stop execution
		 */
		if ($errno == E_USER_ERROR) {
			exit;
		} 
		return true;
	} 

	/**
	 * To Read a file into a string and return it
	 * 
	 * @param string $filename The path to the file needed to be opened
	 * @param bool $useCache Whether to save/retrieve this file from a cache
	 * @param bool $unserialize Whether to unserialize the string or not
	 * @return string $contents The file's contents
	 * @access private 
	 */
	function _readFile ($filename, $useCache = true, $unserialize = true) {
		if (is_file($filename)) {
			if ($useCache === true) {
				if (isset($this -> _CACHE[$filename])) {
					return $this -> _CACHE[$filename];
				} 
			} 
			if (($contents = @file_get_contents($filename)) !== false) {
				if ($unserialize === true) {
					if (($contents = @unserialize($contents)) === false) {
						return false;
					} 
				} 
				if ($useCache === true) {
					$this -> _CACHE[$filename] = $contents;
				} 
				return $contents;
			} 
		} 
		return false;
	} 

	/**
	 * Check to see whether a user is connected or not
	 * 
	 * @return bool $connected Whether the user is connected or not
	 * @access private 
	 */
	function _isconnected () {
		return true;
	} 

	/**
	 * To check whether a database exists or not
	 * 
	 * @param string $db The name of the database
	 * @return bool Whether the db exists or not
	 * @access private 
	 */
	function _dbexist ($db) {
		return is_dir("$this->_LIBPATH/$db") ? true : false;
	} 

	/**
	 * To check whether a table exists or not
	 * 
	 * @param string $table The name of the table
	 * @param string $db The name of the database the table is in
	 * @return bool Whether the db exists or not
	 * @access private 
	 */
	function _tableexist ($table, $db) {
		/**
		 * Check to see if the database exists
		 */
		if (!empty($db)) {
			if (!$this -> selectdb($db)) {
				$this -> _error(E_USER_NOTICE, 'Database, \'' . $db . '\', doesn\'t exist');
				return false;
			} 
		} 

		/**
		 * Check to see if the table exists
		 */
		if (is_array($table)) {
			foreach($table as $vo) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$vo";
				$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $vo);
				if (in_array($table_frm, $this->hash_db)) {
					$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$vo";
				} 
				if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
					return false;
				} 
			} 
		} else {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
			$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table);
			if (in_array($table_frm, $this->hash_db)) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$table";
			} 
			if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
				return false;
			} 
		} 
		return true;
	} 

	/**
	 * To build an if-statement which can be used to see if a row
	 * fits the given credentials
	 * 
	 * @param mixed $where The array containing the where clause
	 * @param mixed $cols The array containing the column definitions
	 * @return string $query The string which contains the php-equivelent to the where clause
	 * @access private 
	 */
	public function _where_query($where, $cols) {
		$query = '';

		/**
		 * Start creating the query
		 */
		foreach ($where as $key => $value) {
			/**
			 * Are we on an 'and|or'?
			 */
			if ($key % 2 == 1) {
				/**
				 * Check for a valid "and|or"
				 */
				$and = strtolower($value) == 'and';
				$or = strtolower($value) == 'or';
				$xor = strtolower($value) == 'xor';
				if ($and === false && $or === false && $xor === false) {
					$this -> _error(E_USER_NOTICE, 'Only boolean seperators AND, and OR are allowed');
					return false;
				} 
				$query .= ($and === true) ? ' && ' : (($xor === true) ? ' XOR ' : ' || ');
				continue;
			} 

			/**
			 * Find out which operator we are going to use to create the if
			 * NOTE: I'm pretty sure the order in which these operators are checked
			 *        are correct. If anyone notices a bug in the order, let me know
			 */
			$f1 = '(';
			$f2 = ') ';
			switch (true) {
				case strpos($value, '!='): $type = 1;
					$op = '!=';
					break;
				case strpos($value, '!~'): $type = 3;
					$op = '!~';
					break;
				case strpos($value, '=~'): $type = 3;
					$op = '=~';
					break;
				case strpos($value, '<='): $type = 2;
					$op = '<=';
					break;
				case strpos($value, '>='): $type = 2;
					$op = '>=';
					break;
				case strpos($value, '='): $type = 1;
					$op = '=';
					break;
				case strpos($value, '<>'): $type = 1;
					$op = '<>';
					break;
				case strpos($value, '<'): $type = 2;
					$op = '<';
					break;
				case strpos($value, '>'): $type = 2;
					$op = '>';
					break;
				case strpos($value, '!?'): $type = 5;
					$op = '!?';
					break;
				case strpos($value, '?'): $type = 5;
					$op = '?';
					break;
				default:
					/**
					 * Check for a valid function that requires no operator
					 */
					$val = 'TRUE';
					if (substr(trim($value), 0, 1) == '!') {
						$val = 'FALSE';
						$value = substr($value, strpos($value, '!') + 1);
					} 

					$function = substr($value, 0, strpos($value, '('));
					$col = substr($value, strlen($function) + 1, strlen($value) - strlen($function) - 2);

					if ($function !== false) {
						$type = 4;
						$op = '===';
						$f1 = $function . '(';
						/**
						 * switch ( strtolower($function) )
						 * {
						 * case 'isnumeric':  $f1 = 'is_numeric('; break 2;
						 * case 'isstring':   $f1 = 'is_string('; break 2;
						 * case 'isfile':     $f1 = 'is_file('; break 2;
						 * case 'isdir':      $f1 = 'is_dir('; break 2;
						 * case 'iswritable': $f1 = 'is_writable(';  break 2;
						 * case 'empty':      $f1 = 'empty(';  break 2;
						 * }
						 */
					} 
					/**
					 * There is an error in your where clause
					 */
					// $this->_error(E_USER_NOTICE, 'You have an error in your where clause, (operators allowed: =, !=, <>, =~, !~, <, >, <=, >=)'); return FALSE;
			} 

			/**
			 * Split string by the proper operator, as long as there is an operator
			 */
			if (!isset($function)) {
				list ($col, $val) = explode($op, $value, 2);
			} 
			$val = trim($val, '"');
			$val = trim($val, '\'');
			/**
			 * Check to see if we are utilizing a function
			 */
			if (substr_count($col, '(') == 1 && substr_count($col, ')') == 1) {
				$function = substr($col, 0, strpos($col, '('));

				if ($val != '' && $col{strlen($col)-1} . $val{0} == "  ") {
					$col = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - (($col{strlen($col)-1} != " ") ? 2 : 3)) . " ";
					$val = $val;
				} else {
					$col = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - (($col{strlen($col)-1} != " ") ? 2 : 3));
				} 

				/**
				 * Check for a valid function call
				 */
				switch (strtolower($function)) {
					case 'strlower': $f1 = 'strtolower(';
						break;
					case 'strupper': $f1 = 'strtoupper(';
						break;
					case 'chop':
					case 'rtrim': $f1 = 'rtrim(';
						break;
					case 'ltrim': $f1 = 'ltrim(';
						break;
					case 'trim': $f1 = 'trim(';
						break;
					case 'md5': $f1 = 'md5(';
						break;
					case 'stripslash': $f1 = 'stripslashes(';
						break;
					case 'strlength': $f1 = 'strlen(';
						break;
					case 'strreverse': $f1 = 'strrev(';
						break;
					case 'ucfirst': $f1 = 'ucfirst(';
						break;
					case 'ucwords': $f1 = 'ucwords(';
						break;
					case 'bin2hex': $f1 = 'bin2hex(';
						break;
					case 'entdecode': $f1 = 'html_entity_decode(';
						break;
					case 'entencode': $f1 = 'htmlentities(';
						break;
					case 'soundex': $f1 = 'soundex(';
						break;
					case 'ceil': $f1 = 'ceil(';
						break;
					case 'floor': $f1 = 'floor(';
						break;
					case 'round': $f1 = 'round(';
						break;

					/**
					 * These are functions that should NOT have an operator
					 */
					case 'isnumeric':
					case 'isstring':
					case 'isfile':
					case 'isdir':
						$this -> _error(E_USER_NOTICE, 'Function, ' . $function . ', requires that NO operator be present in the clause');
						return false;

					default:
						$this -> _error(E_USER_NOTICE, 'Function, ' . $function . ', hasn\'t been implemented');
						return false;
				} 
			} 

			/**
			 * What if the column name is primary?
			 */
			if (strtolower(trim($col)) == 'primary') {
				/**
				 * Make sure there is a primary key
				 */
				if (empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'No primary key has been assigned to this table');
					return false;
				} 
				$col = $cols['primary'];
			} 

			/**
			 * Does the specified column exist?
			 */
			if (($position = $this -> _getColPos(rtrim($col), $cols)) === false) {
				continue;
				//$this -> _error(E_USER_NOTICE, 'Column \'' . rtrim($col) . '\' doesn\'t exist');
				//return false;
			} 

			/**
			 * Create/Add-To the queries
			 */
			$val = str_replace("\'", "'", addslashes($val));
			$val = ($col{strlen($col)-1} . $val{0} == "  ") ? substr($val, 1) : $val;

			if (empty($val) && ($type == '5' || $f1 != '(')) {
				$this -> _error(E_USER_NOTICE, 'Forgot to specify a value to match in your where clause');
				return false;
			} 

			switch ($type) {
				/**
				 * Test for equality
				 */
				case 1:
				case 2: $quotes = (!is_numeric($val) || $cols[rtrim($col)]['type'] != 'int') ? '"' : '';
					$query .= ' ( ' . $f1 . '$value[' . $position . ']' . $f2 . ' ' . ($op == '=' ? '==' : $op) . ' ' . $quotes . $val . $quotes . ' ) ';
					break;

				/**
				 * Test using regex, with[out] a function
				 */
				case 3: $val = str_replace(array('(', ')', '{', '}', '.', '$', '/', '\%', '*', '%', '$$PERC$$'),
						array('\(', '\)', '\{', '\}', '\.', '\$', '\/', '$$PERC$$', '\*', '(.+)?', '%'), $val);
					$query .= ' ( ' . ($op == '!~' ? '!' : '') . 'preg_match("/^' . $val . '$/iU", ' . $f1 . '$value[' . $position . ']' . $f2 . ') ) ';
					break;

				/**
				 * Test involving a function
				 */
				case 4: $query .= ' ( ' . $f1 . '$value[' . $position . ']' . $f2 . ' === ' . $val . ' ) ';
					break;

				/**
				 * Test involving a strpos with[out] function
				 */
				case 5: $query .= ' ( $this->_sql_IN(\' \'.$value['.$position.'], \''.$val.'\') '.(($op == '!?') ? '=' : '!' ).'== FALSE ) ';
			} 
			unset($function, $f1, $f2, $quotes, $position, $val, $col, $op);
		} 
		return $query;
	}
	//模拟sql的in方法
	function _sql_IN($instr,$str){
		$arr=explode(',',$str);
		foreach($arr as $k=>$vo){
			$vo=trim($vo);
			if($vo==trim($instr)) return true;
		}
		return false;
	}
	function _buildIf ($where, $cols) {
		/**
		 * We can only work with a string containing where
		 */
		if (!is_array($where) || empty($where)) {
			$this -> _error(E_USER_NOTICE, 'Where clause must be an array');
			return false;
		} 
		$_query = ''; 
		// 多条件,先筛选出数组然后进行组合
		for($k = 0;$k < count($where);$k++) {
			if (is_array($where[$k])) {
				$_op = isset($where[$k-1]) ? $where[$k-1] : ' and ';
				if ($k == 0) $_op = '';
				$_query .= "{$_op}(" . $this -> _where_query($where[$k], $cols) . ")";
				unset($where[$k]);
				if (isset($where[$k-1])) unset($where[$k-1]);
			} 
		} 
		// 进行组合
		$query = $this -> _where_query($where, $cols) . $_query;
		// //多条件,第一个是数组的话就圈起来
		// if(is_array($where[0]) && (strtolower($where[1])=='or' || strtolower($where[1])=='and' || strtolower($where[1])=='xor')){
		// $where1=$where[0];
		// $op1=$where[1];
		// array_shift($where);
		// array_shift($where);
		// $query1=$this->_where_query($where1, $cols);
		// $query2=$this->_where_query($where, $cols);
		// $query='( '.$query1." ) $op1 ".$query2;
		// }else{
		// $query= $this->_where_query($where, $cols);
		// }
		/**
		 * Make sure that we have a valid query ending
		 */
		$andor = substr($query, -3, -1);
		if ($andor == '&&' || $andor == '||' || $andor == 'OR') {
			$this -> _error(E_USER_NOTICE, 'You have an error in your where clause, cannot end statement with an AND, OR, or XOR');
			return false;
		} 
		return $query;
	} 

	/**
	 * To retrieve the index of the column from the columns' array
	 * 
	 * @param string $colname The name of the column to be searched for
	 * @param mixed $cols The column definitions array
	 * @return int $position The index of the column in the array
	 * @access private 
	 */
	function _getColPos ($colname, $cols) {
		/**
		 * Make sure array is not empty, and the parameter is an array
		 */
		if (empty($cols) || !is_array($cols) || !array_key_exists($colname, $cols)) {
			return false;
		} 
		unset($cols['primary']);

		/**
		 * Get the index for the column
		 */
		if (($position = array_search($colname, array_keys($cols))) === false) {
			return false;
		} 
		return $position;
	} 

	/**
	 * To sort a multi-dimensional array by a key
	 * 
	 * @author fmmarzoa@gmx.net <fmmarzoa@gmx.net> 
	 * @param mixed $array The array to be sorted
	 * @param string $num The name of the key to sort the array by
	 * @return string $order Either a 'ASC' or 'DESC' for sorting order
	 * @access private 
	 */
	// 修改于2013-01-26，添加2个条件排序，修改原函数在数组大的情况下会溢出问题
	// 2013-06-09 修改为支持多条件查询
	function _qsort($arr, $orderbyKey = 'id', $type = "ASC", $left = 0, $right = -1) {
		if (count($arr) >= 1) {
			if (is_array($orderbyKey) && $type == 'array') {
				if (count($orderbyKey) % 2 != 0) return false;
				// $column0 = array();
				// $column3 = array();
				// foreach($arr as $key => $value) {
				// $column0[$key] = $value[$orderbyKey[0]];
				// $column3[$key] = $value[$orderbyKey[3]];
				// }
				// $type1 = strtoupper($orderbyKey[1]) == "ASC" ? SORT_ASC : SORT_DESC;
				// $type4 = strtoupper($orderbyKey[4]) == "ASC" ? SORT_ASC : SORT_DESC;
				// array_multisort($column0, $type1, $column3, $type4, $arr); 
				// 修改为支持多条件排序
				$temp1 = $temp = $arg = $key = $sort = array();
				$i = 1;
				foreach($orderbyKey as $k => $vo) {
					// 获取排序规则
					if ($i % 2 != 0) {
						$key[] = $orderbyKey[$k];
					} else {
						$sort[] = strtoupper($orderbyKey[$k]) == "ASC" ? 'SORT_ASC' : 'SORT_DESC';
					} 
					$i++;
				} 
				foreach($key as $k => $vo) {
					foreach($arr as $kk => $vvo) {
						$temp[] = $vvo[$key[$k]];
					} 
					$arg[] = $temp;
					unset($temp);
					$arg[] = $sort[$k];
				} 
				foreach($arg as $k => $vo) {
					if ($k % 2 == 0) {
						$temp1[$k] = $vo;
						$arg[$k] = '$temp1[' . $k . ']';
					} 
				} 
				$argstr = implode(',', $arg);
				eval('array_multisort(' . $argstr . ',$arr);');
			} else {
				$column = array();
				foreach($arr as $key => $value) {
					$column[$key] = $value[$orderbyKey];
				} 
				$type = strtoupper($type) == "ASC" ? SORT_ASC : SORT_DESC;
				array_multisort($column, $type, $arr);
			} 
			return $arr;
		} 
		return false;
	} 

	/**
	 * Does what unique_array() does but with multidimensional arrays
	 * 
	 * @param mixed $array The array that will be filtered
	 * @param string $sub_key The $key that will be examined for duplicates
	 */
	function unique_multi_array ($array, $sub_key) {
		$target = array();
		$existing_sub_key_values = array();

		foreach ($array as $key => $sub_array) {
			if (!in_array($sub_array[$sub_key], $existing_sub_key_values)) {
				$existing_sub_key_values[] = $sub_array[$sub_key];
				$target[$key] = $sub_array;
			} 
		} 
		return $target;
	} 

	/**
	 * Returns the current txtSQL version
	 * 
	 * @return string $version The current version of txtSQL
	 * @access public 
	 */
	function version() {
		return '2.2 super';
	} 
	/**
	 * To extract data from a database, given that the row fits the given credentials
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return mixed selected An array containing the rows that matched the where clause
	 * @access private 
	 */
	function _select($arg) {
		/**
		 * If the user specified a different database, we must
		 * then automatically select it for them
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * If we have no database selected, we have no table specified
		 * stop execution of script and issue an error
		 */
		$this -> _isselect();
		$this -> _isemptytable($arg['table']);
		/**
		 * If no selection is specified, then we will select
		 * all of the listed column
		 */
		if (empty($arg['select'])) {
			$arg['select'] = array('*');
		} 

		/**
		 * Read in the records and column definitions, and if an error occurs
		 * then we issue a warning saying table doesn't exist
		 */
		$table = $arg['table'];
		if (is_array($table)) {
			$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table[0]);
			$cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM");
			$temparr = array();
			foreach($table as $vo) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$vo";
				$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $vo);
				if (in_array($table_frm, $this->hash_db)) {
					$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$vo";
				} 
				$temp1 = $this -> _readFile($filename . '.MYD');
				if (!empty($temp1)) $temparr[] = $temp1;
			} 
			$rows = array();
			foreach($temparr as $k => $vo) {
				$rows = array_merge($rows, $vo);
			} 
		} else {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
			$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $table);
			if (in_array($table_frm, $this->hash_db)) {
				$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/$table";
			} 
			if (($rows = $this -> _readFile($filename . '.MYD')) === false || ($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM")) === false) {
				$this -> _error(E_USER_NOTICE, 'Table "' . $table . '" doesn\'t exist');
				return false;
			} 
		} 
		if (empty($rows)) {
			return array();
		} 
		/**
		 * Save changes in the cache
		 */
		$this->_CACHE[$filename.'.MYD'] = $rows;
		$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;
		/**
		 * Check to see if we have a where clause to work with
		 */
		$matches = 'TRUE'; //搜索的条件where
		if (isset($arg['where'])) {
			/**
			 * Create the rule to match records, this goes inside the $rowmatches()
			 * function statement and tells us whether the current row matches the
			 * given criteria or not
			 */
			if (($matches = $this -> _buildIf($arg['where'], $cols)) === false) {
				return false;
			} 
		} 

		/**
		 * Parse the limit clause, looking for any complications, like finish
		 * value larger than the start value, non-numeric values, if no 
		 * limit is specified, or is it is not an array.
		 */
		if (empty($arg['limit']) || (!empty($arg['limit']) && !is_array($arg['limit']))) {
			$arg['limit'][0] = 0;
			$arg['limit'][1] = count($rows)-1;
		} elseif (isset($arg['limit'][0]) && !isset($arg['limit'][1])) {
			$arg['limit'][1] = $arg['limit'][0];
			$arg['limit'][0] = 0;
		} elseif (!isset($arg['limit'][0]) || !isset($arg['limit'][1]) || $arg['limit'][0] > $arg['limit'][1]) {
			$arg['limit'][0] = 0;
			$arg['limit'][1] = count($rows)-1;
		} 
		$arg['limit'][0] = (int) $arg['limit'][0];
		$arg['limit'][1] = (int) $arg['limit'][1];

		/**
		 * If we have a wildcard as a select, then we need to
		 * create the selection list ourselves
		 */
		if ($arg['select'][0] == '*') {
			$col = $cols;
			unset($col['primary']);
			$arg['select'] = array_keys($col);
		} 
		/**
		 * Create the selection index, this speeds things up tremendously
		 * because it saves calls to _getColPos()
		 */
		foreach ($arg['select'] as $key => $value) {
			if (strtolower($value) == 'primary') {
				if (empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'No primary key assigned to table ' . $arg['table']);
					return false;
				} 
				$value = $cols['primary'];
			} 

			if (($colPos = $this -> _getColPos($value, $cols)) === false) {
				continue;
				//$this -> _error(E_USER_NOTICE, 'Column \'' . $value . '\' doesn\'t exist');
				//return false;
			} 

			$temp[$value] = $colPos;
		} 
		$arg['select'] = $temp;
		/**
		 * Initialize Some Variables
		 */
		$found = -1;
		$added = -1;
		$selected = array();
		/**
		 * Go through each record, if the row matches and we are in our limits
		 * then select the row with the proper type (string, boolean, or integer)
		 */
		$function = 'foreach ( $rows as $key => $value ){';
		if($matches!='TRUE'){
			$function.='if(('.$matches.')===false){ continue; }';
		}
		$function .='$added++;';
		foreach ($arg['select'] as $key => $select_value) {
			$function .= "\$selected[\$added]['$key'] = \$value[$select_value];";
		} 
		$function .= '}';
		eval($function);
		/**
		 * Sort the results by a key, this is a very expensive
		 * operation and can take quite some time which is why
		 * it is not reccomended for large amounts of data
		 */
		if (!empty($arg['orderby']) && !empty($selected) && count($selected) > 0) {
			/**
			 * We need a valid array to sort the results correctly
			 */
			if (!is_array($arg['orderby']) || count($arg['orderby']) < 2) {
				$this -> _error(E_USER_NOTICE, 'Invalid Order By Clause; Must be array, with two values. array(string "column name", [ASC|DESC])');
				return false;
			} 

			/**
			 * We cannot sort the results by a non-existing key
			 */
			if (!array_key_exists($arg['orderby'][0], $selected[0])) {
				$this -> _error(E_USER_NOTICE, 'Cannot sort results by column \'' . $arg['orderby'][0] . '\'; Column not in result set');
				return false;
			} 

			/**
			 * We can only sort results by ascending order or 
			 * descending order
			 */
			if (strtolower($arg['orderby'][1]) != 'asc' && strtolower($arg['orderby'][1]) != 'desc') {
				$this -> _error(E_USER_NOTICE, 'Results can only be sorted \'asc\' (ascending) or \'desc\' (descending)');
				return false;
			} 
			// 去掉多的数组，因为现在只支持2个排序
			if (count($arg['orderby']) > 4) {
				// $arg['orderby']=array_slice($arg['orderby'],0,4);
			} 
			// 添加orderby多条件排序
			if (count($arg['orderby']) > 2) {
				$selected = $this -> _qsort($selected, $arg['orderby'], 'array');
			} else {
				$selected = $this -> _qsort($selected, $arg['orderby'][0], $arg['orderby'][1]);
			} 
		} 
		$selected = array_slice($selected, $arg['limit'][0], $arg['limit'][1] + 1);
		/**
		 * Apply the DISTINCT feature to the result set
		 */
		if (!empty($arg['distinct'])) {
			if ($this -> _getColPos($arg['distinct'], $cols) === false) {
				$this -> _error(E_USER_NOTICE, 'Column \'' . $arg['distinct'] . '\' doesn\'t exist');
				return false;
			} 

			$selected = $this -> unique_multi_array($selected, $arg['distinct']);
		} 
		/**
		 * Return the selected records
		 */
		return $selected;
	} 

	/**
	 * To insert a row of data into a table.
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _insert ($arg) {
		/**
		 * If the user specifies a different database, then
		 * automatically select it for them
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * If we have no database selected, or no table to work with
		 * then stop script execution
		 */
		$this -> _isselect();
		$this -> _isemptytable($arg['table']);

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (($rows = $this -> _readFile($filename . '.MYD')) === false || ($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM")) === false) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Create the model of the row
		 */
		$model = array();
		foreach ($cols as $key => $value) {
			if ($key == 'primary') continue;

			if ($value['auto_increment'] == 1) {
				$model[] = ($cols[$key]['autocount']++) + 1;
			} elseif ($value['type'] == 'date') {
				$arg['values'][$key] = '';
			} else {
				$model[] = $value['default'];
			} 
		} 

		/**
		 * We first create the selection indexes inside the foreach loop,
		 * inside the same one, we check that max values have not been
		 * exceeded, the table isn't permanent, and auto increment features
		 */
		$max = count($rows);
		foreach ($arg['values'] as $key => $value) {
			unset($arg['values'][$key]);

			/**
			 * If the user is referring to the primary column, then
			 * we substitute it with the actual primary column. We
			 * also check to see if the column exists or not
			 */
			if (strtolower($key) == 'primary') {
				if (empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'No primary key assigned to table ' . $arg['table']);
					return false;
				} 
				$key = $cols['primary'];
			} 
			if (($colPos = $this -> _getColPos($key, $cols)) === false) {
				continue;
				//$this -> _error(E_USER_NOTICE, 'Column \'' . $key . '\' doesn\'t exist');
				//return false;
			} 
			$value = array($colPos, $value);

			/**
			 * Make sure that the max value for this column has not
			 * yet been exceeded
			 */
			if ($cols[$key]['type'] == 'int' && $cols[$key]['max'] > 0 && strlen($value[1]) > $cols[$key]['max']) {
				$this -> _error(E_USER_NOTICE, 'Cannot exceed maximum value for column ' . $key . '<br>value：' . $value[1] . ' <br>max：' . $cols[$key]['max']);
				return false;
			} elseif ($cols[$key]['max'] > 0 && strlen($value[1]) > $cols[$key]['max']) {
				$this -> _error(E_USER_NOTICE, 'Cannot exceed maximum value for column ' . $key . '<br>value：' . $value[1] . ' <br>max：' . $cols[$key]['max']);
				return false;
			} 

			/**
			 * If the value is empty, and there is a default value
			 * set for this column, then we substitute the value
			 * with the default
			 */// 这里原来使用empty();,当为0的时候就为真了
			if ($value[1] == '' && !empty($cols[$key]['default'])) {
				$value[1] = $cols[$key]['default'];
			} 

			/**
			 * If this is an auto increment column, then we will
			 * will use the already incremented column value
			 */
			if ($cols[$key]['auto_increment'] == 1 && !isset($arg['keepid'])) {
				$value[1] = $model[$colPos];
			} 

			/**
			 * Insert the new row of data into the rows of information
			 * with the right data type
			 */
			switch (strtolower($cols[$key]['type'])) {
				case 'enum': if (empty($cols[$key]['enum_val'])) {
						$cols[$key]['enum_val'] = serialize(array(''));
					} 
					$enum_val = unserialize($cols[$key]['enum_val']);
					foreach ($enum_val as $key => $value1) {
						if (strtolower($value[1]) == strtolower($value1)) {
							break;
						} 
						if ($key == (count($enum_val) - 1)) {
							$value[1] = $enum_val[$key];
							break;
						} 
					} 
				case 'text':
				case 'string': $model[$value[0]] = (string)$value[1];
					break;
				case 'int': $model[$value[0]] = (int)$value[1];
					break;
				case 'bool': $model[$value[0]] = (boolean)$value[1];
					break;
				case 'date': $model[$value[0]] = time();
					break;
			} 
		} 
		$rows[] = $model;
		/**
		 * Save the new information in their proper files
		 */
		$fp = @fopen($filename . ".MYD", 'w') or $this -> _error(E_USER_ERROR, 'Error opening table ' . $arg['table']);
		@flock($fp, LOCK_EX);
		@fwrite($fp, serialize($rows)) or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $arg['table']);
		@flock($fp, LOCK_UN);
		@fclose($fp);
		$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Error opening table ' . $arg['table']);
		@flock($fp, LOCK_EX);
		@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $arg['table']);
		@flock($fp, LOCK_UN);
		@fclose($fp);

		/**
		 * Save files to cache
		 */
		$this -> _CACHE[$filename . '.MYD'] = $rows;
		$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;

		/**
		 * Return the new number of records in the database
		 */
		return true;
	} 

	/**
	 * Removes (a) row(s) that fit(s) the given credentials from a table. If none
	 * are specified, it will empty out the table.
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return int deleted The number of rows deleted
	 * @access private 
	 */
	function _delete ($arg) {
		/**
		 * If the user specifies a different database, then
		 * automatically select it for them
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * If no database is selected, or we have no table to
		 * work with, then stop execution of script
		 */
		$this -> _isselect();
		$this -> _isemptytable($arg['table']);

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (($rows = $this -> _readFile($filename . '.MYD')) === false || ($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM")) === false) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Check to see if we have a where clause to work with
		 */
		if (isset($arg['where'])) {
			/**
			 * Create the rule to match records, this goes inside the eval()
			 * statement and tells us whether the current row matches or not
			 */
			if (($matches = $this -> _buildIf($arg['where'], $cols)) === false) {
				return false;
			} 
		} else {
			$rows = array();
		} 

		/**
		 * Parse the limit clause looking for any complications
		 * like it not being an array, or if we don't have a numeric
		 * value
		 */
		if (!isset($arg['limit']) || empty($arg['limit']) || !is_numeric($arg['limit'][0])) {
			$arg['limit']['0'] = count($rows);
		} 

		/**
		 * Initialize some variables
		 */
		$found = 0;
		$deleted = 0;

		/**
		 * Go through each record, if the row matches and we are in our limits
		 * then delete the row
		 */
		$function = '
		foreach ( $rows as $key => $value )
		{
			if ( ' . (isset($matches) ? $matches : 'TRUE') . ' )
			{
				$found++;
				if ( $found <= $arg[\'limit\'][0] )
				{
					$deleted++;
					unset($rows[$key]);
					if ( $found >= $arg[\'limit\'][0] )
					{
						break;
					}
					continue;
				}
				break;
			}
		}';
		eval($function);

		/**
		 * Save the new record information
		 */
		$fp = @fopen($filename . ".MYD", 'w') or $this -> _error(E_USER_ERROR, 'Error opening table ' . $arg['table']);
		@flock($fp, LOCK_EX);
		@fwrite($fp, serialize($rows)) or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $arg['table']);
		@flock($fp, LOCK_UN);
		@fclose($fp);

		/**
		 * Save files to cache
		 */
		$this -> _CACHE[$filename . '.MYD'] = $rows;
		$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;
		/**
		 * Return the number of deleted rows
		 */
		return $deleted;
	} 

	/**
	 * Updates a row that matches the given credentials with
	 * the new data
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return int updated The number of rows that were updated
	 * @access private 
	 */
	function _update ($arg) {
		/**
		 * If the user specifies a different database
		 * then we must automatically select it for them.
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * If there is no database selected, or we have no table
		 * selected, then stop execution of script
		 */
		$this -> _isselect();
		$this -> _isemptytable($arg['table']);

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (($rows = $this -> _readFile($filename . '.MYD')) === false || ($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM")) === false) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 
		/**
		 * Check to see if we have a where clause to work with
		 */
		if (!isset($arg['where'])) {
			$this -> _error(E_USER_NOTICE, 'Must specify a where clause');
			return false;
		} 

		/**
		 * Create the rule to match records, this goes inside the eval()
		 * statement and tells us whether the current row matches or not
		 */
		elseif (($matches = $this -> _buildIf($arg['where'], $cols)) === false) {
			return false;
		} 

		/**
		 * If we have no values to substitute, issue a warning and return
		 */
		elseif (!isset($arg['values']) || empty($arg['values'])) {
			$this -> _error(E_USER_NOTICE, 'Must specify values to update');
			return false;
		} 

		/**
		 * Parse the limit looking for any complications like
		 * non-numeric values, and not being an array
		 */
		if (empty($arg['limit'])) {
			$arg['limit']['0'] = count($rows);
		} elseif (!is_array($arg['limit']) || !is_numeric($arg['limit'][0]) || $arg['limit'][0] <= 0) {
			$arg['limit']['0'] = count($rows);
		} 

		/**
		 * Create the selection index, this little thing saves calls
		 * to _getColPos() about 10000 times, and speeds things up
		 */
		foreach ($arg['values'] as $key => $value) {
			/**
			 * If the user specifies the primary column,
			 * substitute the actual column name for it.
			 */
			if (strtolower($key) == 'primary') {
				if (empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'No primary key assigned to table ' . $arg['table']);
					return false;
				} 
				$key = $cols['primary'];
			} 

			/**
			 * If the column doesn't exist
			 */
			if (($colPos = $this -> _getColPos($key, $cols)) === false) {
				// $this->_error(E_USER_NOTICE, 'Column \''.$key.'\' doesn\'t exist');
				// 更新字段不存在则continue
				unset($arg['values'][$key]);
				continue;
			} 

			/**
			 * If the column is permanent
			 */
			if ($cols[$key]['permanent'] == 1) {
				$this -> _error(E_USER_NOTICE, 'Column ' . $key . ' is set to permanent');
				unset($arg['values'][$key]);
				continue;
			} 

			/**
			 * does it exceed max val?
			 */
			if ($cols[$key]['type'] == 'int' && $cols[$key]['max'] > 0 && strlen($value) > $cols[$key]['max']) {
				$this -> _error(E_USER_NOTICE, 'Cannot exceed maximum value for column ' . $key . ', value：' . $value . ', maxlen：' . $cols[$key]['max']);
				return false;
			} elseif ($cols[$key]['max'] > 0 && strlen($value) > $cols[$key]['max']) {
				$this -> _error(E_USER_NOTICE, 'Cannot exceed maximum value for column ' . $key . ' ,value: ' . $value . ' maxlen：' . $cols[$key]['max']);
				return false;
			} 
			$arg['values'][$key] = array($colPos, $value);
			unset($key, $value);
		} 

		/**
		 * Initialize some variables
		 */
		$found = 0;
		$updated = 0;

		/**
		 * Start going through each row of information looking for a match,
		 * and if it matches then updates the row with the proper information
		 */

		$function = '	foreach ( $rows as $key => $value )
				{
					if ( ' . $matches . ' )
					{
						$found++;
						if ( $found <= $arg[\'limit\'][0] )
						{
							$updated++;';
		foreach ($arg['values'] as $key1 => $value1) {
			switch (strtolower($cols[$key1]['type'])) {
				case 'enum': if (empty($cols[$key1]['enum_val'])) {
						$cols[$key1]['enum_val'] = serialize(array(''));
					} 
					$enum_val = unserialize($cols[$key1]['enum_val']);
					foreach ($enum_val as $key2 => $value2) {
						if (strtolower($arg['values'][$key1][1]) == strtolower($value2)) {
							break;
						} 
						if ($key2 == (count($enum_val) - 1)) {
							$arg['values'][$key1][1] = $enum_val[$key2];
							break;
						} 
					} 
				case 'text':
				case 'string': $type = "string";
					break;
				case 'int': $type = "integer";
					break;
				case 'bool': $type = "boolean";
					break;
				default: $type = "string";
			} 

			$function .= "\$rows[\$key][$value1[0]] = ( $type ) \$arg['values']['$key1'][1];";
		} 
		$function .= '				continue;
						}
						break;
					}
				}
		';
		eval($function);
		/**
		 * Save the new row information
		 */
		// $fp = @fopen ("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		//  @flock ($fp, LOCK_EX);
		//  @fwrite ($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		//  @flock ($fp, LOCK_UN);
		//  @fclose ($fp);
		// $fp = @fopen ($filename.".MYD", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		//  @flock ($fp, LOCK_EX);
		//  @fwrite ($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		//  @flock ($fp, LOCK_UN);
		//  @fclose ($fp);
		@file_put_contents("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM", serialize($cols)) or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $table_frm);
		@file_put_contents($filename . ".MYD", serialize($rows)) or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $arg['table']);
		/**
		 * Save files to cache
		 */
		$this -> _CACHE[$filename . '.MYD'] = $rows;
		$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;

		/**
		 * Return the number of rows that were updated
		 */
		return $updated;
	} 

	/**
	 * Returns an array with a list of tables inside of a database
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return mixed tables An array containing the tables inside of a db
	 * @access private 
	 */
	function _showtables ($arg = null) {
		/**
		 * Are we showing tables inside of another database?
		 */
		if (!empty($arg['db'])) {
			/**
			 * Does it exist?
			 */
			if (!$this -> selectdb($arg['db'])) {
				return false;
			} 
		} 

		/**
		 * Is a database selected?
		 */
		$this -> _isselect();

		/**
		 * Can we open the directory up?
		 */
		if (($fp = @opendir("$this->_LIBPATH/$this->_SELECTEDDB")) === false) {
			$this -> _error(E_USER_ERROR, 'Could not open directory, ' . $this -> _LIBPATH . '/' . $this -> _SELECTEDDB . ', for reading');
		} 

		/**
		 * Make sure that it's a directory, and not a '..' or '.'
		 */
		$table = array();
		while (($file = @readdir($fp)) !== false) {
			if ($file != "." && $file != ".." && $file != 'user.MYI') {
				/**
				 * If it's a valid txtsql table
				 */
				$extension = substr($file, strrpos($file, '.') + 1);
				if (($extension == 'MYD' || $extension == 'FRM') && is_file("$this->_LIBPATH/$this->_SELECTEDDB/$file")) {
					$table[] = substr($file, 0, strrpos($file, '.'));
				} 
			} 
		} 
		@closedir($fp);

		/**
		 * Get only the tables that are valid
		 */
		$tables = array();
		foreach ($table as $key => $value) {
			if (isset($temp[$value])) {
				$tables[] = $value;
			} else {
				$temp[$value] = true;
			} 
		} 

		/**
		 * Return only the names of the tables
		 */
		return !empty($tables) ? $tables : array();
	} 

	/**
	 * Creates a table inside of a database, with the specified credentials of the column
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _createtable ($arg = null) {
		/**
		 * Inside another database?
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * Do we have a selected database?
		 */
		$this -> _isselect();

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Do we have a valid table name?
		 */
		if (empty($arg['table']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['table'])) {
			$this -> _error(E_USER_NOTICE, 'Table name can only contain letters, and numbers');
			return false;
		} 

		/**
		 * Do we have any columns?
		 */
		if (empty($arg['columns']) || !is_array($arg['columns'])) {
			$this -> _error(E_USER_NOTICE, 'Invalid columns for table ' . $arg['table']);
			return false;
		} 

		/**
		 * Start creating an array and populating it with
		 * the column names, and types
		 */
		$cols = array('primary' => '');
		$primaryset = false;
		foreach ($arg['columns'] as $key => $value) {
			/**
			 * What an untouched column looks like
			 */
			$model = array('permanent' => 0,
				'auto_increment' => 0,
				'max' => 0,
				'type' => 'string',
				'default' => '',
				'autocount' => (int) 0,
				'enum_val' => '');

			/**
			 * Column cannot be named primary
			 */
			if ($key == 'primary') {
				$this -> _error(E_USER_NOTICE, 'Use of reserved word [primary]');
				return false;
			} 

			/**
			 * $value has to be an array
			 */
			if ((!empty($value) && !is_array($value)) || empty($key)) {
				$this -> _error(E_USER_NOTICE, 'Invalid columns for table ' . $arg['table']);
				return false;
			} 

			/**
			 * Go through each column type
			 */
			foreach ($value as $key1 => $value1) {
				switch (strtolower($key1)) {
					case 'auto_increment':
						/**
						 * Need either a 1 or 0
						 */
						$value1 = (int) $value1;
						if ($value1 < 0 || $value1 > 1) {
							$this -> _error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
							return false;
						} 

						/**
						 * Has to be an integer type
						 */
						if (isset($value['type']) && $value['type'] != 'int' && $value1 == 1) {
							$this -> _error(E_USER_NOTICE, 'auto_increment must be an integer type');
							return false;
						} 
						$model['auto_increment'] = $value1;
						break;
					case 'permanent':
						/**
						 * Need either a 1 or 0
						 */
						$value1 = (int) $value1;
						if ($value1 < 0 || $value1 > 1) {
							$this -> _error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
							return false;
						} 
						$model['permanent'] = $value1;
						break;
					case 'max':
						/**
						 * Need an integer value greater than -1, less than 1,000,000
						 */
						$value1 = (int) $value1;
						if ($value1 < 0 || $value1 > 1000000) {
							$this -> _error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
							return false;
						} 
						$model['max'] = $value1;
						break;
					case 'type':
						/**
						 * Can only accept an integer, string, boolean
						 */
						switch (strtolower($value1)) {
							case 'text':
								$model['type'] = 'text';
								break;
							case 'string':
								$model['type'] = 'string';
								break;
							case 'int':
								$model['type'] = 'int';
								break;
							case 'bool':
								$model['type'] = 'bool';
								break;
							case 'enum':
								if (!isset($value['enum_val']) || !is_array($value['enum_val']) || empty($value['enum_val'])) {
									$this -> _error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
									return false;
								} 
								$model['type'] = 'enum';
								$model['enum_val'] = serialize($value['enum_val']);
								break;
							case 'date':
								$model['type'] = 'date';
								break;
							default:
								$this -> _error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
								return false;
						} 
						break;
					case 'default':
						$model['default'] = $value1;
						break;
					case 'primary':
						/**
						 * Need either a 1 or 0
						 */
						$value1 = (int) $value1;
						if ($value1 < 0 || $value1 > 1) {
							$this -> _error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
							return false;
						} 

						/**
						 * Make sure primary hasn't already been set
						 */
						if ($primaryset === true && $value1 == 1) {
							$this -> _error(E_USER_NOTICE, 'Only one primary column can be set');
							return false;
						} 

						if ($value1 == 1) {
							/**
							 * Primary keys have to be integer and auto_increment
							 */
							$value['auto_increment'] = isset($value['auto_increment']) ? $value['auto_increment'] : 0;
							$value['type'] = isset($value['type']) ? $value['type'] : 0;

							if ($value['auto_increment'] != 1 || $value['type'] != 'int') {
								$this -> _error(E_USER_NOTICE, 'Primary keys must be of type \'integer\' and auto_increment');
								return false;
							} 

							$cols['primary'] = $key;
						} 
						break;
					case 'enum_val':
						break;
					default:
						$this -> _error(E_USER_NOTICE, 'Invalid column definition, ["' . $key1 . '"], specified');
						return false;
						break;
				} 
			} 
			$cols[$key] = $model;
		} 

		/**
		 * Create two files, $name.myd (empty), and $name.frm (the column defintions)
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";

		/**
		 * Make sure table doesn't exist already
		 */
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (is_file($filename . ".MYD") || is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' already exists');
			return false;
		} 

		/**
		 * Go ahead and create the files
		 */
		$fp = @fopen($filename . ".MYD", 'w') or $this -> _error(E_USER_ERROR, 'Error creating table ' . $arg['table']);
		@flock($fp, LOCK_EX);
		@fwrite($fp, 'a:0:{}') or $this -> _error(E_USER_ERROR, 'Error writing to table ' . $arg['table'] . ' while creating it');
		@flock($fp, LOCK_UN);
		@fclose($fp);
		@chmod($filename . ".MYD", 0766);

		$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Error creating table ' . $arg['table']);
		@flock($fp, LOCK_EX);
		@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Error creating table ' . $arg['table']);
		@flock($fp, LOCK_UN);
		@chmod("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 0766);
		@fclose($fp);

		/**
		 * Save files to cache
		 */
		$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM"] = $cols;
		return true;
	} 

	/**
	 * Drops a table given that it already exists within a database
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _droptable ($arg = null) {
		/**
		 * Make sure that we have a name, and that it's valid
		 */
		if (empty($arg['table']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['table'])) {
			$this -> _error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return false;
		} 

		/**
		 * Does the table exist in another database?
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * Do we have selected database?
		 */
		$this -> _isselect();

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Does table exist?
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Delete two files $name.myd, $name.frm
		 */
		if (!@unlink($filename . '.MYD') || !@unlink("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
			$this -> _error(E_USER_ERROR, 'Could not delete table ' . $arg['table']);
		} 
		return true;
	} 

	/**
	 * Alters a table by working with its columns. You can rename, insert, edit, delete columns.
	 * Also allows for manipulation of primary keys.
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _altertable ($arg = null) {
		/**
		 * Is inside another database?
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * Do we have a selected database?
		 */
		$this -> _isselect();

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Check to see if action is not empty, and name is valid
		 */
		if (!empty($arg['name']) && !preg_match('/^[A-Za-z0-9_]+$/', $arg['name'])) {
			$this -> _error(E_USER_NOTICE, 'Names can only contain letters, numbers, and underscored');
			return false;
		} elseif (empty($arg['action'])) {
			$this -> _error(E_USER_NOTICE, 'No action specified in alter table query');
			return false;
		} 

		/**
		 * Check to see if the table exists
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (!is_file($filename . '.MYD') || !is_file("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Read in the information for the table
		 */
		if (($rows = $this -> _readFile($filename . '.MYD')) === false || ($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM")) === false) {
			$this -> _error(E_USER_NOTICE, 'Table "' . $arg['table'] . '" doesn\'t exist');
			return false;
		} 

		/**
		 * Check for a primary key
		 */
		$primaryset = !empty($cols['primary']) ? true : false;

		/**
		 * Are we allowed to change the column?
		 */
		$action = strtolower($arg['action']);

		/**
		 * Perform the proper action
		 */
		switch (strtolower($arg['action'])) {
			/**
			 * ======================================================================
			 * Insert A Column Into The Table
			 * ======================================================================
			 */
			case 'insert':
				/**
				 * Make sure we have a column name
				 */
				if (empty($arg['name'])) {
					$this -> _error(E_USER_NOTICE, 'Forgot to input new column\'s name');
					return false;
				} 

				/**
				 * Cannot name column primary
				 */
				if ($arg['name'] == 'primary') {
					$this -> _error(E_USER_NOTICE, 'Cannot name column primary (use of reserved words)');
					return false;
				} 

				/**
				 * Check whether the column exists already or not
				 */
				elseif (isset($cols[$arg['name']])) {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['name'] . ' already exists');
					return false;
				} 

				/**
				 * Check to see if we have a column to insert after
				 */
				if (empty($arg['after'])) {
					$colNames = array_keys($cols);
					$arg['after'] = $colNames[count($cols)-1];
				} 

				/**
				 * Parse the types for this column
				 */
				$model = array('permanent' => 0,
					'auto_increment' => 0,
					'max' => 0,
					'type' => 'int',
					'default' => '',
					'autocount' => 0,
					'enum_val' => '');

				foreach ($arg['values'] as $key => $value) {
					switch (strtolower($key)) {
						case 'auto_increment':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
								return false;
							} 

							/**
							 * Has to be an integer type
							 */
							if (isset($arg['values']['type']) && $arg['values']['type'] != 'int' && $value == 1) {
								$this -> _error(E_USER_NOTICE, 'auto_increment must be an integer type');
								return false;
							} 
							$model['auto_increment'] = $value;
							break;
						case 'permanent':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
								return false;
							} 
							$model['permanent'] = $value;
							break;
						case 'max':
							/**
							 * Need an integer value greater than -1, less than 1,000,000
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1000000) {
								$this -> _error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
								return false;
							} 
							$model['max'] = $value;
							break;
						case 'type':
							/**
							 * Can only accept an integer, string, boolean
							 */
							switch (strtolower($value)) {
								case 'text':
									$model['type'] = 'text';
									break;
								case 'string':
									$model['type'] = 'string';
									break;
								case 'int':
									$model['type'] = 'int';
									break;
								case 'bool':
									$model['type'] = 'bool';
									break;
								case 'enum':
									if (!isset($arg['values']['enum_val']) || !is_array($arg['values']['enum_val']) || empty($arg['values']['enum_val'])) {
										$this -> _error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
										return false;
									} 
									$model['type'] = 'enum';
									$model['enum_val'] = serialize($arg['values']['enum_val']);
									break;
								case 'date':
									$model['type'] = 'date';
									break;
								default:
									$this -> _error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
									return false;
							} 
							break;
						case 'default':
							$model['default'] = $value;
							break;
						case 'primary':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
								return false;
							} 

							/**
							 * Make sure primary hasn't already been set
							 */
							if ($primaryset === true && $value == 1) {
								$this -> _error(E_USER_NOTICE, 'Only one primary column can be set');
								return false;
							} 

							if ($value == 1) {
								$cols['primary'] = $arg['name'];
							} 
							break;
						case 'enum_val':
							break;
						default:
							$this -> _error(E_USER_NOTICE, 'Invalid column definition, ["' . $key . '"], specified');
							return false;
					} 
				} 

				/**
				 * Determine the column in which we insert after
				 */
				if ($arg['after'] == 'primary') {
					$afterColPos = 1;
				} else {
					if (($afterColPos = $this -> _getColPos($arg['after'], $cols) + 2) === false) {
						$this -> _error(E_USER_NOTICE, 'Column \'' . $arg['after'] . '\' doesn\'t exist');
						return false;
					} 
				} 

				/**
				 * Add the column to the list of already existing columns,
				 * but after the specified column
				 */
				$i = 0;
				foreach ($cols as $key => $value) {
					$temp[$key] = $value;
					$i++;
					if ($i == $afterColPos) {
						$temp[$arg['name']] = $model;
					} 
				} 
				$cols = $temp;

				/**
				 * Add the column to each row of data
				 */
				if (!empty($rows)) {
					foreach ($rows as $key => $value) {
						$i = 0;
						foreach ($value as $key1 => $value1) {
							if ($i < $afterColPos-1) {
								$temp1[$key][$key1] = $value1;
							} 
							if ($i == $afterColPos - 1 || ($i == count($value) - 1 && $i == $afterColPos - 2)) {
								$temp1[$key][ (($i == count($value) - 1 && $i == $afterColPos - 2) ? $key1 + 1 : $key1) ] = '';
								$i++;
							} 
							if ($i > $afterColPos-1) {
								$temp1[$key][$key1 + 1] = $value1;
							} 
							$i++;
						} 
					} 
					$rows = $temp1;
				} 

				/**
				 * Save the information
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $table_frm . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				$fp = @fopen($filename . '.MYD', 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.MYD for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($rows)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.MYD');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.MYD');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE[$filename . '.MYD'] = $rows;
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;
				return true;
				break;

			/**
			 * ======================================================================
			 * MODIFY A TABLE'S COLUMN
			 * ======================================================================
			 */
			case 'modify':
				/**
				 * Are we allowed to change this column?
				 */
				if ($arg['name'] == 'primary') {
					$this -> _error(E_USER_NOTICE, 'Column primary doesn\'t exist');
					return false;
				} 

				/**
				 * Check whether the column exists already or not
				 */
				elseif (!isset($cols[$arg['name']])) {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['name'] . ' doesn\'t exist');
					return false;
				} 

				/**
				 * Do we have any values to work with?
				 */
				elseif (empty($arg['values'])) {
					$this -> _error(E_USER_NOTICE, 'Empty column set given');
					return false;
				} 

				/**
				 * Are we allowed to modify the column?
				 */
				/**
				 * if ( $cols[$arg['name']]['permanent'] == 1 && !isset($arg['values']['permanent']) )
				 * {
				 * $this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' is set to permanent');
				 * return FALSE;
				 * }
				 */

				/**
				 * Parse the types for this column
				 */
				$model = array('permanent' => $cols[$arg['name']]['permanent'],
					'auto_increment' => $cols[$arg['name']]['auto_increment'],
					'max' => $cols[$arg['name']]['max'],
					'type' => $cols[$arg['name']]['type'],
					'default' => $cols[$arg['name']]['default'],
					'autocount' => $cols[$arg['name']]['autocount'],
					'enum_val' => $cols[$arg['name']]['enum_val']);

				foreach ($arg['values'] as $key => $value) {
					switch (strtolower($key)) {
						case 'auto_increment':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
								return false;
							} 

							/**
							 * Has to be an integer type
							 */
							if (isset($arg['values']['type']) && $arg['values']['type'] != 'int' && $value == 1) {
								$this -> _error(E_USER_NOTICE, 'auto_increment must be an integer type');
								return false;
							} 
							$model['auto_increment'] = $value;
							break;
						case 'permanent':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
								return false;
							} 
							$model['permanent'] = $value;
							break;
						case 'max':
							/**
							 * Need an integer value greater than -1, less than 1,000,000
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1000000) {
								$this -> _error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
								return false;
							} 
							$model['max'] = $value;
							break;
						case 'type':
							/**
							 * Can only accept an integer, string, boolean
							 */
							switch (strtolower($value)) {
								case 'text':
									$model['type'] = 'text';
									break;
								case 'string':
									$model['type'] = 'string';
									break;
								case 'int':
									$model['type'] = 'int';
									break;
								case 'bool':
									$model['type'] = 'bool';
									break;
								case 'enum':
									if (!isset($arg['values']['enum_val']) || !is_array($arg['values']['enum_val']) || empty($arg['values']['enum_val'])) {
										$this -> _error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
										return false;
									} 
									$model['type'] = 'enum';
									$model['enum_val'] = serialize($arg['values']['enum_val']);
									break;
								case 'date':
									$model['type'] = 'date';
									break;
								default:
									$this -> _error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
									return false;
							} 
							break;
						case 'default':
							$model['default'] = $value;
							break;
						case 'primary':
							/**
							 * Need either a 1 or 0
							 */
							$value = (int) $value;
							if ($value < 0 || $value > 1) {
								$this -> _error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
								return false;
							} 

							/**
							 * Make sure primary hasn't already been set
							 */
							if ($primaryset === true && $value == 1) {
								$this -> _error(E_USER_NOTICE, 'Only one primary column can be set');
								return false;
							} 

							if ($value == 1) {
								$cols['primary'] = $arg['name'];
							} 
							break;
						case 'enum_val':
							break;
						default:
							$this -> _error(E_USER_NOTICE, 'Invalid column definition, ["' . $key . '"], specified');
							return false;
					} 
				} 

				/**
				 * Check for a primary key
				 */
				if (($model['type'] != 'int' || $model['auto_increment'] != 1) && strtolower($cols['primary']) == strtolower($arg['name'])) {
					$cols['primary'] = '';
					$this -> _error(E_USER_NOTICE, 'The primary key has been dropped, column must be auto_increment, and integer');
				} 

				/**
				 * Add the column to the list of columns
				 */
				$cols[$arg['name']] = $model;

				/**
				 * Save the results
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM"] = $cols;
				return true;
				break;

			/**
			 * ======================================================================
			 * DROP A TABLE'S COLUMN
			 * ======================================================================
			 */
			case 'drop':
				/**
				 * Chcek for a valid name
				 */
				if (empty($arg['name']) or !preg_match('/^[A-Za-z0-9_]+$/', $arg['name'])) {
					$this -> _error(E_USER_NOTICE, 'Column name can only contain letters, numbers, and underscores');
					return false;
				} 

				/**
				 * Does the column exist?
				 */
				if (!isset($cols[$arg['name']]) || $arg['name'] == 'primary') {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['name'] . ' doesn\'t exist');
					return false;
				} 

				/**
				 * Make sure dropping this column doesn't jeopordize the table
				 */
				if (count($cols) - 2 <= 0) {
					$this -> _error(E_USER_NOTICE, 'Cannot drop column; There has to be at-least ONE column present');
					return false;
				} 

				/**
				 * Get the position that the column was in
				 */
				$i = -1;
				foreach ($cols as $key => $value) {
					if ($key == $arg['name'] && $i > -1) {
						$position = $i;
						break;
					} 
					$i++;
				} 

				/**
				 * Drop the column from list of columns, including primary key
				 */
				if ($cols['primary'] == $arg['name']) {
					$cols['primary'] = '';
				} 
				unset($cols[$arg['name']]);

				/**
				 * Delete the column from each of the rows of data
				 */
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $key => $value) {
						unset($rows[$key][$position]);
						$rows[$key] = array_splice($rows[$key], 0);
					} 
				} 

				/**
				 * Save the results
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				$fp = @fopen($filename . '.MYD', 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.MYD for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($rows)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.MYD');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.MYD');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE[$filename . '.MYD'] = $rows;
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"] = $cols;
				return true;
				break;

			/**
			 * ======================================================================
			 * RENAME A TABLE'S COLUMN
			 * ======================================================================
			 */
			case 'rename col':
				/**
				 * Check for valid names
				 */
				if (empty($arg['name']) || empty($arg['values']['name']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['values']['name'])) {
					$this -> _error(E_USER_NOTICE, 'Column names can only contain letters, numbers, and underscores');
					return false;
				} 

				/**
				 * Check to make sure column exists
				 */
				if (!isset($cols[$arg['name']])) {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['name'] . ' doesn\'t exist');
					return false;
				} 

				/**
				 * Are we allowed to modify the column?
				 * if ( $cols[$arg['name']]['permanent'] == 1 )
				 * {
				 * $this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' is set to permanent');
				 * return FALSE;
				 * }
				 */

				/**
				 * Check to see whether new column name doesn't exist
				 */
				if (isset($cols[$arg['values']['name']]) && $arg['values']['name'] != $arg['name']) {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['name'] . ' already exists');
					return false;
				} 

				/**
				 * If it was primary key, change primary key
				 */
				if ($cols['primary'] == $arg['name']) {
					$cols['primary'] = $arg['values']['name'];
				} 

				/**
				 * Rename column
				 */
				$tmp = $cols;
				$cols = array();
				foreach ($tmp as $key => $value) {
					if ($key == $arg['name']) {
						$key = $arg['values']['name'];
					} 
					$cols[$key] = $value;
				} 

				/**
				 * Save the results
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM"] = $cols;
				return true;
				break;

			/**
			 * ======================================================================
			 * RENAME A TABLE COLLECTIVELY
			 * ======================================================================
			 */
			case 'rename table':
				/**
				 * Check for valid names
				 */
				if (!preg_match('/^[A-Za-z0-9_]+$/', $arg['name'])) {
					$this -> _error(E_USER_NOTICE, 'Table name can only contain letters, numbers, and underscores');
					return false;
				} 

				/**
				 * Make sure new table doesn't exit
				 */
				$fp1 = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['name']}";
				if ((is_file($fp1 . '.FRM') || is_file($fp1 . '.MYD')) && strtolower($arg['name']) != strtolower($arg['table'])) {
					$this -> _error(E_USER_NOTICE, 'Table ' . $arg['name'] . ' already exists');
					return false;
				} 

				/**
				 * Do the renaming
				 */
				@rename("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", $fp1 . '.FRM') or $this -> _error(E_USER_ERROR, 'Error renaming file ' . $filename . '.FRM');
				@rename($filename . '.MYD', $fp1 . '.MYD') or $this -> _error(E_USER_ERROR, 'Error renaming file ' . $filename . '.MYD');

				return true;
				break;

			/**
			 * ======================================================================
			 * ADD A PRIMARY KEY TO A TABLE
			 * ======================================================================
			 */
			case 'addkey':
				/**
				 * Check for a valid column name
				 */
				if (empty($arg['values']['name'])) {
					$this -> _error(E_USER_NOTICE, 'Invalid Column Name');
					return false;
				} 
				if ($this -> _getColPos($arg['values']['name'], $cols) === false) {
					$this -> _error(E_USER_NOTICE, 'Column ' . $arg['values']['name'] . ' doesn\'t exist');
					return false;
				} 

				/**
				 * Does the primary key already exist?
				 */
				if (!empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'Primary key already set to \'' . $cols['primary'] . '\'');
					return false;
				} 

				/**
				 * Primary key must be integer, and auto_increment
				 */
				if (($cols[$arg['values']['name']]['type'] != 'int') || ($cols[$arg['values']['name']]['auto_increment'] === false)) {
					$this -> _error(E_USER_NOTICE, 'Primary key must be integer type, and auto increment');
					return false;
				} 

				/**
				 * Set the column as the primary
				 */
				$cols['primary'] = $arg['values']['name'];

				/**
				 * Save the results
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM"] = $cols;
				return true;
				break;

			/**
			 * ======================================================================
			 * DROP THE TABLE'S PRIMARY KEY
			 * ======================================================================
			 */
			case 'dropkey':
				/**
				 * Does the table have a primary key?
				 */
				if (empty($cols['primary'])) {
					$this -> _error(E_USER_NOTICE, 'No Primary key exists for table ' . $arg['table']);
					return false;
				} 

				/**
				 * Delete the primary key
				 */
				$cols['primary'] = '';

				/**
				 * Save the results
				 */
				$fp = @fopen("$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM", 'w') or $this -> _error(E_USER_ERROR, 'Could not open ' . $filename . '.FRM for writing');
				@flock($fp, LOCK_EX);
				@fwrite($fp, serialize($cols)) or $this -> _error(E_USER_ERROR, 'Could not write to file ' . $filename . '.FRM');
				@flock($fp, LOCK_UN);
				@fclose($fp) or $this -> _error(E_USER_ERROR, 'Could not close ' . $filename . '.FRM');

				/**
				 * Save files to cache
				 */
				$this -> _CACHE["$this->_LIBPATH/$this->_SELECTEDDB/$table_frm.FRM"] = $cols;
				return true;
				break;

			default:
				$this -> _error(E_USER_NOTICE, 'Invalid action specified for alter table query');
				return false;
		} 
		return false;
	} 

	/**
	 * Returns an array containing a list of the columns, and their
	 * corresponding properties
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return mixed cols An array populated with details on the fields in a table
	 * @access private 
	 */
	function _describe ($arg = null) {
		/**
		 * Inside of another database?
		 */
		$this -> _checkdb($arg['db']);

		/**
		 * Do we have a selected database?
		 */
		$this -> _isselect();

		/**
		 * Does table exist?
		 */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		$table_frm = preg_replace('#^([a-z]+)\d+#', '$1', $arg['table']);
		if (in_array($table_frm, $this->hash_db)) {
			$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table_frm/{$arg['table']}";
		} 
		if (!(is_file($filename . '.MYD') && is_file("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM"))) {
			$this -> _error(E_USER_NOTICE, 'Table ' . $arg['table'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Read in the column definitions
		 */
		if (($cols = $this -> _readFile("$this->_LIBPATH/$this->_SELECTEDDB/{$table_frm}.FRM")) === false) {
			$this -> _error(E_USER_ERROR, 'Couldn\'t open file ' . $filename . '.FRM for reading');
		} 

		/**
		 * Return the information
		 */
		$errorLevel = error_reporting(0);
		foreach ($cols as $key => $col) {
			if ($cols[$key]['type'] == 'enum') {
				$cols[$key]['enum_val'] = unserialize($cols[$key]['enum_val']);
			} 
		} 
		error_reporting($errorLevel);
		return $cols;
	} 

	/**
	 * Returns a list of all the databases in the current working directory
	 * 
	 * @return mixed db An array populated with the list of databases in the CWD
	 * @access private 
	 */
	function _showdatabases () {
		/**
		 * Can we open the directory up?
		 */
		if (($fp = @opendir("$this->_LIBPATH")) === false) {
			$this -> _error(E_USER_ERROR, 'Could not open directory, ' . $this -> _LIBPATH . ', for reading');
		} 

		/**
		 * Make sure that it's a directory, and not a '..' or '.'
		 */
		while (($file = @readdir($fp)) !== false) {
			if ($file != "." && $file != ".." && strtolower($file) != 'txtsql' && is_dir("$this->_LIBPATH/$file")) {
				$db[] = $file;
			} 
		} 
		@closedir($fp);

		return isset($db) ? $db : array();
	} 

	/**
	 * Creates a database with the given name inside of the CWD
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _createdatabase ($arg = null) {
		/**
		 * Make sure that we have a name, and that it's valid
		 */
		if (empty($arg['db']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['db'])) {
			$this -> _error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return false;
		} 

		/**
		 * Does the database already exist?
		 */
		if ($this -> _dbexist($arg['db'])) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $arg['db'] . ' already exists');
			return false;
		} 

		/**
		 * Go ahead and create the database
		 */
		if (! (mkdir("$this->_LIBPATH/$arg[db]", 0755) && chmod("$this->_LIBPATH/$arg[db]", 0755))) {
			$this -> _error(E_USER_NOTICE, 'Error creating database ' . $arg['db']);
			return false;
		} 
		return true;
	} 

	/**
	 * Drops a database given that it exists within the CWD
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _dropdatabase ($arg = null) {
		/**
		 * Do we have a valid name?
		 */
		if (empty($arg['db']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['db'])) {
			$this -> _error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return false;
		} elseif (strtolower($arg['db']) == 'txtsql') {
			$this -> _error(E_USER_NOTICE, 'Cannot delete database txtsql');
			return false;
		} 

		/**
		 * Does database exist?
		 */
		if (!$this -> _dbexist($arg['db'])) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $arg['db'] . ' doesn\'t exist');
			return false;
		} 

		/**
		 * Make sure the database isn't locked
		 */
		if ($this -> isLocked($arg['db'])) {
			$this -> _error(E_USER_NOTICE, 'Database \'' . $arg['db'] . '\' is locked');
			return false;
		} 

		/**
		 * Remove any files inside of the directory
		 */
		if (($fp = @opendir("$this->_LIBPATH/$arg[db]")) === false) {
			$this -> _error(E_USER_ERROR, 'Could not delete database ' . $arg['db']);
		} while (($file = @readdir($fp)) !== false) {
			if ($file != "." && $file != "..") {
				if (is_dir("$this->_LIBPATH/$arg[db]/$file") || !@unlink("$this->_LIBPATH/$arg[db]/$file")) {
					$this -> _error(E_USER_ERROR, 'Could not delete database ' . $arg['db']);
				} 
			} 
		} 
		@closedir($fp);

		/**
		 * Go ahead and delete the database
		 */
		if (!@rmdir("$this->_LIBPATH/$arg[db]")) {
			$this -> _error(E_USER_ERROR, 'Could not delete database ' . $arg['db']);
		} 
		return true;
	} 

	/**
	 * Updates a database by changing its name
	 * 
	 * @param mixed $ arg The arguments that are passed to the txtSQL as an array.
	 * @return void 
	 * @access private 
	 */
	function _renamedatabase ($arg = null) {
		/**
		 * Valid database names?
		 */
		if (empty($arg[0]) || empty($arg[1]) || !preg_match('/^[A-Za-z0-9_]+$/', $arg[0]) || !preg_match('/^[A-Za-z0-9_]+$/', $arg[1])) {
			$this -> _error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return false;
		} elseif (strtolower($arg[0]) == 'txtsql') {
			$this -> _error(E_USER_NOTICE, 'Cannot rename database txtsql');
			return false;
		} 

		/**
		 * Does the old or new database exist?
		 */
		if (!$this -> _dbexist($arg[0])) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $arg[0] . ' doesn\'t exist');
			return false;
		} elseif ($this -> _dbexist($arg[1]) && strtolower($arg[0]) != strtolower($arg[1])) {
			$this -> _error(E_USER_NOTICE, 'Database ' . $arg[1] . ' already exists');
			return false;
		} 

		/**
		 * Make sure the database isn't locked
		 */
		$this -> _checkdblock();

		/**
		 * Do the renaming
		 */
		if (!@rename("$this->_LIBPATH/$arg[0]", "$this->_LIBPATH/$arg[1]")) {
			$this -> _error(E_USER_ERROR, 'Could not rename database ' . $arg[0] . ', to ' . $arg[1]);
		} 
		return true;
	} 
} 
