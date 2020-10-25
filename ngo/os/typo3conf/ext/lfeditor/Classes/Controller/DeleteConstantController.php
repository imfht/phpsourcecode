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
use SGalinski\Lfeditor\Service\FileOverrideService;
use SGalinski\Lfeditor\Utility\Functions;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * DeleteConstant controller. It contains extbase actions for DeleteConstant page.
 */
class DeleteConstantController extends AbstractBackendController {
	/**
	 * Opens deleteConstant view.
	 * It is called in 2 cases:
	 * - on selection of deleteConstant option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 */
	public function deleteConstantAction() {
		try {
			$this->view->assign('controllerName', 'DeleteConstant');

			$this->prepareExtensionAndLangFileOptions();
			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);
			$fileObject = $this->configurationService->getFileObj();
			if ($this->session->getDataByKey('editingMode') === 'override') {
				/** @var FileOverrideService $overrideFileObject */
				$overrideFileObject = $fileObject;
				$overrideFileObject->deleteDuplicates();
			}

			$langData = $fileObject->getLocalLangData();
			$constantOptions = $this->configurationService->menuConstList(
				$langData, LocalizationUtility::translate('select.nothing', 'lfeditor')
			);
			$this->assignViewWidthMenuVariables('constant', $constantOptions);
			$this->prepareDeleteConstantViewMainSectionContent();
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in deleteConstant view.
	 * It is called on change of selection of any select menu in deleteConstant view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @param string $constantSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction(
		$extensionSelection = NULL, $languageFileSelection = NULL, $constantSelection = NULL
	) {
		$this->saveSelectionsInSession($extensionSelection, $languageFileSelection, NULL, $constantSelection);
		$this->redirect('deleteConstant');
	}

	/**
	 * Saves the changes made in main section of deleteConstant view.
	 *
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function deleteConstantSaveAction() {
		try {
			$constantSelection = $this->session->getDataByKey('constantSelection');
			$langArray = Functions::buildLangArray();

			// build modArray
			$newLang = array();
			foreach ($langArray as $lang) {
				$newLang[$lang][$constantSelection] = '';
			}

			$this->configurationService->execWrite($newLang, array(), TRUE);

			$this->addFlashMessage(
				LocalizationUtility::translate('lang.file.write.success', 'lfeditor'),
				'',
				$severity = AbstractMessage::OK,
				$storeInSession = TRUE
			);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('deleteConstant');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('deleteConstant');
	}

	/**
	 * Prepares main section content of deleteConstant view.
	 *
	 * @throws LFException
	 * @return void
	 */
	protected function prepareDeleteConstantViewMainSectionContent() {
		$constantSelection = $this->session->getDataByKey('constantSelection');
		if (empty($constantSelection) || $constantSelection == '###default###') {
			throw new LFException('failure.select.noConst', 1);
		}

		$this->view->assign('constantSelection', $constantSelection);
	}
}

?>
