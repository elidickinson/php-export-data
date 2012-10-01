<?php

require "../php-export-data.class.php";

$excel = new ExportDataExcel('browser');
$excel->filename = "test.xml";

$data = array(
	array(1,2,3),
	array("asdf","jkl","semi"), 
	array("1273623874628374634876","=asdf","10-10"),
	array("2010-01-02 10:00AM","1/1/11","10-10"),
	array("1234","12.34","-123."),
	array("-12345678901234567890","0.0000000000123456789","-"),
);

$excel->initialize();
foreach($data as $row) {
	$excel->addRow($row);
}
$excel->finalize();
