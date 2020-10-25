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
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ViewTree controller. It contains extbase actions for ViewTree page.
 */
class ViewTreeController extends AbstractBackendController {
	/*********************************************
	 *  Actions called from viewTree view  *
	 *********************************************/

	/**
	 * Opens viewTree view.
	 * It is called in 2 cases:
	 * - on selection of viewTree option in main menu,
	 * - after redirection from action which must not change the view.
	 *
	 * @return void
	 */
	public function viewTreeAction() {
		try {
			$this->view->assign('controllerName', 'ViewTree');

			$this->prepareExtensionAndLangFileOptions();
			$this->configurationService->initFileObject(
				$this->session->getDataByKey('languageFileSelection'),
				$this->session->getDataByKey('extensionSelection')
			);
			$languageOptions = $this->configurationService->menuLangList(
				$this->configurationService->getFileObj()->getLocalLangData(), '', $this->backendUser
			);
			$this->assignViewWidthMenuVariables('language', $languageOptions);
			$referenceLanguageOptions = $this->configurationService->menuLangList(
				$this->configurationService->getFileObj()->getLocalLangData(),
				LocalizationUtility::translate('select.nothing', 'lfeditor'), $this->backendUser
			);
			$this->assignViewWidthMenuVariables('referenceLanguage', $referenceLanguageOptions);
			$this->prepareViewTreeViewMainSectionContent();
		} catch (LFException $e) {
			$this->addLFEFlashMessage($e);
		}
		$this->commonViewRenderingActionSettings();
	}

	/**
	 * This action saves in session currently selected options from selection menus in viewTree view.
	 * It is called on change of selection of any select menu in viewTree view.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @param string $languageSelection
	 * @param string $referenceLanguageSelection
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function changeSelectionAction(
		$extensionSelection = NULL, $languageFileSelection = NULL, $languageSelection = NULL,
		$referenceLanguageSelection = NULL
	) {
		$this->saveSelectionsInSession(
			$extensionSelection, $languageFileSelection, $referenceLanguageSelection, NULL, $languageSelection
		);
		$this->redirect('viewTree');
	}

	/**
	 * Selects explodeToken.
	 *
	 * @param string $explodeToken
	 * @throws UnsupportedRequestTypeException
	 * @return void
	 */
	public function selectExplodeTokenAction($explodeToken) {
		$this->session->setDataByKey('explodeToken', $explodeToken);
		$this->redirect('viewTree');
	}

	/**
	 * Clears extensionAndLangFileOptions cache, and in that way refreshes list of language file options in select box.
	 *
	 * @return void
	 */
	public function refreshLanguageFileListAction() {
		$this->clearSelectOptionsCache('extensionAndLangFileOptions');
		$this->redirect('viewTree');
	}

	/**
	 * Prepares main section content of viewTree view.
	 *
	 * @return void
	 */
	protected function prepareViewTreeViewMainSectionContent() {
		$explodeToken = $this->session->getDataByKey('explodeToken');
		if ($explodeToken === NULL) {
			$explodeToken = '.';
		}

		$langData = $this->configurationService->getFileObj()->getLocalLangData();
		$refLangSelection = $this->session->getDataByKey('referenceLanguageSelection');
		$languageSelection = $this->session->getDataByKey('languageSelection');
		$tree = Functions::genTreeInfoArray($langData[$languageSelection], $langData[$refLangSelection], $explodeToken);
		$extConfig = $this->configurationService->getExtConfig();
		$treeHide = $extConfig['treeHide'];

		$fluidTree = array();
		$levelIndex = 0;
		$this->addLevelElementsToFluidTree($tree, $levelIndex, NULL, $fluidTree, $treeHide);

		$this->view->assign('fluidTree', $fluidTree);
		$this->view->assign('treeHide', $treeHide);
		$this->view->assign('explodeToken', $explodeToken);
	}

	/**
	 * @var integer Width of margin which is added if a branch is missing.
	 */
	protected $marginLeftSpaceUnit = 18;

	/**
	 * Makes tree structure which contains constants. The structure is optimised for recursive use on fluid pages.
	 *
	 * @param array $sourceTree
	 * @param int $level
	 * @param string $parentKey
	 * @param array $fluidTree This structure is used for setting tree-data for use in fluid.
	 * @param boolean $treeHide Default state of tree (TRUE - hidden, FALSE - opened)
	 * @throws LFException
	 * @return void
	 */
	protected function addLevelElementsToFluidTree(
		array $sourceTree, $level, $parentKey, array &$fluidTree, $treeHide
	) {
		if (empty($sourceTree)) {
			throw new LFException('failure.select.emptyLanguage', 1);
		}
		$constKeys = array_keys($sourceTree[$level]);
		$index = 0;
		foreach ($sourceTree[$level] as $constKey => $treeNode) {
			if ($level === 0 || $treeNode['parent'] === $parentKey) {
				$fluidTreeElem = array();
				$fluidTreeElem['label'] = $treeNode['name'];
				$fluidTreeElem['parent'] = $fluidTree; //$treeNode['parent'];//; //
				$fluidTreeElem['type'] = $treeNode['type'];
				$fluidTreeElem['isBottom'] = (!array_key_exists($index + 1, $constKeys) ||
					$sourceTree[$level][$constKeys[$index + 1]]['parent'] !== $treeNode['parent']) ? 1 : 0;

				$icons = $this->prepareTreeIcons($level, $treeHide, $fluidTreeElem, $treeNode['childs'] !== NULL);
				$fluidTreeElem['icons'] = $icons;

				if ($sourceTree[$level + 1]) {
					$this->addLevelElementsToFluidTree(
						$sourceTree, $level + 1, $constKey, $fluidTreeElem, $treeHide
					);
				}
				if ($level > 0) {
					$fluidTree['children'][$constKey] = $fluidTreeElem;
				} else {
					$fluidTree[$constKey] = $fluidTreeElem;
				}
			}
			$index++;
		}
	}

	/**
	 * Sorts and adds icons in tree structure.
	 *
	 * @param int $level Tree level of constant.
	 * @param boolean $treeHide Indicator which shows should all tree elements be closed (hidden) by default.
	 * @param array $fluidTreeElem Element of tree which is built for use on fluid page.
	 * @param boolean $hasChildren Indicator does this tree element have children
	 * @return array
	 */
	protected function prepareTreeIcons($level, $treeHide, array $fluidTreeElem, $hasChildren) {
		$icons = array();
		$leftMargins = array();
		$marginLeftSpaceCounter = 0;
		for ($iconLevel = $level, $currentFluidTreeElem = $fluidTreeElem;
			 $currentFluidTreeElem;
			 $iconLevel--, $currentFluidTreeElem = $currentFluidTreeElem['parent']) {
			$iconName = '.png';
			if ($iconLevel === $level) {
				if ($currentFluidTreeElem['isBottom']) {
					$iconName = 'Bottom' . $iconName;
				}
				if ($hasChildren) {
					$iconName = 'tree' . ($treeHide && $level != 0 ? 'Plus' : 'Minus') . $iconName;
				} else {
					$iconName = 'join' . $iconName;
				}
			} else {
				if (!$currentFluidTreeElem['isBottom']) {
					$iconName = 'line' . $iconName;
					$leftMargins[] = $marginLeftSpaceCounter;
					$marginLeftSpaceCounter = 0;
				} else {
					$marginLeftSpaceCounter += $this->marginLeftSpaceUnit;
					continue;
				}
			}
			$icons[] = array('name' => $iconName);
		}
		$leftMargins[] = $marginLeftSpaceCounter;
		for ($iterator = 0, $iconsSize = count($icons); $iterator < $iconsSize; $iterator++) {
			$icons[$iterator]['marginLeft'] = $leftMargins[$iterator];
		}
		return $icons;
	}
}

?>
