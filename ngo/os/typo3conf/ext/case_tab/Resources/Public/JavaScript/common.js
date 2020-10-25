function toggle(i){
	var hasClass = $(i).hasClass('glyphicon-chevron-down');
	if(hasClass == true){
		 $(i).addClass('glyphicon glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
		 $(i).parent().next('ul').show('slow');
	}else{
		$(i).addClass('glyphicon glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
		 $(i).parent().next('ul').hide('slow');
	}
}

