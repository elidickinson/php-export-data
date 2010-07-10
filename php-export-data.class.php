<?php

abstract class ExportData {
	// protected $config = array('useTempFile' => FALSE);
	protected $exportTo; // Set in constructor to one of 'browser', 'file', 'string'
	protected $stringData; // stringData so far, used if export string mode
	protected $tempFile; // handle to temp file (for export file mode)
	protected $tempFilename; // temp file name and path (for export file mode)

	public $filename; // file mode: the output file name; browser mode: file name for download; string mode: not used

	public function __construct($exportTo = "browser", $filename = "export") {
		// $this->config = array_merge($this->config, $config);
		
		if(!in_array($exportTo, array('browser','file','string') )) {
			throw new Exception("$exportTo is not a valid ExportData export type");
		}
		$this->exportTo = $exportTo;
		$this->filename = $filename;
	}
	
	public function initialize() {
		
		switch($this->exportTo) {
			case 'browser':
				$this->sendHttpHeaders();
				break;
			case 'string':
				$this->stringData = '';
				break;
			case 'file':
				$this->tempFilename = tempnam(sys_get_temp_dir(), 'exportdata');
				$this->tempFile = fopen($this->tempFilename, "w");
				break;
		}
		
		$this->write($this->generateHeader());
	}
	
	public function addRow($row) {
		$this->write($this->generateRow($row));
	}
	
	public function finalize() {
		
		$this->write($this->generateFooter());
		
		switch($this->exportTo) {
			case 'browser':
				flush();
				exit(); // not sure about this...
				break;
			case 'string':
				// do nothing
				break;
			case 'file':
				fclose($this->tempFile);
				rename($this->tempFilename, $this->filename);
				break;
		}
	}
	
	public function getString() {
		return $this->stringData;
	}
	
	abstract public function sendHttpHeaders();
	
	protected function write($data) {
		switch($this->exportTo) {
			case 'browser':
				echo $data;
				break;
			case 'string':
				$this->stringData .= $data;
				break;
			case 'file':
				fwrite($this->tempFile, $data);
				break;
		}
	}
	
	protected function generateHeader() {
		
	}
	
	protected function generateFooter() {
		
	}
	
	abstract protected function generateRow($row);
	
}

class ExportDataTSV extends ExportData {
	
	function generateRow($row) {
		foreach ($row as $key => $value) {
			// Escape inner quotes and wrap all contents in new quotes.
			$row[$key] = '"'. str_replace('"', '\"', $value) .'"';
		}
		return implode("\t", $row) . "\n";
	}
	
	function sendHttpHeaders() {
		header("Content-type: text/tab-separated-values");
    header("Content-Disposition: attachment; filename=".basename($this->filename));
	}
}

class ExportDataCSV extends ExportData {
	
	function generateRow($row) {
		foreach ($row as $key => $value) {
			// Escape inner quotes and wrap all contents in new quotes.
			$row[$key] = '"'. str_replace('"', '\"', $value) .'"';
		}
		return implode(",", $row) . "\n";
	}
	
	function sendHttpHeaders() {
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".basename($this->filename));
	}
}

class ExportDataExcel extends ExportData {
	// Excel XML code based on Excel_XML (http://github.com/oliverschwarz/php-excel) by Oliver Schwarz
	const XmlHeader = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
	const XmlFooter = "</Workbook>";
	
	public $encoding = 'UTF-8';
	public $title = '';
	
	function generateHeader() {		
		
		$title = $this->title ? $this->title : "Untitled";
		
		// workbook header
		$output = stripslashes(sprintf(self::XmlHeader, $this->encoding)) . "\n";
		
		// worksheet header
		$output .= sprintf("<Worksheet ss:Name=\"%s\">\n    <Table>\n", htmlentities($title));
		
		return $output;
	}
	
	function generateFooter() {
		$output = '';
		
		// worksheet footer
		$output .= "    </Table>\n</Worksheet>\n";
		
		// workbook footer
		$output .= self::XmlFooter;
		
		return $output;
	}
	
	function generateRow($row) {
		$output = '';
		$output .= "        <Row>\n";
		foreach ($row as $k => $v) {
			$output .= $this->generateCell($v);
		}
		$output .= "        </Row>\n";
		return $output;
	}
	
	private function generateCell($item) {
		$output = '';
		
		$type = 'String';
		if (is_numeric($item)) {
			$type = 'Number';
			if ($item{0} == '0' && strlen($item) > 1 && $item{1} != '.') {
				$type = 'String';
			}
		}
		$item = str_replace('&#039;', '&apos;', htmlspecialchars($item, ENT_QUOTES));
		$output .= sprintf("            <Cell><Data ss:Type=\"%s\">%s</Data></Cell>\n", $type, $item);
		
		return $output;
	}
	
	function sendHttpHeaders() {
		header("Content-Type: application/vnd.ms-excel; charset=" . $this->encoding);
		header("Content-Disposition: inline; filename=\"" . basename($this->filename) . "\"");
	}
	
}