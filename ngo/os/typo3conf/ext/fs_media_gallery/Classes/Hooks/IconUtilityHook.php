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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\Folder;

/**
 * IconUtility Hook to add media gallery icon
 */
class IconUtilityHook implements \TYPO3\CMS\Backend\Utility\IconUtilityOverrideResourceIconHookInterface
{
    /**
     * @var array
     */
    static protected $mediaFolders;

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceInterface $folderObject
     * @param $iconName
     * @param array $options
     * @param array $overlays
     */
    public function overrideResourceIcon(
        \TYPO3\CMS\Core\Resource\ResourceInterface $folderObject,
        &$iconName,
        array &$options,
        array &$overlays
    ) {

        if ($folderObject && $folderObject instanceof Folder &&
            in_array($folderObject->getRole(), [Folder::ROLE_DEFAULT, Folder::ROLE_USERUPLOAD])
        ) {
            $mediaFolders = self::getMediaFolders();

            if (count($mediaFolders)) {
                /** @var \MiniFranske\FsMediaGallery\Service\Utility $utility */
                $utility = GeneralUtility::makeInstance('MiniFranske\\FsMediaGallery\\Service\\Utility');
                $collections = $utility->findFileCollectionRecordsForFolder(
                    $folderObject->getStorage()->getUid(),
                    $folderObject->getIdentifier(),
                    array_keys($mediaFolders)
                );

                if ($collections) {
                    $iconName = 'tcarecords-sys_file_collection-folder';
                    $hidden = true;
                    foreach ($collections as $collection) {
                        if ((int)$collection['hidden'] === 0) {
                            $hidden = false;
                            break;
                        }
                    }
                    if ($hidden) {
                        $overlays['status-overlay-hidden'] = [];
                    }
                }
            }
        }
    }

    /**
     * Get media folders
     */
    protected static function getMediaFolders()
    {
        if (self::$mediaFolders === null) {
            /** @var \MiniFranske\FsMediaGallery\Service\Utility $utility */
            $utility = GeneralUtility::makeInstance('MiniFranske\\FsMediaGallery\\Service\\Utility');
            self::$mediaFolders = $utility->getStorageFolders();
        }
        return self::$mediaFolders;
    }
}
