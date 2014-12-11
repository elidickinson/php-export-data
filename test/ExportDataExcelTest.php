<?php
require_once dirname(__FILE__) . '/../php-export-data.class.php';

class ExportDataExcelWrap extends ExportDataExcel {
	public function wrapGenerateCell($item) {
		return $this->generateCell($item);
	}
}

/**
 *
 */
class ExportDataExcelTest extends PHPUnit_Framework_TestCase {
	protected $output = 'browser';
	/**
	 * @var ExportDataExcel
	 */
	protected $object;

	/**
	 * Sets up the fixture
	 */
	protected function setUp() {
		$this->object = new ExportDataExcelWrap($this->output);
	}

	/**
	 * @covers ExportDataExcel::generateCell
	 * @dataProvider dateProvider
	 */
	public function testDates($exp, $date) {
		$res = $this->object->wrapGenerateCell($date);
		$this->assertNotEmpty($res);
		$res = trim($res);
		$this->assertEquals($exp, $res);
	}

	public function dateProvider() {
		return array(
			// iso dates: https://en.wikipedia.org/wiki/ISO_8601
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T00:00:00</Data></Cell>',"2010-01-02"),
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T09:52:00</Data></Cell>', "2010-01-02 9:52"),
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T10:00:00</Data></Cell>', "2010-01-02T10:00"),
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T10:00:00</Data></Cell>', "2010-01-02T10:00:00"),
			# microseconds not supported by strtotime
#			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T06:12:00</Data></Cell>', "2010-01-02T06:12:00,000"),
#			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-01-02T00:00:00</Data></Cell>', "2010-01-02T10:00:00,000"),
			// US dates
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-02-08T00:00:00</Data></Cell>', "02/08/2010 00:00"),
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-02-08T00:00:00</Data></Cell>', "02/08/2010"),
			array('<Cell ss:StyleID="sDT"><Data ss:Type="DateTime">2010-02-09T00:00:00</Data></Cell>', "2/9/2010"),
		);
	}
}
