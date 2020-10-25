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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * General controller. It contains extbase actions for general page.
 */
class GeneralController extends AbstractBackendController {
	/** Code for normal split operation */
	const NORMAL_SPLIT = 1;

	/** Code for merge operation */
	const MERGE = 2;

	/**
	 * Renders last-opened page. This action ai called upon opening LFEditor.
	 *
	 * @param bool $doStateRedirect
	 * @return void
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function indexAction($doStateRedirect = TRUE) {
		if ($doStateRedirect) {
			$this->redirectToLastCalledControllerActionPair();
		}

		$this->resetLastCalledControllerActionPair();
		$this->redirect('general', 'General');
	}

	/**
	 * Opens general view.
	 * It is called in 2 cases:
	 * - on selection of general option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 */
	public function generalAction() {
		try {
			$this->view->assign('controllerName', 'General');

			$this->prepareExtensionAndLangFileOptions();
			if (!$this->session->getDataByKey('languageFileSelection')) {
				throw new LFException('failure.select.noLangfile', 1);
			}
			$this->prepareGeneralViewMainSectionContent();
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in general view.
	 * It is called on change of selection of any select menu in general view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 *
	 * @return void
	 */
	public function changeSelectionAction($extensionSelection = NULL, $languageFileSelection = NULL) {
		$this->saveSelectionsInSession($extensionSelection, $languageFileSelection);
		$this->redirect('general');
	}

	/**
	 * Saves the changes made in main section of general view.
	 *
	 * @param string $authorName
	 * @param string $authorEmail
	 * @param string $metaDescription
	 * @param string $transformFile
	 * @param integer $splitFile
	 * @return void
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function generalSaveAction(
		$authorName = NULL, $authorEmail = NULL, $metaDescription = NULL, $transformFile = NULL, $splitFile = NULL
	) {
		try {
			$metaArray = array(
				'authorName' => $authorName,
				'authorEmail' => $authorEmail,
				'description' => $metaDescription
			);
			$this->configurationService->execWrite(array(), $metaArray);

			// split or merge
			if ($transformFile === 'xlf') {
				$splitFile = self::NORMAL_SPLIT;
			}
			if (($splitFile == self::NORMAL_SPLIT || $splitFile == self::MERGE)) {
				$langModes = array();
				// set vars
				if ($splitFile != self::NORMAL_SPLIT && $splitFile != self::MERGE) {
					$splitFile = 0;
				}
				$langKeys = Functions::buildLangArray();

				// generate langModes
				foreach ($langKeys as $langKey) {
					if (!isset($langModes[$langKey])) {
						$langModes[$langKey] = $splitFile;
					}
				}

				// exec split or merge
				$this->configurationService->execSplitFile($langModes);
				// reinitialize file object
				$this->configurationService->initFileObject(
					$this->session->getDataByKey('languageFileSelection'),
					$this->session->getDataByKey('extensionSelection')
				);
			}

			if (!empty($transformFile)
				&& $this->configurationService->getFileObj()->getVar('fileType') != $transformFile
			) {
				$newFile = SgLib::setFileExtension(
					$transformFile, $this->configurationService->getFileObj()->getVar('relFile')
				);
				$this->configurationService->execTransform($transformFile, $newFile);
				$this->clearSelectOptionsCache('extensionAndLangFileOptions');
			}

			$this->addFlashMessage(
				LocalizationUtility::translate('lang.file.write.success', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('general');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('general');
	}

	/**
	 * Prepares main section content of general view.
	 *
	 * @return void
	 * @throws LFException
	 */
	protected function prepareGeneralViewMainSectionContent() {
		$this->configurationService->initFileObject(
			$this->session->getDataByKey('languageFileSelection'),
			$this->session->getDataByKey('extensionSelection')
		);
		$extConfig = $this->configurationService->getExtConfig();
		$referenceLanguageSelection = $extConfig['defaultLanguage'];
		$langArray = $this->configurationService->getLangArray($this->backendUser);
		$infoArray = Functions::genGeneralInfoArray(
			$referenceLanguageSelection,
			$langArray, $this->configurationService->getFileObj()
		);
		$description = $infoArray['default']['meta']['description'];
		$langFileExtension = $this->configurationService->getFileObj()->getVar('fileType');
		$preselectMerge = $this->isOriginSameForAllLanguages($infoArray);

		$this->view->assign('infos', $infoArray);
		$this->view->assign('refLangNumTranslated', $infoArray[$referenceLanguageSelection]['numTranslated']);
		$this->view->assign('numTextAreaRows', $extConfig['numTextAreaRows']);
		$this->view->assign('metaDescription', $description);
		$this->view->assign('langFileExtension', $langFileExtension);
		$this->view->assign('preselectMerge', $preselectMerge);
	}

	/**
	 * Prepares parameters for redirection to viewTreeAction.
	 *
	 * @param string $language
	 * @return void
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function goToEditFileAction($language) {
		$this->session->setDataByKey('languageSelection', $language);
		$this->redirect('editFile', 'EditFile');
	}

	/**
	 * Switches between override mode and normal mode.
	 *
	 * @param string $editingMode 'extension', 'l10n', 'override'.
	 * @return void
	 * @throws UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	public function switchEditingModeAction($editingMode = 'extension') {
		$this->session->setDataByKey('editingMode', $editingMode);
		$this->indexAction();
	}

	/**
	 * Checks do all language translations originate from same file.
	 *
	 * @param array $infoArray
	 * @return bool
	 */
	private function isOriginSameForAllLanguages(array $infoArray) {
		foreach ($infoArray as $langInfo) {
			if ($infoArray['default']['origin'] !== $langInfo['origin']) {
				return FALSE;
			}
		}
		return TRUE;
	}
}

?>
