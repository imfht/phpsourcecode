const laravelAlert = require('./alert');

module.exports = (file, ajaxConfig, successCall, errorCall, onprogress) => {
    if (!file || typeof file === 'undefined') {
        return;
    }

    if (!ajaxConfig || !ajaxConfig.paramName || !ajaxConfig.url) {
        laravelAlert.show({
            type: 'danger',
            message: '参数错误！'
        });
        return;
    }

    let formData = new FormData();
    formData.append(ajaxConfig.paramName, file);

    $.ajax({
        type: 'POST',
        url: ajaxConfig.url,
        cache: false,
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        xhr: function () {
            let xhr = $.ajaxSettings.xhr();
            if(onprogress && xhr.upload) {
                xhr.upload.addEventListener("progress" , onprogress, false);
                return xhr;
            }
        },
        success: function (result) {
            if (successCall) {
                return successCall(result);
            } else {
                laravelAlert.show({
                    type: 'danger',
                    message: '上传成功！'
                });
            }

        },
        error: function (code, xhr, message) {
            console.error(message);
            if (errorCall) {
                return errorCall;
            }
        }
    });
};
