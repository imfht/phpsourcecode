(function($){
    $(function(){
	
		if ( $("#timeline_bodytext").length > 0 ) {
			UE.getEditor("timeline_bodytext");
		}		
		
		$('.btn-del').click(function(){
			if(confirm('您确认要删除该条记录吗？')) return true;
			return false;
		});
		
		//全选&取消全选
		$('input[type="checkbox"].selectall').bind('click', function() { 
			$(this).closest('table').find('.sel').prop("checked", this.checked);
		}); 
		
		//删除选中
		$('.btn-delete-all').bind('click',function(){
			var items='';
			$('input[type="checkbox"].sel').each(function(){
				if($(this).prop("checked")){
					items += $(this).val()+',';
				}
			});
			
			if(items == ''){
				alert('请您选择需要删除的记录！');
			}else{
				$('#multidelete-items').val(items);
				//alert($('#multidelete-items').val()   );
				return true;
			}
			return false;
		});
		
		// PDF文件校验
		$("#id_filemange_file").validate({
			errorElement: "span", 
			rules: {
				'tx_filemanage_filemanage[filemanage][title]': {
					required: true
				},
				'tx_filemanage_filemanage[filepath]': {
					fileck: true
				},
				'tx_filemanage_filemanage[fileimg]': {
					fileimgck: true
				},
				'tx_filemanage_filemanage[filemanage][filetypes]': {
					required: true
				},
				'tx_filemanage_filemanage[filemanage][senddate]': {
					required: true
				},
				'tx_filemanage_filemanage[filemanage][sort]': {
					required: true
				}
			},
			messages: {
				'tx_filemanage_filemanage[filemanage][title]': {
					required: "请输入文件名称!"
				},
				'tx_filemanage_filemanage[filepath]': {
					fileck: "请上传文件!"
				},
				'tx_filemanage_filemanage[fileimg]': {
					fileimgck: "请上传封面图！"
				},
				'tx_filemanage_filemanage[filemanage][filetypes]': {
					required: "请选择文件分类！"
				},
				'tx_filemanage_filemanage[filemanage][senddate]': {
					required: "请选择发布时间!"
				},
				'tx_filemanage_filemanage[filemanage][sort]': {
					required: "请输入排序！"
				}
			}   
        });

        // 其他文件校验
		$("#id_filemange_otherfile").validate({
			errorElement: "span", 
			rules: {
				'tx_filemanage_filemanage[filemanage][title]': {
					required: true
				},
				'tx_filemanage_filemanage[filepath]': {
					fileck: true
				},
				'tx_filemanage_filemanage[filemanage][filetypes]': {
					required: true
				},
				'tx_filemanage_filemanage[filemanage][senddate]': {
					required: true
				},
				'tx_filemanage_filemanage[filemanage][sort]': {
					required: true
				}
			},
			messages: {
				'tx_filemanage_filemanage[filemanage][title]': {
					required: "请输入文件名称!"
				},
				'tx_filemanage_filemanage[filepath]': {
					fileck: "请上传文件!"
				},
				'tx_filemanage_filemanage[filemanage][filetypes]': {
					required: "请选择文件分类！"
				},
				'tx_filemanage_filemanage[filemanage][senddate]': {
					required: "请选择发布时间!"
				},
				'tx_filemanage_filemanage[filemanage][sort]': {
					required: "请输入排序！"
				}
			}   
        });
        jQuery.validator.addMethod("fileck", function(value, element) {
		    var bool = true;
		    var fil = $("#filePathUid").val();
		    if(value==""){
		    	if(fil=="" || fil == null){
		    		bool=false;
		    	}
		    }
		    return bool;
		}, "请上传文件!");

		jQuery.validator.addMethod("fileimgck", function(value, element) {
		    var bool = true;
		    var fi2 = $("#fileImgUid").val();
		    if(value==""){
		    	if(fi2=="" || fi2 == null){
		    		bool=false;
		    	}
		    }
		    return bool;
		}, "请上传封面图!");

		//验证上传文件类型并即时显示
		$("#exampleInputFile").change(function () {
			if($("#see_image").children('img').length>0){
				$("#see_image").children('img').remove();
			}
	        var filepath = $("#exampleInputFile").val();
	        var extStart = filepath.lastIndexOf(".");
	        var ext = filepath.substring(extStart, filepath.length).toUpperCase();
	        if (ext == ".PNG" || ext == ".JPG" || ext ==".JPEG") {
	        	$("#see_image").append("<img src='"+getObjectURL($(this)[0].files[0])+"' width='150px' />");
	        } else {
	          	message.empty();
	          	message.css("color","red");
	          	message.html("仅支持JPG、PNG、JPEG格式的图片");
	          	return false;
	        }
	        return true;
	    });
    });
})(jQuery);

//获得上传图片URL地址
function getObjectURL(file) {
    var url = null ;
    if (window.createObjectURL!=undefined) { // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }
    return url ;
}
function isMobile() {
    return /(iPhone|iPad|iPod|iOS|android|MicroMessenger)/i.test(navigator.userAgent);
}