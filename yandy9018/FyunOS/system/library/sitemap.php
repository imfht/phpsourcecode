<?php


final class sitemaps {
	
	private $filename = 'sitemap.xml'; 
	private $isXml = true; 
	private $isGZ = true; 
	

	
	private function Path() {
		return dirname ( DIR_APPLICATION) . '/';
	}
	
	
	public function createSitemap(array $urls, $changeFreq, $priority) {
		$xmlPath = $this->path () . $this->filename;
		$sitemapXML = $this->buildSitemaps ( $urls, $changeFreq, $priority );
		if ($this->isXml)
			$this->buildXMLFile ( $xmlPath, $sitemapXML );
		if ($this->isGZ)
			$this->buildGZFile ( $xmlPath, $sitemapXML );
		return;
	}
	
	
	private function buildXMLFile($xmlFile, $xml) {
		if (! file_exists ( $xmlFile )) {
			$fp = fopen ( $xmlFile, 'w' );
			if (! $fp) {
				echo 'create xml file fail';
				exit ();
			}
		}
		
		if (function_exists ( 'file_put_contents' )) {
			file_put_contents ( $xmlFile, $xml );
		}
		return;
	}
	
	
	private function buildGZFile($xmlFile, $xml) {
		if (function_exists ( 'gzopen' ) && function_exists ( 'gzwrite' ) && function_exists ( 'gzclose' )) {
			$gz = gzopen ( $xmlFile . '.gz', 'w' );
			gzwrite ( $gz, $xml );
			gzclose ( $gz );
		}
		return;
	}
	

	private function buildSitemaps($urls, $changeFreq, $priority) {
		$xml = '';
		$xml .= '<?xml version="1.0" encoding="UTF-8" ' . '?' . '>' . "\n";
		$xml .= '<?xml-stylesheet type="text/xsl" href="'.HTTP_CATALOG.'sitemap.xsl"?>' . "\n";
		$xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$xml .= $this->buildXMLs ( $urls,$priority , $changeFreq);
		$xml .= '</urlset>';
		return $xml;
	}
	
	
	private function buildXMLs($urls,$changeFreq, $priority) {
		if (! $urls)
			return '';
		$xml = '';
		foreach ( $urls as $url ) {
			$xml .= $this->buildXML ( $url,$changeFreq, $priority );
		}
		return $xml;
	}
	
	
	private function buildXML($url,$changeFreq, $priority) {
		$xml = '';
		$xml .= "\t<url>\n";
		$xml .= "\t\t<loc>" . $this->EscapeXML ( $url['url'] ) . "</loc>\n";
		$xml .= "\t\t<lastmod>" . date ( 'Y-m-d\TH:i:s+00:00' ) . "</lastmod>\n";
		$xml .= "\t\t<changefreq>" . $changeFreq . "</changefreq>\n";
		$xml .= "\t\t<priority>" . $priority . "</priority>\n";
		//$xml .= "\t\t<name>" . $url['name']  . "</name>\n";
		$xml .= "\t</url>\n";
		return $xml;
	}
	
	
	private function EscapeXML($string) {
		return str_replace ( array ('&', '"', "'", '<', '>' ), array ('&amp;', '&quot;', '&apos;', '&lt;', '&gt;' ), $string );
	}

}
?>