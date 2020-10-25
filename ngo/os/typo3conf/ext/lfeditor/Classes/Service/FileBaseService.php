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
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * base workspace class
 */
abstract class FileBaseService extends FileService {
	/**
	 * @param string $content
	 * @param string $lang
	 * @return mixed
	 */
	abstract protected function getLocalizedFile($content, $lang);

	/**
	 * @param string $filename
	 * @param string $langKey
	 * @return mixed
	 */
	abstract public function checkLocalizedFile($filename, $langKey);

	/**
	 * @param string $langKey
	 * @return mixed
	 */
	abstract public function nameLocalizedFile($langKey);

	/**
	 * @param string $file
	 * @param string $langKey
	 * @return mixed
	 */
	abstract protected function readLLFile($file, $langKey);

	/**
	 * extended init
	 *
	 * @param string $file name of the file (can be a path, if you need this (no check))
	 * @param string $path path to the file
	 * @param string $metaFile
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws LFException
	 */
	public function init($file, $path, $metaFile) {
		$locales = GeneralUtility::makeInstance(Locales::class);
		$availableLanguages = implode('|', $locales->getLocales());

		// localization files should not be edited
		if ($this->checkLocalizedFile(basename($file), $availableLanguages)) {
			throw new LFException('failure.langfile.notSupported');
		}

		$this->setWorkspace('base');
		parent::init($file, $path, $metaFile);
	}

	/**
	 * reads the absolute language file with all localized sub files
	 *
	 * @throws \Exception
	 * @throws LFException
	 * @return void
	 */
	public function readFile() {
		// read absolute file
		$localLang = $this->readLLFile($this->absFile, 'default');
		// loop all languages
		$languages = SgLib::getSystemLanguages();
		$originLang = [];
		foreach ($languages as $lang) {
			$originLang[$lang] = $this->absFile;
			if ($lang === 'default' || (\is_array($localLang[$lang]) && \count($localLang[$lang]))) {
				if (\is_array($localLang[$lang]) && \count($localLang[$lang])) {
					\ksort($localLang[$lang]);
				}
				continue;
			}

			// get localized file
			$lFile = $this->getLocalizedFile($localLang[$lang], $lang);
			if ($lFile && $this->checkLocalizedFile(basename($lFile), $lang)) {
				$originLang[$lang] = $lFile;
				$localLang[$lang] = [];

				if (!is_file($lFile)) {
					continue;
				}

				// read the content
				try {
					$llang = $this->readLLFile($lFile, $lang);
				} catch (\Exception $e) {
					throw $e;
				}

				// merge arrays and save origin of current language
				ArrayUtility::mergeRecursiveWithOverrule($localLang, $llang);
			}
		}

		// check
		if (!\is_array($localLang)) {
			throw new LFException('failure.search.noFileContent');
		}

		// copy all to object variables, if everything was ok
		$this->localLang = $localLang;
		$this->originLang = $originLang;
	}

	/**
	 * Checks if a localized file is found in labels pack (e.g. a language pack was downloaded in the backend)
	 * or if $sameLocation is set, then checks for a file located in "{language}.locallang.xlf" at the same directory
	 *
	 * @param string $fileRef Absolute file reference to locallang file
	 * @param string $language Language key
	 * @param bool $sameLocation If TRUE, then locallang localization file name will be returned with same directory as $fileRef
	 * @return string|null Absolute path to the language file, or null if error occurred
	 */
	protected function getLocalizedFileName($fileRef, $language, $sameLocation = FALSE) {
		// If $fileRef is already prefixed with "[language key]" then we should return it as is
		$fileName = PathUtility::basename($fileRef);
		if (GeneralUtility::isFirstPartOfStr($fileName, $language . '.')) {
			return GeneralUtility::getFileAbsFileName($fileRef);
		}

		if ($sameLocation) {
			return GeneralUtility::getFileAbsFileName(str_replace($fileName, $language . '.' . $fileName, $fileRef));
		}

		// Analyze file reference
		$validatedPrefix = Typo3Lib::getLocalizedFilePrefix($fileRef);
		if ($validatedPrefix) {
			// Divide file reference into extension key, directory (if any) and base name:
			list($extensionKey, $file_extPath) = explode('/', substr($fileRef, strlen($validatedPrefix)), 2);
			$temp = GeneralUtility::revExplode('/', $file_extPath, 2);
			if (count($temp) === 1) {
				array_unshift($temp, '');
			}
			// Add empty first-entry if not there.
			list($file_extPath, $file_fileName) = $temp;

			// The filename is prefixed with "[language key]." because it prevents the llxmltranslate tool from detecting it.
			return Typo3Lib::getLabelsPath() . $language . '/' . $extensionKey . '/' . ($file_extPath ? $file_extPath . '/' : '') . $language . '.' . $file_fileName;
		}
		return NULL;
	}
}
