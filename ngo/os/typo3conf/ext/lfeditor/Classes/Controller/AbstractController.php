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
use SGalinski\Lfeditor\Service\BackupService;
use SGalinski\Lfeditor\Service\ConfigurationService;
use SGalinski\Lfeditor\Session\PhpSession;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Abstract Controller
 */
abstract class AbstractController extends ActionController {
	/**
	 * @var \SGalinski\Lfeditor\Session\PhpSession
	 */
	protected $session;

	/**
	 *
	 * @var ConfigurationService
	 */
	protected $configurationService;

	/**
	 * Inject the ConfigurationService
	 *
	 * @param ConfigurationService $configurationService
	 */
	public function injectConfigurationService(ConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 *
	 * @var BackupService
	 */
	protected $backupService;

	/**
	 * Inject the BackupService
	 *
	 * @param BackupService $backupService
	 */
	public function injectBackupService(BackupService $backupService) {
		$this->backupService = $backupService;
	}

	/**
	 * Initializes the actions.
	 * - Initializes the session object.
	 * - Fetches configuration.
	 *
	 * @return void
	 * @throws \SGalinski\Lfeditor\Exceptions\DirectoryAccessRightsException
	 */
	public function initializeAction() {
		parent::initializeAction();
		if (!($this->session instanceof PhpSession)) {
			$this->session = $this->objectManager->get('SGalinski\Lfeditor\Session\PhpSession');
			$this->session->setSessionKey('tx_lfeditor_sessionVariables');
		}
		$this->configurationService->prepareConfig();
	}

	/**
	 * Saves in session currently selected values of select tags.
	 *
	 * @param string $extensionSelection
	 * @param string $languageFileSelection
	 * @param string $referenceLanguageSelection
	 * @param string $constantSelection
	 * @param string $languageSelection
	 * @param string $constantTypeSelection
	 * @param string $bottomReferenceLanguageSelection
	 * @param string $numSiteConstsSelection
	 * @return void
	 */
	protected function saveSelectionsInSession(
		$extensionSelection = NULL, $languageFileSelection = NULL, $referenceLanguageSelection = NULL,
		$constantSelection = NULL, $languageSelection = NULL, $constantTypeSelection = NULL,
		$bottomReferenceLanguageSelection = NULL, $numSiteConstsSelection = NULL
	) {
		/* Extension/language file select box can't be unselected.
		Only situation when $extensionSelection === NULL is when the form is submitted by
		selection change of some other box. That is because <f:be.menus.actionMenu> with 'optgroup' tags is used */
		if ($extensionSelection) {
			$this->session->setDataByKey('extensionSelection', $extensionSelection);
		}
		if ($languageFileSelection) {
			$this->session->setDataByKey('languageFileSelection', $languageFileSelection);
		}
		if ($referenceLanguageSelection) {
			$this->session->setDataByKey('referenceLanguageSelection', $referenceLanguageSelection);
		}
		if ($constantSelection) {
			$this->session->setDataByKey('constantSelection', $constantSelection);
		}
		if ($languageSelection) {
			$this->session->setDataByKey('languageSelection', $languageSelection);
		}
		if ($constantTypeSelection) {
			$this->session->setDataByKey('constantTypeSelection', $constantTypeSelection);
		}
		if ($bottomReferenceLanguageSelection) {
			$this->session->setDataByKey('bottomReferenceLanguageSelection', $bottomReferenceLanguageSelection);
		}
		if ($numSiteConstsSelection) {
			$this->session->setDataByKey('numSiteConstsSelection', $numSiteConstsSelection);
		}
	}

	/**
	 * Assigns view width menu options and default menu selection which is fetched from session.
	 *
	 * @param string $menuName Name of the menu, which will be used as prefix of view keys for menu options and menu selection.
	 * Example: menuName 'extension' will produce view keys 'extensionOptions' and 'extensionSelection'
	 * @param array $options menu options to be assigned to view
	 * @return void
	 */
	protected function assignViewWidthMenuVariables($menuName, $options) {
		$this->view->assign($menuName . 'Options', $options);

		$selection = $this->checkMenuSelection($menuName, $options);
		$this->view->assign($menuName . 'Selection', $selection);
	}

	/**
	 * Checks does selection exists in session or among menu options and if it does not,
	 * first option becomes selected.
	 *
	 * @param string $menuName Name of the menu, which will be used as prefix of view keys for menu options and menu selection.
	 * Example: menuName 'extension' will produce view keys 'extensionOptions' and 'extensionSelection'
	 * @param array $options menu options to be checked upon.
	 * @return string
	 */
	protected function checkMenuSelection($menuName, array $options) {
		$selection = $this->session->getDataByKey($menuName . 'Selection');
		if (!\array_key_exists($selection, $options)) {
			$selection = NULL;
		}
		if ($selection === NULL && !empty($options)) {
			\reset($options);
			$selection = \key($options);
			$this->session->setDataByKey($menuName . 'Selection', $selection);
			return $selection;
		}
		return $selection;
	}

	/**
	 * Sets FlashMessage from LFException.
	 *
	 * @param LFException $lFException
	 * @return void
	 */
	public function addLFEFlashMessage(LFException $lFException) {
		if ($lFException->getCode() === 0) {
			$this->addFlashMessage(
				$lFException->getMessage(),
				$messageTitle = LocalizationUtility::translate('failure.failure', 'lfeditor'),
				$severity = AbstractMessage::ERROR,
				$storeInSession = TRUE
			);
		} elseif ($lFException->getCode() === 1) {
			$this->addFlashMessage(
				$lFException->getMessage(),
				$messageTitle = '',
				$severity = AbstractMessage::NOTICE,
				$storeInSession = TRUE
			);
		}
	}

	/**
	 * Prepares language file select options for each extension and sets combined data in view.
	 *
	 * @throws LFException
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 * @return void
	 */
	protected function prepareExtensionAndLangFileOptions() {
		/** @var CacheManager $cacheManager */
		$cacheManager = $this->objectManager->get(CacheManager::class);
		$extensions = $cacheManager->getCache('lfeditor_select_options_cache')->get('extensionAndLangFileOptions');
		if (empty($extensions)) {
			$extensionOptions = $this->configurationService->menuExtList();
			$extensionGroupCount = 0;
			foreach ($extensionOptions as $extAddress => $extLabel) {
				$extension['extLabel'] = $extLabel;
				$extension['languageFileOptions'] = [];
				$isExtensionGroupStart = $extAddress === '###extensionGroup###' . $extLabel;
				try {
					if (!$isExtensionGroupStart) {
						$extension['languageFileOptions'] = $this->configurationService->menuLangFileList($extAddress);
						if (empty($extension['languageFileOptions'])) {
							continue;
						}
					} elseif (++$extensionGroupCount > 1) {
						$extensions[$extAddress . 'EmptySpaceBefore'] = [
							'extLabel' => '',
							'languageFileOptions' => [],
						];
					}
				} catch (LFException $e) {
					continue;
				}
				$extensions[$extAddress] = $extension;

				if ($isExtensionGroupStart) {
					$extensions[$extAddress . 'DelimiterAfter'] = [
						'extLabel' => '======',
						'languageFileOptions' => [],
					];
				}
			}
			$cacheManager->getCache('lfeditor_select_options_cache')->set('extensionAndLangFileOptions', $extensions);
		}
		$this->checkExtensionAndLangFileSelection($extensions);
		$extensionSelection = $this->session->getDataByKey('extensionSelection');

		$this->view->assign('extensions', $extensions);
		$this->view->assign('extensionSelection', $extensionSelection);
		$this->view->assign('extensionLabel', $extensions[$extensionSelection]['extLabel']);
		$this->view->assign('languageFileSelection', $this->session->getDataByKey('languageFileSelection'));
	}

	/**
	 * Checks do extensionSelection and languageFileSelection exist in session or among menu options and if it does not,
	 * first language file and belonging extension become selected and saved to session.
	 *
	 * @param array $extensions
	 * @return void
	 */
	protected function checkExtensionAndLangFileSelection(array $extensions) {
		$extensionSelection = $this->session->getDataByKey('extensionSelection');
		$languageFileSelection = $this->session->getDataByKey('languageFileSelection');

		$selectFirstLanguageFileAndBelongingExtension = !$extensionSelection || !$languageFileSelection ||
			!$extensions[$extensionSelection]['languageFileOptions'][$languageFileSelection];
		if ($selectFirstLanguageFileAndBelongingExtension) {
			foreach ($extensions as $extAddress => $extension) {
				if (empty($extension['languageFileOptions'])) {
					continue;
				}
				\reset($extension['languageFileOptions']);
				$languageFileSelection = \key($extension['languageFileOptions']);
				$this->session->setDataByKey('languageFileSelection', $languageFileSelection);
				$this->session->setDataByKey('extensionSelection', $extAddress);
				break;
			}
		}
	}

	/**
	 * Clears cache used for storing select options.
	 * If $identifier is set, it clears only that entry in cache,
	 * otherwise it clears whole select options cache.
	 *
	 * @param string $identifier
	 * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
	 */
	protected function clearSelectOptionsCache($identifier = NULL) {
		/** @var CacheManager $cacheManager */
		$cacheManager = $this->objectManager->get('TYPO3\CMS\Core\Cache\CacheManager');
		if ($identifier !== NULL) {
			$cacheManager->getCache('lfeditor_select_options_cache')->set($identifier, NULL);
		} else {
			$cacheManager->getCache('lfeditor_select_options_cache')->flush();
		}
	}
}

?>
