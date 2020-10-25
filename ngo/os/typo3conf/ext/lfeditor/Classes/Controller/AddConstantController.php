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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * AddConstant controller. It contains extbase actions for AddConstant page.
 */
class AddConstantController extends AbstractBackendController {
	/**
	 * Opens addConstant view.
	 * It is called in 2 cases:
	 * - on selection of addConstant option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 */
	public function addConstantAction() {
		try {
			$this->view->assign('controllerName', 'AddConstant');

			$this->prepareExtensionAndLangFileOptions();
			$this->prepareAddConstantViewMainSectionContent();
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in addConstant view.
	 * It is called on change of selection of any select menu in addConstant view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction($extensionSelection = NULL, $languageFileSelection = NULL) {
		$this->saveSelectionsInSession($extensionSelection, $languageFileSelection, NULL, NULL);
		$this->redirect('addConstant');
	}

	/**
	 * Saves the changes made in main section of addConstant view.
	 *
	 * @param string $nameOfConstant
	 * @param array $addConstTextArea
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function addConstantSaveAction($nameOfConstant, array $addConstTextArea) {
		try {
			if (empty($nameOfConstant)) {
				throw new LFException('failure.select.noConstDefined');
			}
			$extConfig = $this->configurationService->getExtConfig();

			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);

			$langData = $this->configurationService->getFileObj()->getLocalLangData();

			$constExists = !empty($langData['default'][$nameOfConstant])
				|| !empty($langData[$extConfig['defaultLanguage']][$nameOfConstant]);
			if ($constExists) {
				throw new LFException('failure.langfile.constExists');
			}

			$newConstLanguages = array();
			foreach ($addConstTextArea as $lang => $value) {
				$newConstLanguages[$lang][$nameOfConstant] = $value;
			}

			$this->configurationService->execWrite($newConstLanguages);
			$this->session->setDataByKey('constantSelection', $nameOfConstant);

			$this->addFlashMessage(
				LocalizationUtility::translate('lang.file.write.success', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('addConstant');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('addConstant');
	}

	/**
	 * Prepares main section content of addConstant view.
	 *
	 * @throws LFException
	 * @return void
	 */
	protected function prepareAddConstantViewMainSectionContent() {
		$extConfig = $this->configurationService->getExtConfig();
		$langArray = $this->configurationService->getLangArray($this->backendUser);

		$this->view->assign('languages', $langArray);
		$this->view->assign('numTextAreaRows', $extConfig['numTextAreaRows']);
	}
}

?>
