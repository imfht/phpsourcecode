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

use SGalinski\Lfeditor\Exceptions\LFException;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * include some general functions only usable for the 'lfeditor' module
 */
abstract class FileService extends AbstractService {
	/**
	 * @var array
	 */
	protected $localLang = array();

	/**
	 * Absolute address (origin) of language file
	 * which contains translation for language given as a key of the array.
	 *
	 * @var array
	 */
	protected $originLang = array();

	/**
	 * @var string
	 */
	protected $absPath;

	/**
	 * @var string
	 */
	protected $relFile;

	/**
	 * @var string
	 */
	protected $absFile;

	/**
	 * @var string
	 */
	protected $fileType;

	/**
	 * @var string
	 */
	protected $workspace;

	/**
	 * @var array
	 */
	protected $meta = array();

	/**
	 * @param $editedLanguages
	 * @return mixed
	 */
	abstract protected function prepareFileContents($editedLanguages);

	/**
	 * @return mixed
	 */
	abstract public function readFile();

	/**
	 * sets some variables
	 *
	 * @param string $file filename or relative path from second param to the language file
	 * @param string $path absolute path to the extension or language file
	 * @param string $metaFile absolute path to the meta file (includes filename) DO NOT REMOVE! It's required in some
	 * implementations.
	 * @return void
	 */
	public function init($file, $path, $metaFile) {
		$this->setAbsPath($path);
		$this->setRelFile($file);
		$this->setAbsFile($this->absPath . '/' . $this->relFile);
	}

	/**
	 * sets information
	 *
	 * structure:
	 * $infos["absPath"] = absolute path to an extension or file
	 * $infos["relFile"] = relative path with filename from "absPath"
	 * $infos["workspace"] = workspace (base or xll)
	 * $infos["fileType"] = file type (php or xml)
	 * $infos["localLang"] = language data
	 * $infos["originLang"] = origin language array
	 * $infos["meta"] = meta data
	 *
	 * @param array $informations
	 * @return void
	 */
	public function setVar($informations) {
		// path and file information
		if (!empty($informations['absPath'])) {
			$this->absPath = Typo3Lib::fixFilePath($informations['absPath'] . '/');
		}
		if (!empty($informations['relFile'])) {
			$this->relFile = Typo3Lib::fixFilePath($informations['relFile']);
		}
		$this->absFile = $this->absPath . $this->relFile;

		// file type and workspace
		if (!empty($informations['workspace'])) {
			$this->workspace = $informations['workspace'];
		}
		if (!empty($informations['fileType'])) {
			$this->fileType = $informations['fileType'];
		}

		// data arrays
		if (!count($this->localLang) && is_array($informations['localLang'])) {
			$this->localLang = $informations['localLang'];
		}
		if (!count($this->originLang) && is_array($informations['originLang'])) {
			$this->originLang = $informations['originLang'];
		}
		if (!count($this->meta) && is_array($informations['meta'])) {
			$this->meta = $informations['meta'];
		}
	}

	/**
	 * returns requested information
	 *
	 * @param $info string
	 * @return string
	 */
	public function getVar($info) {
		$value = '';
		if ($info == 'relFile') {
			$value = $this->relFile;
		} elseif ($info == 'absPath') {
			$value = $this->absPath;
		} elseif ($info == 'absFile') {
			$value = $this->absFile;
		} elseif ($info == 'fileType') {
			$value = $this->fileType;
		} elseif ($info == 'workspace') {
			$value = $this->workspace;
		}

		return $value;
	}

	/**
	 * returns language data
	 *
	 * @param string $langKey valid language key
	 * @return array language data
	 */
	public function getLocalLangData($langKey = '') {
		if (empty($langKey)) {
			return $this->localLang;
		} elseif (is_array($this->localLang[$langKey])) {
			return $this->localLang[$langKey];
		} else {
			return array();
		}
	}

	/**
	 * deletes or sets constants in local language data
	 *
	 * @param string $constant constant name (if empty an index number will be used)
	 * @param string $value new value (if empty the constant will be deleted)
	 * @param string $langKey language shortcut
	 * @param boolean $forceDel set to true, if you want delete default values too
	 * @return void
	 */
	public function setLocalLangData($constant, $value, $langKey, $forceDel = FALSE) {
		if (!empty($value) || (($langKey === 'default' && !$forceDel))) {
			if($this->localLang[$langKey] === '') {
				$this->localLang[$langKey] = [];
			}
			$this->localLang[$langKey][$constant] = $value;
		} elseif (isset($this->localLang[$langKey][$constant])) {
			if ($this->session->getDataByKey('editingMode') === 'override' &&
				isset($this->localLang[$langKey][$constant])
			) {
				$this->localLang[$langKey][$constant] = "";
			} else {
				unset($this->localLang[$langKey][$constant]);
			}
		}
	}

	/**
	 * returns origin
	 *
	 * @param string $langKey valid language key
	 * @return mixed an origin or full array
	 */
	public function getOriginLangData($langKey = '') {
		if (empty($langKey)) {
			return $this->originLang;
		} else {
			return $this->originLang[$langKey];
		}
	}

	/**
	 * sets new origin of a given language
	 *
	 * @param string $origin new origin
	 * @param string $langKey language shortcut
	 * @return void
	 */
	public function setOriginLangData($origin, $langKey) {
		if (!empty($origin)) {
			$this->originLang[$langKey] = $origin;
		}
	}

	/**
	 * returns meta data
	 *
	 * @param string $metaIndex special meta index
	 * @return mixed meta data
	 */
	public function getMetaData($metaIndex = '') {
		if (!empty($metaIndex)) {
			return $this->meta[$metaIndex];
		} else {
			return $this->meta;
		}
	}

	/**
	 * deletes or sets constants in meta data
	 *
	 * @param string $metaIndex
	 * @param string $value new value (if empty the meta index will be deleted)
	 * @return void
	 */
	public function setMetaData($metaIndex, $value) {
		if (!empty($value)) {
			$this->meta[$metaIndex] = $value;
		} elseif (isset($this->meta[$metaIndex])) {
			unset($this->meta[$metaIndex]);
		}
	}

	/**
	 * writes language files
	 *
	 * @param array | NULL $editedLanguages
	 * @throws LFException raised if a file cant be written
	 * @return void
	 */
	public function writeFile($editedLanguages = NULL) {
		// get prepared language files
		$languageFiles = $this->prepareFileContents($editedLanguages);
		$this->writeFilesWithContent($languageFiles);
	}

	/**
	 * Writes the given files with the given content.
	 *
	 * Array structure:
	 * array (
	 *        '/var/www/file.xlf' => 'My content',
	 *        ...
	 * )
	 *
	 * @param array $files
	 * @throws LFException
	 * @return void
	 */
	public function writeFilesWithContent(array $files = array()) {
		// check write permissions of all files
		foreach ($files as $file => $content) {
			if (!SgLib::checkWritePerms($file)) {
				throw new LFException('failure.file.badPermissions');
			}
		}

		// write files
		foreach ($files as $file => $content) {
			if (!GeneralUtility::writeFile($file, $content)) {
				throw new LFException('failure.file.notWritten');
			}
		}
	}

	/**
	 * Writes generator meta tag.
	 *
	 * @return void
	 */
	protected function addGeneratorString() {
		$this->meta['generator'] = 'LFEditor';
	}

	/**
	 * Returns $absFile.
	 *
	 * @return string
	 */
	public function getAbsFile() {
		return $this->absFile;
	}

	/**
	 * Sets $absFile.
	 *
	 * @param string $absFile
	 * @return void
	 */
	public function setAbsFile($absFile) {
		$this->absFile = $absFile;
	}

	/**
	 * Returns $absPath - absolute path to an extension or file.
	 *
	 * @return string
	 */
	public function getAbsPath() {
		return $this->absPath;
	}

	/**
	 * Sets $absPath - absolute path to an extension or file.
	 *
	 * @param string $absPath
	 * @return void
	 */
	public function setAbsPath($absPath) {
		$this->absPath = $absPath;
	}

	/**
	 * Returns $fileType
	 *
	 * @return string
	 */
	public function getFileType() {
		return $this->fileType;
	}

	/**
	 * Sets $fileType.
	 *
	 * @param string $fileType
	 * @return void
	 */
	public function setFileType($fileType) {
		$this->fileType = $fileType;
	}

	/**
	 * Returns $locallang
	 *
	 * @return array
	 */
	public function getLocalLang() {
		return $this->localLang;
	}

	/**
	 * Sets $locallang.
	 *
	 * @param array $localLang
	 */
	public function setLocalLang(array $localLang) {
		$this->localLang = $localLang;
	}

	/**
	 * Returns meta data.
	 *
	 * @return array
	 */
	public function getMeta() {
		return $this->meta;
	}

	/**
	 * Sets meta data.
	 *
	 * @param array $meta
	 */
	public function setMeta(array $meta) {
		$this->meta = $meta;
	}

	/**
	 * Returns $originLang.
	 *
	 * @return array
	 */
	public function getOriginLang() {
		return $this->originLang;
	}

	/**
	 * Sets $originLang.
	 *
	 * @param array $originLang
	 */
	public function setOriginLang(array $originLang) {
		$this->originLang = $originLang;
	}

	/**
	 * Returns relFile - relative path with filename from "absPath".
	 *
	 * @return string
	 */
	public function getRelFile() {
		return $this->relFile;
	}

	/**
	 * Sets relFile - relative path with filename from "absPath".
	 *
	 * @param string $relFile
	 */
	public function setRelFile($relFile) {
		$this->relFile = $relFile;
	}

	/**
	 * Returns workspace.
	 *
	 * @return string
	 */
	public function getWorkspace() {
		return $this->workspace;
	}

	/**
	 * Sets workspace.
	 *
	 * @param string $workspace
	 */
	public function setWorkspace($workspace) {
		$this->workspace = $workspace;
	}
}

?>
