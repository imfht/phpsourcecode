<?php
$pdf_filepath = isset($_REQUEST['pdf_filepath']) && !empty($_REQUEST['pdf_filepath']) ? $_REQUEST['pdf_filepath'] : 'source/lib/pdfjs/web/compressed.tracemonkey-pldi-09.pdf';
$root_path = dirname(dirname(dirname(dirname(__FILE__))));
if(file_exists($root_path.'/'.$pdf_filepath)){
	include('./web/viewer.html');
}