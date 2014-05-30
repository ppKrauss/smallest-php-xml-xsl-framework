<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!--
		<xsl:call-template name="flash">
		<xsl:with-param name="src" select="'flash/test.swf'"/>
		<xsl:with-param name="width" select="'200'"/>
		<xsl:with-param name="height" select="'50'"/>
		</xsl:call-template>
	-->

	<xsl:template name="flash">
		<xsl:param name="src" />
		<xsl:param name="width" />
		<xsl:param name="height" />
		<xsl:param name="flashvars" select="''" />
		<xsl:param name="quality" select="'best'" />
		<xsl:param name="wmode" select="'transparent'" />

		<object width="{$width}" height="{$height}">
			<param name="movie" value="{$src}" />
			<param name="wmode" value="{$wmode}" />
			<param name="quality" value="{$quality}" />
			<param name="flashvars" value="{$flashvars}" />
			<embed src="{$src}" width="{$width}" height="{$height}"
				flashvars="{$flashvars}" wmode="{$wmode}" quality="{$quality}" />
		</object>

	</xsl:template>

</xsl:stylesheet>
