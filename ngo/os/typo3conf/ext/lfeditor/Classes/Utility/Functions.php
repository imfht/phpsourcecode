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
use SGalinski\Lfeditor\Service\FileBasePHPService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * contains functions for the 'lfeditor' extension
 */
class Functions {
	/**
	 * Prepares the extension array.
	 *
	 * This function creates the surface of the select box and adds
	 * some additional information to each entry.
	 *
	 * Structure of file array:
	 * $fileArray[textHeader] = further arrays with extension paths
	 *
	 * @param array $fileArray see above
	 * @return array prepared array
	 */
	public static function prepareExtList($fileArray) {
		$myArray = array();
		foreach ($fileArray as $header => $extPaths) {
			if (!is_array($extPaths) || !count($extPaths)) {
				continue;
			}

			unset($prepArray);
			foreach ($extPaths as $extPath) {
				((int) ExtensionManagementUtility::isLoaded(basename($extPath))) ?
					$state = LocalizationUtility::translate('ext.loaded', 'lfeditor') :
					$state = LocalizationUtility::translate('ext.notLoaded', 'lfeditor');

				$prepArray[$extPath] = basename($extPath) . ' [' . $state . ']';
			}
			ksort($prepArray);
			$myArray = array_merge($myArray, array('###extensionGroup###' . $header => $header), $prepArray);
		}
		return $myArray;
	}

	/**
	 * searches extensions in a given path
	 *
	 * Modes for $state:
	 * 0 - loaded and unloaded
	 * 1 - only loaded
	 * 2 - only unloaded
	 *
	 * @throws Exception raised, if the given path cant be opened for reading
	 * @param string $path path
	 * @param integer $state optional: extension state to ignore (see above)
	 * @param string $extIgnoreRegExp optional: directories to ignore (regular expression; pcre with slashes)
	 * @param string $extWhitelistRegExp optional: keep only those directories (regular expression; pcre with slashes)
	 * @return array result of the search
	 */
	public static function searchExtensions($path, $state = 0, $extIgnoreRegExp = '', $extWhitelistRegExp = '') {
		if (!@$fhd = opendir($path)) {
			throw new Exception('cant open "' . $path . '"');
		}

		$path = rtrim($path, '/');
		$extArray = array();
		while ($extDir = readdir($fhd)) {
			$extDirPath = $path . '/' . $extDir;

			// ignore all unless the file is a directory and no point dir
			if (!is_dir($extDirPath) || preg_match('/^\.{1,2}$/', $extDir)) {
				continue;
			}

			// check, if the directory/extension should be saved
			if (preg_match($extIgnoreRegExp, $extDir)) {
				continue;
			}

			// check, if the directory/extension should be saved
			if ($extWhitelistRegExp !== '' && !preg_match($extWhitelistRegExp, $extDir)) {
				continue;
			}

			// state filter
			if ($state) {
				$extState = (int) ExtensionManagementUtility::isLoaded($extDir);
				if (($extState && $state == 2) || (!$extState && $state == 1)) {
					continue;
				}
			}

			$extArray[] = $extDirPath;
		}
		closedir($fhd);

		return $extArray;
	}

	/**
	 * prepares a given language string for section output
	 *
	 * @param string $value language string
	 * @return string prepared output in sections
	 */
	public static function prepareSectionName($value) {
		return html_entity_decode(LocalizationUtility::translate($value, 'lfeditor'));
	}

	/**
	 * checks and returns given languages or TYPO3 language list if the given content was empty
	 *
	 * @param array $languages optional: some language shortcuts
	 * @return array language list
	 */
	public static function buildLangArray($languages = NULL) {
		if (!is_array($languages) || !count($languages)) {
			return SgLib::getSystemLanguages();
		} else {
			return $languages;
		}
	}

	/**
	 * generates output for a diff between the backup and original file
	 *
	 * Note that the generated diff will be an array with a normal structure like
	 * any language content array.
	 *
	 * Modes of diffType:
	 * - all changes at the original since the backup was done (0)
	 * - only changes at the original (1)
	 * - only changes at the backup (2)
	 *
	 * @param integer $diffType see above for available modes
	 * @param array $origLang original language data
	 * @param array $backupLocalLang backup language data
	 * @return mixed generated diff
	 */
	public static function getBackupDiff($diffType, $origLang, $backupLocalLang) {
		// get all languages and generate the diff
		$langKeys = array_merge(array_keys($origLang), array_keys($backupLocalLang));
		$diff = array();
		foreach ($langKeys as $langKey) {
			// prevent warnings
			if (!is_array($origLang[$langKey])) {
				$origLang[$langKey] = array();
			}
			if (!is_array($backupLocalLang[$langKey])) {
				$backupLocalLang[$langKey] = array();
			}
			$origDiff[$langKey] = array();
			$backupDiff[$langKey] = array();

			// generate diff
			if (!$diffType || $diffType == 1) {
				$origDiff[$langKey] = array_diff_assoc($origLang[$langKey], $backupLocalLang[$langKey]);
			}
			if (!$diffType || $diffType == 2) {
				$backupDiff[$langKey] = array_diff_assoc(
					$backupLocalLang[$langKey],
					$origLang[$langKey]
				);
			}
			$diff[$langKey] = array_merge($origDiff[$langKey], $backupDiff[$langKey]);
		}
		return $diff;
	}

	/**
	 * generates output for a meta diff between the backup and original file
	 *
	 * Note that the generated diff will be an array with a normal structure like
	 * any meta content array.
	 *
	 * Modes of diffType:
	 * - all changes at the original since the backup was done (0)
	 * - only changes at the original (1)
	 * - only changes at the backup (2)
	 *
	 * @param integer $diffType see above for available modes
	 * @param array $origMeta original meta data
	 * @param array $backupMeta backup meta data
	 * @return mixed generated diff
	 */
	public static function getMetaDiff($diffType, $origMeta, $backupMeta) {
		$origDiff = array();
		$backupDiff = array();

		if (!$diffType || $diffType == 1) {
			$origDiff = array_diff_assoc($origMeta, $backupMeta);
		}
		if (!$diffType || $diffType == 2) {
			$backupDiff = array_diff_assoc($backupMeta, $origMeta);
		}

		if ($diffType == 1) {
			return $origDiff;
		} elseif ($diffType == 2) {
			return $backupDiff;
		} else {
			return array_merge($origDiff, $backupDiff);
		}
	}

	/**
	 * generates a general information array
	 *
	 * @param string $refLang reference language
	 * @param array $languages language key array
	 * @param FileBasePHPService $fileObj file object
	 * @return array general information array
	 * @see outputGeneral()
	 */
	public static function genGeneralInfoArray($refLang, $languages, $fileObj) {
		// reference language data information
		$localRefLangData = $fileObj->getLocalLangData($refLang);

		// generate needed data
		$infos = array();
		foreach ($languages as $langKey) {
			// get origin data and meta information
			$origin = $fileObj->getOriginLangData($langKey);
			$infos['default']['meta'] = $fileObj->getMetaData();

			// language data
			$localLangData = $fileObj->getLocalLangData($langKey);

			// detailed constants information
			$infos[$langKey]['numUntranslated'] =
				count(array_diff_key($localRefLangData, $localLangData));
			$infos[$langKey]['numUnknown'] =
				count(array_diff_key($localLangData, $localRefLangData));
			$infos[$langKey]['numTranslated'] =
				count(array_intersect_key($localLangData, $localRefLangData));

			// set origin
			try {
				$infos[$langKey]['origin'] = '[-]';
				if (!empty($origin)) {
					$infos[$langKey]['origin'] = Typo3Lib::transTypo3File($origin, FALSE);
				}
			} catch (Exception $e) {
				if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
					$pathSite = PATH_site;
				} else {
					$pathSite = Environment::getPublicPath() . '/';
				}

				$infos[$langKey]['origin'] = SgLib::trimPath($pathSite, $origin);
			}
		}

		// Sort by numTranslated DESC
		foreach ($infos as $key => $row) {
			$numTranslated[$key] = $row['numTranslated'];
		}
		array_multisort($numTranslated, SORT_DESC, $infos);

		return $infos;
	}

	/**
	 * generates a tree information array
	 *
	 * structure:
	 * tree[dimension][branch]['name'] = name of constant
	 * tree[dimension][branch]['type'] = type of constant (0=>normal;1=>untranslated;2=>unknown)
	 * tree[dimension][branch]['parent'] = parentOfBranch (absConstName)
	 * tree[dimension][branch]['childs'] = amount of children
	 *
	 * @param array $langData language data (only one language)
	 * @param array $refLang reference data (only reference language)
	 * @param string $expToken explode token
	 * @return array tree information array
	 */
	public static function genTreeInfoArray($langData, $refLang, $expToken) {
		// reference language
		$refConsts = array();
		if (is_array($refLang) && count($refLang)) {
			$refConsts = array_keys($refLang);
		}
		$langConsts = array_merge(array_keys($langData), $refConsts);

		// generate tree information array
		$curAbsName = '';
		$tree = array();
		foreach ($langConsts as $constant) {
			// add root
			$tree[0]['Root']['name'] = 'Root';

			// get type
			$type = 0; // normal
			if (!in_array($constant, $refConsts)) {
				$type = 2;
			} // unknown
			elseif (empty($langData[$constant])) {
				$type = 1;
			} // untranslated

			$branches = explode($expToken, $constant);
			$numBranches = count($branches);
			for ($i = 0, $curDim = 1; $i < $numBranches; ++$i, ++$curDim) {
				// get current absolute constant name
				if (!$i) {
					$curAbsName = $branches[$i];
				} else {
					$curAbsName .= $expToken . $branches[$i];
				}

				if (isset($tree[$curDim][$curAbsName]['name'])) {
					continue;
				}

				// add branch
				$tree[$curDim][$curAbsName]['name'] = $branches[$i];
				$tree[$curDim][$curAbsName]['type'] = $type;

				// set parent
				if ($i > 0) {
					$parentAbsName = substr($curAbsName, 0, strrpos($curAbsName, $expToken));
				} else {
					$parentAbsName = 'Root';
				}
				$tree[$curDim][$curAbsName]['parent'] = $parentAbsName;
				++$tree[$curDim - 1][$parentAbsName]['childs'];
			}
		}
		return $tree;
	}

	/**
	 * get best explode token of a given language data
	 *
	 * @param string $curToken current token
	 * @param array $langData some test language data
	 * @return string new token
	 */
	public static function getExplodeToken($curToken, $langData) {
		// get current token
		if (!empty($curToken)) {
			return $curToken;
		}

		// return default token, if no test data found
		if (!is_array($langData) || !count($langData)) {
			return '.';
		}

		// get ascii codes (possible explode values)
		$ascii['.'] = ord('.');
		$ascii['_'] = ord('_');

		// get best possible character of the default language
		$defKeys = array_keys($langData);
		$numKeys = count($defKeys);
		$maxTestCount = ($numKeys >= 10) ? 10 : $numKeys;
		$counts = array();
		for ($i = 0; $i < $maxTestCount; ++$i) {
			$curCounts = count_chars($defKeys[$i], 1);
			foreach ($ascii as $sign) {
				$counts[$sign] += $curCounts[$sign];
			}
		}

		// get curToken
		foreach ($counts as $sign => $curCounts) {
			if ($curCounts > $counts[$curToken]) {
				$curToken = $sign;
			}
		}

		return chr($curToken);
	}
}

?>
