/**
 * 商品排序
 * @param  {[type]} ){} [description]
 * @return {[type]}       [description]
 */
$(function(){
	//得到当前排序规则
	var sort = $('input[name="sort"]').val();
	//排序字符串转数组
	var sort_arr = sort.split('_');

	if(sort_arr[0]=='price'){
		var sort_name = sort_arr[0];
		var sort_order = sort_arr[1];
	}else{
		var sort_name = sort_arr[0]+'_'+sort_arr[1];
		var sort_order = sort_arr[2];
	}

	if(sort == 'all'){
		sort_name = 'all';
	}

	$('div[data-type="sort-box"] a').each(function(){
		var _this = $(this);

		_this.removeClass('active');
		if(sort_name === _this.data('name')){
			_this.addClass('active');

			if(sort_order === 'desc'){
				_this.find('i').addClass('icon icon-long-arrow-down');
			}
			if(sort_order === 'asc'){
				_this.find('i').addClass('icon icon-long-arrow-up');
			}
		}
	})

})