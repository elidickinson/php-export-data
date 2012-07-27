php-export-data by Eli Dickinson

http://github.com/elidickinson/php-export-data

Released under the permissive MIT License: http://www.opensource.org/licenses/mit-license.php

A simple library for exporting tabular data to Excel-friendly XML, CSV, or TSV. It supports streaming exported data to a file or directly to the browser as a download so it is suitable for exporting large datasets (you won't run out of memory).

Excel XML code is based on Excel_XML by Oliver Schwarz (http://github.com/oliverschwarz/php-excel)


## How to use it

    <?php

    // When executed in a browser, this script will prompt for download 
    // of 'test.xls' which can then be opened by Excel or OpenOffice.

    require 'php-export-data.class.php';

    // 'browser' tells the library to stream the data directly to the browser.
    // other options are 'file' or 'string'
    // 'test.xls' is the filename that the browser will use when attempting to 
    // save the download
    $exporter = new ExportDataExcel('browser', 'test.xls');

    $exporter->initialize(); // starts streaming data to web browser

    // pass addRow() an array and it converts it to Excel XML format and sends 
    // it to the browser
    $exporter->addRow(array("This", "is", "a", "test")); 
    $exporter->addRow(array(1, 2, 3, "123-456-7890"));

    // doesn't care how many columns you give it
    $exporter->addRow(array("foo")); 

    $exporter->finalize(); // writes the footer, flushes remaining data to browser.

    exit(); // all done

    ?>


See the test/ directory for more examples.


Some other options for creating Excel files from PHP are listed here: http://stackoverflow.com/questions/3930975/alternative-for-php-excel/3931142#3931142