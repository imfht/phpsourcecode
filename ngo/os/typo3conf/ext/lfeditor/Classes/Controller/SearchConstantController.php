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
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;

/**
 * SearchConstant controller. It contains extbase actions for SearchConstant page.
 */
class SearchConstantController extends AbstractBackendController {
	/**
	 * Opens searchConstant view.
	 * It is called in 2 cases:
	 * - on selection of searchConstant option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @param bool $searchDone
	 * @return void
	 */
	public function searchConstantAction($searchDone = FALSE) {
		try {
			$this->view->assign('controllerName', 'SearchConstant');

			$this->prepareExtensionAndLangFileOptions();
			$this->prepareSearchConstantViewMainSectionContent($searchDone);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in searchConstant view.
	 * It is called on change of selection of any select menu in searchConstant view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction($extensionSelection = NULL, $languageFileSelection = NULL) {
		$this->saveSelectionsInSession($extensionSelection, $languageFileSelection);
		$this->redirect('searchConstant');
	}

	/**
	 * Saves the changes made in main section of searchConstant view.
	 *
	 * @param boolean $caseSensitive
	 * @param string $searchStr
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function searchConstantSearchAction($caseSensitive, $searchStr) {
		$searchResultArray = array();
		try {
			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);
			$langData = $this->configurationService->getFileObj()->getLocalLangData();
			$viewLanguages = $this->configurationService->getLangArray($this->backendUser);

			$searchOptions = $caseSensitive ? '' : 'i';
			if (!preg_match('/^\/.*\/.*$/', $searchStr) && !empty($searchStr)) {
				foreach ($viewLanguages as $langKey) {
					if (!is_array($langData[$langKey])) {
						continue;
					}
					foreach ($langData[$langKey] as $labelKey => $labelValue) {
						$labelKeyOrValueContainSearchStr = preg_match(
								'/' . $searchStr . '/' . $searchOptions, $labelKey
							) || preg_match('/' . $searchStr . '/' . $searchOptions, $labelValue);
						if ($labelKeyOrValueContainSearchStr) {
							$searchResultArray[$langKey][$labelKey] = $labelValue;
						}
					}

				}
				if (!count($searchResultArray)) {
					throw new LFException('failure.search.noConstants', 1);
				}
			} else {
				throw new LFException('function.const.search.enterSearchStr', 1);
			}
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->session->setDataByKey('searchResultArray', $searchResultArray);
		$this->session->setDataByKey('searchString', $searchStr);
		$this->session->setDataByKey('searchCaseSensitive', $caseSensitive);

		$this->redirect('searchConstant', NULL, NULL, array('searchDone' => TRUE));
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('searchConstant', NULL, NULL, array('searchDone' => TRUE));
	}

	/**
	 * Prepares main section content of searchConstant view.
	 *
	 * @param bool $searchDone
	 * @return void
	 */
	protected function prepareSearchConstantViewMainSectionContent($searchDone) {
		$searchResultArray = array();
		if ($searchDone) {
			$searchResultArray = $this->session->getDataByKey('searchResultArray');
		} else {
			$this->session->setDataByKey('searchResultArray', array());
		}
		$searchString = $this->session->getDataByKey('searchString');
		$searchCaseSensitive = $this->session->getDataByKey('searchCaseSensitive');

		$this->view->assign('searchResultArray', $searchResultArray);
		$this->view->assign('searchString', $searchString);
		$this->view->assign('searchCaseSensitive', $searchCaseSensitive);
	}
}

?>
