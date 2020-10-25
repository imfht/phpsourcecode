<?php

namespace SGalinski\Lfeditor\Utility;

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
 * Class which adds CDATA support to SimpleXMLElement.
 */
class CdataSupportingSimpleXMLElement extends \SimpleXMLElement {
	/**
	 * Add CDATA text in a node
	 *
	 * @param string $cdataText The CDATA value  to add
	 * @return void
	 */
	protected function addCData($cdataText) {
		$node = dom_import_simplexml($this);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($cdataText));
	}

	/**
	 * Create a child with CDATA value
	 *
	 * @param string $name The name of the child element to add.
	 * @param string $cdataText The CDATA value of the child element.
	 * @return CdataSupportingSimpleXMLElement
	 */
	public function addChildCData($name, $cdataText) {
		/** @var CdataSupportingSimpleXMLElement $child */
		$child = $this->addChild($name);
		if ($cdataText !== '') {
			$child->addCData($cdataText);
		}
		return $child;
	}
}

?>
