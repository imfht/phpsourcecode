<?php

namespace SGalinski\Lfeditor\Controller;

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
use SGalinski\Lfeditor\Utility\Functions;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ManageBackups controller. It contains extbase actions for ManageBackups page.
 */
class ManageBackupsController extends AbstractBackendController {
	/**
	 * A constant which is used in backup feature to indicate that
	 * language constant is added after backup file was made.
	 */
	const LANG_CONST_ADDED = 2;

	/**
	 * A constant which is used in backup feature to indicate that
	 * language constant is deleted after backup file was made.
	 */
	const LANG_CONST_DELETED = 1;

	/**
	 * A constant which is used in backup feature to indicate that
	 * language constant isn't deleted nor added after backup file was made. It was just changed.
	 */
	const LANG_CONST_NORMAL = 0;

	/**
	 * Opens manageBackups view.
	 * It is called in 2 cases:
	 * - on selection of manageBackups option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @param string $fileName
	 * @param string $langFile language file which was altered.
	 * @param bool $showDiff
	 * @return void
	 */
	public function manageBackupsAction($fileName = '', $langFile = '', $showDiff = FALSE) {
		try {
			$this->view->assign('controllerName', 'ManageBackups');

			$extensionOptions = $this->configurationService->menuExtList();
			$this->assignViewWidthMenuVariables('extension', $extensionOptions);

			$this->initializeBackupObject($langFile);
			$this->prepareManageBackupsViewMainSectionContent();
			if ($showDiff) {
				$this->generateDiffContent($fileName);
			}
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in manageBackups view.
	 * It is called on change of selection of any select menu in searchConstant view.
	 *
	 * @param string $extensionSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction($extensionSelection = NULL) {
		$this->saveSelectionsInSession($extensionSelection);
		$this->redirect('manageBackups');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('manageBackups');
	}

	/**
	 * Prepares main section content of manageBackupsConstant view.
	 *
	 * @throws LFException
	 * @return void
	 */
	protected function prepareManageBackupsViewMainSectionContent() {
		$backups = $this->makeBackupsList();
		if (empty($backups)) {
			$this->addFlashMessage(
				LocalizationUtility::translate('failure.backup.noFiles', 'lfeditor'),
				'',
				$severity = AbstractMessage::NOTICE,
				$storeInSession = TRUE
			);
			return;
		}
		$recoverLabelThead = strtoupper(
			substr(LocalizationUtility::translate('function.backupMgr.recover', 'lfeditor'), 0, 1)
		);
		$differenceLabelThead = strtoupper(
			substr(LocalizationUtility::translate('function.backupMgr.diff.diff', 'lfeditor'), 0, 1)
		);

		$this->view->assign('backups', $backups);
		$this->view->assign('recoverLabelThead', $recoverLabelThead);
		$this->view->assign('differenceLabelThead', $differenceLabelThead);
	}

	/**
	 * Makes list of backups for use in fluid page.
	 *
	 * @return array
	 */
	protected function makeBackupsList() {
		$backups = array();
		$metaArray = $this->backupService->getBackupObj()->getMetaInfos(2);
		if (is_array($metaArray)) {
			$keys = array_keys($metaArray);
			foreach ($keys as $langFile) {
				foreach ($metaArray[$langFile] as $fileName => $informations) {
					$backup = array();

					// get path to filename
					$backupPath = $informations['pathBackup'];
					if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
						$pathSite = PATH_site;
					} else {
						$pathSite = Environment::getPublicPath() . '/';
					}

					$file = Typo3Lib::fixFilePath($pathSite . '/' . $backupPath . '/' . $fileName);
					$origFile = Typo3Lib::fixFilePath(
						$this->session->getDataByKey('extensionSelection') . '/' . $langFile
					);

					// check state
					if (!is_file($file)) {
						$backup['state'] = 'function.backupMgr.missing';
					} elseif (!is_file($origFile)) {
						$backup['state'] = 'lang.file.missing';
					} else {
						$backup['state'] = 'function.backupMgr.ok';
					}

					$backup['date'] = date('Y-m-d H:i:s', $informations['createdAt']);
					$backup['langFile'] = $langFile;
					$backup['fileName'] = $fileName;
					$backups[] = $backup;
				}
			}
		}
		return $backups;
	}

	/**
	 * Deletes all backups.
	 *
	 * @return void
	 */
	public function deleteAllBackupAction() {
		try {
			$this->initializeBackupObject();
			$delFiles = array();
			$metaArray = $this->backupService->getBackupObj()->getMetaInfos(2);
			foreach ($metaArray as $langFile => $metaPiece) {
				$files = array_keys($metaPiece);
				foreach ($files as $filename) {
					$delFiles[$filename] = $langFile;
				}
			}
			$this->backupService->execBackupDelete($delFiles);
			$this->addFlashMessage(
				LocalizationUtility::translate('function.backupMgr.success.deleteAll', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('manageBackups');
	}

	/**
	 * Deletes backup.
	 *
	 * @param string $fileName
	 * @param string $langFile
	 * @return void
	 */
	public function deleteBackupAction($fileName, $langFile) {
		try {
			$this->initializeBackupObject($langFile);
			$delFiles = array();
			$delFiles[$fileName] = '';
			$this->backupService->execBackupDelete($delFiles);
			$this->addFlashMessage(
				LocalizationUtility::translate('function.backupMgr.success.delete', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('manageBackups');
	}

	/**
	 * Uses backup to recover changes.
	 *
	 * @param string $fileName
	 * @param string $langFile
	 * @return void
	 */
	public function recoverBackupAction($fileName, $langFile) {
		try {
			$this->initializeBackupObject($langFile);
			// set backup file
			$metaArray = $this->backupService->getBackupObj()->getMetaInfos(3);
			if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
				$pathSite = PATH_site;
			} else {
				$pathSite = Environment::getPublicPath() . '/';
			}

			$information = array(
				'absPath' => $pathSite . $metaArray[$fileName]['pathBackup'],
				'relFile' => $fileName,
			);
			$this->backupService->getBackupObj()->setVar($information);
			$this->backupService->getBackupObj()->readFile();
			// read original file
			$this->configurationService->initFileObject(
				$this->backupService->getBackupObj()->getVar('langFile'),
				Typo3Lib::fixFilePath($pathSite . '/' . $this->backupService->getBackupObj()->getVar('extPath'))
			);
			// restore
			$this->backupService->execBackupRestore();
			$this->addFlashMessage(
				LocalizationUtility::translate('function.backupMgr.success.restore', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('manageBackups');
	}

	/**
	 * Shows differences between backup and original.
	 *
	 * @param string $fileName
	 * @param string $langFile
	 * @return void
	 */
	public function showDifferenceBackupAction($fileName, $langFile) {
		$this->redirect(
			'manageBackups', NULL, NULL, array('fileName' => $fileName, 'langFile' => $langFile, 'showDiff' => TRUE)
		);
	}

	/**
	 * Initializes backup object.
	 *
	 * @param string $langFile
	 * @throws LFException
	 * @throws \Exception
	 * @return void
	 */
	protected function initializeBackupObject($langFile = '') {
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		$information = array(
			'extPath' => SgLib::trimPath($pathSite, $this->session->getDataByKey('extensionSelection')),
			'langFile' => $langFile,
		);
		$this->backupService->initBackupObject('base', $information);
	}

	/**
	 * Generates content which illustrates differences between backup and current state.
	 *
	 * @param string $fileName
	 * @return void
	 */
	protected function generateDiffContent($fileName) {
		$localLangDiff = NULL;
		$metaDiff = NULL;
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$pathSite = PATH_site;
		} else {
			$pathSite = Environment::getPublicPath() . '/';
		}

		// set backup file
		$metaArray = $this->backupService->getBackupObj()->getMetaInfos(3);
		$informations = array(
			'absPath' => Typo3Lib::fixFilePath(
				$pathSite . '/' .
				$metaArray[$fileName]['pathBackup']
			),
			'relFile' => $fileName,
		);
		$this->backupService->getBackupObj()->setVar($informations);

		// exec diff
		// read original file
		$this->configurationService->initFileObject(
			$this->backupService->getBackupObj()->getVar('langFile'),
			Typo3Lib::fixFilePath($pathSite . '/' . $this->backupService->getBackupObj()->getVar('extPath'))
		);

		// read backup file
		$this->backupService->getBackupObj()->readFile();

		// get language data
		$originalLocalLang = $this->configurationService->getFileObj()->getLocalLangData();
		$backupLocalLang = $this->backupService->getBackupObj()->getLocalLangData();

		// get meta data
		$origMeta = $this->configurationService->getFileObj()->getMetaData();
		$backupMeta = $this->backupService->getBackupObj()->getMetaData();

		SgLib::fixMetaAttributes($origMeta);
		unset($originalLocalLang['trans-unit']);
		$localLangDiff = Functions::getBackupDiff(0, $originalLocalLang, $backupLocalLang);
		$metaDiff = Functions::getMetaDiff(0, $origMeta, $backupMeta);

		// generate diff
		if (count($localLangDiff)) {
			$this->outputManageBackupsDiff(
				$localLangDiff, $metaDiff, $originalLocalLang, $backupLocalLang,
				$this->configurationService->getFileObj()->getOriginLangData(),
				$this->backupService->getBackupObj()->getOriginLangData(),
				$origMeta, $backupMeta
			);
		}
	}

	/**
	 * Generates output of difference between backup and original.
	 *
	 * @param array $diff language content (difference between backup and origin)
	 * @param array $metaDiff meta content (difference between backup and origin)
	 * @param array $origLang original language content
	 * @param array $backupLang backup language content
	 * @param array $origOriginLang original origins of each language
	 * @param array $backupOriginLang backup origins of each language
	 * @param array $origMeta original meta content
	 * @param array $backupMeta backup meta content
	 * @return void
	 */
	protected function outputManageBackupsDiff(
		$diff, $metaDiff, $origLang, $backupLang, $origOriginLang, $backupOriginLang, $origMeta, $backupMeta
	) {
		$differences = array();

		// meta entry
		if (count($metaDiff)) {
			$difference = array();
			$difference['legend'] = LocalizationUtility::translate('function.backupMgr.diff.meta', 'lfeditor');
			$difference['constants'] = array();
			foreach ($metaDiff as $label => $value) {
				$constant = array();
				$constant['value'] = $value;
				$constant['label'] = $label;
				if (!isset($backupMeta[$label])) {
					$constant['state'] = self::LANG_CONST_ADDED;
				} elseif (!isset($origMeta[$label])) {
					$constant['state'] = self::LANG_CONST_DELETED;
				} else {
					$constant['state'] = self::LANG_CONST_NORMAL;
				}
				$difference['constants'][] = $constant;
			}
			$differences[] = $difference;
		}

		// loop each language entry
		foreach ($diff as $langKey => $data) {
			$difference = array();
			if (!count($data) && ($origOriginLang[$langKey] == $backupOriginLang[$langKey])) {
				continue;
			}
			try {
				$languageFile = Typo3Lib::transTypo3File($backupOriginLang[$langKey], FALSE);
			} catch (\Exception $e) {
				$languageFile = $backupOriginLang[$langKey];
			}
			$difference['legend'] = $langKey . ' (' . $languageFile . ')';
			$difference['constants'] = array();
			foreach ($data as $label => $value) {
				$constant = array();
				$constant['value'] = $value;
				$constant['label'] = $label;

				if (!isset($backupLang[$langKey][$label])) {
					$constant['state'] = self::LANG_CONST_ADDED;
				} elseif (!isset($origLang[$langKey][$label])) {
					$constant['state'] = self::LANG_CONST_DELETED;
				} else {
					$constant['state'] = self::LANG_CONST_NORMAL;
				}
				$difference['constants'][] = $constant;
			}
			$differences[] = $difference;
		}
		$this->view->assign('differences', $differences);
	}
}

?>
