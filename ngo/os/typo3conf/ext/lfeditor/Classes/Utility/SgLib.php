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

use Exception;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * personal library with lots of useful methods
 */
class SgLib {
	#################################
	######## string functions #######
	#################################

	/**
	 * trims some string from an given path
	 *
	 * @param string $replace string part to delete
	 * @param string $path some path
	 * @param string $prefix some prefix for the new path
	 * @return string new path
	 */
	public static function trimPath($replace, $path, $prefix = '') {
		return trim(str_replace($replace, '', $path), '/') . $prefix;
	}

	#####################################
	######## filesystem functions #######
	#####################################

	/**
	 * reads the extension of a given filename
	 *
	 * @param string $file filename
	 * @return string extension of a given filename
	 */
	public static function getFileExtension($file) {
		return substr($file, strrpos($file, '.') + 1);
	}

	/**
	 * replaces the file extension in a given filename
	 *
	 * @param string $type new file extension
	 * @param string $file filename
	 * @return string new filename
	 */
	public static function setFileExtension($type, $file) {
		return substr($file, 0, strrpos($file, '.') + 1) . $type;
	}

	/**
	 * checks write permission of a given file (checks directory permission if file does not exists)
	 *
	 * @param string $file file path
	 * @return boolean true or false
	 */
	public static function checkWritePerms($file) {
		if (!is_file($file)) {
			$file = dirname($file);
		}

		if (!is_writable($file)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * deletes given files
	 *
	 * @param array $files files
	 * @return void
	 * @throws Exception raised, if some files cant be deleted (thrown after deletion of all)
	 */
	public static function deleteFiles($files) {
		// delete all old files
		$error = [];
		foreach ($files as $file) {
			if (is_file($file)) {
				if (!unlink($file)) {
					$error[] = $file;
				}
			}
		}

		if (count($error)) {
			throw new Exception('following files cant be deleted: "' . implode(', ', $error) . '"');
		}
	}

	/**
	 * Creates a full path (all nonexistent directories will be created)
	 *
	 * @param string $path full path
	 * @param string $protectArea protected path (i.e. /var/www -- needed for basedir restrictions)
	 * @return void
	 * @throws Exception raised if some path token cant be created
	 */
	public static function createDir($path, $protectArea) {
		if (!is_dir($path)) {
			$pathAsArray = explode('/', self::trimPath($protectArea, $path));
			$tmp = '';
			foreach ($pathAsArray as $dir) {
				$tmp .= $dir . '/';
				if (is_dir($protectArea . $tmp)) {
					continue;
				}

				$concurrentDirectory = $protectArea . $tmp;
				GeneralUtility::mkdir_deep($concurrentDirectory);
				if (!is_dir($concurrentDirectory)) {
					throw new Exception('path "' . $protectArea . $tmp . '" can\'t be created.');
				}
			}
		}
	}

	/**
	 * deletes a directory (all subdirectories and files will be deleted)
	 *
	 * @param string $path full path
	 * @return void
	 * @throws Exception raised if a file or directory cant be deleted
	 */
	public static function deleteDir($path) {
		if (!$dh = @opendir($path)) {
			throw new Exception('directory "' . $path . '" cant be readed');
		}

		while ($file = readdir($dh)) {
			$myFile = $path . '/' . $file;

			// ignore links and point directories
			if (preg_match('/\.{1,2}/', $file) || is_link($myFile)) {
				continue;
			}

			if (is_file($myFile)) {
				if (!unlink($myFile)) {
					throw new Exception('file "' . $myFile . '" cant be deleted');
				}
			} elseif (is_dir($myFile)) {
				SgLib::deleteDir($myFile);
			}
		}
		closedir($dh);

		if (!@rmdir($path)) {
			throw new Exception('directory "' . $path . '" cant be deleted');
		}
	}

	/**
	 * searches defined files in a given path recursively
	 *
	 * @param string $path search in this path
	 * @param string $searchRegex optional: regular expression for files
	 * @param integer $pathDepth optional: current path depth level (max 9)
	 * @return array
	 * @throws Exception raised if the search directory cant be read
	 */
	public static function searchFiles($path, $searchRegex = '', $pathDepth = 0) {
		// endless recursion protection
		$fileArray = [];
		if ($pathDepth >= 9) {
			return $fileArray;
		}

		// open directory
		if (!$fhd = @opendir($path)) {
			throw new Exception('directory "' . $path . '" cant be read');
		}

		// iterate through the directory entries
		while ($file = readdir($fhd)) {
			$filePath = $path . '/' . $file;

			// ignore links and special directories (. and ..)
			if (preg_match('/^\.{1,2}$/', $file) || is_link($filePath)) {
				continue;
			}

			// if it's a file and not excluded by the search filter, we can add it
			// to the file array
			if (is_file($filePath)) {
				if ($searchRegex == '') {
					$fileArray[] = $filePath;
				} elseif (preg_match($searchRegex, $file)) {
					$fileArray[] = $filePath;
				}

				continue;
			}

			// next dir
			if (is_dir($filePath)) {
				$fileArray = array_merge(
					$fileArray,
					(array) SgLib::searchFiles($filePath, $searchRegex, $pathDepth + 1)
				);
			}
		}
		closedir($fhd);

		return $fileArray;
	}

	/**
	 * Returns all available system languages defined in TYPO3
	 *
	 * @return array
	 */
	public static function getSystemLanguages() {
		if (class_exists('TYPO3\CMS\Core\Localization\Locales')) {
			/** @var $locales Locales */
			$locales = GeneralUtility::makeInstance('TYPO3\CMS\Core\Localization\Locales');
			$locales->initialize();
			$availableLanguageKeys = $locales->getLocales();
		} else {
			$availableLanguageKeys = explode('|', TYPO3_languages);
		}
		foreach ($availableLanguageKeys as $index => $language) {
			if ($language === 'default') {
				$availableLanguageKeys[$index] = 'default';
				break;
			}
		}
		return $availableLanguageKeys;
	}

	/**
	 * Convert special HTML characters to HTML entities, but ignores CDATA section.
	 *
	 * @param string $value
	 * @return string
	 */
	public static function htmlSpecialCharsIgnoringCdata($value) {
		$cdataStart = strpos($value, '<![CDATA[');
		$cdataEnd = strpos($value, ']]>');
		if ($cdataStart !== FALSE && $cdataEnd !== FALSE) {
			$cdataEnd += 3;
			$valueBefore = substr($value, 0, $cdataStart);
			$cdataValue = substr($value, $cdataStart, $cdataEnd - $cdataStart);
			$valueAfter = substr($value, $cdataEnd, strlen($value) - $cdataEnd);
			$value = htmlspecialchars($valueBefore) . '&lt;![CDATA[' . $cdataValue . ']]&gt;'
				. htmlspecialchars($valueAfter);
		} else {
			$value = htmlspecialchars($value);
		}
		return $value;
	}

	/**
	 * Checks if CDATA tag exists in string.
	 *
	 * @param string $value
	 * @return bool
	 */
	public static function checkForCdataInString($value) {
		$cdataStart = strpos($value, '<![CDATA[');
		$cdataEnd = strpos($value, ']]>');
		return ($cdataStart !== FALSE && $cdataEnd !== FALSE);
	}

	/**
	 * Writes at end of PHP file, just before php closing tag or at the end of file if php closing tag does not exist.
	 * If specified file does not exist, it will be crated.
	 *
	 * @param string $filePath
	 * @param string $lineToAdd
	 * @return void
	 */
	public static function appendToPHPFile($filePath, $lineToAdd) {
		if (!is_file($filePath)) {
			$emptyPhpFileContent = '<?php' . chr(0x0A) . '?>';
			file_put_contents($filePath, $emptyPhpFileContent);
		}
		$configuration = file_get_contents($filePath);
		if ($configuration === FALSE) {
			return;
		}
		$phpEndTagPosition = strrpos($configuration, '?>');
		if ($phpEndTagPosition) {
			$configuration = substr($configuration, 0, $phpEndTagPosition);
		} else {
			$configuration .= chr(0x0A);
		}
		$configuration .= chr(0x09) . $lineToAdd;
		if ($phpEndTagPosition) {
			$configuration .= chr(0x0A) . '?>';
		}
		file_put_contents($filePath, $configuration);
	}

	/**
	 * Compares two strings ignoring \r character and seeing \n as &lt;br /&gt; in first string.
	 *
	 * @param array|string $string1
	 * @param string $string2
	 * @return bool
	 */
	public static function strCmpIgnoreCR($string1, $string2) {
		return is_string($string1) && str_replace("\r", '', $string1) === $string2;
	}

	/**
	 * Changes meta tag '@attributes' to 'attributes', if it exists.
	 *
	 * @param array $origMeta
	 */
	public static function fixMetaAttributes(array &$origMeta) {
		if (isset($origMeta['@attributes'])) {
			$origMeta['attributes'] = $origMeta['@attributes'];
			unset($origMeta['@attributes']);
		}
	}
}
