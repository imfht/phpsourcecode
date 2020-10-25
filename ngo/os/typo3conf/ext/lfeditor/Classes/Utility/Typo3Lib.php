<?php

namespace SGalinski\Lfeditor\Utility;

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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * includes special typo3 methods
 */
class Typo3Lib {
	/**
	 * @deprecated since version 5, will be removed as soon as TYPO3 8 support is dropped
	 */
	const PATH_LOCAL_EXT = 'typo3conf/ext/';

	/**
	 * @deprecated since version 5, will be removed as soon as TYPO3 8 support is dropped
	 */
	const PATH_GLOBAL_EXT = 'typo3/ext/';

	/**
	 * @deprecated since version 5, will be removed as soon as TYPO3 8 support is dropped
	 */
	const PATH_SYS_EXT = 'typo3/sysext/';

	/**
	 * @deprecated since version 5, will be removed as soon as TYPO3 8 support is dropped
	 */
	const PATH_L10N = 'typo3conf/l10n/';

	/**
	 * checks the file location type
	 *
	 * @param string $file
	 * @return string
	 */
	public static function checkFileLocation($file) {
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			$pathExtensions = self::PATH_LOCAL_EXT;
		} else {
			$pathExtensions = Environment::getExtensionsPath() . '/';
		}

		if (strpos($file, $pathExtensions) !== FALSE) {
			return 'local';
		}

		if (strpos($file, self::PATH_GLOBAL_EXT) !== FALSE) {
			trigger_error(
				'The typo3/ext folder does not exist anymore, so this functionality will be dropped',
				E_USER_DEPRECATED
			);
			return 'global';
		}

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			$pathSysExtensions = self::PATH_SYS_EXT;
		} else {
			$pathSysExtensions = Environment::getFrameworkBasePath() . '/';
		}

		if (strpos($file, $pathSysExtensions) !== FALSE) {
			return 'system';
		}

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			$pathL10N = self::PATH_L10N;
		} else {
			$pathL10N = Environment::getLabelsPath() . '/';
		}
		if (strpos($file, $pathL10N) !== FALSE) {
			return 'l10n';
		}

		return '';
	}

	/**
	 * @param string $fileRef Absolute path of the language file
	 * @return string Absolute prefix to the language file location
	 */
	public static function getLocalizedFilePrefix($fileRef) {
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			// Analyze file reference
			if (GeneralUtility::isFirstPartOfStr($fileRef, self::PATH_SYS_EXT)) {
				// Is system
				$validatedPrefix = PATH_site . self::PATH_SYS_EXT;
			} elseif (GeneralUtility::isFirstPartOfStr($fileRef, self::PATH_GLOBAL_EXT)) {
				// Is global
				$validatedPrefix = PATH_site . self::PATH_GLOBAL_EXT;
			} elseif (GeneralUtility::isFirstPartOfStr($fileRef, self::PATH_LOCAL_EXT)) {
				// Is local
				$validatedPrefix = PATH_site . self::PATH_LOCAL_EXT;
			} else {
				$validatedPrefix = '';
			}
		} else {
			// Analyze file reference
			if (GeneralUtility::isFirstPartOfStr($fileRef, Environment::getFrameworkBasePath() . '/')) {
				// Is system
				$validatedPrefix = Environment::getFrameworkBasePath() . '/';
			} elseif (GeneralUtility::isFirstPartOfStr($fileRef, Environment::getBackendPath() . '/ext/')) {
				// Is global
				$validatedPrefix = Environment::getBackendPath() . '/ext/';
			} elseif (GeneralUtility::isFirstPartOfStr($fileRef, Environment::getExtensionsPath() . '/')) {
				// Is local
				$validatedPrefix = Environment::getExtensionsPath() . '/';
			} else {
				$validatedPrefix = '';
			}
		}

		return $validatedPrefix;
	}

	/**
	 * @return string Absolute path to the l10n directory
	 */
	public static function getLabelsPath() {
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
			$pathL10N = PATH_site . self::PATH_L10N;
		} else {
			$pathL10N = Environment::getLabelsPath() . '/';
		}

		return $pathL10N;
	}

	/**
	 * converts an absolute or relative typo3 style (EXT:) file path
	 *
	 * @param string $file absolute file or an typo3 relative file (EXT:)
	 * @param boolean $absolute generate to relative(false) or absolute file
	 * @return string converted file path
	 *
	 * @throws \Exception Conversion of file path failed
	 */
	public static function transTypo3File($file, $absolute) {
		$path = GeneralUtility::getFileAbsFileName($file);
		if (!$absolute) {
			$fileLocation = self::checkFileLocation($path);
			if ($fileLocation === 'local') {
				if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
					$pathToRemove = PATH_site . self::PATH_LOCAL_EXT;
				} else {
					$pathToRemove = Environment::getExtensionsPath() . '/';
				}
			} elseif ($fileLocation === 'system') {
				if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5.0', '<')) {
					$pathToRemove = PATH_site . self::PATH_SYS_EXT;
				} else {
					$pathToRemove = Environment::getFrameworkBasePath() . '/';
				}
			} else {
				throw new \Exception('Can not convert absolute file "' . $file . '"');
			}

			$path = 'EXT:' . SgLib::trimPath($pathToRemove, $path);
		}

		return $path;
	}

	/**
	 * generates portable file paths
	 *
	 * @param string $file file
	 * @return string fixed file
	 */
	public static function fixFilePath($file) {
		return GeneralUtility::fixWindowsFilePath(str_replace('//', '/', $file));
	}

	/**
	 * Adds configuration line to AdditionalConfiguration file.
	 *
	 * @param string $configLine line to be added.
	 * @param string $additionalConfigurationFilePath
	 *
	 * @return void
	 */
	public static function writeLineToAdditionalConfiguration($configLine, $additionalConfigurationFilePath) {
		SgLib::appendToPHPFile($additionalConfigurationFilePath, $configLine);
	}
}
