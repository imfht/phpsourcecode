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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * RenameConstant controller. It contains extbase actions for RenameConstant page.
 */
class RenameConstantController extends AbstractBackendController {
	/**
	 * Opens renameConstant view.
	 * It is called in 2 cases:
	 * - on selection of renameConstant option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 */
	public function renameConstantAction() {
		try {
			$this->view->assign('controllerName', 'RenameConstant');

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
			$this->prepareRenameConstantViewMainSectionContent();
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in renameConstant view.
	 * It is called on change of selection of any select menu in renameConstant view.
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
		$this->redirect('renameConstant');
	}

	/**
	 * Saves the changes made in main section of renameConstant view.
	 *
	 * @param string $newConstantName
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function renameConstantSaveAction($newConstantName) {
		try {
			$oldConstantName = $this->session->getDataByKey('constantSelection');
			$extConfig = $this->configurationService->getExtConfig();

			if ($oldConstantName === $newConstantName) {
				throw new LFException('failure.langfile.noChange');
			}

			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);

			$langData = $this->configurationService->getFileObj()->getLocalLangData();
			$constExists = !empty($langData['default'][$newConstantName])
				|| !empty($langData[$extConfig['defaultLanguage']][$newConstantName]);
			if ($constExists) {
				throw new LFException('failure.langfile.constExists');
			}

			$langArray = Functions::buildLangArray();
			$newLang = array();
			foreach ($langArray as $lang) {
				if (isset($langData[$lang][$oldConstantName])) {
					$newLang[$lang][$newConstantName] = $langData[$lang][$oldConstantName];
				}
				$newLang[$lang][$oldConstantName] = '';
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
		$this->redirect('renameConstant');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('renameConstant');
	}

	/**
	 * Prepares main section content of deleteConstant view.
	 *
	 * @throws LFException
	 * @return void
	 */
	protected function prepareRenameConstantViewMainSectionContent() {
		$constantSelection = $this->session->getDataByKey('constantSelection');
		if (empty($constantSelection) || $constantSelection == '###default###') {
			throw new LFException('failure.select.noConst', 1);
		}

		$this->view->assign('constantSelection', $constantSelection);
	}
}

?>
