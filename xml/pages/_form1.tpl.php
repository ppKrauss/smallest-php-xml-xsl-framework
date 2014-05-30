<!-- this XML data file is generated dynamically -->
<?php
$cname = "Tatarynowicz et al"; // exemple of PHP internal placeholder
?>
<article type="sputnik" vesion="1.0">
	<author>&#169;2008, 2011 - <?=$cname?> </author>
	<name>Form results</name>

	<title>Form results</title>
	<intro> <!-- example of PHP loop using posted variables -->
		<?php foreach ($_POST as $key=>$val): ?>
			<p> <?="$key = $val" ?> </p>
		<?php endforeach; ?>
	</intro>
	<body>
		<p><b>Link</b> <link this="test">to test-1 page</link></p>
	</body>
</article>

