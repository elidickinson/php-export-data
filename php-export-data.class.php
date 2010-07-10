<?php


abstract class ExportData {
	protected $config = array('useTempFile' => FALSE);
	protected $rows = array();
	protected $tempFile;
	protected $tempFilename;
	public $filename;

	public function __construct($config = array()) {
		$this->config = array_merge($this->config, $config);
		
		if($this->config['useTempFile']) {
			$this->tempFilename = tempnam(sys_get_temp_dir(), 'exportdata');
			$this->tempFile = fopen($this->tempFilename, "w");
			fwrite($this->tempFile, $this->generateHeader());
		}
		
	}
	
	public function getConfig() {
		return $this->config;
	}
	
	public function addRow($row) {
		
		if($this->config['useTempFile']) {
			// write data to file
			fwrite($this->tempFile, $this->generateRow($row));
		}
		else {
			// store data in rows array
			$this->rows[] = $row;
		}
	}
	
	public function exportToString() {
		$output = '';
		$output .= $this->generateHeader();
		foreach($this->rows as $row) {
			$output .= $this->generateRow($row);
		}
		$output .= $this->generateFooter();
		return $output;
	}
	
	
	abstract public function sendHttpHeaders();
	
	public function exportToBrowser() {
		$this->sendHttpHeaders();
		echo $this->exportToString();
		exit();
	}
	
	public function exportToFile() {
		if($this->config['useTempFile']) {
			fwrite($this->tempFile, $this->generateFooter());
			fclose($this->tempFile);
			rename($this->tempFilename, $this->filename);
		}
		else {
			$f = fopen($this->filename,"w");
			fwrite($f, $this->generateHeader());
			foreach($this->rows as $row) {
				fwrite($f,$this->generateRow($row));
			}
			fwrite($f, $this->generateFooter());
			fclose($f);
			// file_put_contents($filename, $this->exportToString());
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
	/**
	 * Excel code ripped out of Excel_XML (php-excel) by 
	 *	Oliver Schwarz <oliver.schwarz@gmail.com>
	 */
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
		header("Content-Disposition: inline; filename=\"" . $this->filename . "\"");
	}
	
}