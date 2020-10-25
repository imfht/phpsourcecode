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
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * backup class
 */
class FileBackupService extends FileService {
	/**
	 * @var string
	 */
	private $metaFile;

	/**
	 * @var array
	 */
	private $metaArray = array();

	/**
	 * @var string
	 */
	private $extName;

	/**
	 * @var string
	 */
	private $extPath;

	/**
	 * @var string
	 */
	private $langFile;

	/**
	 * extended init
	 *
	 * @throws LFException raised if the meta file cant be correctly read
	 * @param string $file name of the file (can be a path, if you need this (no check))
	 * @param string $path path to the file
	 * @param string $metaFile absolute path to the meta file (includes filename)
	 * @return void
	 */
	public function init($file, $path, $metaFile) {
		// init
		$this->setVar(['metaFile' => $metaFile]);
		parent::init($file, $path, $metaFile);

		// read meta file
		try {
			if (is_file($this->metaFile)) {
				$this->readMetaFile();
			}
		} catch (LFException $e) {
			throw $e;
		}
	}

	#####################
	###### Set/Get ######
	#####################

	/**
	 * sets information
	 *
	 * structure:
	 * $infos["metaFile"] = absolute path to the meta file (includes filename)
	 * $infos["extPath"] = extension path
	 * $infos["langFile"] = language file
	 *
	 * @param array $informations information (see above)
	 * @return void
	 */
	public function setVar($informations) {
		if (!empty($informations['metaFile'])) {
			$this->metaFile = Typo3Lib::fixFilePath($informations['metaFile']);
		}

		if (!empty($informations['extPath'])) {
			$this->extPath = Typo3Lib::fixFilePath($informations['extPath']);
			$this->extName = basename($informations['extPath']);
		}

		if (!empty($informations['langFile'])) {
			$this->langFile = Typo3Lib::fixFilePath($informations['langFile']);
		}

		parent::setVar($informations);
	}

	/**
	 * returns requested information
	 *
	 * @param string $info
	 * @return mixed
	 */
	public function getVar($info) {
		if ($info == 'metaFile') {
			return $this->metaFile;
		} elseif ($info == 'extName') {
			return $this->extName;
		} elseif ($info == 'extPath') {
			return $this->extPath;
		} elseif ($info == 'langFile') {
			return $this->langFile;
		} else {
			return parent::getVar($info);
		}
	}

	/**
	 * returns meta information about backup files
	 *
	 * Modes:
	 * - 0 => full meta information (default)
	 * - 1 => only meta information of given extension key
	 * - 2 => only meta information of given extension key and workspace
	 * - 3 => only meta information of given extension key, workspace and language file
	 *
	 * @param integer $mode
	 * @param string $extName extension Name (default = $this->extName)
	 * @param string $workspace (default = $this->workspace)
	 * @param string $langFile language file (default = $this->langFile)
	 * @return array
	 */
	public function getMetaInfos($mode = 0, $extName = '', $workspace = '', $langFile = '') {
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$workspace = empty($workspace) ? $this->workspace : $workspace;

		// build return value
		if (!$mode) {
			return $this->metaArray;
		} elseif ($mode == 1) {
			return $this->metaArray[$extName];
		} elseif ($mode == 2) {
			return $this->metaArray[$extName][$workspace];
		} elseif ($mode == 3) {
			return $this->metaArray[$extName][$workspace][$langFile];
		} else {
			return array();
		}
	}

	/**
	 * rewrites current meta information array with the given equivalent
	 *
	 * Modes:
	 * - 0 => full meta information (default)
	 * - 1 => only meta information of given extension key
	 * - 2 => only meta information of given extension key and workspace
	 * - 3 => only meta information of given extension key, workspace and language file
	 *
	 * @param array $metaArray meta information
	 * @param integer $mode
	 * @param string $extName extension Name (default = $this->extName)
	 * @param string $workspace (default = $this->workspace)
	 * @param string $langFile language file (default = $this->langFile)
	 * @return void
	 */
	private function setMetaInfos($metaArray, $mode = 0, $extName = '', $workspace = '', $langFile = '') {
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$workspace = empty($workspace) ? $this->workspace : $workspace;

		// build new meta information array
		if (is_array($metaArray) && count($metaArray)) {
			if (!$mode) {
				$this->metaArray = $metaArray;
			} elseif ($mode == 1) {
				$this->metaArray[$extName] = $metaArray;
			} elseif ($mode == 2) {
				$this->metaArray[$extName][$workspace] = $metaArray;
			} elseif ($mode == 3) {
				$this->metaArray[$extName][$workspace][$langFile] = $metaArray;
			}
		} else {
			if (!$mode) {
				unset($this->metaArray);
			} elseif ($mode == 1) {
				unset($this->metaArray[$extName]);
			} elseif ($mode == 2) {
				unset($this->metaArray[$extName][$workspace]);
			} elseif ($mode == 3) {
				unset($this->metaArray[$extName][$workspace][$langFile]);
			}
		}
	}

	###############################
	###### Meta FileService Methods ######
	###############################

	/**
	 * reads the meta information file and parses the content into $this->metaArray
	 *
	 * @throws LFException raised if no meta content was generated
	 * @return void
	 */
	private function readMetaFile() {
		// read file and parse xml to array
		$metaArray = GeneralUtility::xml2array(file_get_contents($this->metaFile));
		if (!is_array($metaArray)) {
			throw new LFException('failure.backup.metaFile.notRead');
		}

		$this->metaArray = $metaArray;
	}

	/**
	 * generate meta XML
	 *
	 * @return string meta information (xml)
	 */
	private function genMetaXML() {
		// define assocTagNames
		$options['parentTagMap'] = array(
			'' => 'extKey',
			'extKey' => 'workspace',
			'workspace' => 'langFile',
			'langFile' => 'file',
		);
		return GeneralUtility::array2xml($this->getMetaInfos(), '', 0, 'LFBackupMeta', 0, $options);
	}

	/**
	 * writes the meta information file
	 *
	 * @throws LFException raised if the meta file cant be written
	 * @return void
	 */
	private function writeMetaFile() {
		$metaXML = $this->genMetaXML();
		if (empty($metaXML)) {
			throw new LFException('failure.backup.metaFile.notWritten');
		}

		if (!GeneralUtility::writeFile($this->metaFile, $this->getXMLHeader() . $metaXML)) {
			throw new LFException('failure.backup.metaFile.notWritten');
		}
	}

	#################################
	###### Backup FileService Methods ######
	#################################

	/**
	 * generates the xml header
	 *
	 * @return string xml header
	 */
	private function getXMLHeader() {
		return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n";
	}

	/**
	 * removes the meta information entry and the backup file
	 *
	 * @param string $filename
	 * @param string $extName (default = $this->extName)
	 * @param string $langFile (default = $this->langFile)
	 * @throws Exception|LFException
	 * @throws LFException
	 * @return void
	 */
	public function deleteSpecFile($filename, $extName = '', $langFile = '') {
		// get needed meta information
		$extName = empty($extName) ? $this->extName : $extName;
		$langFile = empty($langFile) ? $this->langFile : $langFile;
		$metaArray = $this->getMetaInfos(3, $extName, '', $langFile);

		// check backup file
		if (!isset($metaArray[$filename])) {
			throw new LFException('failure.backup.notDeleted');
		}

		// get file
		$backupPath = $metaArray[$filename]['pathBackup'];
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		$file = GeneralUtility::fixWindowsFilePath($pathSite . $backupPath . '/' . $filename);

		// build new meta information file
		unset($metaArray[$filename]);
		if (!count($metaArray)) {
			$metaArray = NULL;
		}
		$this->setMetaInfos($metaArray, 3, $extName, '', $langFile);

		$extMetaArray = $this->getMetaInfos(2, $extName);
		if (!count($extMetaArray)) {
			$extMetaArray = NULL;
		}
		$this->setMetaInfos($extMetaArray, 2, $extName);

		// write meta information
		try {
			$this->writeMetaFile();
		} catch (LFException $e) {
			throw $e;
		}

		// delete backup file
		try {
			SgLib::deleteFiles(array($file));
		} catch (Exception $e) {
			throw new LFException(
				'failure.backup.notDeleted', 0,
				'(' . $e->getMessage(), ')'
			);
		}
	}

	/**
	 * wrapper for deleteSpecFile()
	 *
	 * @throws LFException raised if the backup or meta file cant be written
	 * @return void
	 */
	public function deleteFile() {
		try {
			$this->deleteSpecFile($this->relFile);
		} catch (LFException $e) {
			throw $e;
		}
	}

	/**
	 * reads a backup file
	 *
	 * @throws LFException raised if the backup file can't be read
	 * @return void
	 */
	public function readFile() {
		if (!is_file($this->absFile)) {
			throw new LFException('failure.backup.notRead');
		}

		// read file and transform from xml to array
		$phpArray = GeneralUtility::xml2array(file_get_contents($this->absFile));
		if (!is_array($phpArray)) {
			throw new LFException('failure.backup.notRead');
		}

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		// read array
		$localLang = $originLang = array();
		foreach ($phpArray['data'] as $langKey => $informations) {
			// read origin
			try {
				$originLang[$langKey] = Typo3Lib::transTypo3File($informations['meta']['origin'], TRUE);
			} catch (Exception $e) {
				$originLang[$langKey] = $pathSite . $informations['meta']['origin'];
			}

			// read data
			if (is_array($informations['langData'])) {
				foreach ($informations['langData'] as $const => $value) {
					$localLang[$langKey][$const] = $value;
				}
			}
		}

		// check
		if (!count($localLang) || !count($originLang)) {
			throw new LFException('failure.backup.notRead');
		}

		$this->localLang = $localLang;
		$this->originLang = $originLang;
		$this->meta = $phpArray['meta'];
	}

	/**
	 * prepares the final Content
	 *
	 * @return string prepared content (xml)
	 */
	private function prepareBackupContent() {
		// set meta
		$phpArray['meta'] = $this->meta;

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		// set array
		foreach ($this->originLang as $lang => $origin) {
			// set origin
			try {
				$phpArray['data'][$lang]['meta']['origin'] = Typo3Lib::transTypo3File($origin, FALSE);
			} catch (Exception $e) {
				$phpArray['data'][$lang]['meta']['origin'] = substr($origin, strlen($pathSite));
			}

			// set data
			if (\is_array($this->localLang[$lang])) {
				foreach ($this->localLang[$lang] as $labelKey => $labelVal) {
					$phpArray['data'][$lang]['langData'][$labelKey] = $labelVal;
				}
			}
		}

		// define assocTagNames
		$options['parentTagMap'] = [
			'data' => 'languageKey',
			'langData' => 'label'
		];

		// get xml
		return GeneralUtility::array2xml($phpArray, '', 0, 'LFBackup', 0, $options);
	}

	/**
	 * prepares the backup file and writes the new meta information
	 *
	 * @param array | NULL $editedLanguages
	 * @return array raised if meta file cant be written
	 * @throws Exception
	 * @throws LFException
	 * @return array
	 */
	protected function prepareFileContents($editedLanguages = NULL) {
		// get content
		$xml = $this->prepareBackupContent();

		// get and set name of backup
		$backupName = 'locallang_' . GeneralUtility::shortMD5(md5($xml)) . '.bak';
		$this->setVar(array('relFile' => $backupName));

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		// get new meta information
		$metaArray = $this->getMetaInfos(3);
		$metaArray[$this->relFile]['createdAt'] = time();
		$metaArray[$this->relFile]['pathBackup'] = str_replace($pathSite, '', $this->absPath);
		$this->setMetaInfos($metaArray, 3);

		// write meta information file
		try {
			$this->writeMetaFile();
		} catch (LFException $e) {
			throw $e;
		}

		$backupFiles[$this->absPath . $this->relFile] = $this->getXMLHeader() . $xml;
		return $backupFiles;
	}
}

?>
