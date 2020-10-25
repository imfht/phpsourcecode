(function($){
    $(function(){
	
		if ( $("#news_feront_edit_bodytext").length > 0 ) {
			setEditor( 'news_feront_edit_bodytext');
		}

        /*if(!isMobile()){
            $(".select2").select2({
                placeholder: "请选择",
                allowClear: true
            });
        }*/
		
		
		$('.btn-del').click(function(){
			if(confirm('确认删除')) return true;
			return false;
		});

		//全选&取消全选
		$('input[type="checkbox"].selectall').bind('click', function() { 
			$(this).closest('table').find('.sel').prop("checked", this.checked);
		}); 
		
		//导出选中
		$('.btn-execl-all').bind('click',function(){
			var items='';
			$('input[type="checkbox"].sel').each(function(){
				if($(this).prop("checked")){
					items += $(this).val()+',';
				}
			});
			
			if(items == ''){
				alert('没有选中任何项目，不能批量操作');
			}else{
				$('#multiexecl-items').val(items);
				// alert($('#multiexecl-items').val()   );
				return true;
			}
			return false;
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
				alert('没有选中任何项目，不能批量操作');
			}else{
				$('#multidelete-items').val(items);
				//alert($('#multidelete-items').val()   );
				return true;
			}
			return false;
		});
		
		$(function(){
			 $("#btn-sub").click(function(){
		         if(confirm("数据提交后无法修改，您确定要提交吗?")){
		               $("#ckstat").val("2");
		               return true;
		             }else{
		               return false;
		             }
		     });
			$("#informMessage").validate({
				errorElement: "span", 
				rules: {
					'tx_kjgcontent_kjgsys[information][title]': {
						required: true
				　　     },
					'tx_kjgcontent_kjgsys[information][datetime]': {
						required: true
				　　     },
					'tx_kjgcontent_kjgsys[information][categories]': {
						required: true
				　　     },
					'tx_kjgcontent_kjgsys[information][author]': {
						required: true
				　　     },
					'tx_kjgcontent_kjgsys[information][bodytext]': {
						required: true
				　　     }
				},
				messages: {
					'tx_kjgcontent_kjgsys[information][title]': {
						required: "请输入资讯标题!"//,remote:jQuery.format("用户名已经被注册")
					},
					'tx_kjgcontent_kjgsys[information][datetime]': {
						required: "请选择资讯时间!"
				　　     },
					'tx_kjgcontent_kjgsys[information][categories]': {
						required: "请输入资讯分类!"
				　　     },
					'tx_kjgcontent_kjgsys[information][author]': {
						required: "请输入作者!"
				　　     },
					'tx_kjgcontent_kjgsys[information][bodytext]': {
						required: "请资讯内容!"
				　　     }
				}   
		    });
		});
		
    });
})(jQuery);

function getMoney(){
	var money = document.getElementById('money').value;
	document.getElementById('pay-money').value=money;
}

function isMobile() {
    return /(iPhone|iPad|iPod|iOS|android|MicroMessenger)/i.test(navigator.userAgent);
}