//编辑器调用
$.fn.duxEditor = function (options) {
    var defaults = {
        uploadUrl: duxConfig.editorUploadUrl,
        uploadParams: function () {},
        config: {}
    }
    var options = $.extend(defaults, options);
    var uploadParams = {
        session_id: duxConfig.sessId
    };
    this.each(function () {
        var id = this;
        var idName = $(this).attr('id') + '_editor';
        Do.ready('editor', function () {
            //编辑器
            var editorConfig = {
                allowFileManager: false,
                uploadJson: options.uploadUrl,
                filterMode: false,
                extraFileUploadParams: $.extend(uploadParams, options.uploadParams()),
                afterBlur: function () {
                    this.sync();
                },
                width: '100%'
            };
            editorConfig = $.extend(editorConfig, options.config);
            var str = idName + ' = KindEditor.create(id, editorConfig);';
            eval(str);
        });

    });
};

//多图上传
$.fn.duxMultiUpload = function (options) {
    var defaults = {
        uploadUrl: duxConfig.uploadUrl,
        uploadParams: function () {},
        complete: function () {},
        type: ''
    }
    var options = $.extend(defaults, options);
    this.each(function () {
        var upButton = $(this);
        var dataName = upButton.attr('data');
        var div = $('#' + dataName);
        var data = div.attr('data');
        /*创建上传*/
        Do.ready('webuploader', 'sortable', function () {
            var uploader = WebUploader.create({
                swf: duxConfig.baseDir + 'webuploader/Uploader.swf',
                server: options.uploadUrl,
                pick: upButton,
                resize: false,
                auto: true,
                accept: {
                    title: '指定格式文件',
                    extensions: options.type
                }
            });
            //上传开始
            uploader.on('uploadStart', function (file) {
                uploader.option('formData', $.extend(options.uploadParams(), {
                    'class_id': $('#class_id').val()
                }));
                upButton.attr('disabled', true);
                upButton.find('.webuploader-pick span').text(' 等待');
            });
            //上传完毕
            uploader.on('uploadSuccess', function (file, data) {
                upButton.attr('disabled', false);
                upButton.find('.webuploader-pick span').text(' 上传');
                if (data.status) {
                    htmlList(data.data);
                    options.complete(data.data);
                } else {
                    alert(data.info);
                }
            });
            uploader.on('uploadError', function (file) {
                alert('文件上传失败');
            });
            uploader.on('uploadComplete', function (file) {
                //图片排序
                div.sortable();
            });
            /*
                //处理图片预览
                function zoomPic() {
                    xOffset = 10;
                    yOffset = 30;
                    var maxWidth = 400;
                    div.on('mouseenter', '.pic img', function (e) {
                        $("body").append("<div id='imgZoom'><img class='pic' src='" + $(this).attr('src') + "' /></div>");
                        $("#imgZoom").css("top", (e.pageY - xOffset) + "px").css("left", (e.pageX + yOffset) + "px").fadeIn("fast");
                        var imgZoom = $("#imgZoom").find('.pic');
                        imgZoom.css("width", 300).css("height", 200);
                    });
                    div.on('mouseleave', '.pic img', function (e) {
                        $("#imgZoom").remove();
                    });
                    div.on('mousemove', '.pic img', function (e) {
                        $("#imgZoom").css("top", (e.pageY - xOffset) + "px").css("left", (e.pageX + yOffset) + "px");
                    });
                }
                zoomPic();
                */
            div.sortable();
            //处理上传列表
            function htmlList(file) {
                var html = '<div class="media radius clearfix">\
                    <a class="del" href="javascript:;" alt="删除"><img src="' + file.url + '" ></a>\
                    <div class="media-body">\
                    <input name="' + dataName + '[url][]" type="hidden" class="input" value="' + file.url + '" />\
                    <input name="' + dataName + '[title][]" type="text" class="input" value="' + file.title + '" />\
                    </div>\
                    </div>\
                    ';
                div.append(html);
            }
            //处理删除
            div.on('click', '.del', function () {
                debug('xxxxxx');
                $(this).parent().remove();
            });
        });
    });
};



//图表插件
$.fn.duxChart = function (options) {
    var defaults = {
        data: []
    }
    var options = $.extend(defaults, options);
    var chartObj = this;
    Do.ready('chartJs', function () {
        var ctx = $(chartObj).get(0).getContext("2d");
        var chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            multiTooltipTemplate: "<%= value %>",
        };
        var myLineChart = new Chart(ctx).Line(options.data, chartOptions);
    });
};

//表单页面处理
$.fn.duxFormPage = function (options) {
    var defaults = {
        uploadUrl: duxConfig.uploadUrl,
        editorUploadUrl: duxConfig.editorUploadUrl,
        uploadComplete: function () {},
        uploadParams: function () {},
        uploadType: [],
        postFun: {},
        returnUrl: '',
        returnFun: {},
        form: true
    }
    var options = $.extend(defaults, options);
    this.each(function () {
        var form = this;
        form = $(form);
        //表单处理
        if (options.form) {
            form.duxForm({
                postFun: options.postFun,
                returnUrl: options.returnUrl,
                returnFun: options.returnFun
            });
        }
        //文件上传
        if (form.find(".js-file-upload").length > 0) {
            form.find('.js-file-upload').duxFileUpload({
                type: '*',
                uploadUrl: options.uploadUrl,
                complete: options.uploadComplete,
                uploadParams: options.uploadParams
            });
        }
        //图片上传
        if (form.find(".js-img-upload").length > 0) {
            form.find('.js-img-upload').duxFileUpload({
                type: 'jpg,png,gif,bmp,jpeg',
                uploadUrl: options.uploadUrl,
                complete: options.uploadComplete,
                uploadParams: options.uploadParams
            });
        }
        //多图片上传
        if (form.find(".js-multi-upload").length > 0) {
            form.find('.js-multi-upload').duxMultiUpload({
                type: 'jpg,png,gif,bmp,jpeg',
                uploadUrl: options.uploadUrl,
                complete: options.uploadComplete,
                uploadParams: options.uploadParams
            });
        }
        //编辑器
        if (form.find(".js-editor").length > 0) {
            form.find('.js-editor').duxEditor({
                uploadUrl: options.editorUploadUrl,
                uploadParams: options.uploadParams
            });
        }
        //时间选择
        if (form.find(".js-time").length > 0) {
            form.find('.js-time').duxTime();
        }
    });
};