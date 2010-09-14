<?php
require_once(PATH_tslib.'class.tslib_content.php');
require('typo3conf/ext/x4equalificationgeneral/pi1/class.tx_x4equalificationgeneral_pi1.php');
require('typo3conf/ext/listfeuser_uni/pi1/class.tx_listfeuseruni_pi1.php');
class pdfQualis extends tx_x4equalificationgeneral_pi1 {
	var $pdf;
	var $author = array();
	var $data = array();

	function pdfQualis() {
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		// get page information
		$this->getContentElementInformation();
	}

	function main($content,$conf) {
		if (isset($_GET[$this->prefixId])) {
			foreach($_GET[$this->prefixId] as $key => $value) {
				$this->piVars[$key] = $value;
			}
		}
		parent::init($content,$conf);
		//$this->fields = t3lib_div::trimExplode(',','lastname,title,type,finished,organizer');
	}

	function pi_list_row($c) {
		foreach($this->fields as $v) {
			$row[$this->getFieldHeader($v)] = html_entity_decode(strip_tags($this->getFieldContent($v)));
		}

		array_push($this->data,$row);
	}

	function getContentElementInformation(){
		$this->cObj->data = array_pop($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tt_content','pid = '.$GLOBALS['TSFE']->id.' AND list_type="x4equalificationgeneral_pi1"'.$this->cObj->enableFields('tt_content')));
	}

}

$qual =& new pdfQualis();
$qual->main('',$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_x4equalificationgeneral_pi1.']);
$qual->LLkey = 'de';
$qual->listView();

include ('class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('typo3conf/ext/x4equalificationgeneral/pdf/Helvetica.afm');

$pattern='Seite {PAGENUM}/{TOTALPAGENUM}';
$pdf->ezStartPageNumbers(500,10,8,right,$pattern);
$pdf->ezStartPageNumbers(35,10,8,'right',"Stand: ".date('d.m.y',time()));
$pdf->addPngFromFile('fileadmin/histsem/_templates/images/pdf_logo.png',392,770,188);
$pdf->ezText('Kunsthistorisches Seminar Basel - Abschlussarbeiten',14);
//$pdf->ezText('© 2002/2003 Kai Seidler, oswald@apachefriends.org, GPL',10);
$pdf->ezText('',12);




/*$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('l.title,s.lastname','tx_x4equalificationgeneral_list as l LEFT JOIN tx_x4equalificationgeneral_student as s ON (FIND_IN_SET(s.uid,l.personsinvolved))','hidden=0');

//$result=mysql_query("SELECT id,titel,interpret,jahr FROM cds ORDER BY interpret;");

$i=0;
while( $row=mysql_fetch_assoc($result) ) {
	foreach($row as $k=>$v) {
		$data[$i][$k] = $v;
	}
	//$data[$i]=array('interpret'=>$row['interpret'],'titel'=>$row['titel'],'jahr'=>$row['jahr']);
	$i++;
}
t3lib_div::debug($data);*/

//t3lib_div::debug($qual->data);
$pdf->ezTable($qual->data,"","",array('xPos'=>'left','xOrientation'=>'right','width'=>560));

$pdf->ezStream();

exit;

?>