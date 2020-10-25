<?php

namespace SGalinski\Lfeditor\ViewHelpers;

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

/**
 * View helper to add custom javascript files
 *
 * Example:
 * {namespace lfe=SGalinski\Lfeditor\ViewHelpers}
 * <lfe:addJavaScriptFile javaScriptFile="{f:uri.resource(path: 'Scripts/Frontend.js')}" />
 */
class AddJavaScriptFileViewHelper extends AbstractViewHelper {

	/**
	 * Register the ViewHelper arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('javaScriptFile', 'string', 'The JavaScript file to include', TRUE);
	}

	/**
	 * Adds a custom javascript file
	 *
	 * @return void
	 */
	public function render() {
		$javaScriptFile = (TYPO3_MODE === 'FE' ? $this->getBaseUrl() : '') . $this->arguments['javaScriptFile'];
		$this->getPageRenderer()->addJsFile($javaScriptFile);
	}
}
