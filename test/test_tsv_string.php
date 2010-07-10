<?php

// Shows how exporting to string works. 
// This isn't a good way to export very large datasets, since all the exported data must be kept in memory 

require "../php-export-data.class.php";

$tsv = new ExportDataTSV('string');
$tsv->filename = "test.xls";

$data = array(
	array(1,2,3),
	array("asdf","jkl","semi"), 
	array("1273623874628374634876","=asdf","10-10"),
);
$tsv->initialize();
foreach($data as $row) {
	$tsv->addRow($row);
}
$tsv->finalize();

$exportedData =  $tsv->getString();

print $exportedData;