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

use Exception;
use SGalinski\Lfeditor\Exceptions\LFException;
use SGalinski\Lfeditor\Utility\Functions;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class BackupService
 */
class BackupService extends AbstractService {
	/**
	 * @var \SGalinski\Lfeditor\Service\FileBackupService
	 */
	private $backupObj;

	/**
	 * init backup object
	 *
	 * @throws LFException raised if directories cant be created or backup class instantiated
	 * @throws Exception|LFException
	 * @param string $mode workspace
	 * @param boolean|array $infos set to true if you want use information from the file object
	 * @return void
	 */
	public function initBackupObject($mode = 'base', $infos = NULL) {
		$mode = ($mode ?: 'base');
		/** @var ConfigurationService $confService */
		$confService = $this->objectManager->get('SGalinski\Lfeditor\Service\ConfigurationService');

		// create backup and meta directory
		$extConfig = $confService->getExtConfig();
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		try {
			SgLib::createDir($extConfig['pathBackup'], $pathSite);
			SgLib::createDir(dirname($extConfig['metaFile']), $pathSite);
		} catch (Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		// get information
		$extPath = '';
		$langFile = '';
		if (!is_array($infos)) {
			// build language file and extension path
			if ($mode == 'xlf') {
				try {
					$typo3RelFile = $confService->getFileObj()->getVar('typo3RelFile');
					$typo3AbsFile = Typo3Lib::transTypo3File($typo3RelFile, TRUE);
				} catch (Exception $e) {
					throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
				}

				$langFile = SgLib::trimPath('EXT:', $typo3RelFile);
				$langFile = substr($langFile, strpos($langFile, '/') + 1);

				$extPath = SgLib::trimPath(
					$langFile, SgLib::trimPath(
					$pathSite,
					$typo3AbsFile
				), '/'
				);
			} else {
				$extPath = SgLib::trimPath($pathSite, $confService->getFileObj()->getVar('absPath'), '/');
				$langFile = $confService->getFileObj()->getVar('relFile');
			}

			// set data information
			$informations['localLang'] = $confService->getFileObj()->getLocalLangData();
			$informations['originLang'] = $confService->getFileObj()->getOriginLangData();
			$informations['meta'] = $confService->getFileObj()->getMetaData();
		}

		// set information
		$informations['workspace'] = $mode;
		$informations['extPath'] = is_array($infos) ? $infos['extPath'] : $extPath;
		$informations['langFile'] = is_array($infos) ? $infos['langFile'] : $langFile;

		// create and initialize the backup object
		try {
			$this->backupObj = $this->objectManager->get('SGalinski\Lfeditor\Service\FileBackupService');
			$this->backupObj->init('', $extConfig['pathBackup'], $extConfig['metaFile']);
			$this->backupObj->setVar($informations);
		} catch (LFException $e) {
			throw $e;
		}
	}

	/**
	 * executes the deletion of backup files
	 *
	 * @throws LFException raised if a backup file couldnt be deleted
	 * @param array $delFiles files as key and the language file as value
	 * @return void
	 */
	public function execBackupDelete($delFiles) {
		// delete files
		try {
			foreach ($delFiles as $filename => $langFile) {
				$this->backupObj->deleteSpecFile($filename, '', $langFile);
			}
		} catch (LFException $e) {
			throw $e;
		}
	}

	/**
	 * restores a backup file
	 *
	 * @throws LFException raised if some unneeded files couldnt be deleted
	 * @throws Exception|LFException
	 * @return void
	 */
	public function execBackupRestore() {
		/** @var ConfigurationService $confService */
		$confService = $this->objectManager->get('SGalinski\Lfeditor\Service\ConfigurationService');

		// get vars
		$localLang = array();
		$meta = array();
		$origLang = $confService->getFileObj()->getLocalLangData();
		$origMeta = $confService->getFileObj()->getMetaData();
		$backupMeta = $this->backupObj->getMetaData();
		$backupLocalLang = $this->backupObj->getLocalLangData();
		$backupOriginLang = $this->backupObj->getOriginLangData();

		// get differences between original and backup file
		$origDiff = Functions::getBackupDiff(1, $origLang, $backupLocalLang);
		$backupDiff = Functions::getBackupDiff(2, $origLang, $backupLocalLang);

		if (count($origDiff)) {
			foreach ($origDiff as $langKey => $data) {
				foreach ($data as $label => $value) {
					if (isset($backupLocalLang[$langKey][$label])) {
						$localLang[$langKey][$label] = $value;
					} else {
						$localLang[$langKey][$label] = '';
					}
				}
			}
		}

		if (count($backupDiff)) {
			foreach ($backupDiff as $langKey => $data) {
				foreach ($data as $label => $value) {
					$localLang[$langKey][$label] = $value;
				}
			}
		}

		// get differences between original and backup meta
		SgLib::fixMetaAttributes($origMeta);
		$origDiff = Functions::getMetaDiff(1, $origMeta, $backupMeta);
		$backupDiff = Functions::getMetaDiff(2, $origMeta, $backupMeta);

		if (count($origDiff)) {
			foreach ($origDiff as $label => $value) {
				if (isset($backupMeta[$label])) {
					$meta[$label] = $value;
				} else {
					$meta[$label] = '';
				}
			}
		}

		if (count($backupDiff)) {
			foreach ($backupDiff as $label => $value) {
				$meta[$label] = $value;
			}
		}

		// restore origins of languages
		$deleteFiles = array();
		foreach ($backupOriginLang as $langKey => $file) {
			$curFile = $confService->getFileObj()->getOriginLangData($langKey);
			if ($curFile != $file && $curFile != $confService->getFileObj()->getVar('absFile')) {
				$deleteFiles[] = $curFile;
			}
			$confService->getFileObj()->setOriginLangData($file, $langKey);
		}

		// write modified language array
		try {
			$confService->setExecBackup(0);
			$confService->execWrite($localLang, $meta, TRUE);
		} catch (LFException $e) {
			throw $e;
		}

		// delete all old files
		try {
			if (count($deleteFiles)) {
				SgLib::deleteFiles($deleteFiles);
			}
		} catch (Exception $e) {
			throw new LFException(
				'failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')'
			);
		}
	}

	/**
	 * exec the backup of files and deletes automatic old files
	 *
	 * @throws LFException raised if backup file cant written or unneeded files cant deleted
	 * @return boolean
	 */
	public function execBackup() {
		/** @var ConfigurationService $confService */
		$confService = $this->objectManager->get('SGalinski\Lfeditor\Service\ConfigurationService');

		// create backup object
		try {
			$this->initBackupObject('base', TRUE);
		} catch (LFException $e) {
			throw $e;
		}

		// write backup file
		try {
			$this->backupObj->writeFile();
		} catch (LFException $e) {
			throw $e;
		}

		// exec automatic deletion of backup files, if anzBackup greater zero
		$extConfig = $confService->getExtConfig();
		if ($extConfig['anzBackup'] <= 0) {
			return TRUE;
		}

		// get difference information
		$metaArray = $this->backupObj->getMetaInfos(3);
		$rows = count($metaArray);
		$dif = $rows - $extConfig['anzBackup'];

		if ($dif <= 0) {
			return TRUE;
		}

		// sort metaArray
		foreach ($metaArray as $key => $row) {
			$createdAt[$key] = $row['createdAt'];
		}
		array_multisort($createdAt, SORT_DESC, $metaArray);

		// get filenames
		$files = array_keys($metaArray);
		$numberFiles = count($files);

		// delete files
		try {
			for (; $dif > 0; --$dif, --$numberFiles) {
				$this->backupObj->deleteSpecFile($files[$numberFiles - 1]);
			}
		} catch (LFException $e) {
			try { // delete current written file
				$this->backupObj->deleteFile();
			} catch (LFException $e) {
				throw $e;
			}
			throw $e;
		}

		return FALSE;
	}

	/**
	 * @return FileBackupService
	 */
	public function getBackupObj() {
		return $this->backupObj;
	}

	/**
	 * @param FileBackupService $backupObj
	 */
	public function setBackupObj($backupObj) {
		$this->backupObj = $backupObj;
	}
}
