<?php
/*
 * Created on 13.03.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require('typo3conf/ext/publics/pi1/class.pdfTextTable.php');
require('typo3conf/ext/tx_x4equalificationgeneral_pi1/pi1/class.tx_x4equalificationgeneral_pi1.php');
require('typo3conf/ext/listfeuser_uni/pi1/class.tx_listfeuseruni_pi1.php');
$GLOBALS['TYPO3_DB']->debugOutput = true;
class pdfQualis extends tx_x4equalificationgeneral_pi1 {
	var $pdf;
	var $author = array();

	function pdfPublics($pdf) {
		$this->pdf = $pdf;
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
	}

	function main($content,$conf) {
		foreach($_GET['tx_publics_pi1'] as $key => $value) {
			$this->piVars[$key] = $value;
		}
		parent::init($content,$conf);
	}

	function pi_list_row($c) {
		foreach($c as $k=>$v) {
			$row[$k] = strip_tags($this->getFieldContent($k));
		}
		$this->pdf->addRow($row);
	}

}





 $cols = array ('author'=>100,'title'=>250,'type'=>50,'finished'=>50,'organizer'=>100);
 $pdf =& new pdfTextTable('P','pt');
 $pdf->contentWidth = 600;
 $pdf->AliasNbPages();
 $qual =& new pdfQualis($pdf);
 $pdf->setTitle("Historisches Seminar - Abschlussarbeiten");
 $pdf->setCols($cols);
 $pdf->SetFont('Arial','',12);

 $publ->main('',$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_publics_pi1.']);
 $publ->listBySubCategory($_GET['x4equalificationgeneral']['showUid']);

 $pdf->EndTable();
 $pdf->Output('Historisches Seminar - Abschlussarbeiten.pdf','D');

?>