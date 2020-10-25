<?php
class MediaController extends Dwz_Controller_Action {
	function _filter(&$map) {
		$map ['is_delete'] = array ('!=', 1 );
		if (! empty ( $_POST ['name'] )) {
			$map ['name'] = array ('like', "%" . $_POST ['name'] . "%" );
		}
	
	}
}
?>