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
		
		
		$("#timeline_validation").validate({
			errorElement: "span", 
			rules: {
				'tx_timeline_timeline[timeline][title]': {
					required: true
				},
				'tx_timeline_timeline[timeline][eventdate]': {
					required: true
				},
				'tx_timeline_timeline[timeline][bodytext]': {
					required: true
				}
			},
			messages: {
				'tx_timeline_timeline[timeline][title]': {
					required: "请输入事件标题!"
				},
				'tx_timeline_timeline[timeline][eventdate]': {
					required: "请选择事件发生时间!"
				},
				'tx_timeline_timeline[timeline][bodytext]': {
					required: "请输入事件内容!"
				}
			}   
        });
    });
})(jQuery);


function isMobile() {
    return /(iPhone|iPad|iPod|iOS|android|MicroMessenger)/i.test(navigator.userAgent);
}