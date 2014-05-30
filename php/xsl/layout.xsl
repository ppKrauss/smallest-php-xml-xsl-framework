<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet [<!ENTITY copy "&#169;">]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output 
		method="xml" 
		media-type="application/xhtml+xml"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
		exclude-result-prefixes="xs fn" 
		omit-xml-declaration="yes"
		encoding="UTF-8"
		indent="yes"
	/>

	<xsl:template match="/">
		<html xml:lang="en" lang="en">
			<head>
				<title><xsl:value-of select="$pagetitle"/></title>
				<link rel="stylesheet" type="text/css" href="css/reset.css" />
				<link rel="stylesheet" type="text/css" href="css/default.css" />
				<link rel="stylesheet" type="text/css" href="css/article.css" />
			</head>
			<body>
				<div id="all">
					<div id="header"><h1><xsl:value-of select="//name"/></h1></div>
					<div id="page" class="clear"><xsl:apply-templates/></div>
					<div id="footer">
						<div id="driven"><a href="http://sputnik.pl">Sputnik.pl</a></div>
						<div id="copy">&copy;2008 <xsl:value-of select="//author"/></div>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="//content">
		<div id="content">
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<xsl:template match="//extra">
		<div id="sidebar">
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<xsl:template match="//extra/nav">
		<div class="Nav clear">
			<h3>Navigation</h3>
			<ul>
			<xsl:for-each select="link">
			<xsl:sort select="@title"/>
				<li><a href="{@url}"><xsl:value-of select="@title"/></a></li>
			</xsl:for-each>
			</ul>
		</div>
	</xsl:template> 


	<xsl:template match="//extra/links">
		<div class="Links clear">
			<h3>Links</h3>
			<ul>
			<xsl:for-each select="link">
			<xsl:sort select="@title"/>
				<li><a href="{@url}"><xsl:value-of select="@title"/></a></li>
			</xsl:for-each>
			</ul>
		</div>
	</xsl:template> 

	<xsl:template match="//extra/credits">
		<div class="Credits clear">
			<h3>Credits</h3>
			<ul>
			<xsl:for-each select="link">
			<xsl:sort select="@title"/>
				<li><a href="{@url}"><xsl:value-of select="@title"/></a></li>
			</xsl:for-each>
			</ul>
		</div>
	</xsl:template> 

	<xsl:template match="//author"></xsl:template>
	<xsl:template match="//name"></xsl:template>

</xsl:stylesheet>
