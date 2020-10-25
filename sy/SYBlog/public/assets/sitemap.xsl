<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9">
<xsl:output method="html" encoding="utf-8" indent="yes" version="1.0" />
<xsl:template match="/">
<html>
<head>
<title>Sitemap</title>
<meta name="generator" content="http://blog.sylingd.com" />
<style type="text/css">
*{font-family:"Microsoft Yahei","微软雅黑",sans-serif;font-size:15px}
a{text-decoration:none;color:rgb(3, 155, 229)}
h1{font-size:3.56rem;margin:0;font-weight:400}

#header{padding:10px;}
#submit {margin:8px;line-height:23px;box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);transition:box-shadow .25s;padding:15px;margin:0.5rem 0 1rem 0;border-radius:2px;background-color:#fff}
#submit a{margin-right:10px;}

body{background:rgb(238, 238, 238)}

table,th,td{border:none}
table{width:100%;display:table}
table>tbody>tr:nth-child(odd){background-color:#f2f2f2}
table>tbody>tr>td{border-radius:0px}
thead{border-bottom:1px solid #d0d0d0}
td,th{padding:15px 5px;display:table-cell;text-align:left;vertical-align:middle;border-radius:2px}
</style>
</head>
<body>
	<div id="header">
		<h1>Sitemap</h1>
		<div id="submit">
			<p>你可以通过以下网址将Sitemap提交给搜索引擎（PS:若以索引模式生成，则<strong>只需提交索引文件</strong>）</p>
			<a target="_blank" href="http://www.google.com/webmasters/tools/">Google</a>
			<a target="_blank" href="http://www.bing.com/webmaster">Bing</a>
			<a target="_blank" href="http://sitemap.baidu.com">百度</a>
		</div><!-- end #submit -->
	</div><!-- end #header -->
	<xsl:apply-templates select="sm:urlset" />
</body>
</html>
</xsl:template>

<xsl:template match="sm:urlset">
<div id="content">
<table>
	<thead>
	<tr>
		<th>地址</th>
		<th>最后更新</th>
		<th>更新频率</th>
		<th>权重</th>
	</tr>
	</thead>
	<tbody>
		<xsl:for-each select="sm:url">
		<tr>
			<td><a>
				<xsl:attribute name="href"><xsl:value-of select="sm:loc" /></xsl:attribute>
				<xsl:value-of select="sm:loc" />
			</a></td>
			<td><xsl:value-of select="concat(substring-before(sm:lastmod, 'T'),' ',substring(sm:lastmod,12,5))" /></td>
			<td>
				<xsl:choose>
					<xsl:when test="sm:changefreq = 'never'">从不</xsl:when>
					<xsl:when test="sm:changefreq = 'yearly'">每年</xsl:when>
					<xsl:when test="sm:changefreq = 'monthly'">每月</xsl:when>
					<xsl:when test="sm:changefreq = 'weekly'">每周</xsl:when>
					<xsl:when test="sm:changefreq = 'daily'">每天</xsl:when>
					<xsl:when test="sm:changefreq = 'hourly'">每小时</xsl:when>
					<xsl:otherwise>实时</xsl:otherwise><!-- always -->
				</xsl:choose>
			</td>
			<td><xsl:value-of select="concat(sm:priority*100,'%')" /></td>
		</tr>
		</xsl:for-each>
	</tbody>
</table>
</div>
</xsl:template>

</xsl:stylesheet>
