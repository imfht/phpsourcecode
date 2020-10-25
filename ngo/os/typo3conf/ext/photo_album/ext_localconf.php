<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.PhotoAlbum',
            'Album',
            [
                'Album' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Album' => 'list, show, new, create, edit, update, delete'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.PhotoAlbum',
            'Photos',
            [
                'Photos' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Photos' => 'list, show, new, create, edit, update, delete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    album {
                        iconIdentifier = photo_album-plugin-album
                        title = LLL:EXT:photo_album/Resources/Private/Language/locallang_db.xlf:tx_photo_album_album.name
                        description = LLL:EXT:photo_album/Resources/Private/Language/locallang_db.xlf:tx_photo_album_album.description
                        tt_content_defValues {
                            CType = list
                            list_type = photoalbum_album
                        }
                    }
                    photos {
                        iconIdentifier = photo_album-plugin-photos
                        title = LLL:EXT:photo_album/Resources/Private/Language/locallang_db.xlf:tx_photo_album_photos.name
                        description = LLL:EXT:photo_album/Resources/Private/Language/locallang_db.xlf:tx_photo_album_photos.description
                        tt_content_defValues {
                            CType = list
                            list_type = photoalbum_photos
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'photo_album-plugin-album',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:photo_album/Resources/Public/Icons/user_plugin_album.svg']
			);
		
			$iconRegistry->registerIcon(
				'photo_album-plugin-photos',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:photo_album/Resources/Public/Icons/user_plugin_photos.svg']
			);
		
    }
);
