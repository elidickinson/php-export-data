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

$excel = new ExportDataExcel();
$excel->filename = "test_big_excel.xls";


for($i = 1; $i<10000; $i++) {
	$row = array($i, genRandomString(), genRandomString(), genRandomString(), genRandomString(), genRandomString());
	$excel->addRow($row);
}


// print $excel->exportToString();
// $excel->exportToBrowser();
$excel->exportToFile();

print number_format(memory_get_peak_usage());