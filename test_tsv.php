<?php

require "php-export-data.class.php";

$tsv = new ExportDataTSV();
$tsv->filename = "test.xls";

$data = array(
	array(1,2,3),
	array("asdf","jkl","semi"), 
	array("1273623874628374634876","=asdf","10-10"),
);

foreach($data as $row) {
	$tsv->addRow($row);
}

// print $tsv->exportToString();
// $tsv->exportToBrowser();
$tsv->writeToFile();