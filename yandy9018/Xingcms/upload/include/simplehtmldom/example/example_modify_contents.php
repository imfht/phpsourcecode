<?php

include('../simple_html_dom.php');
$html = file_get_html('http://www.google.cn/');
foreach($html->find('img') as $e)
$e->outertext = '';
foreach($html->find('input') as $e)
$e->outertext = '[INPUT]';
echo $html;

?>