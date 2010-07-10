<?php

require "../php-export-data.class.php";

$excel = new ExportDataExcel('browser');
$excel->filename = "test.xml";

$data = array(
	array(1,2,3),
	array("asdf","jkl","semi"), 
	array("1273623874628374634876","=asdf","10-10"),
);

$excel->initialize();
foreach($data as $row) {
	$excel->addRow($row);
}
$excel->finalize();
