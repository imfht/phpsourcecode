<?php

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

$fieldDefinition = array(
	'lfeditor_change_editing_modes' => array(
		'exclude' => TRUE,
		'label' => 'LLL:EXT:lfeditor/Resources/Private/Language/locallang_mod.xlf:mlang_tabs_tab',
		'config' => array(
			'type' => 'check',
			'items' => array(
				'1' => array(
					'0' => 'LLL:EXT:lfeditor/Resources/Private/Language/locallang_mod.xlf:settings_canChangeEditingMode'
				)
			)
		),
		'displayCond' => 'FIELD:admin:REQ:FALSE',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'be_users', 'lfeditor_change_editing_modes', '', 'after:allowed_languages'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_users', $fieldDefinition);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'be_users',
	'lfeditor',
	'lfeditor_change_editing_modes',
	'after:allowed_languages'
);
