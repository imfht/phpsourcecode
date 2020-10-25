<?php

include_once('../../simple_html_dom.php');
function scraping_IMDB($url) {
$html = file_get_html($url);
$ret['Title'] = $html->find('title',0)->innertext;
$ret['Rating'] = $html->find('div[class="general rating"] b',0)->innertext;
foreach($html->find('div[class="info"]') as $div) {
if($div->find('h5',0)->innertext=='User Comments:')
return $ret;
$key = '';
$val = '';
foreach($div->find('*') as $node) {
if ($node->tag=='h5')
$key = $node->plaintext;
if ($node->tag=='a'&&$node->plaintext!='more')
$val .= trim(str_replace("\n",'',$node->plaintext));
if ($node->tag=='text')
$val .= trim(str_replace("\n",'',$node->plaintext));
}
$ret[$key] = $val;
}
$html->clear();
unset($html);
return $ret;
}
$ret = scraping_IMDB('http://imdb.com/title/tt0335266/');
foreach($ret as $k=>$v)
echo '<strong>'.$k.' </strong>'.$v.'<br>';

?>