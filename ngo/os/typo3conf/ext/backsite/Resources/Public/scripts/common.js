function setEditor(id) {
    var editor = CKEDITOR.replace(id);
    CKFinder.setupCKEditor(editor);
}

(function ($) {
    $(document).ready(function ($) {
        $('.colorpicker').colorpicker({ /*options...*/ });
    });
})(jQuery);