<?php

require "php-export-data.class.php";

function genRandomString($length = 100) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz _";
    $string = "";
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $string;
}

$excel = new ExportDataExcel('file');
$excel->filename = "test_big_excel.xls";

$excel->initialize();
for($i = 1; $i<100000; $i++) {
	$row = array($i, genRandomString(), genRandomString(), genRandomString(), genRandomString(), genRandomString());
	$excel->addRow($row);
}
$excel->finalize();


print number_format(memory_get_peak_usage());