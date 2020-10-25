(function($){
    $(function(){
		if ( $("#news_feront_edit_bodytext").length > 0 ) {
			UE.getEditor("news_feront_edit_bodytext");
		}
		
		if(!isMobile()){
            $(".select2").select2({
                placeholder: "请选择",
                allowClear: true
            });
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
				//alert($('#multidelete-items').val());
				return true;
			}
			return false;
		});

		//验证上传文件类型并即时显示
		$("#exampleInputFile").change(function () {
			var message = $("#see_image");
	        var filepath = $("#exampleInputFile").val();
	        var extStart = filepath.lastIndexOf(".");
	        var ext = filepath.substring(extStart, filepath.length).toUpperCase();
	        if (ext == ".PNG" || ext == ".JPG" || ext == ".JPEG") {
	        	message.empty();
	        	message.html("<img src='"+getObjectURL($(this)[0].files[0])+"' id='preview' class='img-responsive' height='200' width='160' />");
	        } else {
	          	message.empty();
	          	message.css("color","red");
	          	message.html("仅支持<br/>JPG、PNG、JPEG格式的图片");
	          	return false;
	        }
	        return true;
	    });
    });
})(jQuery);

$(function(){
	$("#id_teamlist_newteam").validate({
		errorElement: "span", 
		rules: {
			'tx_teamlist_teamwork[newTeam][name]': {
				required: true
		　　 },
			'tx_teamlist_teamwork[newTeam][intro]': {
				required: true
		　　},			
			'tx_teamlist_teamwork[image]': {
				required: true
			},
			'tx_teamlist_teamwork[newTeam][order]': {
				required: true,
				number: true
			},
			'tx_teamlist_teamwork[newTeam][detail]': {
				required: true
			}
		},
		messages: {
			'tx_teamlist_teamwork[newTeam][name]': {
				required: "请输入姓名！"
		　　    },
			'tx_teamlist_teamwork[newTeam][intro]': {
				required: "请填写简介!"
			},
			'tx_teamlist_teamwork[image]': {
				required: "请上传照片!"
			},
			'tx_teamlist_teamwork[newTeam][order]': {
				required: "请输入排序数字！",
				number: "请输入正确的数字！"
			},
			'tx_teamlist_teamwork[newTeam][detail]': {
				required: "请填写详情!"
			}
		}   
    });
});


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
