<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output 
		method="xml" media-type="application/xhtml+xml"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
		exclude-result-prefixes="xs fn" 
		omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes"
	/>

	<xsl:template match="/"><xsl:apply-templates select="_ROOT_" /></xsl:template>

	<xsl:template match="/_ROOT_">
		<html xml:lang="en" lang="en">
			<head>

				<link rel="stylesheet" type="text/css" href="css/reset.css" />
				<link rel="stylesheet" type="text/css" href="css/skin1.css" />
				<xsl:choose>
					<xsl:when test="article/@type='sputnik'">
						<link rel="stylesheet" type="text/css" href="css/article.css" />
						<title><xsl:value-of select="article//name"/></title>
					</xsl:when>
					<xsl:otherwise>
						<link rel="stylesheet" type="text/css" href="css/ViewNLM.css" />
						<xsl:call-template name="make-title"/>
					</xsl:otherwise>
				</xsl:choose>
				 <xsl:if test="normalize-space(article/addHead)">
					<xsl:copy-of select="article/addHead"/><!-- chech if scripts and styles can be CDATA -->
				 </xsl:if>

			</head>
			<body>
				<div id="all">
					<div id="header"><h1><xsl:value-of select="//name | //article-meta/article-id[@pub-id-type='other']"/></h1></div>
					
					<div id="page_menu" class="clear"><xsl:apply-templates select="site/extra" /></div>

					<div id="page" class="clear">

						<xsl:choose>
						  <xsl:when test="site/extra/nav/link[nav]/@this=/_ROOT_/pageState/@this">
						  <div id="sidebar2">
							<div class="Nav clear">
								<h3>Submenu </h3>
								<ul>
									<xsl:apply-templates select="site/extra/nav/link[@this = /_ROOT_/pageState/@this]/nav"/>
								</ul>
							</div>

							  <xsl:if test="site/extra/nav/link[@this=/_ROOT_/pageState/@last]/nav/link/@this = /_ROOT_/pageState/@this">								<div class="Nav clear">
									<h3>Back to<br/>
										<xsl:for-each select="site/extra/nav/link[@this = /_ROOT_/pageState/@last]">
											<xsl:call-template name="aHref"/>
										</xsl:for-each>
									</h3>
								</div>
							  </xsl:if>
						  </div>
						  </xsl:when>
						  <xsl:when test="site/extra/nav/link[@this=/_ROOT_/pageState/@last]/nav/link/@this = /_ROOT_/pageState/@this">
						  <div id="sidebar2">
							<div class="Nav clear">
								<h3>Back to<br/>
									<xsl:for-each select="site/extra/nav/link[@this=/_ROOT_/pageState/@last]">
										<xsl:call-template name="aHref"/>
									</xsl:for-each>
								</h3>
							</div>
						  </div>
						  </xsl:when>

						  <xsl:when test="site/extra/nav/back/@this = /_ROOT_/pageState/@this">
						  <xsl:variable name="to"><xsl:value-of select="site/extra/nav/back[@this = /_ROOT_/pageState/@this]/@to"/></xsl:variable>
						  <div id="sidebar2">
							<div class="Nav clear">
								<h3>Back to<br/>
									<xsl:for-each select="site/extra/nav//link[@this=$to and position()=1]">
										<xsl:call-template name="aHref"/>
									</xsl:for-each>
								</h3>
							</div>
						  </div>
						  </xsl:when>

						</xsl:choose>



						<div id="content" class="Article"> 
						<xsl:for-each select="article[not(@type) or @type!='sputnik']">
							<xsl:call-template name="make-front"/>
							<p>&#160;</p>
						</xsl:for-each>
						<xsl:apply-templates select="article/*[local-name()!='addHead']"/>
						</div>

					</div><!-- page -->

					<div id="footer">
						<div id="driven"><a href="http://code.google.com/p/smallest-php-xml-xsl-framework/">smallest-php-xml-frmwrk</a></div>
						<div id="copy"><xsl:value-of select="//author | //copyright-statement"/></div>
					</div>
				</div><!-- all -->
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
		<ul> 
		<xsl:apply-templates select="nav"/>
		<xsl:apply-templates select="links"/>
		<xsl:apply-templates select="credits"/>
		</ul>
		</div>
	</xsl:template>

	<xsl:template match="nav|links|credits">
			<xsl:for-each select="link">
				<!--xsl:sort select="@title"/ --> 
				<li class="{local-name(..)}"><xsl:call-template name="aHref"/></li>
			</xsl:for-each>
	</xsl:template> 


	<xsl:template match="//author"></xsl:template>
	<xsl:template match="//name"></xsl:template>

	<xsl:template name="aHref">
		<!-- Need: @title and (@url or @this) -->
		<xsl:variable name="hint"><xsl:choose>
			<xsl:when test="normalize-space(../@label)"><xsl:value-of select="../@label"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="local-name(..)"/></xsl:otherwise>
		</xsl:choose></xsl:variable>

		<xsl:variable name="title"><xsl:choose>
			<xsl:when test="normalize-space(@title)"><xsl:value-of select="@title"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
		</xsl:choose></xsl:variable>

		<xsl:choose>
			<xsl:when test="normalize-space(@url)"><a target="_blank" href="{@url}" title="{$hint}"><xsl:value-of select="$title"/></a></xsl:when>
			<xsl:when test="normalize-space(@this) and @this=/_ROOT_/pageState/@this">
				<span id="aqui"><xsl:value-of select="$title"/></span>
			</xsl:when>
			<xsl:when test="normalize-space(@this)"><a href="?{@this}"><xsl:value-of select="$title"/></a></xsl:when>
			<xsl:otherwise><xsl:value-of select="$title"/></xsl:otherwise>
		</xsl:choose>		
	</xsl:template>



</xsl:stylesheet>
