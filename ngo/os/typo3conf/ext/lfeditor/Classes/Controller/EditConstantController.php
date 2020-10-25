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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * EditConstant controller. It contains extbase actions for EditConstant page.
 */
class EditConstantController extends AbstractBackendController {
	/**
	 * Opens editConstant view.
	 * It is called in 2 cases:
	 * - on selection of editConstant option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 */
	public function editConstantAction() {
		try {
			$this->view->assign('controllerName', 'EditConstant');

			$this->prepareExtensionAndLangFileOptions();
			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);

			$langData = $this->configurationService->getFileObj()->getLocalLangData();
			$constantOptions = $this->configurationService->menuConstList(
				$langData, LocalizationUtility::translate('select.nothing', 'lfeditor')
			);
			$this->assignViewWidthMenuVariables('constant', $constantOptions);

			$this->prepareEditConstantViewMainSectionContent($langData);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}

		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in editConstant view.
	 * It is called on change of selection of any select menu in editConstant view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @param string $constantSelection
	 * @return void
	 */
	public function changeSelectionAction(
		$extensionSelection = NULL, $languageFileSelection = NULL, $constantSelection = NULL
	) {
		$this->saveSelectionsInSession($extensionSelection, $languageFileSelection, NULL, $constantSelection);
		$this->redirect('editConstant');
	}

	/**
	 * Saves the changes made in main section of editConstant view.
	 *
	 * @param array $editConstTextArea
	 * @return void
	 */
	public function editConstantSaveAction(array $editConstTextArea) {
		try {
			$this->configurationService->execWrite($editConstTextArea);

			$this->addFlashMessage(
				LocalizationUtility::translate('lang.file.write.success', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('editConstant');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('editConstant');
	}

	/**
	 * Prepares main section content of editConstant view.
	 *
	 * @param array $langData
	 * @throws LFException
	 * @return void
	 */
	protected function prepareEditConstantViewMainSectionContent(array $langData = NULL) {
		$extConfig = $this->configurationService->getExtConfig();
		$langArray = $this->configurationService->getLangArray($this->backendUser);

		$constantSelection = $this->session->getDataByKey('constantSelection');
		if (empty($constantSelection) || $constantSelection == '###default###') {
			throw new LFException('failure.select.noConst', 1);
		}

		$languages = array();
		foreach ($langArray as $lang) {
			if ($langData[$lang]) {
				$languages[$lang] = $langData[$lang][$constantSelection];
			} else {
				$languages[$lang] = '';
			}
		}

		$this->view->assign('constantSelection', $constantSelection);
		$this->view->assign('languages', $languages);
		$this->view->assign('numTextAreaRows', $extConfig['numTextAreaRows']);
		$this->view->assign('defaultLanguage', $extConfig['defaultLanguage']);
	}

	/**
	 * Sets chosen constant and redirects to editConstant view.
	 *
	 * @param string $constantKey
	 * @return void
	 */
	protected function prepareEditConstantAction($constantKey) {
		$this->session->setDataByKey('constantSelection', $constantKey);

		$this->redirect('editConstant', 'EditConstant');
	}
}

?>
