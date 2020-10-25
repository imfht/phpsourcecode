<?php

namespace SGalinski\Lfeditor\Session;

/***************************************************************
 *  Copyright notice
 *
 *  (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\SingletonInterface;

/**
 * PHP Session handler
 */
class PhpSession implements SingletonInterface {
	/**
	 * @var string
	 */
	protected $sessionKey;

	/**
	 * Constructor
	 */
	public function __construct() {
		session_start();
		$this->sessionKey = uniqid();
	}

	/**
	 * @param string $sessionKey
	 * @return void
	 */
	public function setSessionKey($sessionKey) {
		$this->sessionKey = $sessionKey;
	}

	/**
	 * Returns the current session key
	 *
	 * @return string
	 */
	public function getSessionKey() {
		return $this->sessionKey;
	}

	/**
	 * Exchanges the complete session data
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function exchangeData($data) {
		$this->destroy();
		$_SESSION[$this->sessionKey] = $data;
	}

	/**
	 * Returns the complete session data
	 *
	 * @return mixed
	 */
	public function getData() {
		return $_SESSION[$this->sessionKey];
	}

	/**
	 * Sets data inside the session below the given key
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return void
	 */
	public function setDataByKey($key, $data) {
		$_SESSION[$this->sessionKey][$key] = $data;
	}

	/**
	 * Returns data of the session defined by the given key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getDataByKey($key) {
		return $_SESSION[$this->sessionKey][$key];
	}

	/**
	 * Removes data defined by the given key.
	 *
	 * @param string $key
	 * @return void
	 */
	public function unsetDataByKey($key) {
		unset($_SESSION[$this->sessionKey][$key]);
	}

	/**
	 * Removes all session data
	 *
	 * @return void
	 */
	public function destroy() {
		unset($_SESSION[$this->sessionKey]);
	}
}

?>
