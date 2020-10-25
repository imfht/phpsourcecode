<?php
namespace MiniFranske\FsMediaGallery\Hooks;

/***************************************************************
 *  Copyright notice
 *  (c) 2016 Frans Saris <franssaris@gmail.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Folder;

/**
 * Slots that pick up signals after (re)moving folders to update mediagallery record (sys_file_collection)
 */
class FileChangedSlot implements \TYPO3\CMS\Core\SingletonInterface
{

    protected $folderMapping = [];

    /**
     * @var \MiniFranske\FsMediaGallery\Service\Utility
     */
    protected $utilityService;

    /**
     * __contruct
     */
    public function __construct()
    {
        $this->utilityService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('MiniFranske\\FsMediaGallery\\Service\\Utility');
    }

    /**
     * Slot for postFile add
     *
     * @param FileInterface $file
     * @param Folder $targetFolder
     */
    public function postFileAdd(FileInterface $file, Folder $targetFolder)
    {
        $this->utilityService->clearMediaGalleryPageCache($targetFolder);
    }

    /**
     * Slot for postFileCreate
     *
     * @param $newFileIdentifier
     * @param Folder $targetFolder
     */
    public function postFileCreate($newFileIdentifier, Folder $targetFolder)
    {
        $this->utilityService->clearMediaGalleryPageCache($targetFolder);
    }

    /**
     * Slot for postFileCopy
     *
     * @param FileInterface $file
     * @param Folder $targetFolder
     */
    public function postFileCopy(FileInterface $file, Folder $targetFolder)
    {
        $this->utilityService->clearMediaGalleryPageCache($targetFolder);
    }

    /**
     * Slot for postFileMove
     *
     * @param FileInterface $file
     * @param Folder $targetFolder
     * @param Folder $originalFolder
     */
    public function postFileMove(FileInterface $file, Folder $targetFolder, Folder $originalFolder)
    {
        $this->utilityService->clearMediaGalleryPageCache($originalFolder);
        $this->utilityService->clearMediaGalleryPageCache($targetFolder);
    }

    /**
     * Slot for postfileDelete
     *
     * @param FileInterface $file
     */
    public function postFileDelete(FileInterface $file)
    {
        if ($file instanceof File) {
            $this->utilityService->clearMediaGalleryPageCache($file->getParentFolder());
        }
    }

    /**
     * Slot for postFileRename
     *
     * @param FileInterface $file
     * @param $sanitizedTargetFileName
     */
    public function postFileRename(FileInterface $file, $sanitizedTargetFileName)
    {
        if ($file instanceof File) {
            $this->utilityService->clearMediaGalleryPageCache($file->getParentFolder());
        }
    }

    /**
     * Slot for postFileReplace
     *
     * @param FileInterface $file
     * @param $localFilePath
     */
    public function postFileReplace(FileInterface $file, $localFilePath)
    {
        if ($file instanceof File) {
            $this->utilityService->clearMediaGalleryPageCache($file->getParentFolder());
        }
    }
}
