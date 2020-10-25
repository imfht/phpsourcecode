/* 参数说明
* t1 最外层容器
* t2 触发按钮
* t3 返回值控件
* t4 单次允许上传文件数量 0为不限制
*/
//上传文件
function upfile(t1,t2,t3,t4){
    var $ = jQuery,
        $list = $('#'+t1).find('.filelist'),
        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1,

        // 缩略图大小
        thumbnailWidth = 100 * ratio,
        thumbnailHeight = 100 * ratio,


        // Web Uploader实例
        uploader;

    // 初始化Web Uploader
    uploader = WebUploader.create({

        // 自动上传。
        auto: true,
        //允许上传图片数量
        fileNumLimit:t4,
        // swf文件路径
        swf: '/Public/js/plugins/webuploader/js/Uploader.swf',
        // 文件接收服务端。
        server: '/api/Uploads/uploadimg',
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#'+t2,

        // 只允许选择文件，可选。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function(file) {
        var $li = $( '<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"><img></p>'+
                '</li>' ),
        $btns = $('<div class="file-panel">' +
                '<span class="cancel">删除</span>').appendTo( $li ),
        $info = $('<p class="error"></p>'),
        showError = function( code ) {
                switch( code ) {
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

                $info.text( text ).appendTo( $li );
            };
            $img = $li.find('img');
            $li.on( 'mouseenter', function() {
            $btns.stop().animate({height: 30});
        });

        $li.on( 'mouseleave', function() {
            $btns.stop().animate({height: 0});
        });

        $btns.on( 'click', 'span', function() {
            var index = $(this).index(),
                deg;
                
            switch ( index ) {
                case 0:
                    uploader.removeFile(file);
                    return;
            }

        });

        $list.append( $li );

        // 创建缩略图
        uploader.makeThumb( file, function( error, src ) {
            if ( error ) {
                $img.replaceWith('<span>不能预览</span>');
                return;
            }

            $img.attr( 'src', src );
        }, thumbnailWidth, thumbnailHeight );
    });

    // 负责view的销毁
    function removeFile( file ) {
        var $li = $('#'+file.id);
        alert('fsdf');
        delete percentages[ file.id ];
        updateTotalProgress();
        $li.off().find('.file-panel').off().end().remove();
    }


    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress span');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<p class="progress"><span></span></p>')
                    .appendTo( $li )
                    .find('span');
        }

        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file ) {
        $( '#'+file.id ).addClass('upload-state-done');
    });

    // 文件上传失败，现实上传出错。
    uploader.on( 'uploadError', function( file ) {
        var $li = $( '#'+file.id ),
            $error = $li.find('div.error');

        // 避免重复创建
        if ( !$error.length ) {
            $error = $('<div class="error"></div>').appendTo( $li );
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').remove();
    });

    //获取返回值
    uploader.on( 'uploadSuccess', function( file,response) {
        var imgurl = response.url;
        var piclist=$(t3).val();
        if(t4 == 1){
            $(t3).val(imgurl);
        }else{
            $(t3).val(piclist+'||'+imgurl);
        }
        
    })
}
