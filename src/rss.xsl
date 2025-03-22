<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
  <!-- Template to match the root element (RSS feed) -->
  <xsl:template match="/rss">
    <html>
      <head>
        <title>RSS Feed</title>
      </head>
      <body>
        <h2>RSS Feed</h2>
        <ul>
          <!-- Loop through each RSS item -->
          <xsl:for-each select="channel/item">
            <li>
              <a href="{link}">
                <xsl:value-of select="title" />
              </a>
            </li>
          </xsl:for-each>
        </ul>
      </body>
    </html>
  </xsl:template>
  
</xsl:stylesheet>
