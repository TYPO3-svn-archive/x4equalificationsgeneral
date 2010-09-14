<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Markus Stauffiger (markus@4eyes.ch)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Publications entry' for the 'x4equalificationgeneral' extension.
 *
 * @author	Markus Stauffiger <markus@4eyes.ch>
 */


require_once(t3lib_extMgm::extPath('x4epibase').'class.x4epibase.php');

class tx_x4equalificationgeneral_pi2 extends x4epibase {
	var $prefixId = 'tx_x4equalificationgeneral_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_x4equalificationgeneral_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4equalificationgeneral';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $record = array();	// Array of the publication to be edited
	var $authorUids = array(); // List of authors uids
	var $contentHeading = '';
	var $doRedirect = true;
	var $years = array(); // Array of years
	var $validFileTypes = array('gif','jpg','jpeg','pdf','png');
	var $persExtKey = 'x4epersdb';

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		//$GLOBALS['TYPO3_DB']->debugOutput = true;
		//$GLOBALS["TSFE"]->set_no_cache();
		$this->conf=$conf;
		$this->template = $this->cObj->fileResource($conf['templateFile']);
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$this->getYears();
		if ($this->conf['persExtKey']) {
    		$this->persExtKey = $this->conf['persExtKey'];
    	}

			// load author to display in heading
		switch ($this->piVars['action']) {
			case 'searchStudent':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
					<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
					<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
					';
				$content = $this->displaySearchStudent();
			break;
			case 'newStudent':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
					// add fvalidate javascripts to use for validation
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
						<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.config.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.core.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.lang-enUS.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.validators.js"></script>';
				$content = $this->displayNewStudent();
			break;
			case 'editStudent':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
					// add fvalidate javascripts to use for validation
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
						<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.config.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.core.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.lang-enUS.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.validators.js"></script>';
				$content = $this->displayEditStudent();
			break;
			case 'saveStudent':
				$this->updateStudent();
				$this->piVars['studentSearchWord'] = $this->piVars['stud_lastname'];
				$this->piVars['studentUids'] = '';
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
					<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
					<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
					';
			break;
			case 'createStudent':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
				$this->createStudent();
				$this->piVars['studentSearchWord'] = $this->piVars['stud_lastname'];
				$this->piVars['studentUids'] = '';
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
					<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
					<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
					';
				$content = $this->displaySearchStudent();
			break;
			case 'searchOrganizer':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
					<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
					<link href="typo3conf/ext/x4equalificationgeneral/pi1/templates/iframe.css" rel="stylesheet" type="text/css" />
					';
				$content = $this->displaySearchOrganizer();
			break;
			case 'update':
				//$this->contentHeading .= $this->pi_getLL('headingEdit');
				if (isset($this->piVars['uid'])) {
					$this->updateRecord();
				}
				$this->redirect($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'returnPageUid'));
				exit();
			break;
			case 'saveForm':
				$this->createRecord();
				$this->redirect($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'returnPageUid'));
			break;
			case 'edit':
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
						<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.config.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.core.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.lang-enUS.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.validators.js"></script>';

				$this->contentHeading .= $this->pi_getLL('headingEdit');
				$content = $this->editRecord();
			break;
			default:
			   	$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '
					<link href="'.$this->conf['stylesheet'].'" rel="stylesheet" type="text/css" />';
					// add fvalidate javascripts to use for validation
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.config.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.core.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.lang-enUS.js"></script>
						<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/fValidate/fValidate.validators.js"></script>';
				$content = $this->displayInputForm();
			break;
		}
		return $this->pi_wrapInBaseClass($content);
	}

	function displayNewStudent() {
		$this->contentHeading = '';
		// get template
		$this->template = $this->cObj->fileResource($this->conf['newStudentTemplateFile']);
		$content = $this->cObj->getSubpart($this->template,'###newForm###');
		// replace some markers
		$this->piVars['action'] = 'createStudent';
		$mArr['###formAction###'] = $this->pi_linkTP_keepPIvars_url().'&type=7645';
		$mArr['###submit###'] = $this->pi_getLL('searchStudent.submit');
		$mArr['###close###'] = $this->pi_getLL('searchStudent.close');
		$mArr['###studentUids###'] = $this->piVars['###studentUids###'];
		$mArr['###searchWord###'] = $this->piVars['studentSearchWord'];
		$content = $this->cObj->substituteMarkerArray($content,$mArr);
		return $content;
	}

	function displayEditStudent() {
		$this->contentHeading = '';
		// get template
		$this->template = $this->cObj->fileResource($this->conf['editStudentTemplateFile']);
		$content = $this->cObj->getSubpart($this->template,'###editForm###');
		// replace some markers
		$this->piVars['action'] = 'saveStudent';
		$stud = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,lastname,firstname,address,phone,email','tx_x4equalificationgeneral_student','uid = '.intval($this->piVars['editUid']).$this->cObj->enableFields('tx_x4equalificationgeneral_student'));
		$stud = $stud[0];
		foreach($stud as $key => $value) {
			$mArr['###'.$key.'###'] = $value;
		}
		$mArr['###formAction###'] = $this->pi_linkTP_keepPIvars_url().'&type=7645';
		$mArr['###submit###'] = $this->pi_getLL('searchStudent.submit');
		$mArr['###close###'] = $this->pi_getLL('searchStudent.close');
		$mArr['###studentUids###'] = $this->piVars['###studentUids###'];
		$mArr['###searchWord###'] = $this->piVars['studentSearchWord'];
		$mArr['###editUrl###'] = $this->getStudentEditUrl($stud);
		$content = $this->cObj->substituteMarkerArray($content,$mArr);
		return $content;
	}

	function createStudent() {

		// handle data
		$ins['pid'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pidStudentList');
		$ins['hidden'] = 0;
		$ins['tstamp'] = time();
		$ins['crdate'] = time();
		$ins['lastname'] = $this->piVars['stud_lastname'];
		$ins['firstname'] = $this->piVars['stud_firstname'];
		$ins['address'] = $this->piVars['stud_address'];
		$ins['phone'] = $this->piVars['stud_phone'];
		$ins['email'] = $this->piVars['stud_email'];
			// create record
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_x4equalificationgeneral_student',$ins);
	}

	function updateStudent() {
		// handle data
		$ins['tstamp'] = time();
		$ins['lastname'] = $this->piVars['stud_lastname'];
		$ins['firstname'] = $this->piVars['stud_firstname'];
		$ins['address'] = $this->piVars['stud_address'];
		$ins['phone'] = $this->piVars['stud_phone'];
		$ins['email'] = $this->piVars['stud_email'];
			// create record
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_x4equalificationgeneral_student','uid ='.intval($this->piVars['editUid']),$ins);
	}

	/**
	 * Saves the record
	 *
	 * @return 	string	HTML-String from a template (or empty)
	 */
	function updateRecord() {
		if (intval($this->piVars['uid']) > 0) {
			global $TCA;
			t3lib_div::loadTCA('tx_x4equalificationgeneral_list');
			$upd = array();
			// loop over all recieved values
			foreach($this->piVars as $key => $value) {
				// if value is a valid colum, add to insert array
				if (isset($TCA['tx_x4equalificationgeneral_list']['columns'][$key])) {
					switch($key) {
						case 'student':
						case 'organizer':
							$s = t3lib_div::trimExplode(',',$value);
							$tmpArr = array();
							foreach($s as $tmp) {
								if ($tmp != '') {
									array_push($tmpArr,$tmp);
								}
							}
							$value = implode(',',$tmpArr);
							unset($s,$tmpArr,$tmp);

						default:
							$upd[$key] = $value;
						break;
					}
				}
			}
			unset($key,$value);

			$uid['tstamp'] = time();
			$images = array();

			if ($_FILES['tx_x4equalificationgeneral_pi2']['name']['image0'] != '') {
				array_push($images,$this->uploadImage('image0'));
			}
			if ($_FILES['tx_x4equalificationgeneral_pi2']['name']['image1'] != '') {
				array_push($images,$upd['pictures'] .= ','.$this->uploadImage('image1'));
			}
			if ($this->piVars['images'] != '') {
				$imgs = t3lib_div::trimExplode(',',$this->piVars['images']);
				if ($imgs[0] != '') {
					array_push($images,$imgs[0]);
				}
				if ($imgs[1] != '') {
					array_push($images,$imgs[1]);
				}
			}

			$upd['pictures'] = implode(',',$images);

				// update record
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_x4equalificationgeneral_list','uid='.intval($this->piVars['uid']),$upd);

			unset($key,$value,$upd);

			//return $this->cObj->getSubpart($this->template,'###creationSuccessful###');
		}
	}

	/**
	 * Handels the editing of a record
	 */
	function editRecord() {
		$this->checkPermission();
		$this->loadRecord();
		return $this->getEditForm();
	}

	/**
	 * Loads the record and puts it into the member variable
	 */
	function loadRecord() {
		$this->record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4equalificationgeneral_list','uid = '.intval($this->piVars['uid']).$this->cObj->enableFields('tx_x4equalificationgeneral_list'));
		$this->record = $this->record[0];
	}

	/*
	 * Checks wether active user is author of selected publication
	 * @internal Note: This method has been modified to fix bad security bugs by alessandro@4eyes.ch  
	 */
	function checkPermission() {
		if(intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4epersdb_person','feuser_id='.intval($GLOBALS['TSFE']->fe_user->user['uid']).$this->cObj->enableFields('tx_x4epersdb_person'));
			$pers = $res[0];
			$count = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4equalificationgeneral_list','uid = '.intval($this->piVars['uid']).' AND FIND_IN_SET('.intval($pers['uid']).',organizer)');
			if (($count[0]['count(*)'] == 0) && ($pers['qualiadmin'] != 1)) {
				die("You're not allowed to edit this record!");
			}
		}else{
			die("You're not allowed to edit this record!");
		}
	}

	/**
	 * returns the edit-form according to the selected subcategory
	 */
	function getEditForm() {
		// get columns to show
		$columnsToShow = array('organizer','title','abstract','pictures','finished','abortet','student','type');

		// add columns to content
		foreach($columnsToShow as $c) {

			switch($c) {
				case 'organizer':
					$p['no_cache']=1;
					$p['type']=7645;
					$p[$this->prefixId.'[action]']='searchOrganizer';
					$mArr['###searchOrganizerFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
					$mArr['###organizer###'] = $this->record[$c];
					$subP['###author###'] = $this->cObj->substituteMarkerArray($this->getOrganizers(),$mArr);
				break;
				case 'student':
					$p['no_cache']=1;
					$p['type']=7645;
					$p[$this->prefixId.'[action]']='searchStudent';
					$mArr['###searchStudentFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
					$p[$this->prefixId.'[action]']='newStudent';
					$mArr['###newStudentFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
					$subP['###student###'] = $this->cObj->substituteMarkerArray($this->getStudents(),$mArr);
					$mArr['###students###'] = $this->record[$c];
				break;
				case 'type':
					$mArr['###typeOptions###'] = $this->generateOptionsFromTable('<option value="###value###" ###selected###>###label###</option>','tx_x4equalificationgeneral_cat',$this->record['type']);
					$mArr['###type'.$this->record['type'].'###'] = 'selected="selected"';
				break;
				case 'finished':
					$subP['###finishedOptions###'] = $this->generateOptionsFromArray($this->cObj->getSubpart($this->template,'###finishedOptions###'),$this->years,0,1,$this->record['finished']);
				break;
				case 'abortet':
					$subP['###abortedOptions###'] = $this->generateOptionsFromArray($this->cObj->getSubpart($this->template,'###abortedOptions###'),$this->years,0,1,$this->record['abortet']);
				break;
				default:
					$mArr['###'.$c.'###'] = $this->record[$c];
				break;
			}
		}
			// add form labels and action
		$mArr['###newForm.submit###'] = $this->pi_getLL('editForm.submit');
		$mArr['###newForm.back###'] = $this->pi_getLL('editForm.back');
			// add piVars to parameter array
		$param = array();
		$this->piVars['action'] = 'update';
		foreach($this->piVars as $key => $val) {
			$param[$this->prefixId][$key] = $val;
		}
		unset($key,$val);
		if (intval($_GET['tx_listfeuseruni_pi1']['showUid'])>0) {
			$param['tx_listfeuseruni_pi1[showUid]'] = $_GET['tx_listfeuseruni_pi1']['showUid'];
		}
		//$mArr['###formAction###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$param);
		$mArr['###formAction###'] = $this->generateLinkWithPiAndUid();
		$mArr['###submit###'] = $this->pi_getLL('editForm.submit');
		$mArr['###backUrl###'] = $mArr['###formAction###'];
		unset($key,$val);
		$mArr['###heading###'] = $this->contentHeading;

		$content = $this->cObj->getSubpart($this->template,'###newForm###');

			// add images
		$imgUploadT = $this->cObj->getSubpart($content,'###imgUpload###');
		$imgShowT = $this->cObj->getSubpart($content,'###imgShow###');
		$tmpImgs = t3lib_div::trimExplode(',',$this->record['pictures']);
		$imgs = array();
		foreach($tmpImgs as $i) {
			if ($i != '') {
				array_push($imgs,$i);
			}
		}
		unset($tmpImgs,$i);
		$subP['###imgShow###'] = '';
		$subP['###imgUpload###'] = '';

		for($i=0;$i<2;$i++) {
			if (isset($imgs[$i]) && ($imgs[$i] != '')) {
				$im['###image###'] = $imgs[$i];
				$im['###imageLink###'] = 'uploads/tx_x4equalificationgeneral/'.$imgs[$i];
				$subP['###imgShow###'] .= $this->cObj->substituteMarkerArray($imgShowT,$im);
			} else {
				$subP['###imgUpload###'] .= $this->cObj->substituteMarker($imgUploadT,'###imgName###','image'.$i);
			}
		}

		return $this->cObj->substituteMarkerArrayCached($content,$mArr,$subP);
	}

	/*
	 * Returns the authors of a publication to be inserted into the publication-edit form
	 */
	function getOrganizers() {
		$tmpIds = explode(',',$this->record['organizer']);
		$tmpIdsEnd = array();
		foreach($tmpIds as $v) {
			if (intval($v)>0) {
				array_push($tmpIdsEnd,$v);
			}
		}
		$this->record['organizer'] = implode(',',$tmpIdsEnd);

			// get authors of actual publication
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lastname,firstname','tx_'.$this->persExtKey.'_person','uid IN ('.$this->record['organizer'].')'.$this->cObj->enableFields('tx_'.$this->persExtKey.'_person'));

		$this->internal['currentRow'][$key] = implode(',',$tmpIdsEnd);
		$out = '';
			// get template
		$tmpl = $this->cObj->getSubpart($this->template,'###author###');
		while($a = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($records)) {
			$this->authorUids[] = $a['uid'];
			$mArr['###actualUserUid###'] = $a['uid'];
			$mArr['###actualUserName###'] = $a['lastname'];
			$mArr['###actualUserFirstname###'] = $a['firstname'];
			$out .= $this->cObj->substituteMarkerArray($tmpl,$mArr);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($records);
		return $out;
	}

	/*
	 * Returns the authors of a publication to be inserted into the publication-edit form
	 */
	function getStudents() {
			// get authors of actual publication
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lastname,firstname','tx_x4equalificationgeneral_student','uid IN ('.$this->record['student'].')'.$this->cObj->enableFields('tx_x4equalificationgeneral_student'));
		$out = '';
			// get template
		$tmpl = $this->cObj->getSubpart($this->template,'###student###');
		while($a = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($records)) {
			$this->authorUids[] = $a['uid'];
			$mArr['###uid###'] = $a['uid'];
			$mArr['###lastname###'] = $a['lastname'];
			$mArr['###firstname###'] = $a['firstname'];
			$mArr['###editUrl###'] = $this->getStudentEditUrl($a);
			$out .= $this->cObj->substituteMarkerArray($tmpl,$mArr);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($records);

		return $out;
	}

	/**
	 * Generates the link to edit a student
	 *
	 */
	function getStudentEditUrl($student) {
		// get all pivars
		$params = array();
		foreach($this->piVars as $k => $v) {
			$params[$this->prefixId.'['.$k.']'] = $v;
		}
		$params['type'] = 7645;
		$params[$this->prefixId.'[action]'] = 'editStudent';
		$params[$this->prefixId.'[editUid]'] = $student['uid'];

		return $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$params);
	}

	/**
	 * Creates the publication record with all corresponding relations
	 */
	function createRecord() {
		global $TCA;
		t3lib_div::loadTCA('tx_x4equalificationgeneral_list');
		// loop over all recieved values
		foreach($this->piVars as $key => $value) {
			// if value is a valid colum, add to insert array
			if (isset($TCA['tx_x4equalificationgeneral_list']['columns'][$key])) {
				switch($key) {
						case 'student':
						case 'organizer':
							$s = t3lib_div::trimExplode(',',$value);
							$tmpArr = array();
							foreach($s as $tmp) {
								if ($tmp != '') {
									array_push($tmpArr,$tmp);
								}
							}
							$value = implode(',',$tmpArr);
							unset($s,$tmpArr,$tmp);

						default:
							$ins[$key] = $value;
						break;
					}
			}
		}
		unset($key,$value);

		// handle data
		$ins['pid'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pidList');
		$ins['hidden'] = 0;
		$ins['tstamp'] = time();
		$ins['crdate'] = time();

		$ins['pictures'] = $this->uploadImage('image1');
		$ins['pictures'] .= ','.$this->uploadImage('image2');
			// create record
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_x4equalificationgeneral_list',$ins);
		unset($key,$value,$ins);

		// get id of new record
		//Note by alessandro@4eyes.ch -> Deprecated! Using mysql_insert_id() instead...
		//$id = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid','tx_x4equalificationgeneral_list','hidden=0 AND deleted=0','','crdate DESC',1);
		$id = mysql_insert_id();
		return $id = $id[0]['uid'];
	}

	/**
	 * Uploads picture
	 */
	function uploadImage($inputName) {
		$uploaddir = '/home/vhosts/gkw-4.0/httpdocs/uploads/tx_x4equalificationgeneral/';
		$ending = substr($_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName],strrpos($_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName],'.')+1);
		$name = substr($_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName],0,strpos($_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName],'.'));
			// check file ending
		if (in_array($ending,$this->validFileTypes)) {
				// check file_exists
				$i = 1;
			while (is_file($uploaddir . $_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName])) {
				$_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName] = $name.$i.'.'.$ending;
				$i++;
			}
			if (move_uploaded_file($_FILES['tx_x4equalificationgeneral_pi2']['tmp_name'][$inputName], $uploaddir . $_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName])) {
				chmod($uploaddir . $_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName],0666);
				return $_FILES['tx_x4equalificationgeneral_pi2']['name'][$inputName];
			}
		}
		return '';
	}

	/*
	 * Redirects after creation of record
	 */
	function redirect($uid) {
		// clears page cache
		require_once(PATH_t3lib.'class.t3lib_tcemain.php');
		t3lib_TCEmain::clear_cacheCmd($uid);
		$param = array();
		if (intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid'])>0) {
			$param['tx_'.$this->persExtKey.'_pi1[showUid]'] = intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid']);
		}
		//$pA = t3lib_div::cHashParams('&tx_listfeuseruni_pi1[showUid]='.$_GET['tx_listfeuseruni_pi1']['showUid']);
		//$param['cHash'] = t3lib_div::shortMD5(serialize($pA));
		$param['no_cache'] = 1;
		if (is_array($_GET['tx_x4equalificationgeneral_pi1'])) {
			foreach($_GET['tx_x4equalificationgeneral_pi1'] as $k=>$v) {
				$param['tx_x4equalificationgeneral_pi1['.$k.']'] = $v;
			}
		}
		header("Location: http://".$_SERVER['HTTP_HOST'].'/'.$this->pi_getPageLink($uid,'',$param).'#quali_'.$this->piVars['uid']);
		exit();
	}

	/*
	 * Redirects after creation of record
	 */
	function displayInputForm() {
		$person = $this->getCurrentPerson();
		$this->piVars['action'] = 'saveForm';
		$form = $this->cObj->getSubpart($this->template,'###newForm###');
		$mArr['###formAction###'] = $this->generateLinkWithPiAndUid();
		$mArr['###submit###'] = $this->pi_getLL('newForm.submit');
		$mArr['###actualUserName###'] = $person['lastname'];
		$mArr['###actualUserFirstname###'] = $person['firstname'];
		$mArr['###actualUserUid###'] = $person['uid'];
		$mArr['###organizer###'] =  $person['uid'];
		$subP['###abortedOptions###'] = $this->generateOptionsFromArray($this->cObj->getSubpart($this->template,'###abortedOptions###'),$this->years);
		$subP['###finishedOptions###'] = $this->generateOptionsFromArray($this->cObj->getSubpart($this->template,'###finishedOptions###'),$this->years);
		$p['no_cache'] = 1;
		$p['type'] = 7645;
		$p[$this->prefixId.'[action]'] = 'searchStudent';
		$mArr['###searchStudentFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
		$p[$this->prefixId.'[action]'] = 'searchOrganizer';
		$mArr['###searchOrganizerFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
		$p[$this->prefixId.'[action]']='newStudent';
		$mArr['###newStudentFrameSrc###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$p);
		$mArr['###type0###'] = '';
		$mArr['###type1###'] = '';
		$mArr['###type2###'] = '';
		$mArr['###students###'] = '';
		$mArr['###title###'] = '';
		$mArr['###abstract###'] = '';
		$mArr['###typeOptions###'] = $this->generateOptionsFromTable('<option value="###value###" ###selected###>###label###</option>','tx_x4equalificationgeneral_cat','');
		$subP['###student###'] = '';

		$imgUploadT = $this->cObj->getSubpart($form,'###imgUpload###');
		$subP['###imgUpload###'] = $this->cObj->substituteMarker($imgUploadT,'###imgName###','image1');
		$subP['###imgUpload###'].= $this->cObj->substituteMarker($imgUploadT,'###imgName###','image2');
		$subP['###imgShow###'] = '';
		$mArr['###newForm.back###'] = $this->pi_getLL('editForm.back');
		$mArr['###backUrl###'] = $this->pi_getPageLink($this->getTSFFvar('returnPageUid'));
		return $this->cObj->substituteMarkerArrayCached($form,$mArr,$subP);
	}

	/*
	function getYears() {
		require('typo3conf/ext/x4equalificationgeneral/class.tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years.php');
		$ly = t3lib_div::makeInstance("tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years");
		$this->years = array();
		$ly->main($this->years);
		$this->years = $this->years['items'];
	}
	*/

	/**
     * Generates options for the select menu
     *
     * @param	string	$tmpl		Template to use
     * @param	array	$tableName	Name of the table where to get the info
     * @param	array	$selected	Uid of selected
     * @param	bool	$addEmpty	If true empty element will be inserted
     * @return	string				string with <options>
     */
    function generateOptionsFromTable($tmpl,$tableName,$selected='',$addEmpty=false) {
    	// get correct sql statement
    	global $TCA;
    	$labelKey = $TCA[$tableName]['ctrl']['label'];
    	$valueKey = 'uid';

    	$fields = $valueKey.','.$labelKey;
    	$where = 'hidden=0 AND '.$TCA[$tableName]['ctrl']['delete'].'=0';
    	$orderBy = $TCA[$tableName]['ctrl']['sortby'];

    	// run statement
    	$opts = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,$tableName,$where,'',$orderBy);
    	// free variables
    	unset($fields,$where,$orderBy);
    	// loop the result
       	while ($opt = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($opts)) {
    		$mArr['###label###'] = $opt[$labelKey];
    		$mArr['###value###'] = $opt[$valueKey];
    		if ($opt['uid'] == $selected) {
    			$mArr['###selected###'] = 'selected';
    			$chosen = true;
    		} else {
    			$mArr['###selected###'] = '';
    		}
    		$returnStr .= $this->cObj->substituteMarkerArray($tmpl,$mArr);
    	}
    	// Add empty elment at the beginning, and select if no other element is selected
    	if ($addEmpty) {
    		$mArr['###label###'] = '';
    		$mArr['###value###'] = '';
    		if (!$chosen) {
    			$mArr['###selected###'] = 'selected';
    		} else {
    			$mArr['###selected###'] = '';
    		}
    		$returnStr = $this->cObj->substituteMarkerArray($tmpl,$mArr).$returnStr;
    	}
    	return $returnStr;
    }

	function getYears(){
		global $TCA;
		t3lib_div::loadTCA('tx_x4equalificationgeneral_list');

		if (isset($TCA['tx_x4equalificationgeneral_list']['columns']['finished'])){
			$this->years = $TCA['tx_x4equalificationgeneral_list']['columns']['finished']['config']['items'];
		}
		$this->years[0][1] = '---';
		//t3lib_div::debug($this->years);
	}


	/**
	 *	Displays the searchform with results for an author-search
	 */
	function displaySearchStudent() {
		// get template
		$this->template = $this->cObj->fileResource($this->conf['searchStudentTemplateFile']);
		$content = $this->cObj->getSubpart($this->template,'###searchForm###');
		// replace some markers
		$this->piVars['action'] = 'searchStudent';
		$mArr['###formAction###'] = $this->pi_linkTP_keepPIvars_url().'&type=7645';
		$mArr['###label###'] = $this->pi_getLL('searchStudent.searchLabel');
		$mArr['###submit###'] = $this->pi_getLL('searchStudent.submit');
		$mArr['###close###'] = $this->pi_getLL('searchStudent.close');
		$mArr['###studentUids###'] = $this->piVars['###studentUids###'];
		$mArr['###searchWord###'] = $this->piVars['studentSearchWord'];
		$content = $this->cObj->substituteMarkerArray($content,$mArr);
		return $content.$this->searchStudents();
	}
	/**
	 *	Displays the searchform with results for an author-search
	 */
	function displaySearchOrganizer() {
		// get template
		$this->template = $this->cObj->fileResource($this->conf['searchOrganizerTemplateFile']);
		$content = $this->cObj->getSubpart($this->template,'###searchForm###');
		// replace some markers
		$this->piVars['action'] = 'searchOrganizer';
		$mArr['###formAction###'] = $this->pi_linkTP_keepPIvars_url().'&type=7645';
		$mArr['###label###'] = $this->pi_getLL('searchAuthor.searchLabel');
		$mArr['###submit###'] = $this->pi_getLL('searchAuthor.submit');
		$mArr['###close###'] = $this->pi_getLL('searchAuthor.close');
		$mArr['###organizerUids###'] = $this->piVars['###organizerUids###'];
		$mArr['###searchWord###'] = $this->piVars['organizerSearchWord'];
		$content = $this->cObj->substituteMarkerArray($content,$mArr);
		return $content.$this->searchOrganizers();
	}

	/**
	 *	Runs the search and returns search-result
	 */
	function searchOrganizers() {
		// Create where statement
		if (isset($this->piVars['organizerUids'])) {
			$where = 'pid IN ('.$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'authorsSysFolderUid').') AND (lastname LIKE "%'.$this->piVars['organizerSearchWord'].'%" OR firstname LIKE "%'.$this->piVars['organizerSearchWord'].'%")'.$this->cObj->enableFields('tx_'.$this->persExtKey.'_person');
			if ($this->piVars['###organizerUids###']) {
				$where .= ' AND uid NOT IN ('.$this->piVars['organizersUids'].')';
			}
			$authors = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lastname,firstname','tx_'.$this->persExtKey.'_person',$where,'','lastname');

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($authors) == 0) {
				$content = $this->cObj->getSubpart($this->template,'###noResults###');

				return $this->cObj->substituteMarker($content,'###content###',$this->pi_getLL('searchAuthor.noResultFound'));
			} else {
				$content = $this->cObj->getSubpart($this->template,'###searchRes###');
				$rowT = $this->cObj->getSubpart($content,'###row###');
				$row = '';
				$mArr['###addLabel###'] = $this->pi_getLL('searchAuthor.addLabel');
				while ($a = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($authors)) {
					$mArr['###lastname###'] = $a['lastname'];
					$mArr['###uid###'] = $a['uid'];
					$mArr['###firstname###'] = $a['firstname'];
					$mArr['###editUrl###'] = $this->getStudentEditUrl($a);
					$row .= $this->cObj->substituteMarkerArray($rowT,$mArr);
				}

				return $this->cObj->substituteSubpart($content,'###row###',$row);
			}
		}
	}

	/**
	 *	Runs the search and returns search-result
	 */
	function searchStudents() {
		// Create where statement
		if (isset($this->piVars['studentUids'])) {
			$where = 'pid IN ('.$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pidStudentList').') AND (lastname LIKE "%'.$this->piVars['studentSearchWord'].'%" OR firstname LIKE "%'.$this->piVars['studentSearchWord'].'%")'.$this->cObj->enableFields('tx_x4equalificationgeneral_student');
			if ($this->piVars['###studentUids###']) {
				$where .= ' AND uid NOT IN ('.$this->piVars['studentUids'].')';
			}
			$students = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,lastname,firstname','tx_x4equalificationgeneral_student',$where,'','lastname');

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($students) == 0) {
				$content = $this->cObj->getSubpart($this->template,'###noResults###');

				return $this->cObj->substituteMarker($content,'###content###',$this->pi_getLL('searchStudent.noResultFound'));
			} else {
				$content = $this->cObj->getSubpart($this->template,'###searchRes###');
				$rowT = $this->cObj->getSubpart($content,'###row###');
				$row = '';
				$mArr['###addLabel###'] = $this->pi_getLL('searchStudent.addLabel');
				$mArr['###editLabel###'] = $this->pi_getLL('searchStudent.editLabel');

				while ($a = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($students)) {
					$mArr['###lastname###'] = $a['lastname'];
					$mArr['###uid###'] = $a['uid'];
					$mArr['###firstname###'] = $a['firstname'];
					$this->piVars['editUid'] = $a['uid'];
					$mArr['###editLink###'] = $this->pi_linkTP_keepPIvars_url(array('action'=>'editStudent')).'&type=7645';
					$mArr['###editUrl###'] = $this->getStudentEditUrl($a);
					$row .= $this->cObj->substituteMarkerArray($rowT,$mArr);
				}

				return $this->cObj->substituteSubpart($content,'###row###',$row);
			}
		}
	}

	/**
	 * Returns a link with all pivars + actual user id
	 */
	function generateLinkWithPiAndUid() {
		foreach($this->piVars as $key => $val) {
			$param[$this->prefixId][$key] = $val;
		}
		if (intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid'])>0) {
			$param['tx_'.$this->persExtKey.'_pi1[showUid]'] = intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid']);
		}
		if (is_array(($_GET['tx_x4equalificationgeneral_pi1']))) {
			foreach($_GET['tx_x4equalificationgeneral_pi1'] as $k=>$v) {
				if (trim($v)!='') {
					$param['tx_x4equalificationgeneral_pi1['.$k.']'] = $v;
				}
			}
		}
		return $this->pi_getPageLink($GLOBALS['TSFE']->id,'',$param);
	}

	/**
     * Generates options form the given array
     *
     * @param	string	$tmpl		Template to use
     * @param	array	$values		Name of the table where to get the info
     * @param 	string	$labelKey	Name of the label key entry
     * @param 	string	$valueKey	Name of the value key entry
     * @param	array	$selected	Uid of selected
     * @param	bool	$addEmpty	If true empty element will be inserted
     * @return	string				string with <options>
     */
    function generateOptionsFromArray($tmpl,$values,$valueKey=0,$labelKey=1,$selected='',$addEmpty=false) {
    	// get correct sql statement
    	$returnStr = '';
    	// loop the result
    	foreach($values as $opt) {
       		$mArr['###label###'] = $opt[$labelKey];
    		$mArr['###value###'] = $opt[$valueKey];
    		if ($opt[$valueKey] == $selected) {
    			$mArr['###selected###'] = 'selected';
    			$chosen = true;
    		} else {
    			$mArr['###selected###'] = '';
    		}
    		$returnStr .= $this->cObj->substituteMarkerArray($tmpl,$mArr);
    	}
    	// Add empty elment at the beginning, and select if no other element is selected
    	if ($addEmpty) {
    		$mArr['###label###'] = '';
    		$mArr['###value###'] = '';
    		if (!$chosen) {
    			$mArr['###selected###'] = 'selected';
    		} else {
    			$mArr['###selected###'] = '';
    		}
    		$returnStr = $this->cObj->substituteMarkerArray($tmpl,$mArr).$returnStr;
    	}
    	return $returnStr;
    }

    /**
     * returns the currently a person
     *
     * @author Markus Stauffiger (markus@4eyes.ch)
     */
    function getCurrentPerson() {
		$person = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_'.$this->persExtKey.'_person','feuser_id = '.intval($GLOBALS['TSFE']->fe_user->user['uid']));
		return $person[0];
    }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/pi2/class.tx_x4equalificationgeneral_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/pi2/class.tx_x4equalificationgeneral_pi2.php']);
}

?>