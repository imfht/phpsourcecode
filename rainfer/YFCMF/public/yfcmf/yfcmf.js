var body=$('body');
/************************************************************* form相关js ********************************************************/
//日期
$('.date-picker').each(function(){
    $(this).datepicker({
        autoclose: true,
        todayHighlight: true,
        language:'zh-CN'
    });
});

//日期时间
$('.date-time-picker').each(function(){
    $(this).datetimepicker({
        autoclose: true,
        todayHighlight: true,
        language:'zh-CN'
    });
});

//时间
$('.time-picker').each(function(){
    $(this).timepicker({
        minuteStep: 1,
        autoclose: true,
        showSeconds: true,
        showMeridian: false,
        disableFocus: true,
        icons: {
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down'
        }
    });
});

//日期区间
$('.date-range-picker').each(function(){
    $(this).daterangepicker(null, function (start, end, label) {
    });
});

//颜色
$('.color-picker').each(function(){
    $(this).colorpicker().on("changeColor", function(ev){
        $(this).closest('div').find('.btn-colorpicker').css('backgroundColor',ev.color.toHex());
    });
});

//提示
$('[data-rel=tooltip]').tooltip({container:'body'});

//tag框
$('.input-tag').each(function(i){
    var tag_input=$(this);
    var id=($(this).attr('id'))?$(this).attr('id'):('input-tag'+i);
    var data=$(this).closest('div.data').data('data').toString().split(',');
    try{
        tag_input.tag({
            placeholder: tag_input.attr('placeholder'),
            source: data
        });
        var $tag_obj = tag_input.data('tag');
        var index = $tag_obj.inValues('some tag');
        $tag_obj.remove(index);
    }
    catch(e) {
        tag_input.after('<textarea id="'+id+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
    }
});

//mask框
if($.mask !==undefined){
    $.mask.definitions['~']='[+-]';
    $('.input-mask').each(function(i){
        var mask_input=$(this);
        var format=($(this).data('format'))?$(this).data('format'):'';
        mask_input.mask(format);
    });
}

//textarea自动大小
$('textarea.autosize').each(function(){
    autosize($(this));
});
//textarea长度限制
$('textarea.limited').each(function(){
    $(this).inputlimiter({
        remText: '%n character%s remaining...',
        limitText: 'max allowed : %n.'
    });
});

//多选下拉
$('.input-select').each(function(i){
    var select_input=$(this);
    select_input.selectpicker('val',select_input.data('value').toString().split(','));
});

//范围
$('.rangeslider').each(function(i){
    $(this).ionRangeSlider();
});

//图标
var curr_icon_picker;
var layer_icon;
$('.icon-picker').click(function(){
    curr_icon_picker = $(this);
    var icon_input = curr_icon_picker.find('.icon-input');
    if (icon_input.is(':disabled')) {
        return;
    }
    layer_icon = layer.open({
        type: 1,
        title: '图标选择器',
        area: ['90%', '90%'],
        scrollbar: false,
        content: $('#icon_tab')
    });
});
//icon click
$('.icon-content li').click(function () {
    var icon = $(this).find('i').attr('class');
    curr_icon_picker.find('.input-group-addon.icon').html('<i class="'+icon+'"></i>');
    curr_icon_picker.find('.icon-input').val(icon);
    layer.close(layer_icon);
});
// icon clear
$('.delete-icon').click(function(event){
    event.stopPropagation();
    if ($(this).prev().is(':disabled')) {
        return;
    }
    $(this).prev().val('').prev().html('<i class="fa fa-fw fa-info-circle"></i>');
});
//icon search
var $searchItems = $('.icon-list > li');
var $searchValue = '';
$('.js-icon-search').on('keyup', function(){
    $searchValue = $(this).val().toLowerCase();
    if ($searchValue.length > 2) {
        $searchItems.hide();
        $('code', $searchItems)
            .each(function(){
                if ($(this).text().match($searchValue)) {
                    $(this).parent('li').show();
                }
            });
    } else if ($searchValue.length === 0) {
        $searchItems.show();
    }
});

//多级联动
body.on('change','.linkage',function () {
    var $url = $(this).data("url"),$id=$(this).data('id'),$value=$(this).val(),$obj=$("#"+$id);
    $.ajax({
        url:$url,
        type:"POST",
        data: {
            id: $value
        },
        success: function(data){
            if (data.code == 1) {
                //先清空
                $obj.empty();
                //填充
                $("<option value=''>请选择</option>").appendTo($obj);
                $.each(data.list,function(idx,item){
                    $("<option value="+item.id+">"+item.name+"</option>").appendTo($obj);
                });
            }
        }
    });
});

//上传组件
var webuploader = [];
var curr_uploader = {};
// webuploader 秒传
if (window.WebUploader) {
    WebUploader.Uploader.register(
        {
            "before-send-file": "preupload" //上传前验证
        },
        {
            preupload:function(file){
                var $li = $( '#'+file.id );
                var deferred = WebUploader.Deferred();
                var owner = this.owner;

                owner.md5File(file).then(function(val){
                    $.ajax({
                        type: "POST",
                        url: yfcmf.upload_check_url,
                        data: {
                            md5: val
                        },
                        cache: false,
                        timeout: 10000,
                        dataType: "json"
                    }).then(function(data){
                        if(data.code==1){
                            // 已上传
                            deferred.reject();
                            curr_uploader.trigger('uploadSuccess', file, data);
                            curr_uploader.trigger('uploadComplete', file);
                        }else{
                            // 未上传
                            deferred.resolve();
                            $li.find('.file-state').html('<span class="text-info">正在上传...</span>');
                            $li.find('.img-state').html('<div class="bg-info">正在上传...</div>');
                            $li.find('.progress').show();
                        }
                    }, function(){
                        // post失败
                        deferred.resolve();
                        $li.find('.file-state').html('<span class="text-info">正在上传...</span>');
                        $li.find('.img-state').html('<div class="bg-info">正在上传...</div>');
                        $li.find('.progress').show();
                    });
                });
                return deferred.promise();
            }
        }
    );
}

// 文件上传
$('.upload-file,.upload-files').each(function () {
    var $input_file       = $(this).find('input');
    var $input_file_name  = $input_file.attr('name');
    // 是否多文件上传
    var $multiple         = $input_file.data('multiple');
    // 上传路径
    var $upload_url       = $input_file.data('url');
    // 允许上传的后缀
    var $ext              = $input_file.data('ext');
    // 文件限制大小
    var $size             = $input_file.data('size');
    // 是否保存为原始文件名
    var $save_original    = $input_file.data('original');
    // 保存文件名
    var $save_filename    = $input_file.data('filename');
    // 文件列表
    var $file_list        = $('#file_list_' + $input_file_name);

    // 实例化上传
    var uploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,
        // 去重
        duplicate: true,
        // swf文件路径
        swf: yfcmf.WebUploader_swf,
        // 文件接收服务端
        // 图片接收服务端
        server: $upload_url || yfcmf.file_upload_url,
        // 选择文件的按钮。可选 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: {
            id: '#picker_' + $input_file_name,
            multiple: $multiple
        },
        formData:{
            save_original:$save_original,
            save_filename:$save_filename
        },
        // 文件限制大小
        fileSingleSizeLimit: $size,
        // 只允许选择文件文件。
        accept: {
            title: 'Files',
            extensions: $ext
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        var $li = '<li id="' + file.id + '" class="list-group-item file-item">' +
            '<span class="pull-right file-state"><span class="text-info"><i class="fa fa-sun-o fa-spin"></i> 正在读取文件信息...</span></span>' +
            '<i class="fa fa-file"></i> ' +
            file.name +
            ' [<a href="javascript:void(0);" class="download-file">下载</a>] [<a href="javascript:void(0);" class="remove-file">删除</a>]' +
            '<div class="progress progress-mini remove-margin active" style="display: none"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div></div>'+
            '</li>';

        if ($multiple) {
            $file_list.append($li);
        } else {
            $file_list.html($li);
            // 清空原来的数据
            $input_file.val('');
        }
        // 设置当前上传对象
        curr_uploader = uploader;
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $percent = $( '#'+file.id ).find('.progress-bar');
        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功
    uploader.on( 'uploadSuccess', function( file, data ) {
        var $li = $( '#'+file.id );
        if (data.code) {
            if ($multiple) {
                if ($input_file.val()) {
                    $input_file.val($input_file.val() + ',' + data.url);
                } else {
                    $input_file.val(data.url);
                }
                $li.find('.remove-file').attr('data-id', data.url);
            } else {
                $input_file.val(data.url);
            }
        }
        // 加入提示信息
        $li.find('.file-state').html('<span class="text-info">'+ data.state +'</span>');
        // 添加下载链接
        $li.find('.download-file').attr('href', data.url);
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        var $li = $( '#'+file.id );
        $li.find('.file-state').html('<span class="text-danger">服务器发生错误~</span>');
    });

    // 文件验证不通过
    uploader.on('error', function (type) {
        switch (type) {
            case 'Q_TYPE_DENIED':
                layer.alert('文件类型不正确，只允许上传后缀名为：'+$ext+'，请重新上传！', {icon: 5});
                break;
            case 'F_EXCEED_SIZE':
                layer.alert('文件不得超过'+ ($size/1024) +'kb，请重新上传！', {icon: 5});
                break;
        }
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        setTimeout(function(){
            $('#'+file.id).find('.progress').remove();
        }, 500);
    });

    // 删除文件
    $file_list.delegate('.remove-file', 'click', function(){
        if ($multiple) {
            var id  = $(this).data('id'),
                ids = $input_file.val().split(',');
            if (id) {
                for (var i = 0; i < ids.length; i++) {
                    if (ids[i] == id) {
                        ids.splice(i, 1);
                        break;
                    }
                }
                $input_file.val(ids.join(','));
            }
        } else {
            $input_file.val('');
        }
        $(this).closest('.file-item').remove();
    });
    // 将上传实例存起来
    webuploader.push(uploader);
});
// 图片上传
$('.upload-image,.upload-images').each(function () {
    var $input_file       = $(this).find('input');
    var $input_file_name  = $input_file.attr('name');
    // 是否多图片上传
    var $multiple         = $input_file.data('multiple');
    // 上传路径
    var $upload_url         = $input_file.data('url');
    // 允许上传的后缀
    var $ext              = $input_file.data('ext');
    // 图片限制大小
    var $size             = $input_file.data('size');
    // 是否保存为原始文件名
    var $save_original    = $input_file.data('original');
    // 保存文件名
    var $save_filename    = $input_file.data('filename');
    // 图片列表
    var $file_list        = $('#file_list_' + $input_file_name);
    // 优化retina, 在retina下这个值是2
    var ratio             = window.devicePixelRatio || 1;
    // 缩略图大小
    var thumbnailWidth    = 100 * ratio;
    var thumbnailHeight   = 100 * ratio;
    // 实例化上传
    var uploader = WebUploader.create({
        // 选完图片后，是否自动上传。
        auto: true,
        // 去重
        duplicate: true,
        // 不压缩图片
        resize: false,
        compress: false,
        // swf图片路径
        swf: yfcmf.WebUploader_swf,
        // 图片接收服务端。
        server: $upload_url || yfcmf.img_upload_url,
        // 选择图片的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: {
            id: '#picker_' + $input_file_name,
            multiple: $multiple
        },
        formData:{
            save_original:$save_original,
            save_filename:$save_filename
        },
        // 图片限制大小
        fileSingleSizeLimit: $size,
        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: $ext,
            mimeTypes: 'image/jpg,image/jpeg,image/bmp,image/png,image/gif'
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        var $li = $(
                '<div id="' + file.id + '" class="file-item js-gallery thumbnail">' +
                '<a class="img-link" href="">'+
                '<img>' +
                '</a>'+
                '<div class="info">' + file.name + '</div>' +
                '<i class="fa fa-times-circle remove-picture text-center"></i>' +
                '<div class="progress progress-mini remove-margin active" style="display: none">' +
                '<div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>' +
                '</div>' +
                '<div class="file-state img-state"><div class="bg-info">正在读取...</div>' +
                '</div>'
            ),
            $img = $li.find('img');

        if ($multiple) {
            $file_list.append( $li );
        } else {
            $file_list.html( $li );
            $input_file.val('');
        }

        // 创建缩略图
        // 如果为非图片文件，可以不用调用此方法。
        // thumbnailWidth x thumbnailHeight 为 100 x 100
        uploader.makeThumb( file, function( error, src ) {
            if ( error ) {
                $img.replaceWith('<span>不能预览</span>');
                return;
            }
            $img.attr( 'src', src );
        }, thumbnailWidth, thumbnailHeight );

        // 设置当前上传对象
        curr_uploader = uploader;
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $percent = $( '#'+file.id ).find('.progress-bar');
        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功
    uploader.on( 'uploadSuccess', function( file, data ) {
        var $li = $( '#'+file.id );
        if (data.code) {
            if ($multiple) {
                if ($input_file.val()) {
                    $input_file.val($input_file.val() + ',' + data.url);
                } else {
                    $input_file.val(data.url);
                }
                $li.find('.remove-picture').attr('data-id', data.url);
            } else {
                $input_file.val(data.url);
            }
        }
        $li.find('.file-state').html('<div class="bg-info">'+data.state+'</div>');
        $li.find('a.img-link').attr('href', data.url);
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        var $li = $( '#'+file.id );
        $li.find('.file-state').html('<div class="bg-danger">服务器错误</div>');
    });

    // 文件验证不通过
    uploader.on('error', function (type) {
        switch (type) {
            case 'Q_TYPE_DENIED':
                layer.alert('图片类型不正确，只允许上传后缀名为：'+$ext+'，请重新上传！', {icon: 5});
                break;
            case 'F_EXCEED_SIZE':
                layer.alert('图片不得超过'+ ($size/1024) +'kb，请重新上传！', {icon: 5});
                break;
        }
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        setTimeout(function(){
            $( '#'+file.id ).find('.progress').remove();
        }, 500);
    });

    // 删除图片
    $file_list.delegate('.remove-picture', 'click', function(){
        $(this).closest('.file-item').remove();
        if ($multiple) {
            var ids = [];
            $file_list.find('.remove-picture').each(function () {
                ids.push($(this).data('id'));
            });
            $input_file.val(ids.join(','));
        } else {
            $input_file.val('');
        }
    });

    // 查看大图
    $(this).magnificPopup({
        delegate: 'a.img-link',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
    // 将上传实例存起来
    webuploader.push(uploader);
});
//百度编辑器
var ueditors    = {};
$('.input-ueditor').each(function () {
    var ueditor_name = $(this).attr('name');
    ueditors[ueditor_name] = UE.getEditor(ueditor_name, {
        initialFrameHeight:400,  //初始化编辑器高度,默认320
        autoHeightEnabled:false,  //是否自动长高
        maximumWords: 50000 //允许的最大字符数

    });
});
//刷新本页
body.on('click','.page-header-refesh',function () {
    window.location.reload();
});
//左侧菜单
$('a.nav-left').click(function(){
    var $li=$(this).parent();
    var $id=$li.attr('id');
    var $url=$(this).attr('href');
    var $title=$(this).text();
    var $icon=$(this).data('icon');
/*    if($('#tab_tab_'+$id).length ==0){
        addTabs({id:$id,icon:$icon,title:$title,close: true,url:$url});
    } else {

    }*/
    addTabs({id:$id,icon:$icon,title:$title,close: true,url:$url});
    return false;
});
//触发器
if (yfcmf.triggers != '') {
    $.each(yfcmf.triggers,function(key, val){
        var show=$("[name='"+ val.show +"']");
        var trigger=$("[name='"+ val.trigger +"']");
        var div=show.closest('.form-group');
        var values=val.values.split(',') || [];
        //初始
        var value=trigger.val();
        if(trigger.attr('type') == 'radio'  || trigger.attr('type') == 'checkbox'){
            value=trigger.filter(':checked').val();
        }
        if((trigger.attr('type') == 'radio' || trigger.attr('type') == 'checkbox') && trigger.is(':checked') == false){
            value='0';
        }
        if ($.inArray(value, values) >= 0) {
            div.show();
        }else{
            div.hide();
        }
        //添加事件
        body.on('change',trigger,function () {
            value=trigger.val();
            var chk_value = [];
            if(trigger.attr('type') == 'radio'){
                value=trigger.filter(':checked').val();
            }else if(trigger.attr('type') == 'checkbox'){
                trigger.filter(':checked').each(function () {
                    chk_value.push($(this).val());
                });
            }
            if((trigger.attr('type') == 'radio' || trigger.attr('type') == 'checkbox') && trigger.is(':checked') == false){
                value='0';
            }else if(chk_value.length>0){
                value=chk_value;
            }
            if($.isArray(value)){
                var arr=$.grep( value, function(n,i){
                    return $.inArray(n, values)>-1;
                });
                if(arr.length){
                    div.show(200);
                }else{
                    div.hide(200);
                }
            }else{
                if ($.inArray(value, values) >= 0) {
                    div.show(200);
                }else{
                    div.hide(200);
                }
            }
        });
    });
}
//模态框
body.on('click','.yf-modal-open',function () {
    var modal=$(this);
    var url=modal.data('url');
    var id=modal.data('id');
    var return_url=modal.data('return');
    if(id){
        if(url.indexOf('?')==-1){
            url=url+"?id="+id;
        }else{
            url=url+"&id="+id;
        }
    }
    if(return_url){
        if(url.indexOf('?')==-1){
            url=url+"?return="+return_url;
        }else{
            url=url+"&return="+return_url;
        }
    }
    var title=modal.data('title');
    layer.open({
        type: 2,
        area: [($(window).width()-50)+'px', ($(window).height()-50)+'px'],
        fixed: false, //不固定
        maxmin: true,
        content: url,
        title:title,
        shadeClose: false, //点击遮罩关闭
        shade: 0.5,
        shift:1,
        closeBtn: 1,
        end: function () {
            location.reload();
        }
    });
    return false;
});
//排序
body.on('click','.order-btn',function () {
    var url=$(this).data('url');
    var table=$(this).closest('table');
    $.ajax({
        url:url,
        type:"post",
        data:table.find(".table-input").serialize(),
        dataType:"json",
        error:function(data){
            layer.alert('请求失败!', {icon: 5});
        },
        success:function(data){
            if(data.code==1){
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            }else{
                layer.alert(data.msg, {icon: 5});
            }
        }
    });
    return false;
});
//多选删除
body.on('click','.delall-btn',function () {
    var url=$(this).data('url');
    var table=$(this).closest('table');
    //判断是否选择
    var chk_value = [];
    table.find('input.check-all:checked').each(function () {
        chk_value.push($(this).val());
    });
    if (!chk_value.length) {
        layer.alert('至少选择一个删除项', {icon: 5});
        return false;
    }else{
        $.ajax({
            url:url,
            type:"post",
            data:{"ids":chk_value},
            dataType:"json",
            error:function(data){
                layer.alert('请求失败!', {icon: 5});
            },
            success:function(data){
                if(data.code==1){
                    layer.alert(data.msg, {icon: 6}, function (index) {
                        layer.close(index);
                        window.location.href = data.url;
                    });
                }else{
                    layer.alert(data.msg, {icon: 5});
                }
            }
        });
    }
    return false;
});
//下拉选择ajax
body.on('change','.ajax_change',function () {
    var $form = $(this).parents("form");
    $.ajax({
        type:"POST",
        data:$form.serialize(),
        success: function(data,status){
            $("#ajax-data").html(data);
        }
    });
});
//分页ajax
function ajax_page(page) {
    $.ajax({
        type:"POST",
        data:$('#list-filter').serialize()+'&page='+page,
        success: function(data,status){
            $("#ajax-data").html(data);
        }
    });
}
//table顶部搜索按钮ajax
body.on('click','.ajax-search-form',function () {
    var $form = $(this).parents("form");
    $.ajax({
        type:"POST",
        data:$form.serialize(),
        success: function(data,status){
            $("#ajax-data").html(data);
        }
    });
    return false;
});
//table顶部显示全部按钮ajax
body.on('click','.ajax-display-all',function () {
    $(this).parents("form")[0].reset();
    $.ajax({
        type:"POST",
        data:{},
        success: function(data,status){
            $("#ajax-data").html(data);
        }
    });
    return false;
});
/* 启用状态操作 */
body.on('click','.open-btn',function () {
    var $url = this.href,
        id = $(this).data('id'),
        $btn=$(this);
    $.post($url, {id: id}, function (data) {
        if (data.code==1) {
            if (data.data.result == 0) {
                var a = '<button class="btn btn-minier btn-danger">'+data.msg+'</button>';
                $btn.children('div').html(a).attr('title',data.msg);
                return false;
            } else {
                var b = '<button class="btn btn-minier btn-yellow">'+data.msg+'</button>';
                $btn.children('div').html(b).attr('title',data.msg);
                return false;
            }
        } else {
            layer.alert(data.msg, {icon: 5});
        }
    }, "json");
    return false;
});
//获取地图
//table顶部显示全部按钮ajax
body.on('click','.btn-get-map',function () {
    var keyword=$(this).closest('div').find('input').val();
    if(keyword !== ''){
        var url=$(this).attr('href');
        $.ajax({
            type:"POST",
            url:url,
            data:{'keyword':keyword},
            success: function(data,status){
                $("#text-map_lat").val(data.map_lat);
                $("#text-map_lng").val(data.map_lng);
            }
        });
    }else{
        layer.msg('地址或公司名不能为空');
    }
    return false;
});
/************************************************************* 所有带确认的ajax提交btn ********************************************************/
/* get执行并返回结果，执行后不带跳转 */
$(function () {
    body.on('click','.rst-btn',function () {
        var $url = this.href;
        $.get($url, function (data) {
            if (data.code == 1) {
                layer.alert(data.msg, {icon: 6});
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* get执行并返回结果，执行后带跳转 */
$(function () {
    body.on('click','.rst-url-btn',function () {
        var $url = this.href;
        $.get($url, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            } else {
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
        }, "json");
        return false;
    });
});
/* 直接跳转 */
$(function () {
    body.on('click','.confirm-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            window.location.href = $url;
        });
        return false;
    });
});
/* post执行并返回结果，执行后不带跳转 */
$(function () {
    body.on('click','.confirm-rst-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            $.post($url, {}, function (data) {
                if (data.code==1) {
                    layer.alert(data.msg, {icon: 6});
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            }, "json");
        });
        return false;
    });
});
/* get执行并返回结果，执行后带跳转 */
$(function () {
    body.on('click','.confirm-rst-url-btn',function () {
        var url = this.href,id=$(this).data('id'),
            $info = $(this).data('info');
        if(id){
            if(url.indexOf('?')==-1){
                url=url+"?id="+id;
            }else{
                url=url+"&id="+id;
            }
        }
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            $.get(url, function (data) {
                if (data.code==1) {
                    layer.alert(data.msg, {icon: 6}, function (index) {
                        layer.close(index);
                        window.location.href = data.url;
                    });
                } else {
                    layer.alert(data.msg, {icon: 5}, function (index) {
                        layer.close(index);
                    });
                }
            }, "json");
        });
        return false;
    });
});
$(function () {
    body.on('click','.confirm-url-btn',function () {
        var $url = this.href,
            $info = $(this).data('info');
        layer.confirm($info, {icon: 3}, function (index) {
            layer.close(index);
            window.location.href = $url;
        });
        return false;
    });
});
/*************************************************************************** 所有ajaxForm提交 ********************************************************/
/* 通用表单不带检查操作，失败跳转 */
$(function () {
    $('.ajaxForm').ajaxForm({
        success: complete, // 这是提交后的方法
        dataType: 'json'
    });
});
//失败跳转
function complete(data) {
    if (data.code == 1) {
        layer.alert(data.msg, {icon: 6}, function (index) {
            layer.close(index);
            if(data.data.is_frame){
                window.parent.location = data.url;
            }else{
                window.location.href = data.url;
            }
        });
    } else {
        layer.alert(data.msg, {icon: 5}, function (index) {
            layer.close(index);
            if(data.url){
                if(data.data.is_frame){
                    window.parent.location = data.url;
                }else{
                    window.location.href = data.url;
                }
            }
        });
        return false;
    }
}
/* 通用表单不带检查操作，失败不跳转 */
$(function () {
    $('.ajaxForm-noJump').ajaxForm({
        success: complete_nojump, // 这是提交后的方法
        dataType: 'json'
    });
});
//失败不跳转
function complete_nojump(data) {
    if (data.code == 1) {
        layer.alert(data.msg, {icon: 6}, function (index) {
            layer.close(index);
            if(data.data.is_frame){
                window.parent.location = data.url;
            }else{
                window.location.href = data.url;
            }
        });
    } else {
        layer.alert(data.msg, {icon: 5}, function (index) {
            layer.close(index);
        });
    }
}
/* 通用含验证码表单不带检查操作，失败不跳转 */
$(function () {
    $('.ajaxForm-hasVerify').ajaxForm({
        success: complete_hasverify, // 这是提交后的方法
        dataType: 'json'
    });
});
//失败不跳转,验证码刷新
function complete_hasverify(data) {
    if (data.code == 1) {
        if(data.data.is_frame){
            window.parent.location = data.url;
        }else{
            window.location.href = data.url;
        }
    } else {
        $("input[id^='captcha-input-']").val('');
        $("img[id^='captcha-input-']").click();
        layer.alert(data.msg, {icon: 5});
    }
}
/* 多选删除操作 */
$(function () {
    $('.ajaxForm-allDel').ajaxForm({
        beforeSubmit: checkselectForm, // 此方法主要是提交前执行的方法，根据需要设置，一般是判断为空获取其他规则
        success: complete_nojump, // 这是提交后的方法
        dataType: 'json'
    });
});
//多选表单检查
function checkselectForm() {
    var chk_value = [];
    $('input[id="checkid"]:checked').each(function () {
        chk_value.push($(this).val());
    });
    if (!chk_value.length) {
        layer.alert('至少选择一个删除项', {icon: 5});
        return false;
    }
}
/* 增加编辑表单，带检查，失败跳转 */
$(function () {
    $('.ajaxForm-checkForm').ajaxForm({
        beforeSubmit: checkForm, // 此方法主要是提交前执行的方法，根据需要设置
        success: complete, // 这是提交后的方法
        dataType: 'json'
    });
});
function checkForm() {
    var chk_username=$('#chk_username');
    if(chk_username.length>0){
        var chk_username_val = $.trim(chk_username.val()); //获取INPUT值
        var myReg = /^[\u4e00-\u9fa5]+$/;//验证中文
        if (chk_username_val.indexOf(" ") >= 0) {
            layer.alert('登录用户名包含了空格，请重新输入', {icon: 5}, function (index) {
                layer.close(index);
                chk_username.focus();
            });
            return false;
        }
        if (myReg.test(chk_username_val)) {
            layer.alert('用户名必须是字母，数字，符号', {icon: 5}, function (index) {
                layer.close(index);
                chk_username.focus();
            });
            return false;
        }
    }
    var chk_tel=$("#chk_tel");
    if(chk_tel.length>0){
        if (!chk_tel.val().match(/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
            layer.alert('电话号码格式不正确', {icon: 5}, function (index) {
                layer.close(index);
                chk_tel.focus();
            });
            return false;
        }
    }
}
/*************************************************************************** 所有状态类的ajax提交btn ********************************************************/
/* 审核状态操作 */
$(function () {
    body.on('click','.state-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '未审') {
                    var a = '<button class="btn btn-minier btn-danger">未审</button>';
                    $btn.children('div').html(a).attr('title','未审');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">已审</button>';
                    $btn.children('div').html(b).attr('title','已审');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
$(function () {
    body.on('click','#btnorder',function () {
        var $url=$(this).attr("href");
        if(!$url){
            $url=$(this).parents('form').attr('action');
        }
        $.post($url, $("input.list_order").serialize(), function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    window.location.href = data.url;
                    layer.close(index);
                });
            }else{
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
        }, "json");
        return false;
    });
});

/* 显示状态操作 */
$(function () {
    body.on('click','.display-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '状态禁止') {
                    var a = '<button class="btn btn-minier btn-danger">隐藏</button>';
                    $btn.children('div').html(a).attr('title','已隐藏');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">显示</button>';
                    $btn.children('div').html(b).attr('title','已显示');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 检测状态操作 */
$(function () {
    body.on('click','.notcheck-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '检测') {
                    var a = '<button class="btn btn-minier btn-yellow">检测</button>';
                    $btn.children('div').html(a).attr('title','检测');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-danger">不检测</button>';
                    $btn.children('div').html(b).attr('title','不检测');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/* 激活状态操作 */
$(function () {
    body.on('click','.active-btn',function () {
        var $url = this.href,
            val = $(this).data('id'),
            $btn=$(this);
        $.post($url, {x: val}, function (data) {
            if (data.code==1) {
                if (data.msg == '未激活') {
                    var a = '<button class="btn btn-minier btn-danger">未激活</button>';
                    $btn.children('div').html(a).attr('title','未激活');
                    return false;
                } else {
                    var b = '<button class="btn btn-minier btn-yellow">已激活</button>';
                    $btn.children('div').html(b).attr('title','已激活');
                    return false;
                }
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        }, "json");
        return false;
    });
});
/*************************************************************************** 其它操作 ********************************************************/
/* 多选 */
function CheckAll(form) {
    for (var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if (e.Name != 'chkAll' && e.disabled == false) {
            e.checked = form.chkAll.checked;
        }
    }
}
/* 显示错误信息 */
function showmsg(id, msg) {
    var $tr=$form.find("input[value=" + tables[id] + "]").closest("tr");
    $tr.find(".info,.green").html("");
    $tr.find(".backup").html(msg);
}
$(function () {
    /* 权限配置 */
    //动态选择框，上下级选中状态变化
    $('input.checkbox-parent').on('change', function () {
        var dataid = $(this).attr("dataid");
        $('input[dataid^=' + dataid + '-]').prop('checked', $(this).is(':checked'));
    });
    $('input.checkbox-child').on('change', function () {
        var dataid = $(this).attr("dataid");
        dataid = dataid.substring(0, dataid.lastIndexOf("-"));
        var parent = $('input[dataid=' + dataid + ']');
        if ($(this).is(':checked')) {
            parent.prop('checked', true);
            //循环到顶级
            while (dataid.lastIndexOf("-") != 2) {
                dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                parent = $('input[dataid=' + dataid + ']');
                parent.prop('checked', true);
            }
        } else {
            //父级
            if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                parent.prop('checked', false);
                //循环到顶级
                while (dataid.lastIndexOf("-") != 2) {
                    dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                    parent = $('input[dataid=' + dataid + ']');
                    if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                        parent.prop('checked', false);
                    }
                }
            }
        }
    });

    /*生成安全文件*/
    body.on('click','#security_generate',function () {
        $(this).children().addClass("disabled");
        $(this).find("span").text("正在生成安全文件...");
        $.get(this.href, function (data) {
            if (data.code==1) {
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            } else {
                layer.alert(data.msg, {icon: 5}, function (index) {
                    layer.close(index);
                });
            }
            $(this).children().removeClass("disabled");
            $(this).find("span").text("重新生成安全文件");
        });
        return false;
    });

    /*日期区间确定*/
    body.on('click','.range_inputs .applyBtn',function () {
        var reservation=$('#date-range-reservation');
        var $form = reservation.parents("form");
        reservation.val($('input[name="daterangepicker_start"]').val()+' - '+$('input[name="daterangepicker_end"]').val());
        $.ajax({
            url:$form.attr('action'),
            type:"POST",
            data:$form.serialize(),
            success: function(data){
                $("#ajax-data").html(data);
            }
        });
    });

    /*操作日志清空*/
    body.on('click','.ajax-drop',function () {
        $(this).parents("form")[0].reset();
        var url=$(this).parent('a').attr('href');
        $.ajax({
            type:"POST",
            url:url,
            data:{},
            success: function(data){
                layer.alert(data.msg, {icon: 6}, function (index) {
                    layer.close(index);
                    window.location.href = data.url;
                });
            }
        });
        return false;
    });

    /*操作日志详情*/
    body.on('click','.show-details-btn',function (e) {
        e.preventDefault();
        $(this).closest('tr').next().toggleClass('open');
        $(this).find('.ace-icon').toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
    });

    /*权限管理*/
    var is_disable=false;
    body.on('click','.rule-list',function () {
        var $a=$(this);
        var $tr=$a.parents('tr');
        var $pid=$tr.attr('id');
        var $pid_tr=$("tr[id^='"+$pid+"-']");
        if($a.find('span').hasClass('fa-minus')){
            $pid_tr.attr('style','display:none');
            $a.find('span').removeClass('fa-minus').addClass('fa-plus');
        }else{
            if($pid_tr.length>0){
                $pid_tr.attr('style','');
                $a.find('span').removeClass('fa-plus').addClass('fa-minus');
            }else{
                var $url = this.href,$id=$a.data('id'),$level=$a.data('level');
                if(is_disable===false){
                    is_disable=true;
                    $.post($url,{pid:$id,level:$level,id:$pid}, function (data) {
                        if (data) {
                            $a.find('span').removeClass('fa-plus').addClass('fa-minus');
                            $tr.after(data);
                        }else{
                            $a.find('span').removeClass('fa-plus').addClass('fa-minus');
                        }
                        is_disable=false;
                    }, "json");
                }
            }
        }
        return false;
    });
});