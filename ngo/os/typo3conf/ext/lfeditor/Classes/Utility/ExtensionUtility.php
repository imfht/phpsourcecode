<?php
/**
 *
 * Copyright notice
 *
 * (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace SGalinski\Lfeditor\Utility;

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class ExtensionUtility
 *
 * @package SGalinski\Lfeditor\Utility
 * @author Kevin Ditscheid <kevin.ditscheid@sgalinski.de>
 */
class ExtensionUtility {
	/**
	 * Get the extension configuration
	 *
	 * @return array
	 */
	public static function getExtensionConfiguration(): array {
		if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
			$extConf = \unserialize(
				$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lfeditor'], ['allowed_classes' => FALSE]
			);
			return is_array($extConf) ? $extConf : [];
		}

		return $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['lfeditor'] ?? [];
	}
}
