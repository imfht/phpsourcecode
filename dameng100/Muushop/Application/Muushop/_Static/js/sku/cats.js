/**
 * 商品排序
 * @param  {[type]} ){} [description]
 * @return {[type]}       [description]
 */
$(function(){
	$('div[data-type="sort-box"] a').each(function(){
		var _this = $(this);
		_this.click(function(){
			alert('点了'+_this.data('name'));
		})
	})

})