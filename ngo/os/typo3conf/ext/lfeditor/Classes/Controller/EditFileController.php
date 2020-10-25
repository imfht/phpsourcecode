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
 * EditFile controller. It contains extbase actions of EditFile page.
 */
class EditFileController extends AbstractBackendController {
	/**
	 * Opens editFile view.
	 * It is called in 2 cases:
	 * - on selection of editFile option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @param integer $buttonType :
	 *                    -1 - cancel,
	 *                     0 - no button clicked,
	 *                     1 - back,
	 *                     2 - next,
	 *                     3 - save.
	 * @return void
	 */
	public function editFileAction($buttonType = 0) {
		try {
			$this->view->assign('controllerName', 'EditFile');

			$this->prepareExtensionAndLangFileOptions();
			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);
			$langData = $this->configurationService->getFileObj()->getLocalLangData();

			$languageOptions = $this->configurationService->menuLangList($langData, '', $this->backendUser);
			$this->assignViewWidthMenuVariables('language', $languageOptions);

			$referenceLanguageOptions = $this->configurationService->menuLangList(
				$langData, LocalizationUtility::translate('select.nothing', 'lfeditor'), $this->backendUser
			);
			$this->assignViewWidthMenuVariables('referenceLanguage', $referenceLanguageOptions);

			$bottomReferenceLanguageOptions = $this->configurationService->menuLangList(
				$langData, '', $this->backendUser, TRUE
			);
			$this->assignViewWidthMenuVariables('bottomReferenceLanguage', $bottomReferenceLanguageOptions);

			$constantTypeOptions = $this->getConstantTypeOptions();
			$this->assignViewWidthMenuVariables('constantType', $constantTypeOptions);

			$extConfig = $this->configurationService->getExtConfig();
			$this->assignViewWidthMenuVariables('numSiteConsts', $extConfig['numSiteConstsOptions']);

			$this->prepareEditFileViewMainSectionContent($langData, $buttonType);
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in editFile view.
	 * It is called on change of selection of any select menu in editFile view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @param string $languageSelection
	 * @param string $referenceLanguageSelection
	 * @param string $constantTypeSelection
	 * @param string $bottomReferenceLanguageSelection
	 * @param string $numSiteConstsSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction(
		$extensionSelection = NULL, $languageFileSelection = NULL, $languageSelection = NULL,
		$referenceLanguageSelection = NULL, $constantTypeSelection = NULL, $bottomReferenceLanguageSelection = NULL,
		$numSiteConstsSelection = NULL
	) {
		$this->saveSelectionsInSession(
			$extensionSelection, $languageFileSelection, $referenceLanguageSelection, NULL, $languageSelection,
			$constantTypeSelection, $bottomReferenceLanguageSelection, $numSiteConstsSelection
		);
		$this->redirect('editFile', NULL, NULL, array('buttonType' => 0));
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('editFile', NULL, NULL, array('buttonType' => 0));
	}

	/**
	 * Prepares main section content of editFile view.
	 * Structure of the content:
	 * $constValues[$constant]['edit'],
	 * $constValues[$constant]['pattern'],
	 * $constValues[$constant][$extConfig['defaultLanguage']].
	 *
	 * @param array $langData
	 * @param integer $buttonType :
	 *                    -1 - cancel,
	 *                     0 - no button clicked,
	 *                     1 - back,
	 *                     2 - next,
	 *                     3 - save.
	 * @throws LFException
	 * @return void
	 */
	protected function prepareEditFileViewMainSectionContent(array $langData, $buttonType) {
		$extConfig = $this->configurationService->getExtConfig();
		$numConstantsPerPage = $this->session->getDataByKey('numSiteConstsSelection');

		$langList = $this->session->getDataByKey('languageSelection');
		$patternList = $this->session->getDataByKey('referenceLanguageSelection');
		$constTypeList = $this->session->getDataByKey('constantTypeSelection');
		$parallelEdit = $patternList != '###default###' && $patternList != $langList;

		$langEdit = is_array($langData[$langList]) ? $langData[$langList] : array();
		$langPattern = is_array($langData[$patternList]) ? $langData[$patternList] : array();
		$bottomReferenceLanguageSelection = $this->session->getDataByKey('bottomReferenceLanguageSelection');
		$bottomReferenceLanguage = is_array($langData[$bottomReferenceLanguageSelection])
			? $langData[$bottomReferenceLanguageSelection] : array();
		if (empty($bottomReferenceLanguage)) {
			$bottomReferenceLanguage = is_array($langData['default']) ? $langData['default'] : array();
		}

		$langDataSessionContinued = $buttonType != 3;
		if ($buttonType === 0) {
			$this->session->setDataByKey('sessionLangDataConstantsIterator', 0);
			$this->session->setDataByKey('numberLastPageConstants', 0);
		}
		$sessionLangDataConstantsIterator = $this->session->getDataByKey('sessionLangDataConstantsIterator');
		$numberLastPageConstants = $this->session->getDataByKey('numberLastPageConstants');

		// new translation
		if (!$langDataSessionContinued || $buttonType <= 0) {
			// adjust number of session constants
			if ($constTypeList == 'untranslated' || $constTypeList == 'translated' ||
				$constTypeList == 'unknown' || $buttonType <= 0
			) {
				$sessionLangDataConstantsIterator = 0;
			} elseif (!$langDataSessionContinued) // session written to file
			{
				$sessionLangDataConstantsIterator -= $numberLastPageConstants;
			}

			// delete old data in session
			$this->session->setDataByKey('langfileEditNewLangData', NULL);
			$this->session->setDataByKey('langfileEditConstantsList', NULL);

			// get language data
			if ($constTypeList == 'untranslated') {
				$myLangData = array_diff_key($bottomReferenceLanguage, $langEdit);
			} elseif ($constTypeList == 'unknown') {
				$myLangData = array_diff_key($langEdit, $bottomReferenceLanguage);
			} elseif ($constTypeList == 'translated') {
				$myLangData = array_intersect_key($bottomReferenceLanguage, $langEdit);
			} else {
				$myLangData = $bottomReferenceLanguage;
			}
			$this->session->setDataByKey('langfileEditConstantsList', array_keys($myLangData));
		} elseif ($buttonType == 1) // back button
		{
			$sessionLangDataConstantsIterator -= ($numConstantsPerPage + $numberLastPageConstants);
		}

		// get language constants
		$langData = $this->session->getDataByKey('langfileEditConstantsList');
		$numConsts = count($langData);
		if (!count($langData)) {
			throw new LFException('failure.select.emptyLangDataArray', 1);
		}
		$langfileEditNewLangData = $this->session->getDataByKey('langfileEditNewLangData');

		// prepare constant list for this page
		$numberLastPageConstants = 0;
		$constValues = array();
		do {
			// check number of session constants
			if ($sessionLangDataConstantsIterator >= $numConsts) {
				break;
			}
			++$numberLastPageConstants;

			// set constant value (maybe already changed in this session)
			$constant = $langData[$sessionLangDataConstantsIterator];
			$editLangVal = $langEdit[$constant];
			if (!isset($langfileEditNewLangData[$langList][$constant])) {
				$langfileEditNewLangData[$langList][$constant] = $editLangVal;
			} else {
				$editLangVal = $langfileEditNewLangData[$langList][$constant];
			}

			// set constant value (maybe already changed in this session)
			$editPatternVal = $langPattern[$constant];
			if (!isset($langfileEditNewLangData[$patternList][$constant])) {
				$langfileEditNewLangData[$patternList][$constant] = $editPatternVal;
			} else {
				$editPatternVal =
					$langfileEditNewLangData[$patternList][$constant];
			}

			// save information about the constant
			$constValues[$constant]['edit'] = $editLangVal;
			$constValues[$constant]['pattern'] = $editPatternVal;
			$constValues[$constant]['default'] = $bottomReferenceLanguage[$constant];
		} while (++$sessionLangDataConstantsIterator % $numConstantsPerPage);

		$this->session->setDataByKey('langfileEditNewLangData', $langfileEditNewLangData);
		$this->session->setDataByKey('sessionLangDataConstantsIterator', $sessionLangDataConstantsIterator);
		$this->session->setDataByKey('numberLastPageConstants', $numberLastPageConstants);

		$this->view->assign('numTextAreaRows', $extConfig['numTextAreaRows']);
		$this->view->assign('defaultLanguage', $extConfig['defaultLanguage']);
		$this->view->assign('parallelEdit', $parallelEdit);
		$this->view->assign('displayBackButton', $sessionLangDataConstantsIterator > $numConstantsPerPage);
		$this->view->assign('displayNextButton', $sessionLangDataConstantsIterator < $numConsts);

		$this->view->assign('constValues', $constValues);
		$this->view->assign('curConsts', $sessionLangDataConstantsIterator);
		$this->view->assign('totalConsts', $numConsts);
		$this->view->assign('numSiteConstsSelection', $numConstantsPerPage);
	}

	/**
	 * Used for the constant type selector
	 *
	 * @return array
	 */
	private function getConstantTypeOptions() {
		$constantTypeOptions['all'] = LocalizationUtility::translate('const.type.all', 'lfeditor');
		$constantTypeOptions['translated'] = LocalizationUtility::translate('const.type.translated', 'lfeditor');
		$constantTypeOptions['unknown'] = LocalizationUtility::translate('const.type.unknown', 'lfeditor');
		$constantTypeOptions['untranslated'] = LocalizationUtility::translate('const.type.untranslated', 'lfeditor');
		return $constantTypeOptions;
	}

	/**
	 * Saves the changes made in main section of editFile view.
	 *
	 * @param integer $buttonType
	 * @param array $editFileTextArea editFileTextArea[{languageSelection}][{constKey}]
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function editFileSaveAction($buttonType, array $editFileTextArea) {
		try {
			$languageSelection = $this->session->getDataByKey('languageSelection');
			$referenceLanguageSelection = $this->session->getDataByKey('referenceLanguageSelection');
			$langDataSessionContinued = $buttonType != 3;

			$langfileEditNewLangData = $this->session->getDataByKey('langfileEditNewLangData');
			$langfileEditNewLangData[$languageSelection] =
				array_merge(
					$langfileEditNewLangData[$languageSelection],
					$editFileTextArea[$languageSelection]
				);

			// parallel edit mode?
			if ($referenceLanguageSelection != '###default###' && $referenceLanguageSelection != $languageSelection) {
				$langfileEditNewLangData[$referenceLanguageSelection] =
					array_merge(
						$langfileEditNewLangData[$referenceLanguageSelection],
						$editFileTextArea[$referenceLanguageSelection]
					);
			}
			$this->session->setDataByKey('langfileEditNewLangData', $langfileEditNewLangData);

			// write if no session continued
			if (!$langDataSessionContinued) {
				// Making array of languages that were changed, so only that language files will be edited.
				$editedLanguages = array($languageSelection);
				if ($languageSelection !== $referenceLanguageSelection) {
					$editedLanguages[] = $referenceLanguageSelection;
				}

				$this->configurationService->execWrite($langfileEditNewLangData, array(), FALSE, $editedLanguages);
				$this->addFlashMessage(
					LocalizationUtility::translate('lang.file.write.success', 'lfeditor'),
					'',
					$severity = AbstractMessage::OK,
					$storeInSession = TRUE
				);
			}
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->redirect('editFile', NULL, NULL, array('buttonType' => $buttonType));
	}
}

?>
