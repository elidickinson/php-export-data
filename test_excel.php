<?php

require "php-export-data.class.php";

$excel = new ExportDataExcel();
$excel->filename = "test.xls";

$data = array(
	array(1,2,3),
	array("asdf","jkl","semi"), 
	array("1273623874628374634876","=asdf","10-10"),
);

foreach($data as $row) {
	$excel->addRow($row);
}

//print $excel->exportToString();
$excel->exportToBrowser();