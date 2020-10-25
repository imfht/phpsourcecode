<?php

include_once('../simple_html_dom.php');
function my_callback($element) {
if ($element->tag=='input')
$element->outertext = 'input';
if ($element->tag=='img')
$element->outertext = 'img';
if ($element->tag=='a')
$element->outertext = 'a';
}
$html = file_get_html('http://www.google.com/');
$html->set_callback('my_callback');
echo $html;
?>