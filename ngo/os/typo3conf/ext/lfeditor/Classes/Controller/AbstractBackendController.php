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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Abstract Backend Controller
 */
abstract class AbstractBackendController extends AbstractController {
	/**
	 * @var BackendUserAuthentication
	 */
	protected $backendUser;

	/**
	 * Initializes any action
	 *
	 * @return void
	 * @throws \SGalinski\Lfeditor\Exceptions\DirectoryAccessRightsException
	 */
	public function initializeAction() {
		parent::initializeAction();

		if (TYPO3_MODE === 'BE') {
			$this->backendUser = $GLOBALS['BE_USER'];
		}

		$editingMode = $this->session->getDataByKey('editingMode');
		$availableEditingModes = $this->configurationService->getAvailableEditingModes();
		if ($this->backendUser->isAdmin()) {
			if (!\array_key_exists($editingMode, $availableEditingModes)) {
				$firstAvailableEditMode = \key($availableEditingModes);
				$this->session->setDataByKey('editingMode', $firstAvailableEditMode);
			}
			$canChangeEditingModes = \count($availableEditingModes) > 0;
		} else {
			$canChangeEditingModes = \count($availableEditingModes) > 0
			                         && $this->backendUser->user['lfeditor_change_editing_modes'] !== 0;
			if (!$canChangeEditingModes || !\array_key_exists($editingMode, $availableEditingModes)) {
				\end($availableEditingModes);
				$lastAvailableEditMode = \key($availableEditingModes);
				$this->session->setDataByKey('editingMode', $lastAvailableEditMode);
			}
		}
		$this->session->setDataByKey('defaultLanguagePermission', $this->backendUser->checkLanguageAccess(0));
		$this->session->setDataByKey('canChangeEditingModes', $canChangeEditingModes);
	}

	/**
	 * Saves the the last called controller/action pair into the backend user
	 * configuration if available
	 *
	 * @param bool $saveWithRedirectPair
	 * @return void
	 */
	protected function setLastCalledControllerActionPair($saveWithRedirectPair = TRUE) {
		if (!$this->backendUser) {
			return;
		}

		$extensionKey = $this->request->getControllerExtensionKey();
		$pair = array(
			'action' => $this->request->getControllerActionName(),
			'controller' => $this->request->getControllerName(),
		);

		$this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPair'] = $pair;
		if ($saveWithRedirectPair) {
			$this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPairForRedirect'] = $pair;
		}
		$this->backendUser->writeUC($this->backendUser->uc);
	}

	/**
	 * Resets the last called controller/action pair combination from the
	 * backend user session
	 *
	 * @return void
	 */
	protected function resetLastCalledControllerActionPair() {
		if (!$this->backendUser) {
			return;
		}

		$extensionKey = $this->request->getControllerExtensionKey();
		$this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPair'] = array();
		$this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPairForRedirect'] = array();
		$this->backendUser->writeUC($this->backendUser->uc);

		if ($this->view instanceof ViewInterface) {
			$this->view->assign('lastCalledControllerActionPair', NULL);
		}
	}

	/**
	 * Returns the last called controller/action pair from the backend user session
	 *
	 * @return array
	 */
	protected function getLastCalledControllerActionPair() {
		if (!$this->backendUser) {
			return array();
		}

		$extensionKey = $this->request->getControllerExtensionKey();
		$state = $this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPair'];
		return (!\is_array($state) ? [] : $state);
	}

	/**
	 * Returns the last called controller/action pair from the backend user session
	 *
	 * @return array
	 */
	protected function getLastCalledControllerActionPairForRedirect() {
		if (!$this->backendUser) {
			return array();
		}

		$extensionKey = $this->request->getControllerExtensionKey();
		$state = $this->backendUser->uc[$extensionKey . 'State']['LastActionControllerPairForRedirect'];
		return (!\is_array($state) ? [] : $state);
	}

	/**
	 * Redirects to the last called controller/action pair saved inside the
	 * backend user session
	 *
	 * @return void
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	protected function redirectToLastCalledControllerActionPair() {
		$state = $this->getLastCalledControllerActionPairForRedirect();
		if (\count($state) && \trim($state['action']) !== '' && \trim($state['controller']) !== '') {
			$currentAction = $this->request->getControllerActionName();
			$currentController = $this->request->getControllerName();
			if (!($currentController === $state['controller'] && $currentAction === $state['action'])) {
				$extensionName = $this->request->getControllerExtensionName();
				$moduleSignature = $this->request->getPluginName();
				$extensionConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName];
				$availableControllers = $extensionConfig['modules'][$moduleSignature]['controllers'];
				$controllerExists = isset($availableControllers[$state['controller']]);
				if ($controllerExists) {
					$actionExists = \in_array(
						$state['action'], $availableControllers[$state['controller']]['actions'], TRUE
					);
					if ($actionExists) {
						$this->forward($state['action'], $state['controller']);
					}
				}
			}
		}
	}

	/**
	 * Sets last called controller-action pair and assigns common view variables.
	 * This function should be called at the end of actions which render view
	 * (and does not do redirection or forwarding at the end)
	 */
	protected function commonViewRenderingActionSettings() {
		$this->setLastCalledControllerActionPair();
		$this->view->assign('editingMode', $this->session->getDataByKey('editingMode'));
		$this->view->assign('editingModeOptions', $this->configurationService->getAvailableEditingModes());
		$this->view->assign('adminUser', $this->backendUser->isAdmin());
		$this->view->assign('defaultLanguagePermission', $this->session->getDataByKey('defaultLanguagePermission'));
		$this->view->assign('canChangeEditingModes', $this->session->getDataByKey('canChangeEditingModes'));
	}
}
