<?php

/*
	Id: befen and extends
*/

require EXTEND_PATH . 'befen.php';

require EXTEND_PATH . 'befen_http.php';
require EXTEND_PATH . 'befen_think.php';

require EXTEND_PATH . 'function.php';

if(file_exists(EXTEND_PATH . 'function_extend.php')) {
	require EXTEND_PATH . 'function_extend.php';
}

