$(function(){
	
	//搜索
	var $searchButton = $('#search_button'), $textInput = $searchButton.prev('input');
	
	$searchButton.click(search);
	
	$textInput.keydown(function(e){
		
		if(e.keyCode == 13) search();
		
	});
	
	function search(){
		
		var val = $.trim($textInput.val());
		
		if(val == '') return;
		
		location.href = APP + '/Search/index/keyword/' + encodeURI(val);
		
	}
	
});