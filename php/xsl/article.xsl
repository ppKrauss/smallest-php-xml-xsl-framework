<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:param name="pagetitle"
		select="concat(//name, ': ', //article/title)" />

	<xsl:template match="//article">
		<div id="content" class="Article">
			<xsl:apply-templates />
		</div>
	</xsl:template>

	<xsl:template match="//article/title">
		<h2>
			<xsl:value-of select="//title" />
		</h2>
	</xsl:template>

	<xsl:template match="//article/intro">
		<div class="Intro">
			<xsl:apply-templates />
		</div>
	</xsl:template>

	<xsl:template match="//article/body">
		<div class="Body">
			<xsl:apply-templates />
		</div>
	</xsl:template>

	<xsl:template match="//article//p">
		<p>
			<xsl:apply-templates />
		</p>
	</xsl:template>

	<xsl:template match="//article//b">
		<strong><xsl:apply-templates /></strong>
	</xsl:template>

	<xsl:template match="//article//a">
		<a href="{@href}"><xsl:apply-templates /></a>
	</xsl:template>

	<xsl:template match="//article//ul">
		<ul>
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="//article//li">
		<li><xsl:apply-templates /></li>
	</xsl:template>

</xsl:stylesheet>
