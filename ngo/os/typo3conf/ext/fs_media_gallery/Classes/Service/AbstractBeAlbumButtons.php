<?php

namespace MiniFranske\FsMediaGallery\Service;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\Folder;
use MiniFranske\FsMediaGallery\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract utility class for classes that want to add album add/edit buttons
 * somewhere like a ClickMenuOptions class.
 */
abstract class AbstractBeAlbumButtons
{

    /**
     * Generate album add/edit buttons for click menu or toolbar
     *
     * @param string $combinedIdentifier
     * @return array
     */
    protected function generateButtons($combinedIdentifier)
    {
        $buttons = [];

        // In some folder copy/move actions in file list a invalid id is passed
        try {
            /** @var $file \TYPO3\CMS\Core\Resource\Folder */
            $folder = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()
                ->retrieveFileOrFolderObject($combinedIdentifier);
        } catch (\TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException $exception) {
            $folder = null;
        }

        if ($folder && $folder instanceof Folder &&
            in_array(
                $folder->getRole(),
                [Folder::ROLE_DEFAULT, Folder::ROLE_USERUPLOAD]
            )
        ) {
            /** @var \MiniFranske\FsMediaGallery\Service\Utility $utility */
            $utility = GeneralUtility::makeInstance('MiniFranske\\FsMediaGallery\\Service\\Utility');
            $mediaFolders = $utility->getStorageFolders();

            if (count($mediaFolders)) {
                /** @var \TYPO3\CMS\Core\Charset\CharsetConverter $charsetConverter */
                $charsetConverter = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Charset\\CharsetConverter');
                $collections = $utility->findFileCollectionRecordsForFolder(
                    $folder->getStorage()->getUid(),
                    $folder->getIdentifier(),
                    array_keys($mediaFolders)
                );

                foreach ($collections as $collection) {
                    $buttons[] = $this->createLink(
                        sprintf($this->sL('module.buttons.editAlbum'), $collection['title']),
                        sprintf(
                            $this->sL('module.buttons.editAlbum'),
                            $charsetConverter->crop('utf-8', $collection['title'], 12, '...')
                        ),
                        $this->getIcon('edit-album'),
                        $this->buildEditUrl($collection['uid'])
                    );
                }

                if (!count($collections)) {
                    foreach ($mediaFolders as $uid => $title) {
                        // find parent album for auto setting parent album
                        $parentUid = 0;
                        $parents = $utility->findFileCollectionRecordsForFolder(
                            $folder->getStorage()->getUid(),
                            $folder->getParentFolder()->getIdentifier(),
                            $uid
                        );
                        // if parent(s) found we take the first one
                        if (count($parents)) {
                            $parentUid = $parents[0]['uid'];
                        }
                        $buttons[] = $this->createLink(
                            sprintf($this->sL('module.buttons.createAlbumIn'), $title),
                            sprintf(
                                $this->sL('module.buttons.createAlbumIn'),
                                $charsetConverter->crop('utf-8', $title, 12, '...')
                            ),
                            $this->getIcon('add-album'),
                            $this->buildAddUrl($uid, $parentUid, $folder)
                        );
                    }
                }

                // show hint button for admin users
                // todo: make this better so it can also be used for editors with enough rights to create a storageFolder
            } elseif ($GLOBALS['BE_USER']->isAdmin()) {
                $buttons[] = $this->createLink(
                    $this->sL('module.buttons.createAlbum'),
                    $this->sL('module.buttons.createAlbum'),
                    $this->getIcon('add-album'),
                    'alert("' . StringUtility::slashJS($this->sL('module.alerts.firstCreateStorageFolder')) . '");',
                    false
                );
            }
        }
        return $buttons;
    }

    /**
     * Build edit url
     *
     * @param int $uid Media album uid
     * @return string
     */
    protected function buildEditUrl($uid)
    {
        return BackendUtility::getModuleUrl('record_edit', [
            'edit' => [
                'sys_file_collection' => [
                    $uid => 'edit'
                ]
            ],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ]);
    }

    /**
     * Build Add new media album url
     *
     * @param int $pid
     * @param int $parentAlbumUid
     * @param Folder $folder
     * @return string
     */
    protected function buildAddUrl($pid, $parentAlbumUid, Folder $folder)
    {
        return BackendUtility::getModuleUrl('record_edit', [
            'edit' => [
                'sys_file_collection' => [
                    $pid => 'new'
                ]
            ],
            'defVals' => [
                'sys_file_collection' => [
                    'parentalbum' => $parentAlbumUid,
                    'title' => ucfirst(trim(str_replace('_', ' ', $folder->getName()))),
                    'storage' => $folder->getStorage()->getUid(),
                    'folder' => $folder->getIdentifier(),
                    'type' => 'folder',
                ]
            ],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ]);
    }

    /**
     * Create link/button
     *
     * @param string $title
     * @param string $shortTitle
     * @param string $icon
     * @param string $url
     * @param bool $addReturnUrl
     * @return string
     */
    abstract protected function createLink($title, $shortTitle, $icon, $url, $addReturnUrl = true);

    /**
     * @param string $name
     * @return string|Icon
     */
    protected function getIcon($name)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $icon = $iconFactory->getIcon('action-' . $name, Icon::SIZE_SMALL);

        return $icon;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLangService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Get language string
     *
     * @param string $key
     * @param string $languageFile
     * @return string
     */
    protected function sL($key, $languageFile = 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_be.xlf')
    {
        return $this->getLangService()->sL($languageFile . ':' . $key);
    }
}
