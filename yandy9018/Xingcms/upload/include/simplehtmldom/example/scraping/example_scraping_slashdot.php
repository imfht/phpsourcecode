<?php

include_once('../../simple_html_dom.php');
function scraping_slashdot() {
$html = file_get_html('http://slashdot.org/');
foreach($html->find('div.article') as $article) {
$item['title'] = trim($article->find('div.title',0)->plaintext);
$item['details'] = trim($article->find('div.details',0)->plaintext);
$item['intro'] = trim($article->find('div.intro',0)->plaintext);
$ret[] = $item;
}
$html->clear();
unset($html);
return $ret;
}
$ret = scraping_slashdot();
foreach($ret as $v) {
echo $v['title'].'<br>';
echo '<ul>';
echo '<li>'.$v['details'].'</li>';
echo '<li>'.$v['intro'].'</li>';
echo '</ul>';
}

?>