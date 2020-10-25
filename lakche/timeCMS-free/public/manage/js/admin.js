$(function () {
    var t = $("input[name='_token']").val();

    //图片上传
    if( $("#image-upload").length > 0 ) {
        var uploaderPic = new plupload.Uploader({
            runtimes: 'html5,flash,silverlight,html4',
            browse_button: 'image-upload',
            container: document.getElementById('image-default'),
            url: '/admin/attachment',
            flash_swf_url: '../js/Moxie.swf',
            silverlight_xap_url: '../js/Moxie.xap',
            multipart_params: { _token: t, class: $('#image-upload').attr('data-class'), type: $('#image-upload').attr('data-type'), hash: $("input[name='hash']").val()},
            headers: { 'X-Requested-With': 'XMLHttpRequest'},
            multi_selection: true,
            chunk_size: "1024kb",
            filters: {
                max_file_size: '10mb',
                mime_types: [
                    {title: "图片文件", extensions: "jpg,png,gif"}
                ]
            },
            resize: {
                quality: 70,
                preserve_headers: true
            },
            init: {
                FilesAdded: function (up, files) {
                    uploaderPic.start();
                },
                Error: function (up, err) {
                    alert(err.message);
                },
                FileUploaded: function (uploaderPic, file, info) {
                    var obj = JSON.parse(info.response);
                    if (obj.result) {
                        $('#image-default').val(obj.file);
                        $('#image-thumb').val(obj.thumb);
                        alert('图片上传成功.');
                    } else {
                        $msg = '图片上传失败\n';
                        $.each(obj,function(n,value) {
                            $msg = $msg + value + '\n';
                        });
                        alert($msg);
                    }
                }
            }
        });
        uploaderPic.init();
    }

    //删除数据
    $(".option-del").on("click", function(){
        if(confirm("是否删除")){
            $.ajax({
                type: 'POST',
                url: "/admin/"+$(this).attr('data-class')+"/" + $(this).attr("data-id"),
                data: { _method: 'DELETE', _token: t },
                success: function (data) {
                    alert(data.message);
                    if(data.error==0){
                        location.reload();
                    }
                },
                error: function (data) {
                    alert(data.message);
                }
            });
        }
    });

    //设置管理员
    $(".set-admin").on("click", function(){
        if(confirm("是否设置为管理员")){
            $.ajax({
                type: 'POST',
                url: "/admin/users/" + $(this).attr("data-id"),
                data: { _method: 'PUT', _token: t, attr: 'admin' },
                success: function (data) {
                    alert(data.message);
                    if(data.error==0){
                        location.reload();
                    }
                },
                error: function (data) {
                    alert(data.message);
                }
            });
        }
    });

    //取消管理员
    $(".set-no-admin").on("click", function(){
        if(confirm("是否取消管理员")){
            $.ajax({
                type: 'POST',
                url: "/admin/users/" + $(this).attr("data-id"),
                data: { _method: 'PUT', _token: t, attr: 'admin' },
                success: function (data) {
                    alert(data.message);
                    if(data.error==0){
                        location.reload();
                    }
                },
                error: function (data) {
                    alert(data.message);
                }
            });
        }
    });

    //项目选择参与人员
    $("#choose_person li").on("click", function(){
        var name = $(this).text();
        var id = $(this).attr('data-id')
        var inputId = $("input[name='person_id']");
        var inputName = $("input[name='person_name']");
        if(inputId.val()==''){
            inputId.val(id);
        } else {
            inputId.val(inputId.val()+','+id);
        }
        if(inputName.val()==''){
            inputName.val(name);
        } else {
            inputName.val(inputName.val()+','+name);
        }
        return false;
    });
    $("#person_clear").on("click",function(){
        if(confirm("是否清空")) {
            var inputId = $("input[name='person_id']");
            var inputName = $("input[name='person_name']");
            inputId.val('');
            inputName.val('');
        }
    })

    //增加项目进度
    $('.add-speed').on('click',function(){
        $('.speed-add:last').before(
            '<div class="input-group address-add">'+
            '<div class="input-group-addon">时间</div>'+
            '<input type="text" class="form-control" name="time[]" value="">'+
            '<div class="input-group-addon">事件</div>'+
            '<input type="text" class="form-control map" name="event[]" value="">'+
            '<div class="input-group-addon btn btn-danger del-speed"><i class="glyphicon glyphicon-minus"></i>删除进度</div>'+
            '</div>');
    });

    //删除项目进度
    $(document).on('click','.del-speed',function(){
        $(this).parent().remove();
    });


})