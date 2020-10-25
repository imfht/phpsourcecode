<?php

namespace SGalinski\Lfeditor\Service;

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

use SGalinski\Lfeditor\Session\PhpSession;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractService
 */
abstract class AbstractService implements SingletonInterface {
	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;
	/**
	 * @var \SGalinski\Lfeditor\Session\PhpSession
	 */
	protected $session;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Initializes the session object.
	 *
	 * @return void
	 */
	public function initializeObject() {
		if (!($this->session instanceof PhpSession)) {
			$this->session = $this->objectManager->get('SGalinski\Lfeditor\Session\PhpSession');
			$this->session->setSessionKey('tx_lfeditor_sessionVariables');
		}
	}
}
