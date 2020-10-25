<?php

namespace SGalinski\Lfeditor\Service;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SGalinski\Lfeditor\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SGalinski\SgRoutes\Service\LicensingService
 */
class LicensingService {
	/**
	 * Licensing Service Url
	 */
	const URL = 'https://www.sgalinski.de/?eID=sgLicensing';

	/**
	 * Licensing Service Url
	 */
	const EXTENSION_KEY = 'lfeditor';

	/** @var bool|NULL */
	private static $isLicenseKeyValid;

	/**
	 * @return boolean
	 */
	public static function checkKey() {
		if (static::$isLicenseKeyValid === NULL) {
			static::$isLicenseKeyValid = FALSE;
			$configuration = ExtensionUtility::getExtensionConfiguration();
			if (isset($configuration['key']) && $key = \trim($configuration['key'])) {
				static::$isLicenseKeyValid = (bool) \preg_match('/^([A-Z\d]{6}-?){4}$/', $key);
			}
		}

		return static::$isLicenseKeyValid;
	}

	/**
	 * Licensing Service ping
	 *
	 * @param boolean $returnUrl
	 * @return string
	 */
	public static function ping($returnUrl = FALSE) {
		try {
			$configuration = ExtensionUtility::getExtensionConfiguration();
			$key = '';
			if (isset($configuration['key'])) {
				$key = \trim($configuration['key']);
			}
			$params = [
				'extension' => self::EXTENSION_KEY,
				'host' => GeneralUtility::getIndpEnv('HTTP_HOST'),
				'key' => $key
			];
			$params = \http_build_query($params);
			$pingUrl = self::URL;
			$pingUrl .= $params !== '' ? (\strpos($pingUrl, '?') === FALSE ? '?' : '&') . $params : '';
			if ($returnUrl) {
				return $pingUrl;
			}

			GeneralUtility::getUrl($pingUrl);
		} catch (\Exception $exception) {
		}

		return '';
	}

	/**
	 * Generates a random password string based on the configured password policies.
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 * @throws \InvalidArgumentException
	 */
	public function ajaxPing(ServerRequestInterface $request, ResponseInterface $response = NULL) {
		/** @var BackendUserAuthentication $backendUser */
		$backendUser = $GLOBALS['BE_USER'];
		$moduleKey = 'tools_beuser/index.php/user-LfeditorLfeditor_pinged';
		if ($backendUser && !$backendUser->getModuleData($moduleKey, 'ses')) {
			$backendUser->pushModuleData($moduleKey, TRUE);
			self::ping();
		}

		if ($response === NULL) {
			$response = new NullResponse();
		}

		return $response;
	}
}
