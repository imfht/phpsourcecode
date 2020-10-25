/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/FsMediaGallery/ContextMenuActions
 *
 * JavaScript to handle fs_media_gallery actions from context menu
 * @exports TYPO3/CMS/FsMediaGallery/ContextMenuActions
 */
define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity'], function ($, Modal, Severity) {
    'use strict';

    /**
     * @exports TYPO3/CMS/Filelist/ContextMenuActions
     */
    var ContextMenuActions = {};

    /**
     * Show message to the user when no media storage folder is provided
     */
    ContextMenuActions.missingMediaFolder = function () {
        Modal.advanced({
            content: this.data('title'),
            severity: Severity.warning
        });
    };

    /**
     * Open media album edit form
     *
     * @param {string} table
     * @param {string} uid combined folder identifier
     */
    ContextMenuActions.mediaAlbum = function (table, uid) {
        var albumRecordUid = this.data('albumRecordUid') || 0;

        if (albumRecordUid > 0) {
            top.TYPO3.Backend.ContentContainer.setUrl(
                top.TYPO3.settings.FormEngine.moduleUrl
                + '&edit[sys_file_collection][' + parseInt(albumRecordUid, 10) + ']=edit'
                + '&returnUrl=' + ContextMenuActions.getReturnUrl()
            );
        } else {
            top.TYPO3.Backend.ContentContainer.setUrl(
                top.TYPO3.settings.FormEngine.moduleUrl
                + '&edit[sys_file_collection][' + this.data('pid') + ']=new'
                + '&defVals[sys_file_collection][parentalbum]=' + this.data('parentUid')
                + '&defVals[sys_file_collection][title]=' + this.data('title')
                + '&defVals[sys_file_collection][storage]=' + this.data('storage')
                + '&defVals[sys_file_collection][folder]=' + this.data('folder')
                + '&defVals[sys_file_collection][type]=folder'
                + '&returnUrl=' + ContextMenuActions.getReturnUrl()
            );
        }
    };

    ContextMenuActions.getReturnUrl = function () {
        return top.rawurlencode(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
    };

    return ContextMenuActions;
});
