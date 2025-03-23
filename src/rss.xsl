<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/rss">
    <h1>
      <xsl:value-of select="channel/title" />
    </h1>
    <xsl:for-each select="channel/item">
      <div class="w3-card w3-panel">
        <h3>
          <xsl:value-of select="title" />
        </h3>
        <p class="w3-text-gray">
          <xsl:value-of select="pubDate" />
        </p>
        <p>
          <xsl:value-of select="description" />
        </p>
        <p>Links:</p>
        <ol>
          <xsl:for-each select="link">
            <li>
              <a href="{.}">
                <xsl:value-of select="." />
              </a>
            </li>
          </xsl:for-each>
        </ol>
      </div>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
