$(function(){

	var $searchText = $('#search_text'), $searchBtn = $('#search_btn');
	
	$searchBtn.slideMove({
		
		endCall : function(start_x, start_y, end_x, end_y){
			
			if(!start_y) return;
			
			if(!end_y) search();
			
		},
		
		cancelCall : function(start_x, start_y, end_x, end_y){
			
			if(!start_y) return;
			
			if(!end_y) search();
			
		}

	});
	
	$searchText.keydown(function(e){
		
		if(e.keyCode == 13) search();
		
	});
	
	function search(){

		var val = $.trim($searchText.val());
		
		if(val == '') return;
		
		location.href = APP + '/Search/index/keyword/' + encodeURI(val);
		
	}
	
});