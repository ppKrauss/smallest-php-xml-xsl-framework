smallest-php-xml-xsl1-framework
===============================

Intend to be the "**Smallest PHP XML/XSLT1 website framework**". A fork of https://code.google.com/p/smallest-php-xml-xsl-framework/

We are doing some experiments with
[JATS](https://github.com/ncbi/JATSPreviewStylesheets) and 
[registerPHPFunctions](http://en.wikibooks.org/wiki/PHP_Programming/XSL/registerPHPFunctions).


##STRUCTURE##

* *css*: all css style files (css lib).
* *libs*: all PHP classes and functions.
* *log*: log files generated by error-reports.
* *xml*: XML files for input data, or dynamical XML ports.
* *xsl*: all XSLT template files (XSL lib).


##INSTALLATION##

 1. copy all zip source-code to a /www directory. Example  (you can rename this long uggly name):

   /var/www/smallest-php-xml-xsl-framework


 2. Access it with your web browser. Example:  http://localhost/smallest-php-xml-xsl-framework

 3.  Navigate. If any error, check folder permissions and, check if all XML and XSL PHP-configurations are enabled.


##USE##

Change code and test by your self. Examples:

* change the conf.php file to adapt for another application.
* change title attributes at xml/site (the site map).
* change the xml/pages files (they are the page contents).
* change the css/skin1.css and xsl/SKIN1.xsl files (they express the site layout).

