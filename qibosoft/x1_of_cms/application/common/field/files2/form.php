<?php
function_exists('urls') || die('ERR');

$jscode = '';
if(fun('field@load_js',$field['type'])){
	$upload_check_url = urls('index/ajax/check');
	$file_upload_url = urls('index/attachment/upload','dir=files&module='.request()->dispatch()['module'][0]);
	$jscode = <<<EOT

<link rel="stylesheet" href="__STATIC__/libs/webuploader/webuploader.css">
<link rel="stylesheet" href="__STATIC__/admin/css/bootstrap.min.css">  
<script src="__STATIC__/libs/webuploader/webuploader.min.js"></script>

<script type="text/javascript">
var server_urls = { 
            'WebUploader_swf': '__STATIC__/libs/webuploader/Uploader.swf',
            'file_upload_url': '$file_upload_url', 
            'upload_check_url': '$upload_check_url',
};
 
 
jQuery(document).ready(function() {
    // 文件上传集合
    var webuploader = [];
    // 当前上传对象
    var curr_uploader = {};
 
    // 注册WebUploader事件，实现秒传
    if (window.WebUploader) {
        WebUploader.Uploader.register({
            "before-send-file": "beforeSendFile" // 整个文件上传前
        }, {
            beforeSendFile:function(file){
                var f_li = $( '#'+file.id );
                var deferred = WebUploader.Deferred();
                var owner = this.owner;

                owner.md5File(file).then(function(val){
                    $.ajax({
                        type: "POST",
                        url: server_urls.upload_check_url,
                        data: {
                            md5: val
                        },
                        cache: false,
                        timeout: 10000, // 超时的话，只能认为该文件不曾上传过
                        dataType: "json"
                    }).then(function(res, textStatus, jqXHR){
                        if(res.code==1){	//这里还没有统一改成0,晚点会全部统一改成0
                            // 已上传，触发上传完成事件，实现秒传
                            deferred.reject();
                            curr_uploader.trigger('uploadSuccess', file, res);
                            curr_uploader.trigger('uploadComplete', file);
                        }else{
                            // 文件不存在，触发上传
                            deferred.resolve();
                            f_li.find('.file-state').html('<span class="text-info">正在上传...</span>');
                            f_li.find('.img-state').html('<div class="bg-info">正在上传...</div>');
                            f_li.find('.progress').show();
                        }
                    }, function(jqXHR, textStatus, errorThrown){
                        // 任何形式的验证失败，都触发重新上传
                        deferred.resolve();
                        f_li.find('.file-state').html('<span class="text-info">正在上传...</span>');
                        f_li.find('.img-state').html('<div class="bg-info">正在上传...</div>');
                        f_li.find('.progress').show();
                    });
                });
                return deferred.promise();
            }
        });
    }

    // 文件上传
    $('.js-upload-files').each(function () {
		var file_array = [];
		var add_url_btn		   = $(this).find('.addurl');	//手工添加网址
        var f_input_file       = $(this).find('.post_input');
        var f_input_file_name  = f_input_file.attr('name');
        // 是否多文件上传
        var f_multiple         = f_input_file.data('multiple');
        // 允许上传的后缀
        var f_ext              = f_input_file.data('ext');
        // 文件限制大小
        var f_size             = f_input_file.data('size');
        // 文件列表
        var f_file_list        = $('#file_list_' + f_input_file_name);

		if($(this).is(":visible")==false){
			$(".layui-tab-title li").click(function(){
				setTimeout(function(){uploader.refresh();},800);	//修复	WebUploader的BUG,元素不可见的时候.按钮不能点击			
			});
		}

        // 实例化上传
        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: true,
            // 去重
            duplicate: true,
			timeout:0, //不要超时,默认1分钟超时
            // swf文件路径
            swf: server_urls.WebUploader_swf,
            // 文件接收服务端。
            server: server_urls.file_upload_url,
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: {
                id: '#picker_' + f_input_file_name,
                multiple: f_multiple
            },
            // 文件限制大小
            fileSingleSizeLimit: f_size,
            // 只允许选择文件文件。
            accept: {
                title: 'Files',
                extensions: f_ext
            }
        });

		//标题或网址输入结束后的事件
		var mouseout = function(){
			f_file_list.find(".f_info input").blur(function(e){
				check_value();
			});

			f_file_list.find(".f_info input").bind('keyup',function(e){
				if (event.keyCode == "13") {
					layer.alert('请点击底部的提交按钮来提交表单!');
				}
			});
		}
		mouseout();

		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});
		
		//添加手工加网址
		var add_puturl = function(){
			add_url_btn.click(function(){
				var str = '<li id="addputurl_' + Math.random() + '" class="list-group-item file-item">' +
                '<i class="fa fa-times-circle remove-file"></i><span class="f_info"> 名称：<input class="f_title" value=""> 地址：<input class="f_url" value=""> 收费：<input class="f_money" style="width:40px;" value=""></span>'+
                '</li>';
				f_file_list.append(str);
				mouseout();
			});
		}
		add_puturl();

		//核对数据
		var check_value = function(){
			file_array = [];	//重新设置值
			f_file_list.find(".list-group-item").each(function(e){
				var url = $(this).find(".f_url").val();
				if(url!=""){
					file_array.push({ 'url':url,'title':$(this).find(".f_title").val(),'money':$(this).find(".f_money").val()});
				}				
			});
			if(file_array.length==0){
				f_input_file.val("");
			}else{
				f_input_file.val( JSON.stringify(file_array) );
			}
		};

        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            var f_li = '<li id="' + file.id + '" class="list-group-item file-item">' +
                '<span class="pull-right file-state"><span class="text-info"><i class="fa fa-sun-o fa-spin"></i> 正在读取文件信息...</span></span>' +
                '<i class="fa fa-times-circle remove-file"></i><span class="f_info"> 名称：<input class="f_title" value="' +
                file.name +
                '"></span><div class="progress progress-mini remove-margin active" style="display: none"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div></div>'+
                '</li>';

            if (f_multiple) {
                f_file_list.append(f_li);				
            } else {
                f_file_list.html(f_li);
                // 清空原来的数据
                f_input_file.val('');
            }

            // 设置当前上传对象
            curr_uploader = uploader;
        });

        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var f_percent = $( '#'+file.id ).find('.progress-bar');
            f_percent.css( 'width', percentage * 100 + '%' );
        });

        // 文件上传成功
        uploader.on( 'uploadSuccess', function( file, response ) {
            var f_li = $( '#'+file.id );
            if (response.code==1) {	//暂时还没改为0,后续会改为0
                if (f_multiple) {
					//file_array.push({'url':response.id,'title':f_li.find('.f_title').val()});
					//f_input_file.val( JSON.stringify(file_array) );

                    //if (f_input_file.val()) {
                    //    f_input_file.val(f_input_file.val() + ',' + response.id);
                    //} else {
                    //    f_input_file.val(response.id);
                    //}
                    f_li.find('.remove-file').attr('data-id', response.id);
					f_li.find('.f_info').append(' 地址：<input class="f_url" value="'+response.id+'"> 收费：<input class="f_money" style="width:40px;" value="">');
					check_value();
					mouseout();
                } else {
                    f_input_file.val(response.id);
                }
            }
            // 加入提示信息
            f_li.find('.file-state').html('<span class="text-'+ response.class +'">'+ response.info +'</span>');

            // 文件上传成功后的自定义回调函数
            if (window['dp_file_upload_success'] !== undefined) window['dp_file_upload_success']();
            // 文件上传成功后的自定义回调函数
            if (window['dp_file_upload_success_'+f_input_file_name] !== undefined) window['dp_file_upload_success_'+f_input_file_name]();
        });

        // 文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            var f_li = $( '#'+file.id );
            f_li.find('.file-state').html('<span class="text-danger">服务器发生错误~</span>');

            // 文件上传出错后的自定义回调函数
            if (window['dp_file_upload_error'] !== undefined) window['dp_file_upload_error']();
            // 文件上传出错后的自定义回调函数
            if (window['dp_file_upload_error_'+f_input_file_name] !== undefined) window['dp_file_upload_error_'+f_input_file_name]();
        });

        // 文件验证不通过
        uploader.on('error', function (type) {
            switch (type) {
                case 'Q_TYPE_DENIED':
                    layer.alert('文件类型不正确，只允许上传后缀名为：'+f_ext+'，请重新上传！');
                    break;
                case 'F_EXCEED_SIZE':
                    layer.alert('文件不得超过'+ (f_size/1024) +'kb，请重新上传！');
                    break;
            }
        });

        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on( 'uploadComplete', function( file ) {
            setTimeout(function(){
                $('#'+file.id).find('.progress').remove();
            }, 500);

            // 文件上传完成后的自定义回调函数
            if (window['dp_file_upload_complete'] !== undefined) window['dp_file_upload_complete']();
            // 文件上传完成后的自定义回调函数
            if (window['dp_file_upload_complete_'+f_input_file_name] !== undefined) window['dp_file_upload_complete_'+f_input_file_name]();
        });

        // 删除文件
        f_file_list.delegate('.remove-file', 'click', function(){			
			$(this).closest('.file-item').remove();
            if (f_multiple) {
				/*
                var id  = $(this).data('id'),
                    ids = f_input_file.val().split(',');

                if (id) {
                    for (var i = 0; i < ids.length; i++) {
                        if (ids[i] == id) {
                            ids.splice(i, 1);
                            break;
                        }
                    }
                    f_input_file.val(ids.join(','));
                }*/
				check_value();
            } else {
                f_input_file.val('');
            }
            
        });

        // 将上传实例存起来
        webuploader.push(uploader);
    }); 
});
</script>

EOT;

}

$array = json_decode($info[$name],true)?:[];
$show = '';
foreach($array AS $rs){
	$fileurl = $rs['url'];
	$rs['title'] = str_replace("'",'&#39;',$rs['title']);
	$fileurl!='' && $show .= "<li class='list-group-item file-item'><i title='点击删除' class='fa fa-remove remove-file' data-id='{$fileurl}'></i> <span class='f_info'> 名称：<input class='f_title' value='{$rs['title']}'> 地址：<input class='f_url' value='$fileurl'> 收费：<input class='f_money' style='width:40px;' value='{$rs['money']}'></span> <a href='".tempdir($fileurl)."' target='_blank' title='点击另存下载'><i class='glyphicon glyphicon-save'></i></a></li>";
}

if(!is_array($array)){
	$info[$name] = '';
}else{
	$info[$name] = str_replace("'",'&#39;',$info[$name]);
}

return <<<EOT

<style type="text/css">
.fbtn{
	float:left;
	margin-top:5px;
}
.addurl{
	background:#bbb;
	color:#fff;
	line-height:45px;
	margin-left:15px;
	padding-left:10px;
	padding-right:10px;
	border-radius:3px;
}
</style>
<div class="col-sm-12 js-upload-files">
        <ul class="list-group uploader-list" id="file_list_{$name}">
		$show
         </ul>
        <input type="hidden" class="post_input" name="{$name}" style="width:1200px;" data-multiple="true" data-size="0" data-ext='' id="atc_{$name}" value='{$info[$name]}'>
        <div id="picker_{$name}" class="fbtn">＋上传多文件</div>  <div class="fbtn addurl">＋输入网址</div>               
</div>
 
$jscode 

EOT;
;