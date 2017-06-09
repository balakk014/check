<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * PHP Excel library. Helper class to make spreadsheet creation easier.
 *
 * @package    Spreadsheet
 * @author     Flynsarmy
 * @website    http://www.flynsarmy.com/
 * @license    TEH FREEZ
 */
class Spreadsheet
{
	const VENDOR_PACKAGE = "vendor/phpexcel/PHPExcel/";
	private $_spreadsheet;

	/*
	 * Purpose: Creates the spreadsheet with given or default settings
	 * Input: array $headers with optional parameters: title, subject, description, author
	 * Returns: void
	 */
	public function __construct($headers=array())
	{
		$headers = array_merge(array(
			'title'			=> 'RipeRides',
			'subject'		=> 'RipeRides',
			'description'	=> 'New Spreadsheet',
			'author'		=> 'riperides.ca',

		), $headers);

		$this->_spreadsheet = new PHPExcel();
		// Set properties
		$this->_spreadsheet->getProperties()
			->setCreator( $headers['author'] )
			->setTitle( $headers['title'] )
			->setSubject( $headers['subject'] )
			->setDescription( $headers['description'] );
			//->setActiveSheetIndex(0);
		//$this->_spreadsheet->getActiveSheet()->setTitle('Minimalistic demo');
	}

	/*
	 * Purpose Writes cells to the spreadsheet
	 * Input: array of array( [row] => array([col]=>[value]) ) ie $arr[row][col] => value
	 * Returns: void
	 */
	public function setData(array $data, $multi_sheet=false)
	{
		if ( empty($this->_spreadsheet) )
			$this->create();

		//Single sheet ones can just dump everything to the current sheet
		if ( !$multi_sheet )
		{
			$Sheet = $this->_spreadsheet->getActiveSheet();
			$this->setSheetData( $data, $Sheet );
		}
		//Hvae to do a little more work with multi-sheet
		else
		{
			$letters = range('A','Z');
			$count =0;
			$stylearray=array(
                 'font'    => array(
                      'name'      => 'Arial',
                      'bold'      => true,
                      'italic'    => false,
                      'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE,
                      'strike'    => false,
                      'color'     => array(
                          'rgb' => '808080'
                      )
                  ),
                  'borders' => array(
                      'bottom'     => array(
                          'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                          'color' => array(
                              'rgb' => '808080'
                          )
                      ),
                      'top'     => array(
                          'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                          'color' => array(
                              'rgb' => '808080'
                          )
                      )
                  ),
                  'quotePrefix'    => true
              );

				
			foreach ( $data as $sheetName=>$sheetData )
			{
				$cell_name = $letters[$count]."1";
				$Sheet = $this->_spreadsheet->createSheet();
				$Sheet->setTitle( $sheetName );
				$Sheet->getDefaultStyle()->getFont()->setSize(12);
				$this->_spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($stylearray);
				//$Sheet->getDefaultStyle('A3')->getFont()->setBold(true);
				$this->setSheetData( $sheetData, $Sheet );
			}



//$Sheet->getStyle('A1:G1')->applyFromArray(Kohana::$config->load('phpexcel'));


			//Now remove the auto-created blank sheet at start of XLS
			$this->_spreadsheet->removeSheetByIndex( 0 );
		}

		/*
		array(
			1 => array('A1', 'B1', 'C1', 'D1', 'E1')
			2 => array('A2', 'B2', 'C2', 'D2', 'E2')
			3 => array('A3', 'B3', 'C3', 'D3', 'E3')
		);
		*/
	}

	public function setSheetData( array $data, PHPExcel_Worksheet $Sheet )
	{
		foreach ( $data as $row => $columns )
			foreach ( $columns as $column => $value )
				$Sheet->setCellValueByColumnAndRow($column, $row, $value);
	}

	/*
	 * Purpose: Writes spreadsheet to file
	 * Input: array $settings with optional parameters: format, path, name (no extension)
	 * Returns: Path to spreadsheet
	 */
	public function save( $settings=array(),$filename='' )
	{
		if ( empty($this->_spreadsheet) )
			$this->create();

		//Used for saving sheets
		//require self::VENDOR_PACKAGE.'IOFactory.php';
		$vendor="vendor/phpexcel/Classes/PHPExcel/";
		require $vendor.'IOFactory.php';

		$settings = array_merge(array(
			'format'		=> 'Excel5',
			'path'			=> 'public/downloads/',
			'name'			=> 'NewSpreadsheet'

		), $settings);

		//Generate full path
		$settings['fullpath'] = $settings['path'] . $settings['name'] . '_'.time().'.xls';	    
		$type = 'xls';
	    $filename=$filename . '_'.time();
	
	    header("Content-Disposition: attachment; filename=" . $filename . "." . $type);
	    header("Pragma: no-cache");
	    header("Expires: 0");		

		$Writer = PHPExcel_IOFactory::createWriter($this->_spreadsheet, $settings['format']);
		// If you want to output e.g. a PDF file, simply do:
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		//$Writer->save( $settings['fullpath'] );
		ob_end_clean();
		$Writer->save('php://output');exit;
		return $settings['fullpath'];
	}
	
	/*
	 * Purpose: Writes spreadsheet to file
	 * Input: array $settings with optional parameters: format, path, name (no extension)
	 * Returns: Path to spreadsheet
	 */
	public function save_pdf( $settings=array(),$filename='' )
	{
		if ( empty($this->_spreadsheet) )
			$this->create();

		//Used for saving sheets
		//require self::VENDOR_PACKAGE.'IOFactory.php';
		$vendor="vendor/phpexcel/Classes/PHPExcel/";
		require $vendor.'IOFactory.php';

		$settings = array_merge(array(
			'format'		=> 'Excel5',
			'path'			=> 'public/downloads/',
			'name'			=> 'NewSpreadsheet'

		), $settings);

		//Generate full path
		$settings['fullpath'] = $settings['path'] . $settings['name'] . '_'.time().'.xls';	    
		$type = 'xls';
	    $filename=$filename . '_'.time();
	$rendererName=PHPExcel_Settings::PDF_RENDERER_TCPDF;
	
	$rendererLibraryPath=DOCROOT.'modules/phpexcel/classes/vendor/tcpdf/';
if (!PHPExcel_Settings::setPdfRenderer(
		$rendererName,
		$rendererLibraryPath
	)) {
	die(
		'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
		'<br />' .
		'at the top of this script as appropriate for your directory structure'
	);
}


// Redirect output to a client’s web browser (PDF)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="01simple.pdf"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($this->_spreadsheet, 'PDF');

$objWriter->save('php://output');
exit;
	}
}
