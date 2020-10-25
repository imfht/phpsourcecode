<?php
include './library/inc.php';

header('Content-type: application/xml; charset="UTF-8"', true);
//"always", "hourly", "daily", "weekly", "monthly", "yearly" and "never"
$changefreq = "weekly";
$str = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
$str .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
$res = $db->getAll("SELECT * FROM detail ORDER BY d_order ASC,id DESC LIMIT 0,5000");
foreach ($res as $val) {
  $str .= '  <url>' . PHP_EOL . '    <loc>' . $cms['s_domain'] . '/' . htmlspecialchars(d_url($val['id'])) . '</loc>' . PHP_EOL . '    <lastmod>' . local_date("Y-m-d H:i:s",$val['d_date']) . '</lastmod>' . PHP_EOL . '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL . '    <priority>0.8</priority>' . PHP_EOL . '  </url>' . PHP_EOL;
}
$str .= '</urlset>';

if ($act == 'xml') {
  $fp = fopen("sitemap.xml", "w+");
  fwrite($fp, $str);
  fclose($fp);
  header('Location: ./sitemap.xml');
} else {
  echo $str;
}
