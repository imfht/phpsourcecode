<?php
namespace MiniFranske\FsMediaGallery\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans Saris <franssaris@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * RealUrl AutoConfiguration
 */
class RealUrlAutoConfiguration
{

    /**
     * Generates additional RealURL configuration and merges it with provided configuration
     *
     * @param array $params Default configuration
     * @param tx_realurl_autoconfgen $pObj parent object
     * @return array Updated configuration
     */
    public function addNewsConfig($params, &$pObj)
    {

        return array_merge_recursive($params['config'], [
                'postVarSets' => [
                    '_DEFAULT' => [
                        'album' => [
                            [
                                'GETvar' => 'tx_fsmediagallery_mediagallery[mediaAlbum]',
                                'lookUpTable' => [
                                    'table' => 'sys_file_collection',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '_',
                                    ],
                                    'languageGetVar' => 'L',
                                    'languageExceptionUids' => '',
                                    'languageField' => 'sys_language_uid',
                                    'transOrigPointerField' => 'l10n_parent',
                                    'autoUpdate' => 1,
                                    'expireDays' => 700,
                                ],
                            ],
                            [
                                'GETvar' => 'tx_fsmediagallery_mediagallery[@widget_assets][currentPage]',
                            ],
                        ]
                    ]
                ]
            ]
        );
    }
}