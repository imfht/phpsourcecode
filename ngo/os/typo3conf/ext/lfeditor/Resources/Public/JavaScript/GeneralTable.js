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
 * Indicates if untranslated languages are hidden.
 * @type {boolean}
 */
var untranslatedAreHidden = false;

/**
 * Index of column which contains state of languages.
 * @type {number}
 */
var STATE_COLUMN_INDEX = 1;

/**
 * Index of span element which contains number of translated constants.
 * @type {number}
 */
var TRANSLATED_SPAN_INDEX = 1;

/**
 * Gets list of table rows.
 * @returns {NodeList|*}
 */
function getTableRowElements() {
	var tableElement = document.getElementById('tx-lfeditor-table');
	var tableBodyElement = tableElement ? tableElement.getElementsByTagName('tbody')[0] : null;
	var tableRowElements = tableBodyElement ? tableBodyElement.getElementsByTagName('tr') : null;
	return tableRowElements;
}

/**
 * This function hides table rows which contain untranslated languages.
 * @returns boolean
 */
var hideUntranslatedLanguagesInTable = function() {
	var thereAreHiddenElements = false;
	var tableRowElements = getTableRowElements();
	if (tableRowElements === null) {
		return thereAreHiddenElements;
	}

	for (var tableRowIndex = tableRowElements.length; tableRowIndex--;) {
		var tableDataElements = tableRowElements[tableRowIndex].getElementsByTagName('td');
		if (tableDataElements === null) {
			return thereAreHiddenElements;
		}
		var tableDataSpanElements = tableDataElements[STATE_COLUMN_INDEX].getElementsByTagName('span');
		if (!tableDataSpanElements || !tableDataSpanElements[TRANSLATED_SPAN_INDEX]) {
			return thereAreHiddenElements;
		}
		var numberTranslated = tableDataSpanElements[TRANSLATED_SPAN_INDEX].innerText.trim();
		if (numberTranslated !== '0') {
			continue;
		}
		tableRowElements[tableRowIndex].style.display = 'none';
		untranslatedAreHidden = true;
		thereAreHiddenElements = true;
	}
	return thereAreHiddenElements;
};

/**
 * Reveals hidden table rows.
 * @returns void
 */
function showUntranslatedLanguagesInTable() {
	var tableRowElements = getTableRowElements();
	if (tableRowElements === null) {
		return;
	}

	for (var tableRowIndex = tableRowElements.length; tableRowIndex--;) {
		if (tableRowElements[tableRowIndex].style.display === 'none') {
			tableRowElements[tableRowIndex].style.display = '';
		}
	}
	untranslatedAreHidden = false;
}

/**
 * Hides or un hides rows of table which contain untranslated languages.
 * @returns void
 */
function hideShowUntranslatedLanguagesInTable() {
	if (untranslatedAreHidden) {
		showUntranslatedLanguagesInTable();
	} else {
		hideUntranslatedLanguagesInTable();
	}
}

var initHideShowFunctionality = function() {
	var thereAreHiddenElements = hideUntranslatedLanguagesInTable();
	if (!thereAreHiddenElements) {
		var hideShowLinkElement = document.getElementById('hideShowUntranslatedLanguagesInTableId');
		hideShowLinkElement.style.display = 'none';
	}
};

document.addEventListener('DOMContentLoaded', initHideShowFunctionality);
