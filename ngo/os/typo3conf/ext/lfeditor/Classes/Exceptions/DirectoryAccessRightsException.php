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

namespace SGalinski\Lfeditor\Exceptions;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * DirectoryAccessRightsException can be thrown for exceptions relating to file access and write permissions
 * in directories
 *
 * @package SGalinski\Lfeditor\Exceptions
 * @author Kevin Ditscheid <kevin.ditscheid@sgalinski.de>
 */
class DirectoryAccessRightsException extends \Exception {

	/**
	 * DirectoryAccessRightsException constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param \Exception|NULL $previous
	 */
	public function __construct($message = '', $code = 0, $previous = NULL) {
		if (\strpos($message, 'LLL:') === 0) {
			$locallizedMessage = LocalizationUtility::translate($message, 'lfeditor');
			$message = $locallizedMessage !== NULL ? $locallizedMessage : $message;
		}

		if ($message === '') {
			$message = 'LFExeption: no error message given !!!';
		}

		parent::__construct($message, $code, $previous);
	}
}
