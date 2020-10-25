(function($){
    $(function(){
    	
    	$.extend($.validator.defaults,{ignore:""});
    	
		if ( $("#news_feront_edit_bodytext").length > 0 ) {
			//CKEDITOR.replace( 'news_feront_edit_bodytext');
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
				//alert($('#multidelete-items').val()   );
				return true;
			}
			return false;
		});
		
		$('#addnews').click(function(){
			var bRet = UE.getEditor("news_feront_edit_bodytext").hasContents();
			if(bRet){
				$("#ckRichText").val("ok");
			}else{
				$("#ckRichText").val("");
			}
			return true;  
		});
		
		$("#news_edit_validation").validate({
			errorElement: "span", 
			rules: {
				'tx_newsfrontedit_news[news][title]': {
					required: true
				},
				'tx_newsfrontedit_news[imgpath]': {
					imageCheck: true
				},
				'tx_newsfrontedit_news[ckRichText]': {
					required: true
				},
				'tx_newsfrontedit_news[news][datetime]': {
					required: true
				},
				'tx_newsfrontedit_news[news][categories][]': {
					required: true
				},
				'tx_newsfrontedit_news[news][author]': {
					required: true
				}
			},
			messages: {
				'tx_newsfrontedit_news[news][title]': {
					required: "请输入标题!"
				},
				'tx_newsfrontedit_news[imgpath]': {
					imageCheck: "请上传封面图片!"
				},
				'tx_newsfrontedit_news[ckRichText]': {
					required: "请输入内容!"
				},
				'tx_newsfrontedit_news[news][datetime]': {
					required: "请选择发布时间!"
				},
				'tx_newsfrontedit_news[news][categories][]': {
					required: "请选择分类!"
				},
				'tx_newsfrontedit_news[news][author]': {
					required: "请输入作者!"
				}
			}   
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