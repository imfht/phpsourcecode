<?php

namespace MiniFranske\FsMediaGallery\ContextMenu\ItemProviders;

use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FsMediaGalleryProvider extends AbstractProvider
{
    protected $itemsConfiguration = [];

    /**
     * @return bool
     */
    public function canHandle(): bool
    {
        return $this->table === 'sys_file';
    }

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 90;
    }

    /**
     * Initialize file object
     */
    protected function initialize()
    {
        parent::initialize();
        $resource = ResourceFactory::getInstance()
            ->retrieveFileOrFolderObject($this->identifier);
        if ($resource instanceof Folder
            && in_array(
                $resource->getRole(),
                [Folder::ROLE_DEFAULT, Folder::ROLE_USERUPLOAD],
                true
            )
        ) {
            $this->folder = $resource;
        }
    }

    /**
     * Adds the media album add/edit menu items
     *
     * @param array $items
     * @return array
     */
    public function addItems(array $items): array
    {
        $this->initialize();
        if (!($this->folder instanceof Folder)) {
            return $items;
        }

        /** @var \MiniFranske\FsMediaGallery\Service\Utility $utility */
        $utility = GeneralUtility::makeInstance(\MiniFranske\FsMediaGallery\Service\Utility::class);
        $mediaFolders = $utility->getStorageFolders();

        if (count($mediaFolders) === 0) {

            $this->itemsConfiguration['add-media-gallery'] = [
                'label' => $this->sL('module.buttons.createAlbum'),
                'iconIdentifier' => 'action-add-album',
                'callbackAction' => 'missingMediaFolder',
                'title' => $this->sL('module.alerts.firstCreateStorageFolder')
            ];
        }

        if (count($mediaFolders) > 0) {

            $collections = $utility->findFileCollectionRecordsForFolder(
                $this->folder->getStorage()->getUid(),
                $this->folder->getIdentifier(),
                array_keys($mediaFolders)
            );

            foreach ($collections as $collection) {
                $this->itemsConfiguration['edit-media-gallery-' . $collection['uid']] = [
                    'label' => sprintf($this->sL('module.buttons.editAlbum'), $collection['title']),
                    'iconIdentifier' => 'action-edit-album',
                    'callbackAction' => 'mediaAlbum',
                    'uid' => $collection['uid'],
                ];
            }

            if (!count($collections)) {
                foreach ($mediaFolders as $uid => $title) {

                    // Find parent album for auto setting parent album
                    $parentUid = 0;
                    $parents = $utility->findFileCollectionRecordsForFolder(
                        $this->folder->getStorage()->getUid(),
                        $this->folder->getParentFolder()->getIdentifier(),
                        $uid
                    );

                    // If parent(s) found we take the first one
                    if (count($parents)) {
                        $parentUid = $parents[0]['uid'];
                    }

                    $this->itemsConfiguration['add-media-gallery-' . $uid] = [
                        'label' => sprintf($this->sL('module.buttons.createAlbumIn'), $title),
                        'iconIdentifier' => 'action-add-album',
                        'callbackAction' => 'mediaAlbum',
                        'pid' => $uid,
                        'parentUid' => $parentUid,
                    ];
                }
            }
        }

        if ($this->itemsConfiguration !== []) {
            $items += $this->prepareItems(
                ['media_gallery_divider' => ['type' => 'divider']]
                + $this->itemsConfiguration
            );
        }

        return $items;
    }

    /**
     * @param string $itemName
     * @return array
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        $itemInfo = $this->itemsConfiguration[$itemName] ?? [];

        return [
            'data-callback-module' => 'TYPO3/CMS/FsMediaGallery/ContextMenuActions',
            'data-album-record-uid' => $itemInfo['uid'] ?? 0,
            'data-pid' => $itemInfo['pid'] ?? 0,
            'data-parent-uid' => $itemInfo['parentUid'] ?? 0,
            'data-title' => $itemInfo['title'] ?? $this->folder->getName(),
            'data-storage' => $this->folder->getStorage()->getUid(),
            'data-folder' => $this->folder->getIdentifier(),
        ];
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
        return $this->languageService->sL($languageFile . ':' . $key);
    }

}