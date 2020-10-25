<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
        <html>
           <head>
           <title>站点地图</title>
           </head>
           <body>
           <xsl:value-of select="/" />
           </body>
        </html>
</xsl:template>
</xsl:stylesheet>