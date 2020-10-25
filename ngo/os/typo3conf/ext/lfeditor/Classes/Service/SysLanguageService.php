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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SysLanguageService works with data from sys_language table.
 */
class SysLanguageService extends AbstractService {
	/** @var  array */
	protected $sysLanguageList;

	/** @var  array */
	protected $isoReverseMapping;

	/**
	 * Selects data (uid, title, flag) from sys_language table by list of uid-s ($uids).
	 * If no $uids specified, selects all records.
	 *
	 * @param string $uids Comma separated list of uid-s.
	 * @return array|bool
	 */
	public function selectFromSysLanguageByUids($uids = NULL) {
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('sys_language');
		$queryBuilder = $queryBuilder->select('uid', 'title', 'flag')
			->from('sys_language')
			->groupBy('flag', 'uid');
		if ($uids !== NULL) {
			$queryBuilder = $queryBuilder->where(
				$queryBuilder->expr()->in(
					'uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY)
				)
			);
		}

		$rows = $queryBuilder->execute()->fetchAll();
		$sysLanguageList = [];
		foreach ($rows as $row) {
			$sysLanguageList[$row['flag']]['uid'] = $row['uid'];
			$sysLanguageList[$row['flag']]['title'] = $row['title'];
		}

		return $sysLanguageList;
	}

	/**
	 * Initialises array of system languages from database.
	 */
	public function initSysLanguageList() {
		if (!isset($this->sysLanguageList)) {
			$this->sysLanguageList = $this->selectFromSysLanguageByUids();
		}
	}

	/**
	 * Returns the iso reverse mapped flag language or the given value if nothing could be mapped.
	 *
	 * @param string $flag language acronym (iso language code, e.g.: 'de', 'da', 'fi'...)
	 * @return string
	 */
	public function doIsoReverseMapping($flag) {
		$isoReverseMapping = $this->getIsoReverseMapping();
		$mappedFlag = (string) $isoReverseMapping[$flag];
		if ($mappedFlag !== '') {
			$flag = $mappedFlag;
		}

		return $flag;
	}

	/**
	 * Returns system language id for given language acronym ($flag).
	 * If that language is not registered in system, function returns NULL.
	 *
	 * @param string $flag language acronym (iso language code, e.g.: 'de', 'da', 'fi'...)
	 * @return int|NULL
	 */
	public function getSysLanguageIdByFlag($flag) {
		$this->initSysLanguageList();
		$flag = $this->doIsoReverseMapping($flag);

		// chinese seems to have a big mapping issue. In general it seems that the iso handling is currently more
		// or less simply fucked up in TYPO3. Also it's possible that I don't get the bigger picture here. Who knows...
		// Also note: Chinese isn't working without a default file in the override mode.
		if ($flag === 'zh') {
			$flag = 'cn';
		}

		if (!empty($this->sysLanguageList[$flag]['uid'])) {
			return (int) $this->sysLanguageList[$flag]['uid'];
		}

		return NULL;
	}

	/**
	 * Returns the mapping between ISO language codes and TYPO3 (old) codes.
	 *
	 * @return array
	 */
	public function getIsoReverseMapping() {
		if (!empty($this->isoReverseMapping)) {
			return $this->isoReverseMapping;
		}

		// For TYPO3 8 compatibility, initialize Locales and fetch an object via makeInstance,
		// because prior to TYPO3 9, the initialize method does not return the Locales instance
		Locales::initialize();
		/** @var $locales Locales */
		$locales = GeneralUtility::makeInstance(Locales::class);
		$isoMapping = $locales->getIsoMapping();
		$this->isoReverseMapping = \array_flip($isoMapping);
		return $this->isoReverseMapping;
	}
}
