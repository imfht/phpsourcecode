$.ajaxSetup(
{
    cache : false
}
);
(function ($)
{
    //表格处理
    $.fn.Table = function (options)
    {
        var defaults =
        {
            selectAll : '#selectAll',
            deleteUrl : ''
        }
        var options = $.extend(defaults, options);
        this.each(function ()
        {
            var table = this;
            //处理多选单选
            $(options.selectAll).click(function ()
            {
                if (!!$(options.selectAll).ar("checked"))
                {
                    $(table).find("[name='id[]']").each(function ()
                    {
                        $(this).ar("checked", 'true');
                    }
                    )
                }
                else
                {
                    $(table).find("[name='id[]']").each(function ()
                    {
                        $(this).removeAr("checked");
                    }
                    )
                }
                
            }
            );
            //处理删除
            $(table).find('.u-del').click(function ()
            {
                var obj = this;
                var div = $(obj).parent().parent();
                var url = '';
                if (options.deleteUrl == '')
                {
                    url = $(obj).ar('url');
                }
                else
                {
                    url = options.deleteUrl;
                }
                $.dialog.confirm('你确认删除操作？', function ()
                {
                    $.post(url,
                    {
                        data : $(obj).ar('data')
                    }, function (json)
                    {
                        if (json.status == 'y')
                        {
                            div.remove();
                            $.dialog.tips(json.info, 1);
                        }
                        else
                        {
                            $.dialog.tips(json.info, 3);
                        }
                    }, 'json');
                }, function ()
                {
                    return;
                }
                );
            }
            );
            //处理ajax编辑
            $(table).find('.u-edit').click(function ()
            {
                var oldText = $(this).text();
                var width = $(this).ar('width');
                var obj = this;
                var input = $(obj).find('input');
                var url = $(obj).ar('url');
                var name = $(obj).ar('name');
                if (input.length == 0)
                {
                    var html = '<input class="u-ipt" style="width:' + width + 'px;" value="' + oldText + '" type="text" />';
                    $(obj).html(html);
					input = $(obj).find('input');
                }
				input.focus();
                input.blur(function ()
                {
                    text = input.val();
                    $.post(url,
                    {
                        name : name,
                        data : text
                    }, function (json)
                    {
                        if (json.status == 'y')
                        {
                            $(obj).text(json.info);
                        }
                        else
                        {
                            input.addClass('u-a-err');
                            $.dialog.tips(json.info);
                        }
                    }, 'json');
                }
                );
            }
            );
        }
        );
    };
    
	//Select选择处理
    $.fn.Select = function (options)
    {
        this.each(function ()
        {
            var data = $(this).attr('data');
			var dataName = $(this).attr('name');
			if( data != null){
				$(this).val(data);
			}
        }
        );
    };
	
	//表单处理
    $.fn.Form = function (options)
    {
        var defaults =
        {
            postonce : true,
            tiptype : function (msg, o, cssctl){
				if (!o.obj.is("form")){
                    var objtip = o.obj.siblings("span");
                    cssctl(objtip, o.type);
                    objtip.text(msg);
                }else{
					alert(msg);
                }
            },
        }
        var options = $.extend(defaults, options);
		this.each(function (){
            $(this).Validform(
				options
			);
        });
    };
    
	//KindEditor编辑器调用
    $.fn.Editor = function (options)
    {
        this.each(function ()
        {
            //编辑器
            var id = this;
            var editorConfig =
            {
                allowFileManager : true,
				width: '720px',
				height: '460px',
                afterBlur : function ()
                {
                    this.sync();
                }
            };
            editorConfig = $.extend(editorConfig, options);
            var editor = KindEditor.create(id, editorConfig);
        }
        );
    };
    
	//ueEditor编辑器调用
    $.fn.uEditor = function (options)
    {
        this.each(function ()
        {
            //编辑器
            var id = this;
            var editorConfig =
            {
				initialFrameWidth: '720',
				initialFrameHeight: '460',
            };
            editorConfig = $.extend(editorConfig, options);
            var ue = UE.getEditor(id, editorConfig);
        }
        );
    };
	
    //颜色
    $.fn.Color = function (options)
    {
        var defaults = {
			size:3,
			y:30,
			colorChange:2
		}
        var options = $.extend(defaults, options);
        this.each(function ()
        {
            $(this).soColorPacker(
				options
			);
        }
        );
    };
    
    //plupload单文件上传调用
    $.fn.FileUpload = function (options)
    {
		this.each(function ()
        { 
            var upButton = $(this);
			var dataName = upButton.attr('dataname');
			var data = upButton.attr('data');
			if( !upButton.attr('id') ){
				upButton.attr('id','t-upload'+dataName);
			}
			
			var defaults = {
			config:{
				runtimes : 'html5,flash,silverlight,html4',
				browse_button : upButton.attr('id'), 
				url : rootUrl + 'index.php?r=admin/index/upload',
				multi_selection:false,
				resize : { quality : 70 },
				
				filters : {
					max_file_size : '3mb',
					mime_types: [
						{title : "Image files", extensions : "jpg,gif,png,bmp"},
						{title : "Zip files", extensions : "zip"}
					]
				},
				
				flash_swf_url : baseDir + 'upload/Moxie.swf',
			},
			event:{
				FilesAdded : function(up, files) {upButton.text('准备上传');uploader.start();},
				
				UploadProgress : function (up, file){upButton.text('上传中'+file.percent+'%');},
				
				FileUploaded : function (up, file, data){var response = eval('(' + data.response + ')');$( '#picshow'+dataName ).find('img').replaceWith('<img width="100px" style="margin-right:10px">');var img = $( '#picshow'+dataName ).find('img');img.attr( 'src',response.file.savepath+response.file.savename );$( '#picshow'+dataName ).find('#imgurl').val( response.file.savepath+response.file.savename );},
				
				UploadComplete : function (up, files){ upButton.text('重新选图'); },
				
				Error : function (up, err){alert('文件：'+err.file.name+',错误：'+err.message);},
			},
			data:{
				builddiv : {yesno:true},
			}
			}
			
			var uploadconfig = $.extend(defaults.config, options.config);
			var uploadevent = $.extend(defaults.event, options.event);
			var uploaddata = $.extend(defaults.data, options.data);
			
			if( uploaddata.builddiv.yesno ){
				upButton.before( '<div id="picshow'+dataName+'" style="display: inline-block"><img><input id="imgurl" name="' + dataName + '" type="hidden" value="" /></div>');
			}
			
			if( data ){
				var img = $( '#picshow'+dataName ).find('img');
				img.attr( {'src':data,'width':'100px','style':'margin-right:10px'} );
				$( '#picshow'+dataName ).find('#imgurl').val( data );
			}
			
			var uploader = new plupload.Uploader(uploadconfig);
			uploader.init();
			uploader.bind('FilesAdded', uploadevent.FilesAdded);
			uploader.bind('UploadProgress', uploadevent.UploadProgress);
            uploader.bind('FileUploaded', uploadevent.FileUploaded);
            uploader.bind('UploadComplete', uploadevent.UploadComplete);
            uploader.bind('Error', uploadevent.Error);
		});
    };
    
	//plupload多图上传
    $.fn.MultiUpload = function (options)
    {
        this.each(function ()
        {
            var upButton = $(this);
			var dataName = upButton.attr('dataname');
			var data = upButton.attr('data');
			if( !upButton.attr('id') ){
				upButton.attr('id','t-upload'+dataName);
			}
			
			var defaults = {
			config:{
				runtimes : 'html5,flash,silverlight,html4',
				browse_button : upButton.attr('id'), 
				url : rootUrl + 'index.php?r=admin/index/upload',
				chunk_size : '1mb',
				
				resize : { quality : 70 },
				
				filters : {
					max_file_size : '3mb',
					
					mime_types: [
						{title : "Image files", extensions : "jpg,gif,png,bmp"},
						{title : "Zip files", extensions : "zip"}
					]
				},
				
				flash_swf_url : baseDir + 'upload/Moxie.swf',
			},
			event:{
				FilesAdded : function(up, files) {
                	plupload.each(files, function(file) {
						var li = $('<li id="' + file.id + '"><a class="close" href="javascript:;" onclick="$(this).parent().remove();">×</a><div class="img"><span class="pic"><img></span></div><div class="title"><input name="' + dataName + '[id][]" type="hidden" value="' + file.id + '" /><input name="' + dataName + '[title][]" type="text" value="' + file.name + '" /><input id="imgurl" name="' + dataName + '[url][]" type="hidden" value="" /></div><div class="progress" style="background-color: #AFC;height: 2px;margin-top: 4px;"></div></li>');

						$( '#piclist'+dataName ).append( li );
						li.find('img').replaceWith('<span class="state">准备上传</span>');
                	});
					uploader.start();
				},
				
				UploadProgress : function(up, file) {
					$percent = $( '#'+file.id ).find('.state').html('上传中'+file.percent+'%');
            	},
				
				FileUploaded : function(up, file, info) {
					var response = eval('(' + info.response + ')');
					$( '#'+file.id ).find('.state').replaceWith('<img>');
					var img = $( '#'+file.id ).find('img');
					img.attr( 'src', response.file.savepath+response.file.savename );
					$( '#'+file.id ).find('#imgurl').val( response.file.savepath+response.file.savename );
					$( '#piclist'+dataName ).sortable();
            	},

				UploadComplete : function (up, files){ upButton.text('继续添加'); },
				
				ChunkUploaded: function(up, file, info) {log('[ChunkUploaded] File:', file, "Info:", info);},

				Error : function (up, err){alert('文件：'+err.file.name+',错误：'+err.message);},
			},
			data:{
				builddiv : {yesno:true},
			}
			}
			
			var uploadconfig = $.extend(defaults.config, options.config);
			var uploadevent = $.extend(defaults.event, options.event);
			var uploaddata = $.extend(defaults.data, options.data);
			
			if( uploaddata.builddiv.yesno ){
				upButton.after( '<div id="piclist'+dataName+'" class="m-multi-image"></div>');
			}
			
			/*if( data ){
				var li = $('<li id="' + file.id + '"><a class="close" href="javascript:;" onclick="$(this).parent().remove();">×</a><div class="img"><span class="pic"><img></span></div><div class="title"><input name="' + dataName + '[id][]" type="hidden" value="' + file.id + '" /><input name="' + dataName + '[title][]" type="text" value="' + file.name + '" /><input id="imgurl" name="' + dataName + '[url][]" type="hidden" value="" /></div><div class="progress" style="background-color: #AFC;height: 2px;margin-top: 4px;"></div></li>');
				
				$( '#piclist'+dataName ).append( li );
			}*/
			
			var uploader = new plupload.Uploader(uploadconfig);
			uploader.init();
			uploader.bind('FilesAdded', uploadevent.FilesAdded);
			uploader.bind('UploadProgress', uploadevent.UploadProgress);
            uploader.bind('FileUploaded', uploadevent.FileUploaded);
            uploader.bind('UploadComplete', uploadevent.UploadComplete);
			uploader.bind('ChunkUploaded', uploadevent.ChunkUploaded);
            uploader.bind('Error', uploadevent.Error);
        });
    };
    
	//联动菜单
    $.fn.LinkMenu = function (options)
    {
        var defaults = {}
        var options = $.extend(defaults, options);
        this.each(function ()
        {
            var menu = this;
            var url = $(menu).ar('data');
            var id = $(menu).ar('id');
            var name = $(menu).ar('name');
            if (url == '' || url == null)
            {
                return false;
            }
            //选择绑定
            $(menu).parent().on('change', 'select', function ()
            {
                var subMenu = $(this);
                var pid = subMenu.val();
                if (pid == '' || pid == null)
                {
                    return false;
                }
                subMenu.nextAll('select').remove();
                $.post(url,
                {
                    pid : pid
                }, function (json)
                {
                    if (json.status == 'y' && json.info != '')
                    {
                        //去处属性
                        $(menu).parent().find('select').ar('name', '');
                        $(menu).parent().find('select').ar('id', '');
                        //添加选项
                        var html = '<select class="u-slt" name="' + name + '" id="' + id + '">\<option value="">请选择</option>';
                        for (var i in json.info)
                        {
                            html += '<option value="' + json.info[i].id + '">' + json.info[i].name + '</option>';
                        }
                        html += '</select>';
                        subMenu.after(html);
                    }
                    else
                    {
                        return false;
                    }
                }, 'json');
            }
            );
            
        }
        );
    };
    
    //表单页面处理
    $.fn.FormPage = function (options)
    {
        this.each(function ()
        {
            var form = this;
            form = $(form);
            //表单处理
            form.Form({});
            //多图片上传
            if ($(".t-multiupload").length > 0)
            {
                form.find('.t-multiupload').MultiUpload(
                {
                    type : 'jpg,gif,png,bmp,jpeg',
                    uploadUrl : options.uploadUrl,
                    fileList : options.fileList,
                    complete : options.uploadComplete,
                    uploadParamsCallback : options.uploadParamsCallback,
                    UploadMaxNum : options.UploadMaxNum
                }
                );
            }
            //单图片上传
            if ($(".t-imgupload").length > 0)
            {
                form.find('.t-imgupload').FileUpload(
                {
                    type : 'jpg,gif,png,bmp,jpeg',
                    uploadUrl : options.uploadUrl,
                    complete : options.uploadComplete,
                    uploadParamsCallback : options.uploadParamsCallback
                }
                );
            }
            //单文件上传
            if ($(".t-fileupload").length > 0)
            {
                form.find('.t-fileupload').FileUpload(
                {
                    type : '*',
                    uploadUrl : options.uploadUrl,
                    complete : options.uploadComplete,
                    uploadParamsCallback : options.uploadParamsCallback
                }
                );
            }
            //编辑器
            if ($(".t-editor").length > 0)
            {
                form.find('.t-editor').Editor();
            }
            //颜色
            if ($(".t-color").length > 0)
            {
                form.find('.t-color').Color();
            }
            //时间选择
            if ($(".t-time").length > 0)
            {
                form.find('.t-time').calendar(
                {
                    format : 'yyyy-MM-dd HH:mm:ss'
                }
                );
            }
            //联动菜单
            if ($(".t-linkage").length > 0)
            {
                form.find('.t-linkage').LinkMenu();
            }
            //TAB菜单
            if ($(".t-tabs").length > 0)
            {
                $(".t-tabs li .tab-a").powerSwitch(
                {
                    classAdd : "tab-on"
                }
                );
            }
        }
        );
    };
    
    //AJAX操作带确认
    $.fn.AjaxConfirm = function (options)
    {
        var defaults =
        {
            url : '',
            content : '',
            params : function ()  {},
            success : function ()  {},
            failure : function ()  {}
        }
        var options = $.extend(defaults, options);
        this.each(function ()
        {
            var obj = this;
            $(obj).click(function ()
            {
                var params = options.params(obj);
                debug(params);
                $.dialog.confirm(options.content, function ()
                {
                    $.post(options.url, params, function (json)
                    {
                        if (json.status == 'y')
                        {
                            options.complete(json.info, obj);
                            $.dialog.tips(json.info, 2);
                        }
                        else
                        {
                            options.failure(json.info, obj);
                            $.dialog.tips(json.info, 2);
                        }
                    }, 'json');
                    
                }
                );
            }
            );
            
        }
        );
    };
}
)(jQuery);