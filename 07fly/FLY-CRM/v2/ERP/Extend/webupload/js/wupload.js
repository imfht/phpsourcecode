(function ($) {
    // 当domReady的时候开始初始化
    $(function () {

        var $wrap = $('#uploader'),
                // 图片容器
                $queue = $('<ul class="filelist"></ul>').appendTo($wrap.find('.queueList')),
                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
                // 文件总体选择信息。
                $info = $statusBar.find('.info'),
                // 上传按钮
                $upload = $wrap.find('.uploadBtn'),
                // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),
                $progress = $statusBar.find('.progress').hide(),
                // 添加的文件数量
                fileCount = 0,
                // 添加的文件总大小
                fileSize = 0,
                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,
                // 缩略图大小
                thumbnailWidth = 110 * ratio,
                thumbnailHeight = 110 * ratio,
                // 可能有pedding, ready, uploading, confirm, done.
                state = 'pedding',
                // 所有文件的进度信息，key为file id
                percentages = {},
                supportTransition = (function () {
                    var s = document.createElement('p').style,
                            r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                    s = null;
                    return r;
                })(),

        // 实例化
        uploader = WebUploader.create({
            /**************************************************** 重要参数 ****************************************************/
            auto: true,                                //是否自动上传（true是，false否）
            swf: './Uploader.swf',                      //flash文件地址
            server: '/index.php/Upload/upload_images_webuploader/',                     //上传访问的地址
            formData: {act: 'ad_image'},                //每次请求附带的参数
            pick: {id:'#filePick', label:'添加图片'},   //定义选择文件的按钮
            /**************************************************** 其他参数 ****************************************************/
            dnd: '#dndArea',         // 指定托动区
            disableGlobalDnd: true,  // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            paste: '#uploader',      // 指定监听paste事件的容器，如果不指定，不启用此功能。此功能为通过粘贴来添加截屏的图片
            accept: {                // 指定可以上传那些类型的图片
                title: 'Images',
                extensions: 'jpg,jpeg,png,gif',
                mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'
            },
            thumb:{
                width: 110,
                height: 110,
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 70,
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: false,
                // 是否允许裁剪。
                crop: true,
                // 为空的话则保留原有图片格式。
                // 否则强制转换成指定的类型。
                type: 'image/jpeg'
            },
            compress:{
                width: 1600,
                height: 1600,
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 90,
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: false,
                // 是否允许裁剪。
                crop: false,
                // 是否保留头部meta信息。
                preserveHeaders: true,
                // 如果发现压缩后文件大小比原来还大，则使用原来图片
                // 此属性可能会影响图片自动纠正功能
                noCompressIfLarger: false,
                // 单位字节，如果图片大小小于此值，不会采用压缩。
                compressSize: 0,
                // 强制转换成指定的类型。
                type: 'image/jpeg'
            },
            prepareNextFile:true,  //允许在文件传输时提前把下一个文件准备好 【默认值：false】
            // chunked: false,        //是否要分片处理大文件上传【默认值：false】
            // chunkSize: 512 * 1024, //分多大一片【默认值：5242880】
            // chunkRetry:10, //如果某个分片由于网络问题出错，允许自动重传多少次!【默认值：2】
            // threads:3,     //上传并发数。允许同时最大上传进程数【默认值：3】
            // method:'POST', //文件上传方式，POST或者GET【默认值：'POST'】
            fileNumLimit: 20, //最大上传数量，（验证文件总数量, 超出则不允许加入队列）。
            fileSizeLimit: 200 * 1024 * 1024, // 验证文件总大小是否超出限制, 超出则不允许加入队列
            fileSingleSizeLimit: 50 * 1024 * 1024    // 验证单个文件大小是否超出限制, 超出则不允许加入队列
        });
        
        


        // 当有文件添加进来时执行，负责view的创建
        function addFile(file) {
            var $li = $('<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>' +
                    '<p class="progress"><span></span></p>' +
                    '</li>'),
                    $btns = $('<div class="file-panel">' +
                            '<span class="cancel">删除</span>' +
                            '<span class="rotateRight">向右旋转</span>' +
                            '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find('p.imgWrap'),
                    $info = $('<p class="error"></p>'),
                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }

                        $info.text(text).appendTo($li);
                    };

            if (file.getStatus() === 'invalid') {
                showError(file.statusText);
            } else {
                // @todo lazyload
                $wrap.text('预览中');
                uploader.makeThumb(file, function (error, src) {
                    var img;
                    if (error) {
                        $wrap.text('不能预览');
                        return;
                    }
                    img = $('<img src="' + src + '">');
                    $wrap.empty().append(img);
                }, thumbnailWidth, thumbnailHeight);

                percentages[ file.id ] = [file.size, 0];
                file.rotation = 0;
            }

            file.on('statuschange', function (cur, prev) {
                if (prev === 'progress') {
                    $prgress.hide().width(0);
                } else if (prev === 'queued') {
                    $li.off('mouseenter mouseleave');
                    $btns.remove();
                }

                // 成功
                if (cur === 'error' || cur === 'invalid') {
                    console.log(file.statusText);
                    showError(file.statusText);
                    percentages[ file.id ][ 1 ] = 1;
                } else if (cur === 'interrupt') {
                    showError('interrupt');
                } else if (cur === 'queued') {
                    percentages[ file.id ][ 1 ] = 0;
                } else if (cur === 'progress') {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if (cur === 'complete') {
                    $li.append('<span class="success"></span>');
                }

                $li.removeClass('state-' + prev).addClass('state-' + cur);
            });

            $li.on('mouseenter', function () {
                $btns.stop().animate({height: 30});
            });

            $li.on('mouseleave', function () {
                $btns.stop().animate({height: 0});
            });

            $btns.on('click', 'span', function () {
                var index = $(this).index(),
                        deg;

                switch (index) {
                    case 0:
                        // 把当前文件移出当前对列
                        uploader.removeFile(file);
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if (supportTransition) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                }


            });
            $li.appendTo($queue);
        }

        // 负责view的销毁
        function removeFile(file) {
            var $li = $('#' + file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function setState(val) {
            var file, stats;

            if (val === state) {
                return;
            }

            $upload.removeClass('state-' + state);
            $upload.addClass('state-' + val);
            state = val;

            switch (state) {
                case 'pedding':
                    $placeHolder.removeClass('element-invisible');
                    $queue.hide();
                    //$queue.show();
                    $statusBar.addClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass('element-invisible');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $progress.show();
                    $upload.text('暂停上传');
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text('继续上传');
                    break;

                case 'confirm':
                    $progress.hide();
                    //$queue.show();
                    $upload.text('开始上传');

                    stats = uploader.getStats();
                    if (stats.successNum && !stats.uploadFailNum) {
                        setState('finish');
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if (stats.successNum) {
                        //alert('上传成功');
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        //location.reload();
                    }
                    break;
            }

            updateStatus();
        }












        // 上传过程中....
        uploader.onUploadProgress = function (file, percentage) {
            var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

            $percent.css('width', percentage * 100 + '%');
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();
        };
        // 当文件被加入队列以后触发。
        uploader.onFileQueued = function (file) {
            fileCount++;
            fileSize += file.size;

            if (fileCount === 1) {
                $placeHolder.addClass('element-invisible');
                $statusBar.show();
            }

            addFile(file);
            setState('ready');
            updateTotalProgress();
        };
        // 当文件被移除队列后触发。
        uploader.onFileDequeued = function (file) {
            fileCount--;
            fileSize -= file.size;

            if (!fileCount) {
                setState('pedding');
            }

            removeFile(file);
            updateTotalProgress();

        };
         // 文件上传成功
        uploader.on( 'uploadSuccess', function( file,data ) {
             var $li = $('#' + file.id),
                    $percent = $li.find('.success');
                $dsf = $('<input type="hidden" name="images[]" value="'+data.file+'">').appendTo($li),
            setState('confirm');

        });
        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            setState('uploading');
        });
        // 文件上传失败，显示上传出错
        uploader.on( 'uploadError', function( file ) {
            setState('finish');
        });

        uploader.on("uploadAccept", function( file, data){  
            if ( data.success==true) {  
                // 通过return false来告诉组件，此文件上传有错。  
                return true;  
            }else{
                return false;  
            }
        });  









        // 修改上传进底条的
        function updateTotalProgress() {
            var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

            $.each(percentages, function (k, v) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            });

            percent = total ? loaded / total : 0;

            spans.eq(0).text(Math.round(percent * 100) + '%');
            spans.eq(1).css('width', Math.round(percent * 100) + '%');
            updateStatus();
        }

        // 改变提示的方法
        function updateStatus() {
            var text = '', stats;
            if(state === 'ready'){
                text = '选中' + fileCount + '张图片，共' +WebUploader.formatSize(fileSize) + '。';
            }else if(state === 'confirm') {
                stats = uploader.getStats();
                if (stats.uploadFailNum) {
                    text = '已成功上传' + stats.successNum + '张照片至XX相册，' +
                            stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                }
            }else{
                stats = uploader.getStats();
                text = '共' + fileCount + '张（' +WebUploader.formatSize(fileSize) +'），已上传' + stats.successNum + '张';
                if(stats.uploadFailNum){
                    text += '，失败' + stats.uploadFailNum + '张';
                }
            }
            $info.html(text);
        }

        $upload.on('click', function () {
            if ($(this).hasClass('disabled')) {
                return false;
            }
            if (state === 'ready') {
                uploader.upload();
            } else if (state === 'paused') {
                uploader.upload();
            } else if (state === 'uploading') {
                uploader.stop();
            }
        });
        $upload.addClass('state-' + state);
        updateTotalProgress();





    });    
})(jQuery);