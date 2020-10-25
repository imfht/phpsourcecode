<?php
class AppsBehavior extends Behavior {

    public function run(&$params){
    	$note = F('after_Note');
		F('after_Note',NULL);
    	if ($note) {
			$msg = $note['msg'];
			$title = $note['title'];
			$time = $note['time'];
			$sticky = $note['sticky'];
    		$html = "<script type=\"text/javascript\">
    		$(function(){
		    		var title = \"".$title."\";
		    		var text = \"".$msg."\";
		    		var time = \"".$time."\";
		    		var sticky = \"".$sticky."\";
		    		$.gritter.add({
		            title: (typeof title !== 'undefined') ? title : 'Message - Head',
		            text: (typeof text !== 'undefined') ? text : 'Body',
		            image: (typeof image !== 'undefined') ? image : null,
		            sticky: (typeof sticky !== 'undefined') ? sticky : false,
		            time: (typeof time !== 'undefined') ? time : 3000
	        	});
			});
			</script>";
			echo $html;
    	}
    }
}
