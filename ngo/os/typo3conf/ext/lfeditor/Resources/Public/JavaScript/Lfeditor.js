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

define([
	'jquery',
	'TYPO3/CMS/Backend/Modal',
	'TYPO3/CMS/Backend/Severity'
], function($, Modal, Severity) {
	'use strict';

	var lfEditor = {
		hideAll: null,
		init: function() {
			document.onkeydown = this.saveOnKeyDown;

			$(document).ready(function() {
				$.get(TYPO3.settings.ajaxUrls['lfeditor::ajaxPing']);
			});
		},
		submitLanguageFileEdit: function(buttonType) {
			var $form = $(document.forms.contentForm);
			$form.find("[name$='[buttonType]']").val(buttonType);
			$form.submit();
		},
		/**
		 * Renders confirmation dialog for cancel button.
		 *
		 * @returns {boolean}
		 */
		confirmCancelFileEdit: function() {
			Modal.confirm(
				TYPO3.lang['function.langfile.confirmCancel.title'],
				TYPO3.lang['function.langfile.confirmCancel']
			).on('confirm.button.ok', function() {
				Modal.dismiss();
				TYPO3.lfEditor.submitLanguageFileEdit(-1);
			}).on('confirm.button.cancel', function() {
				Modal.dismiss();
			});
			return false;
		},

		/** args -- fieldID(id), picID(id), bottom(boolean) */
		openCloseTreeEntry: function(prefix, args) {
			var length = arguments.length;
			var pic, curTreeHide;

			for (var i = 1; i < length; i += 3) {
				curTreeHide = 0;
				if (!document.getElementById(arguments[i]).style.display) {
					curTreeHide = 1;
				}

				if (curTreeHide) {
					document.getElementById(arguments[i]).style.display = 'none';
					pic = 'Plus';
				} else {
					document.getElementById(arguments[i]).style.display = '';
					pic = 'Minus';
				}

				if (arguments[i + 2]) {
					pic = pic + 'Bottom';
				}

				document.getElementById(arguments[i + 1]).src = prefix + '/tree' + pic + '.png';
				document.getElementById(arguments[i + 1]).alt = 'tree' + pic + '.png';
			}
		},
		/**
		 * Folds and un folds all constants in tree, on tree view.
		 */
		hideUnHideAll: function() {
			if (this.hideAll === null) {
				this.hideAll = document.getElementById('ul-Root').style.display !== 'none';
			}

			var ulIdRegex = /^ul-/;
			var treeUlElements = [];
			var allUl = document.getElementsByTagName('ul');
			for (var iterator = allUl.length; iterator--;) {
				if (ulIdRegex.test(allUl[iterator].id)) {
					treeUlElements.push(allUl[iterator]);
				}
			}

			var imageIdRegex = /^icon-/;
			var imageMinusSrcRegex = /treeMinus/;
			var imagePlusSrcRegex = /treePlus/;
			var treeImgMinusElements = [];
			var treeImgPlusElements = [];
			var allImg = document.getElementsByTagName('img');
			for (var iterator = allImg.length; iterator--;) {
				if (imageIdRegex.test(allImg[iterator].id)) {
					if (imageMinusSrcRegex.test(allImg[iterator].src)) {
						treeImgMinusElements.push(allImg[iterator]);
					} else if (imagePlusSrcRegex.test(allImg[iterator].src)) {
						treeImgPlusElements.push(allImg[iterator]);
					}
				}
			}

			if (this.hideAll) {
				for (var iterator = treeUlElements.length; iterator--;) {
					treeUlElements[iterator].style.display = 'none';
				}
				for (var iterator = treeImgMinusElements.length; iterator--;) {
					treeImgMinusElements[iterator].src = treeImgMinusElements[iterator].src.replace(imageMinusSrcRegex, 'treePlus');
				}
				this.hideAll = false;
			} else {
				for (var iterator = treeUlElements.length; iterator--;) {
					treeUlElements[iterator].style.display = '';
				}
				for (var iterator = treeImgPlusElements.length; iterator--;) {
					treeImgPlusElements[iterator].src = treeImgPlusElements[iterator].src.replace(imagePlusSrcRegex, 'treeMinus');
				}
				this.hideAll = true;
			}
		},
		/**
		 * Triggers click on button with id = 'tx-lfeditor-button-submit' when user presses Ctrl + Enter.
		 *
		 * @param eventParameter
		 * @returns void
		 */
		saveOnKeyDown: function(eventParameter) {
			var eventObject = window.event ? event : eventParameter;
			if (eventObject.keyCode == 13 && eventObject.ctrlKey) {
				document.getElementById('contentForm').submit();
			}
		},
		changeForm: function(id) {
			Modal.confirm(
				TYPO3.lang['function.langfile.confirmChange.title'],
				TYPO3.lang['function.langfile.confirmChange']
			).on('confirm.button.ok', function() {
				Modal.dismiss();
				document.getElementById(id).submit();
			}).on('confirm.button.cancel', function() {
				Modal.dismiss();
			});
			return false;
		},
		jump: function(select) {
			Modal.confirm(
				TYPO3.lang['function.langfile.confirmChange.title'],
				TYPO3.lang['function.langfile.confirmChange']
			).on('confirm.button.ok', function() {
				Modal.dismiss();
				window.location.href = select.options[select.selectedIndex].value;
			}).on('confirm.button.cancel', function() {
				Modal.dismiss();
			});

		}
	};

	TYPO3.lfEditor = lfEditor;

	lfEditor.init();

	return lfEditor;
});
