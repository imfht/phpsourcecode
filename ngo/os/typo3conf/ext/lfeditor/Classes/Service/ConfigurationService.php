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
use SGalinski\Lfeditor\Exceptions\DirectoryAccessRightsException;
use SGalinski\Lfeditor\Exceptions\LFException;
use SGalinski\Lfeditor\Utility\ExtensionUtility;
use SGalinski\Lfeditor\Utility\Functions;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use SGalinski\Lfeditor\Service\FileOverrideService;

/**
 * Class ConfigurationService
 */
class ConfigurationService extends AbstractService {
	/**
	 * @var array extension configuration
	 * @see prepareConfig()
	 */
	protected $extConfig = [];

	/**
	 * @var string
	 */
	protected $invalidLanguages = '';

	/**
	 * @var array
	 */
	protected $langArray = [];

	/**
	 * Available editing modes based on extension configuration
	 * @var array
	 * @see prepareConfig()
	 */
	protected $availabledEditingModes = [];

	/**
	 * @var \SGalinski\Lfeditor\Service\FileBaseService
	 */
	protected $fileObj;

	/**
	 * @var \SGalinski\Lfeditor\Service\FileBaseService
	 */
	protected $convObj;

	/**
	 * preparation and check of the configuration
	 *
	 * Note that the default value will be set, if a option check fails.
	 *
	 * @return array
	 * @throws DirectoryAccessRightsException
	 */
	public function prepareConfig() {
		if (!empty($this->extConfig)) {
			return $this->extConfig;
		}
		$this->extConfig = ExtensionUtility::getExtensionConfiguration();

		// regular expressions
		if (!\preg_match('/^\/.*\/.*$/', $this->extConfig['searchRegex'])) {
			$this->extConfig['searchRegex'] = '/^([a-z0-9_]*locallang[a-z0-9_-]*\.(php|xml)|[^\.]*\.xlf)$/i';
		}

		if (!\preg_match('/^\/.*\/.*$/', $this->extConfig['extIgnore'])) {
			$this->extConfig['extIgnore'] = '/^csh_.*$/';
		}

		if (!\preg_match('/^\/.*\/.*$/', $this->extConfig['extWhitelist'])) {
			$this->extConfig['extWhitelist'] = '';
		}

		$this->extConfig['execBackup'] = TRUE;
		$this->extConfig['viewSysExt'] = TRUE;
		$this->extConfig['viewGlobalExt'] = TRUE;
		$this->extConfig['viewLocalExt'] = TRUE;
		$this->extConfig['treeHide'] = TRUE;

		$this->extConfig['viewStateExt'] = 1;
		$this->extConfig['numTextAreaRows'] = 5;
		$this->extConfig['numSiteConsts'] = 6;
		$this->extConfig['anzBackup'] = 5;
		// Options for number of constants presented on EditFile page
		$this->extConfig['numSiteConstsOptions']
			= ['100' => 100, '200' => 200, '500' => 500, '1000' => 1000, '1500' => 1500, '2000' => 2000];

		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		// paths and files (dont need to exist)
		$this->extConfig['pathBackup'] = Typo3Lib::fixFilePath(
				$pathSite . '/typo3temp/LFEditor/Backup/'
			) . '/';
		$this->extConfig['metaFile'] = Typo3Lib::fixFilePath(
			$pathSite . '/typo3temp/LFEditor/Backup/Meta.xml'
		);
		$this->extConfig['pathOverrideFiles'] = Typo3Lib::fixFilePath(
			$pathSite . '/typo3conf/LFEditor/OverrideFiles/'
		);

		// files
		$this->extConfig['pathCSS'] = 'Resources/Public/StyleSheets/Lfeditor.css';

		// languages (default is forbidden)
		if (!empty($this->extConfig['viewLanguages'])) {
			$langs = GeneralUtility::trimExplode(',', $this->extConfig['viewLanguages'], TRUE);
			unset($this->extConfig['viewLanguages']);

			$availableLanguageKeys = [];
			$locales = GeneralUtility::makeInstance(ObjectManager::class)->get(Locales::class);
			$languages = $locales->getLocales();

			foreach ($languages as $language) {
				$availableLanguageKeys[$language] = TRUE;
			}

			foreach ($langs as $lang) {
				if (!isset($availableLanguageKeys[$lang])) {
					if ($this->invalidLanguages === '') {
						$this->invalidLanguages = $lang;
					} else {
						$this->invalidLanguages .= ', ' . $lang;
					}
				}

				if ($lang !== 'default') {
					$this->extConfig['viewLanguages'][] = $lang;
				}
			}
		}

		if (empty($this->extConfig['defaultLanguage'])) {
			$this->extConfig['defaultLanguage'] = 'default';
		} else {
			/** @var Locales $locales */
			$locales = GeneralUtility::makeInstance(Locales::class);
			$availableLanguageKeys = $locales->getLanguages();
			if (!isset($availableLanguageKeys[$this->extConfig['defaultLanguage']])) {
				$this->extConfig['defaultLanguage'] = 'default';
			}
		}

		// editing modes
		$this->availabledEditingModes = array(
			'extension' => LocalizationUtility::translate('select.editingMode.extension', 'lfeditor'),
			// The l10n mode does not work for TYPO3 9 and the var folder and we do not want to fix this, yet
			//'l10n' => LocalizationUtility::translate('select.editingMode.l10n', 'lfeditor'),
			// Note: override should be always available because it is the fallback mode for editors
			'override' => LocalizationUtility::translate('select.editingMode.override', 'lfeditor'),
		);

		if (isset($this->extConfig['editModeExtension']) && ((int) $this->extConfig['editModeExtension']) === 0) {
			unset($this->availabledEditingModes['extension']);
		}
		if (isset($this->extConfig['editModeL10n']) && ((int) $this->extConfig['editModeL10n']) === 0) {
			unset($this->availabledEditingModes['l10n']);
		}

        if (isset($this->extConfig['pathAdditionalConfiguration']) && $this->extConfig['pathAdditionalConfiguration']) {
            $pathAdditionalConfiguration = Typo3Lib::fixFilePath($this->extConfig['pathAdditionalConfiguration']);
            $this->extConfig['pathAdditionalConfiguration'] = $pathSite . $pathAdditionalConfiguration;
            $additionalConfigurationDirectory = \dirname($this->extConfig['pathAdditionalConfiguration']);
            if (!\is_writable($additionalConfigurationDirectory)) {
            	$message = 'Directory ' . $additionalConfigurationDirectory . ' is not writable.'
            		. 'This directory should be writable in order to write the language override configuration '
            		. 'file ' . $pathAdditionalConfiguration;
                throw new DirectoryAccessRightsException($message);
            }
        } else {
            /** @var \TYPO3\CMS\Core\Configuration\ConfigurationManager $configurationManager */
            $configurationManager = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ConfigurationManager::class);
            $this->extConfig['pathAdditionalConfiguration'] = $configurationManager->getAdditionalConfigurationFileLocation();
        }

		return $this->extConfig;
	}

	/**
	 * Finds extensions for the extension menu selector.
	 *
	 * Note: $this->extConfig must be initialized before call of this method (method prepareConfig() must be executed before this method).
	 *
	 * @throws LFException raised if no extensions are found
	 * @return array
	 */
	public function menuExtList() {
		// search extensions
		$tmpExtList = [];
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		try {
			// local extensions
			if ($this->extConfig['viewLocalExt']) {
				if (\count(
					$content = Functions::searchExtensions(
						$pathSite . Typo3Lib::PATH_LOCAL_EXT,
						$this->extConfig['viewStateExt'],
						$this->extConfig['extIgnore'],
						$this->extConfig['extWhitelist']
					)
				)
				) {
					$tmpExtList[LocalizationUtility::translate('ext.local', 'lfeditor')] = $content;
				}
			}

			// global extensions
			if ($this->extConfig['viewGlobalExt'] && \is_dir(Typo3Lib::PATH_GLOBAL_EXT)) {
				if (\count(
					$content = Functions::searchExtensions(
						$pathSite . Typo3Lib::PATH_GLOBAL_EXT,
						$this->extConfig['viewStateExt'],
						$this->extConfig['extIgnore'],
						$this->extConfig['extWhitelist']
					)
				)
				) {
					$tmpExtList[LocalizationUtility::translate('ext.global', 'lfeditor')] = $content;
				}
			}

			// system extensions
			if ($this->extConfig['viewSysExt']) {
				if (\count(
					$content = Functions::searchExtensions(
						$pathSite . Typo3Lib::PATH_SYS_EXT,
						$this->extConfig['viewStateExt'],
						$this->extConfig['extIgnore'],
						$this->extConfig['extWhitelist']
					)
				)
				) {
					$tmpExtList[LocalizationUtility::translate('ext.system', 'lfeditor')] = $content;
				}
			}
		} catch (Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		// check extension array
		if (!\count($tmpExtList)) {
			throw new LFException('failure.search.noExtension');
		}

		// create list
		/** @var array $extList */
		$extList = Functions::prepareExtList($tmpExtList);
		$extList = \array_merge([$pathSite . 'fileadmin' => 'fileadmin/', ''], $extList);

		foreach ($extList as $extAddress => $extLabel) {
			unset ($extList[$extAddress]);
			$fixedExtAddress = Typo3Lib::fixFilePath($extAddress);
			$extList[$fixedExtAddress] = $extLabel;
		}
		return $extList;
	}

	/**
	 * Finds language files of extension which address is passed as $extensionAddress parameter.
	 *
	 * @param string $extensionAddress
	 * @return array
	 * @throws LFException
	 */
	public function menuLangFileList($extensionAddress) {
		// check
		if (empty($extensionAddress)) {
			throw new LFException('failure.search.noLangFile', 1);
		}

		// search and prepare files
		try {
			/** @var array $files */
			$files = SgLib::searchFiles(
				$extensionAddress,
				$this->extConfig['searchRegex']
			);
		} catch (Exception $e) {
			throw new LFException(
				'failure.search.noLangFile', 1,
				'(' . $e->getMessage() . ')'
			);
		}

		$fileArray = [];
		if (\count($files)) {
			foreach ($files as $file) {
				$filename = \substr($file, \strlen($extensionAddress) + 1);
				$fileArray[$filename] = $filename;
			}
		} else {
			throw new LFException('failure.search.noLangFile', 1);
		}

		return $fileArray;
	}

	/**
	 * Used for the language menu selector
	 *
	 * @param array $langData language data
	 * @param string $default optional default value (if you dont want a default let it empty)
	 * @param BackendUserAuthentication $backendUser
	 * @param bool $translatedLanguagesOnly looks only for translated languages
	 * @return array
	 * @throws LFException
	 */
	public function menuLangList(
		$langData, $default = '', BackendUserAuthentication $backendUser = NULL, $translatedLanguagesOnly = FALSE
	) {
		// build languages
		$languageArray = $this->getLangArray($backendUser);
		$languageList = [];
		foreach ($languageArray as $language) {
			$constCount = 0;
			if (\is_array($langData[$language])) {
				$constCount = \count($langData[$language]);
			}
			if ($translatedLanguagesOnly && $constCount <= 0) {
				continue;
			}
			$languageLabel = $language;
			if ($language === 'default') {
				$languageLabel = 'en';
			}
			$languageList[$language] = $languageLabel . ' (' . $constCount . ' ' .
				LocalizationUtility::translate('const.consts', 'lfeditor') . ')';
		}

		// add default value
		if (!empty($default)) {
			$languageList = \array_merge(['###default###' => $default], $languageList);
		}
		return $languageList;
	}

	/**
	 * Returns list (array) of constants.
	 *
	 * @param array $langData language data
	 * @param string $default name of default entry
	 * @return array
	 */
	public function menuConstList($langData, $default) {
		// generate constant list
		$constList = [];
		$languages = Functions::buildLangArray();
		foreach ($languages as $language) {
			if (!\is_array($langData[$language]) || !\count($langData[$language])) {
				continue;
			}

			/** @var array $constants */
			$constants = \array_keys($langData[$language]);
			foreach ($constants as $constant) {
				$constList[\str_replace('#', '$*-*$', $constant)] = $constant;
			}
		}

		// sorting and default entry
		\asort($constList);
		$constList = \array_merge(['###default###' => $default], $constList);
		return $constList;
	}

	/**
	 * creates and instantiates a file object
	 *
	 * Naming Convention:
	 * File<workspace><filetype>Service
	 *
	 *
	 * @param string $langFile
	 * @param string $extPath
	 * @param bool $flagReadFile
	 * @throws LFException
	 * @return void
	 */
	public function initFileObject($langFile, $extPath, $flagReadFile = TRUE) {
		$fileType = SgLib::getFileExtension($langFile);
		$className = __NAMESPACE__ . '\FileBase' . \strtoupper($fileType) . 'Service';
		if (!\class_exists($className)) {
			throw new LFException('failure.langfile.unknownType');
		}
		/** @var \SGalinski\Lfeditor\Service\FileService $originalFileObject */
		$originalFileObject = $this->objectManager->get($className);
		$originalFileObject->init($langFile, $extPath, '');

		try {
			if ($this->session->getDataByKey('editingMode') === 'override') {
				/** @var FileOverrideService $overrideFileObj */
				$overrideFileObj = $this->objectManager->get(FileOverrideService::class);
				$overrideFileObj->init($originalFileObject, '', '');
				$this->fileObj = $overrideFileObj;
			} else {
				$this->fileObj = $originalFileObject;
			}
		} catch (Exception $e) {
			throw new LFException('failure.failure', 0, '(' . $e->getMessage() . ')');
		}

		if ($flagReadFile) {
			$this->fileObj->readFile();
		}
	}

	/**
	 * Executes writing of language files
	 *
	 *
	 * @param array $modArray changes (constants with empty values will be deleted)
	 * @param array $modMetaArray meta changes (indexes with empty values will be deleted)
	 * @param boolean $forceDel set to true if you want delete default constants
	 * @param array|NULL $editedLanguages
	 * @throws Exception
	 * @throws LFException if file could not be written or some param criteria is not correct
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 * @return void
	 */
	public function execWrite($modArray, $modMetaArray = [], $forceDel = FALSE, $editedLanguages = NULL) {
		// checks
		if (!\is_array($modArray)) {
			throw new LFException('failure.file.notWritten');
		}

		$fileObject = $this->getFileObj();
		if ($fileObject === NULL) {
			$this->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);
			$fileObject = $this->getFileObj();
		}

		// execute backup
		$extConfig = $this->getExtConfig();
		if ($extConfig['execBackup'] && $this->session->getDataByKey('editingMode') === 'extension') {
			/** @var BackupService $backupService */
			$backupService = $this->objectManager->get('SGalinski\Lfeditor\Service\BackupService');
			$backupService->execBackup();
		}

		if (!$this->session->getDataByKey('defaultLanguagePermission')) {
			unset($modArray[$this->extConfig['defaultLanguage']]);
		}
		// set new language data
		foreach ($modArray as $langKey => $data) {
			if (\is_array($data)) {
				foreach ($data as $const => $value) {
					$fileObject->setLocalLangData($const, $value, $langKey, $forceDel);
				}
			}
		}

		// set changed meta data
		foreach ($modMetaArray as $metaIndex => $metaValue) {
			$fileObject->setMetaData($metaIndex, $metaValue);
		}

		// write new language data
		$fileObject->writeFile($editedLanguages);

		// delete possible language files
		$absFile = $fileObject->getVar('absFile');
		$originLang = $fileObject->getOriginLangData();
		$emptyFiles = [];
		foreach ($originLang as $lang => $origin) {
			if ($origin === $absFile || !\is_file($origin)) {
				continue;
			}

			$langData = $fileObject->getLocalLangData($lang);
			if (\is_array($langData) && !\count($langData)) {
				$emptyFiles[] = $origin;
			}
		}

		// delete all empty language files
		try {
			if (\count($emptyFiles)) {
				SgLib::deleteFiles($emptyFiles);
			}
		} catch (Exception $e) {
			throw new LFException('failure.langfile.notDeleted', 0, '(' . $e->getMessage() . ')');
		}

		/** @var CacheManager $cacheManager */
		$cacheManager = $this->objectManager->get(CacheManager::class);
		$cacheManager->getCache('l10n')->flush();
	}

	/**
	 * Splits or merges a language file
	 *
	 * @throws LFException raised if file could not be split or merged (i.e. empty langModes)
	 * @throws Exception|LFException
	 * @param array $langModes language shortcuts and their mode (1 = splitNormal, 2 = merge)
	 * @return void
	 */
	public function execSplitFile(array $langModes) {
		// check
		if (!\is_array($langModes) || !\count($langModes)) {
			throw new LFException('failure.langfile.notSplittedOrMerged');
		}

		// rewrite originLang array
		$delLangFiles = [];
		foreach ($langModes as $langKey => $mode) {
			if ($langKey === 'default') {
				continue;
			}

			// get origin of this language
			$origin = $this->fileObj->getOriginLangData($langKey);

			// split or merge
			if ($mode === 1) {
				// nothing to do if the file is already a normal splitted file
				if (Typo3Lib::checkFileLocation($origin) !== 'l10n') {
					if ($this->fileObj->checkLocalizedFile(\basename($origin), $langKey)) {
						continue;
					}
				}

				// delete file if was it a l10n file
				if ($this->fileObj->checkLocalizedFile(\basename($origin), $langKey)) {
					$delLangFiles[] = $origin;
				}

				$origin = Typo3Lib::fixFilePath(
					\dirname($this->fileObj->getVar('absFile')) .
					'/' . $this->fileObj->nameLocalizedFile($langKey)
				);
			} elseif ($mode === 2) {
				if ($this->fileObj->checkLocalizedFile(\basename($origin), $langKey)) {
					$delLangFiles[] = $origin;
				}
				$origin = $this->fileObj->getVar('absFile');
			} else {
				continue;
			}
			$this->fileObj->setOriginLangData($origin, $langKey);
		}

		// write new language file
		$this->execWrite([]);

		// delete old localized files, if single mode was selected
		try {
			if (\count($delLangFiles)) {
				SgLib::deleteFiles($delLangFiles);
			}
		} catch (Exception $e) {
			throw new LFException(
				'failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')'
			);
		}
	}

	/**
	 * converts language files between different formats
	 *
	 * @throws LFException raised if transforming or deletion of old files failed
	 * @throws Exception|LFException
	 * @param string $type new file format
	 * @param string $newFile new relative file
	 * @return void
	 */
	public function execTransform($type, $newFile) {
		// copy current object to convObj
		$this->convObj = clone $this->fileObj;
		unset($this->fileObj);

		// init new language file object (dont try to read file)
		$this->initFileObject($newFile, $this->convObj->getVar('absPath'), FALSE);

		// recreate originLang
		$origins = $this->convObj->getOriginLangData();
		foreach ($origins as $langKey => $file) {
			// localized or merged language origin
			$newFile = SgLib::setFileExtension($type, $file);
			if ($this->convObj->getVar('workspace') === 'base') {
				if ($this->convObj->checkLocalizedFile(\basename($file), $langKey)) {
					$newFile = \dirname($file) . '/' . $this->fileObj->nameLocalizedFile($langKey);
				}
			}
			$this->fileObj->setOriginLangData(Typo3Lib::fixFilePath($newFile), $langKey);
		}

		// recreate meta data
		$meta = $this->convObj->getMetaData();
		foreach ($meta as $metaIndex => $metaValue) {
			$this->fileObj->setMetaData($metaIndex, $metaValue);
		}

		// write new language file
		$this->extConfig['execBackup'] = 0;
		$this->execWrite($this->convObj->getLocalLangData());

		// delete all old files
		try {
			$delFiles = $this->convObj->getOriginLangData();
			if (\is_array($delFiles) && \count($delFiles)) {
				SgLib::deleteFiles($delFiles);
			}
		} catch (Exception $e) {
			throw new LFException(
				'failure.langfile.notDeleted', 0,
				'(' . $e->getMessage() . ')'
			);
		}
	}

	/*************************
	 *  Getters and setters  *
	 *************************/

	/**
	 * @return array
	 */
	public function getExtConfig() {
		return $this->extConfig;
	}

	/**
	 * Sets 'execBackup' parameter of extension configuration.
	 *
	 * @param int $execBackup
	 */
	public function setExecBackup($execBackup) {
		$this->extConfig['execBackup'] = $execBackup;
	}

	/**
	 * @param array $extConfig
	 */
	public function setExtConfig($extConfig) {
		$this->extConfig = $extConfig;
	}

	/**
	 * @return string
	 */
	public function getInvalidLanguages() {
		return $this->invalidLanguages;
	}

	/**
	 * @param string $invalidLanguages
	 */
	public function setInvalidLanguages($invalidLanguages) {
		$this->invalidLanguages = $invalidLanguages;
	}

	/**
	 * @return array
	 */
	public function getAvailableEditingModes() {
		return $this->availabledEditingModes;
	}

	/**
	 * @return ObjectManager
	 */
	public function getObjectManager() {
		return $this->objectManager;
	}

	/**
	 * @param ObjectManager $objectManager
	 */
	public function setObjectManager($objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return FileBasePHPService
	 */
	public function getFileObj() {
		return $this->fileObj;
	}

	/**
	 * @param FileBasePHPService $fileObj
	 */
	public function setFileObj($fileObj) {
		$this->fileObj = $fileObj;
	}

	/**
	 * Returns array of system languages.
	 *
	 * @param BackendUserAuthentication $backendUser
	 * @throws LFException
	 * @return array
	 */
	public function getLangArray(BackendUserAuthentication $backendUser = NULL) {
		if ($this->invalidLanguages !== '') {
			throw new LFException('failure.config.invalidLanguage', 0, $this->invalidLanguages);
		}

		if (empty($this->langArray)) {
			$languages = Functions::buildLangArray($this->extConfig['viewLanguages']);
			$languages = $this->narrowToUserLanguages($languages, $backendUser);

			if (!\in_array('default', $languages, TRUE)) {
				$languages = \array_merge(['default'], $languages);
			}
			if (!\in_array($this->extConfig['defaultLanguage'], $languages, TRUE)) {
				$languages = \array_merge([$this->extConfig['defaultLanguage']], $languages);
			}
			$this->langArray = $languages;
		}
		return $this->langArray;
	}

	/**
	 * @param array $langArray
	 */
	public function setLangArray($langArray) {
		$this->langArray = $langArray;
	}

	/**
	 * Return the path to the configured AdditionalConfiguration.php file
	 *
	 * @return string
	 */
	public function getAdditionalConfigurationFilePath() {
		return $this->extConfig['pathAdditionalConfiguration'];
	}

	/**
	 * Narrows $languages to user languages if user is not admin.
	 *
	 * @param array $languages
	 * @param BackendUserAuthentication $backendUser
	 * @return array
	 */
	protected function narrowToUserLanguages(array $languages, BackendUserAuthentication $backendUser) {
		if ($backendUser !== NULL && !$backendUser->isAdmin()) {
			$sysLanguageService = $this->objectManager->get(SysLanguageService::class);
			foreach ($languages as $index => $languageFlag) {
				$sysLanguageId = $sysLanguageService->getSysLanguageIdByFlag($languageFlag);
				if ($languageFlag === 'default'
					|| ($sysLanguageId !== NULL && $backendUser->checkLanguageAccess($sysLanguageId))
				) {
					continue;
				}
				unset($languages[$index]);
			}
		}
		return $languages;
	}
}
