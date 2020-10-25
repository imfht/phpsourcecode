<?php
switch($do){
    case "check":
        session_start();
		$letters_code = $seccode = '';
        $letters_code = $_SESSION['6_letters_code'];
		$seccode = isset($_POST['seccode']) ? $_POST['seccode'] : '';
        if(!empty($letters_code) && !empty($seccode) && ($letters_code == $seccode)){
			echo 1;
		}
        break;
    case "display":
        //TODO ÒýÈëDZ SECCODE
        $img=new seccode();
        $img->display_seccode();
    break;
}
?>