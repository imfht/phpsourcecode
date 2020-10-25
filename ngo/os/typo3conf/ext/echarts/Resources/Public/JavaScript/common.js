(function($){
    $(function(){
    	
    	$.extend($.validator.defaults,{ignore:""});
		
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
			if (confirm('您确认要删除选中记录吗？')) {
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
					// alert($('#multidelete-items').val());
					return true;
				}
				return false;
			} else {
				return false;
			}
		});
		
		$("#form_echarts").validate({
			errorElement: "span", 
			errorPlacement: function(error, element) {
				if (element.is(":checkbox")||element.is(":radio")){
					error.appendTo(element.parent());
				}else{
					error.insertAfter(element);
				}
			},
			rules: {
				'tx_echarts_pi1[echarts][title]': {
					required: true
				}
			},
			messages: {
				'tx_echarts_pi1[echarts][title]': {
					required: "请输入图表标题!"
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
$(function(){
	jQuery.validator.addMethod("isTelphone", function(value, element) { 
		    var tel = /^1[3|4|5|6|7|8|9][0-9]\d{8}$/;
		    return this.optional(element) || (tel.test(value));
		}, "手机号输入错误");
		
	jQuery.validator.addMethod("isPostcode", function(value, element) { 
	    var tel = /^[1-9]\d{5}(?!\d)$/;
	    return this.optional(element) || (tel.test(value));
	}, "邮编错误");
})

// 添加dom元素
function addOneDom(obj) {
	var btnDom = $(obj).parent().parent();
	var serial = $(obj).parent().parent().parent().find("tr").length;
	var newDom = $(obj).parent().parent().prev().clone();
	// console.log(serial);
	// console.log(newDom);
	$(newDom).find('td .uid').val('');
	$(newDom).find('td:first-child').children().val(serial);
	btnDom.before(newDom);
}

// 删除dom元素
function delOneDom(obj, typ = '') {
	var tbody = $(obj).parent().parent().parent();
	var trLength = $(tbody).find("tr").length;
	if (trLength <= 2) {
		alert('最后一列不能被删除!');
		return false;
	} else {
		var btnDom = $(obj).parent().parent();
		$(btnDom).remove();
	}
}