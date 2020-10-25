/*!
 * FileInput <language> Translations - Template file for copying and creating other translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
 * @see http://github.com/kartik-v/bootstrap-fileinput
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
(function ($) {
    "use strict";

    $.fn.fileinput.locales.en = {
        fileSingle: 'file',
        filePlural: 'files',
        browseLabel: '选择文件 &hellip;',
        removeLabel: '移除',
        removeTitle: 'Clear selected files',
        cancelLabel: '取消',
        cancelTitle: 'Abort ongoing upload',
        uploadLabel: '上传',
        uploadTitle: '上传选中的图片',
        msgSizeTooLarge: 'File "{name}" (<b>{size} KB</b>) exceeds maximum allowed upload size of <b>{maxSize} KB</b>. Please retry your upload!',
        msgFilesTooLess: 'You must select at least <b>{n}</b> {files} to upload. Please retry your upload!',
        msgFilesTooMany: 'Number of files selected for upload <b>({n})</b> exceeds maximum allowed limit of <b>{m}</b>. Please retry your upload!',
        msgFileNotFound: 'File "{name}" not found!',
        msgFileSecured: 'Security restrictions prevent reading the file "{name}".',
        msgFileNotReadable: 'File "{name}" is not readable.',
        msgFilePreviewAborted: 'File preview aborted for "{name}".',
        msgFilePreviewError: 'An error occurred while reading the file "{name}".',
        msgInvalidFileType: 'Invalid type for file "{name}". Only "{types}" files are supported.',
        msgInvalidFileExtension: 'Invalid extension for file "{name}". Only "{extensions}" files are supported.',
        msgValidationError: 'File Upload Error',
        msgLoading: 'Loading file {index} of {files} &hellip;',
        msgProgress: 'Loading file {index} of {files} - {name} - {percent}% completed.',
        msgSelected: '{n}个文件准备上传',
        msgFoldersNotAllowed: 'Drag & drop files only! Skipped {n} dropped folder(s).',
        dropZoneTitle: '将文件拖动到这里 &hellip;'
    };

    $.extend($.fn.fileinput.defaults, $.fn.fileinput.locales.en);
})(window.jQuery);