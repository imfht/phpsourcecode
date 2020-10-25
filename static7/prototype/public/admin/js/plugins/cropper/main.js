$(function () {
    $('.avatar-wrapper > img').cropper({
        aspectRatio: 1,
        preview: ".avatar-preview",
        crop: function (e) {
            var json = [
                '{"x":' + Math.round(e.x),
                '"y":' + Math.round(e.y),
                '"height":' + Math.round(e.height),
                '"width":' + Math.round(e.width),
                '"rotate":' + e.rotate + '}'
            ].join();
            $('.avatar-data').val(json);
        }
    });
    // Import image
    var $inputImage = $('#avatarInput');
    var $download = $('#download');
    var $image = $('#image');
    var URL = window.URL || window.webkitURL;
    var blobURL;

    if (URL) {
        $inputImage.change(function () {
            var files = this.files;
            var file;
            if (!$image.data('cropper')) {
                return;
            }
            if (files && files.length) {
                file = files[0];
                if (/^image\/\w+$/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    $image.one('built.cropper', function () {
                        URL.revokeObjectURL(blobURL);// Revoke when load complete
                    }).cropper('reset').cropper('replace', blobURL);
                    $('.avatar-save').attr('disabled', false);
                } else {
                    window.alert('请选择一个图像文件.');
                }
            }
        });
    } else {
        $inputImage.prop('disabled', true).parent().addClass('disabled');
    }
    // Keyboard
    $(document.body).on('keydown', function (e) {
        if (!$image.data('cropper') || this.scrollTop > 300) {
            return;
        }
        switch (e.which) {
            case 37:
                e.preventDefault();
                $image.cropper('move', -1, 0);
                break;
            case 38:
                e.preventDefault();
                $image.cropper('move', 0, -1);
                break;
            case 39:
                e.preventDefault();
                $image.cropper('move', 1, 0);
                break;
            case 40:
                e.preventDefault();
                $image.cropper('move', 0, 1);
                break;
        }
    });
    // Methods
    $('.avatar-btns').on('click', '[data-method]', function () {
        var $this = $(this);
        var data = $this.data();
        var $target;
        var result;

        if ($this.prop('disabled') || $this.hasClass('disabled')) {
            return;
        }
        if ($image.data('cropper') && data.method) {
            data = $.extend({}, data); // Clone a new one
            if (typeof data.target !== 'undefined') {
                $target = $(data.target);
                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
            result = $image.cropper(data.method, data.option, data.secondOption);
            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    $(this).data('option', -data.option);
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        // Bootstrap's Modal
                        $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
                        if (!$download.hasClass('disabled')) {
                            $download.attr('href', result.toDataURL('image/jpeg'));
                        }
                    }
                    break;
            }
            if ($.isPlainObject(result) && $target) {
                try {
                    $target.val(JSON.stringify(result));
                } catch (e) {
                    console.log(e.message);
                }
            }
        }
    });
});