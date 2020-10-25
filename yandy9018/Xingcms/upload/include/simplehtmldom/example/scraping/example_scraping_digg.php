<?php

include_once('../../simple_html_dom.php');
function scraping_digg() {
$html = file_get_html('http://digg.com/');
foreach($html->find('div.news-summary') as $article) {
$item['title'] = trim($article->find('h3',0)->plaintext);
$item['details'] = trim($article->find('p',0)->plaintext);
$item['diggs'] = trim($article->find('li a strong',0)->plaintext);
$ret[] = $item;
}
$html->clear();
unset($html);
return $ret;
}
ini_set('user_agent','My-Application/2.5');
$ret = scraping_digg();
foreach($ret as $v) {
echo $v['title'].'<br>';
echo '<ul>';
echo '<li>'.$v['details'].'</li>';
echo '<li>Diggs: '.$v['diggs'].'</li>';
echo '</ul>';
}

?>