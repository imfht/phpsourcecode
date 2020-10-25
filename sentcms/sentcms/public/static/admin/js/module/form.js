define(['jquery', 'sent', 'form', 'xlsxs'], function($, sent, form, xlsxs){

	var outList = [];

	var formModule = {
		lists: function(){
			if($('.btn-out').size() > 0){
				$('.btn-out').click(function(e){
					e.preventDefault();
					var url = $(this).attr('href');
					var filename = $(this).data('name');
					formModule.outXlsx(url, 1, filename)
				})
			}
		},
		outXlsx: function (url, page = 1, filename){
			var data = $('form').serialize();
			var pagesize = $('select.pagesize').val() || 30;
			data = data + '&out=1&pagesize='+pagesize+'&page='+page
			$.ajax({
				url: url,
				data: data,
				type: 'get',
				beforeSend: function(){
					sent.msg('正在导出第'+page+'页，请耐心等待，不要关闭浏览器');
				},
				error: function(){
					sent.msg('导出失败！');
				},
				success:function(res){
					outList = outList.concat(res.data);
					if (res.last_page > page) {
						formModule.outXlsx(url, page+1, filename)
					}else{
						xlsxs.downloadExl(outList,filename);
						sent.msg('导出完成！');
						setTimeout(function(){}, 3000);
					}
				},
				dataType: 'json'
			})
		}
	};

	return formModule;
})