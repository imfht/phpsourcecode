<?php
namespace MiniFranske\FsMediaGallery\Domain\Model;

    /*                                                                        *
     * This script is part of the TYPO3 project.                              *
     *                                                                        *
     * It is free software; you can redistribute it and/or modify it under    *
     * the terms of the GNU Lesser General Public License, either version 3   *
     * of the License, or (at your option) any later version.                 *
     *                                                                        *
     * The TYPO3 project - inspiring people to share!                         *
     *                                                                        */

/**
 * News
 */
class News extends \GeorgRinger\News\Domain\Model\News {

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum>
     * @lazy
     */
    protected $relatedFsmediaalbums;

    /**
     * Get relatedFsmediaalbums
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum>
     */
    public function getRelatedFsmediaalbums()
    {
        return $this->relatedFsmediaalbums;
    }

    /**
     * Set relatedFsmediaalbums
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $relatedFsmediaalbums related media albums
     * @return void
     */
    public function setRelatedFsmediaalbums($relatedFsmediaalbums)
    {
        $this->relatedFsmediaalbums = $relatedFsmediaalbums;
    }

}