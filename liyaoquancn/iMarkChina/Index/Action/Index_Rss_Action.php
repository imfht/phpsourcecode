<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
header("Content-Type: application/xml"); 
?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0"  xmlns:content="http://purl.org/rss/1.0/modules/content/"  xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/"  xmlns:atom="http://www.w3.org/2005/Atom"  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"  xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
<channel>
  <title><?php Mark_Site_Name(); ?></title>
  <link><?php Mark_Site_Link(); ?></link>
  <description><?php Mark_Site_Desc(); ?></description>
  <language>zh_CN</language>
  <sy:updatePeriod>hourly</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
  <generator>http://www.imarkchina.cn</generator>
<?php while (Mark_Next_Post()) { ?>
    <item>
      <title><?php Mark_The_Title(); ?></title>
      <link><?php Mark_The_Url(); ?></link>
      <guid><?php Mark_The_Url(); ?></guid>
      <dc:creator><?php Mark_Nick_Name(); ?></dc:creator>
      <pubDate><?php Mark_The_Data(); ?> <?php Mark_The_Time(); ?></pubDate>
<?php Mark_The_Tags("<category><![CDATA[", "\n", "]]></category>"); echo "\n"; ?>
      <content:encoded><![CDATA[<?php Mark_The_Content(); ?>]]></content:encoded>
    </item>
<?php } ?>
</channel>
</rss>