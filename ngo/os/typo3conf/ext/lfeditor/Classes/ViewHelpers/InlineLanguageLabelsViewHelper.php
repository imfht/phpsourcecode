<?php

namespace SGalinski\Lfeditor\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 *  All rights reserved
 *
 *  This script is part of the AY project. The AY project is
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * View helper to render language labels to
 * json array to be used in js applications.
 *
 * Renders to AY.lang.'extension name' object
 *
 * Example:
 * {namespace rs=SGalinski\RsEvents\ViewHelpers}
 * <rs:inlineLanguageLabels labels="label01,label02" />
 */
class InlineLanguageLabelsViewHelper extends AbstractViewHelper {

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('labels', 'mixed', 'Comma separated list of label keys to include', FALSE, []);
		$this->registerArgument('htmlEscape', 'bool', 'Escape the output', FALSE, FALSE);
	}

	/**
	 * Renders the required javascript to make the language labels available
	 *
	 * @return string
	 */
	public function render(): string {
		$labels = $this->arguments['labels'];
		$htmlEscape = $this->arguments['htmlEscape'];
		$controllerContext = $this->renderingContext->getControllerContext();
		$extensionName = $controllerContext->getRequest()->getControllerExtensionName();
		if (\is_string($labels)) {
			$labels = GeneralUtility::trimExplode(',', $labels, TRUE);
		}

		if (!\is_array($labels)) {
			throw new \InvalidArgumentException(
				'The argument "labels" was registered with type "array|string", but is of type "' .
				\gettype($labels) . '" in view helper "' . \get_class($this) . '".',
				1256475113
			);
		}

		$languageArray = [];
		foreach ($labels as $key) {
			$value = LocalizationUtility::translate($key, $extensionName);
			$languageArray[$key] = ($htmlEscape ? \htmlentities($value) : $value);
		}

		$javascriptCode = '
            var AY = AY || {};
			AY.lang = AY.lang || {};
			AY.lang.' . $extensionName . ' = AY.lang.' . $extensionName . ' || {};
			var languageLabels = ' . \json_encode($languageArray) . ';
			for (label in languageLabels) {
				AY.lang.' . $extensionName . '[label] = languageLabels[label];
			}
		';

		$this->getPageRenderer()->addJsInlineCode($extensionName, $javascriptCode);
		return '';
	}
}
