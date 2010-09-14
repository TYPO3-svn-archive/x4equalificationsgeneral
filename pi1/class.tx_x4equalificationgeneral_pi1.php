<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Andi Keller (andi@4eyes.ch)
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
 * Plugin 'Qualification workings' for the 'x4equalificationgeneral' extension.
 *
 * @author	Andi Keller <andi@4eyes.ch>
 */
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once('typo3conf/ext/x4epibase/class.x4epibase.php');

class tx_x4equalificationgeneral_pi1 extends x4epibase {
	var $prefixId = 'tx_x4equalificationgeneral_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_x4equalificationgeneral_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'x4equalificationgeneral';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $editMode = false; // if mode = edit, the edit icons will be shown
	var $debugOutput = TRUE;
	var $persExtKey = 'x4epersdb';
	var $types = array(); // Array of the categories

	function init($content,$conf){
		//$GLOBALS['TYPO3_DB']->debugOutput = true;
		//$GLOBALS["TSFE"]->set_no_cache();
		$this->conf=$conf;
		$this->internal = $this->conf['listView.'];
		$this->cols = 0;
		$this->colCount = 0;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$this->pi_initPIflexform();
		$this->fields = t3lib_div::trimExplode(',', $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'fieldsWorking'));
		$this->personSingleUid = $this->conf['personSingleUid'];
		// get uid of page where single view is diplayed
    	$this->singleUid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'detailPageUid');
    	if($this->singleUid == ''){
    		$this->singleUid = $GLOBALS['TSFE']->id;
    	}

    	if ($this->conf['persExtKey']) {
    		$this->persExtKey = $this->conf['persExtKey'];
    	}
	}

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->init($content,$conf);

	

    	if (intval($this->piVars['removeUid']) > 0) {
			$this->checkRemovePermission();
			$this->hideRecord();
		}

		if (strstr($this->cObj->currentRecord,'tt_content'))	{
			$this->conf['pidList'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'pages');
			$this->conf['recursive'] = $this->cObj->data['recursive'];
		}

			// set edit mode
		if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'modeSelection') == 5) {
			$this->editMode = true;
			
		}
		
		$person = $this->getCurrentPerson();
		
		if ($person['qualiadmin']) {
			$this->editMode = true;
		}

		// Add javascript
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='
				<script type="text/javascript" src="typo3conf/ext/x4equalificationgeneral/pi1/templates/code.js"></script>';

		return $this->pi_wrapInBaseClass($this->listView());
		//return $this->listView();
		
	}

	/*
	 * Checks wether active user is author of selected publication
	 * @internal Note: This method has been modified to fix bad security bugs by alessandro@4eyes.ch  
	 */
	function checkRemovePermission() {
		if(intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_x4epersdb_person','feuser_id='.intval($GLOBALS['TSFE']->fe_user->user['uid']).$this->cObj->enableFields('tx_x4epersdb_person'));
			$pers = $res[0];
			$count = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*)','tx_x4equalificationgeneral_list','uid = '.intval($this->piVars['removeUid']).' AND FIND_IN_SET('.intval($pers['uid']).',organizer)');
			if (($count[0]['count(*)'] == 0) && ($pers['qualiadmin'] != 1)) {
				die("You're not allowed to remove this record!");
			}
		}else{
			die("You're not allowed to edit this record!");	
		}
	}

	/**
	 * [Put your description here]
	 */
	function listView()	{
		$this->template = $this->cObj->fileResource($this->conf['templateList']);

		if ($this->listT == '') {
			$this->listT = $this->cObj->getSubpart($this->template,'###list###');
		}

		$this->rowsT = $this->cObj->getSubpart($this->listT,'###rows###');
		$this->rowT[0] = $this->cObj->getSubpart($this->rowsT,'###row0###');
		$this->rowT[1] = $this->cObj->getSubpart($this->rowsT,'###row1###');
		$this->cellT[0] = $this->cObj->getSubpart($this->rowT[0],'###cell###');
		$this->cellT[1] = $this->cObj->getSubpart($this->rowT[1],'###cell###');

		$lConf = $this->conf['listView.'];	// Local settings for the listView function
		$this->detailview = 0;
		//
		// decide whether single view or list view has to display
		//
		
		$this->mode = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'modeSelection');
		if($this->piVars['showUid'] != ''){
			$this->detailview = 1;
			return $this->singleView($content,$conf);
		} else {
			$pUid = $_GET['tx_'.$this->persExtKey.'_pi1']['showUid'];
  			if (($pUid != 0) && ($this->pi_getFFvalue($this->cObj->data['pi_flexform'],'editMode') != 1)) {
  				return $this->makePersonelList($pUid);
  			} elseif($this->mode == '3'){
  				return $this->listByProfs();
  			}
  			else {
  			
  				// decide what kind of working have to be displayed
  			$mode = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'modeSelection');

  			$abortedOr = "(abortet = '' OR abortet = '0')";
  			$abortedAnd = "(abortet = '' AND abortet = '0')";
  			$finishedOr = "(finished != '' OR finished != '0')";
  			$finishedAnd = "(finished != '' AND finished != '0')";

  				// unfinished workings
  			if($mode == '0'){
  				$WHERE = " AND ($finishedOr AND $abortedOr) ";
  				// finished workings
  			} elseif($mode == '1'){
  				$WHERE = " AND ($finishedAnd OR $abortedAnd) ";
  				// all papers but the abortet
  			} elseif($mode == '2'){
  				$WHERE = " AND $abortedOr ";
  				// finished workings which have an abstract
  			} elseif($mode == '4'){
  				$WHERE = " AND ($finishedAnd OR $abortedAnd) AND abstract != '' ";
  			}

    		if (!isset($this->piVars['pointer'])){
    			$this->piVars['pointer']=0;
    		}
    		
    			// Initializing the query parameters:
    		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
    		$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,5);		// Number of results to show in a listing.
    		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
    		$this->internal['searchFieldList']='title';
    		$this->internal['orderByList']='title,organizer,firstname,lastname,type';

			if ($this->piVars['showAll']) {
				$this->internal['results_at_a_time'] = 1000;
				$this->piVars['pointer'] = 0;
			}

  			$WHERE .= $this->generateSearchQuery();
  			$backupPointer = $this->piVars['pointer'];
  			$backupSearchWord = $this->piVars['sword'];
  			$this->piVars['sword'] = '';
  			$this->piVars['pointer'] = 0;
    			// get number of results
    		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s, tx_'.$this->persExtKey.'_person fe','list.student = s.uid and list.organizer = fe.uid'.$WHERE,'','',$this->getLimit());

    		list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

    		$this->piVars['pointer'] = $backupPointer;
    		$WHERE .= $this->generateSearchQuery();
    		$result = array();
    		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid,fe.uid as feUid,fe.lastname as feName,fe.firstname as feFirstname','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s, tx_'.$this->persExtKey.'_person fe','list.student = s.uid and list.organizer = fe.uid'.$WHERE,'',$this->getSorting(),$this->getLimit());

    			// Make listing query, pass query to SQL database:
    		$this->internal['currentTable'] = 'tx_x4equalificationgeneral_list';


    		$this->piVars['sword'] = $backupSearchWord;

    		// Put the whole list together:
    		$fullTable='';	// Clear var;
    		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!

    		// Adds the search box:
    		$fullTable.=$this->pi_list_searchBox();

    		// Adds the result browser:
    		if(!$this->conf['listView.']){
    			$this->conf['listView.'] = array();
    		}
			$fullTable .= '<p>'.$this->pi_list_browseresults(1,'',$this->conf['listView.']).'</p>';

  			
			
    		// Adds the listsview
    		$fullTable .= $this->pi_list_makelist($res);
    		
    			// Returns the content from the plugin.
    		return $this->cObj->substituteMarker($fullTable,'###pdfLink###',$this->getPdfLink());
  		}
		}
	}


	/**
	 * [Put your description here]
	 */
	function makelist($res)	{
		$items=Array();
			// Make list table rows
		while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$items[]=$this->makeListItem();
		}

		$out = '<div'.$this->pi_classParam('listrow').'>
			'.implode(chr(10),$items).'
			</div>';
		return $out;
	}


	/**
	 * [Put your description here]
	 */
	function singleView()	{
			// get template File for single view
		$this->templateSingle = $this->cObj->fileResource($this->conf['templateDetail']);
		$templateAbstract = $this->cObj->getSubpart($this->templateSingle,'###abstractTitle###');
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];

			// very important! if not reset to zero sql query has wrong 'limits'
		$this->piVars['pointer'] = 0;
			// Make listing query, pass query to SQL database:
		$WHERE = ' AND list.uid = '.$this->piVars['showUid'];
		//$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid,fe.uid as feUid,fe.name as feName,fe.tx_listfeuseruni_firstname as feFirstname','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s, fe_users fe','list.student = s.uid and list.organizer = fe.uid'.$WHERE);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid,fe.uid as feUid,fe.lastname as feName,fe.firstname as feFirstname','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s, tx_'.$this->persExtKey.'_person fe','FIND_IN_SET(s.uid, list.student) AND FIND_IN_SET( fe.uid, list.organizer) '.$WHERE);
		//if ($_GET['showSql']) {
		//echo $GLOBALS['TYPO3_DB']->SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid,fe.uid as feUid,fe.name as feName,fe.tx_listfeuseruni_firstname as feFirstname','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s, fe_users fe','list.student = s.uid and list.organizer = fe.uid'.$WHERE);
		//}
		
	//	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s','list.student = s.uid'.$WHERE);
			//$res = $this->pi_exec_query('tx_x4equalificationgeneral_list',0,$WHERE);
		$this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		//t3lib_div::print_array($this->internal['currentRow']);
		$values = array();
		$field = array();
		// get fields to display
		
		foreach($this->internal['currentRow'] as $k => $v){
			$values['###'.$k.'###'] = $this->getFieldContent($k);
		}
			// check whether working is still running or is already terminated
		if(($values['###finished###'] == '' OR $values['###finished###'] == '0') AND ($values['###abortet###'] == '' OR $values['###abortet###'] == '0')){
			$values['###status###'] = $this->pi_getLL('optionCurrent');
		} else {
			$values['###status###'] = $this->pi_getLL('optionFinished');
			if($values['###finished###'] == '' OR $values['###finished###'] == '0'){
				$values['###status###'] .= ' '.$values['###abortet###'];
			} else {
				$values['###status###'] .= ' '.$values['###finished###'];
			}
		}
			// Don't display abstract title if there is no abstract
		if($this->getFieldContent('abstract') != ''){
			$abstract['###abstract###'] = $this->getFieldContent('abstract');
			$abstractSubpart['###abstractTitle###'] = $this->cObj->substituteMarkerArray($templateAbstract,$abstract);
		} else {
			$abstractSubpart['###abstractTitle###'] = '';
		}
		$values['###back###'] = '<a href="javascript:history.back()">'.$this->pi_getLL('pi_list_back_to_list').'</a>';
		return $this->cObj->substituteMarkerArrayCached($this->templateSingle,$values,$abstractSubpart);
	}


	/**
	 * [Put your description here]
	 */
	function getFieldContent($key)	{
		$this->cols++;
		switch($key) {

			case 'start':
			case 'end':
				$values .= strftime('%d. %b %Y',$this->internal['currentRow'][$key]);
			break;

			case 'lUid':
			case 'uid':
			case 'title':
					if($this->detailview == 0){
						$params['tx_x4equalificationgeneral_pi1[showUid]']= $this->internal['currentRow']['lUid'];
						$params['tx_'.$this->persExtKey.'_pi1[showUid]']=$_GET['tx_'.$this->persExtKey.'_pi1']['showUid'];

						if($key == 'title'){
							$values .= $this->pi_linkTP($this->internal['currentRow'][$key],$params,1,$this->singleUid);
						} else {
							$values .= $this->pi_linkTP($this->pi_getLL('more_link'),$params,1,$this->singleUid);
						}
					} else {
						$values .= $this->internal['currentRow'][$key];
					}
			break;

			case 'organizer':
					// first organizer. is used for sorting
				if($this->internal['currentRow'][$key] == ''){
					$values .= '&nbsp;';
					break;
				}

				/*$user = $this->internal['currentRow']['feName'].', '.$this->internal['currentRow']['feFirstname'];
				$params['tx_'.$this->persExtKey.'_pi1[showUid]'] = $this->internal['currentRow']['feUid'];
				$out = $this->pi_linkTP($user,$params,1,$this->personSingleUid);*/

				if($out == ''){
					$values = '&nbsp;';
				}else{
					$values = $out;
				}
					// second till n'd organizer

				$personArray = array();
				if(strpos($this->internal['currentRow'][$key],',') != false){
					$tmpIds = explode(',',$this->internal['currentRow'][$key]);
				} else {
					$tmpIds[0] = $this->internal['currentRow'][$key];
				}
				
				$tmpIdsEnd = array();
				foreach($tmpIds as $v) {
					if (intval($v)>0) {
						array_push($tmpIdsEnd,$v);
					}
				}
				$this->internal['currentRow'][$key] = implode(',',$tmpIdsEnd);
				
				$allUsers = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,lastname,firstname','tx_'.$this->persExtKey.'_person','uid IN ('.$this->internal['currentRow'][$key].')'.$this->cObj->enableFields('tx_'.$this->persExtKey.'_person'));

				$out = '';
				$count = count($allUsers);
		
				if($count > 1){
					if($this->detailview){
						$values .= '<br />';
					} else {
						$values .= '; ';
					}
				}
				$j = 0;
				$users = array();

				for($i = 0; $i < $count; $i++){
					$users[$j] = $allUsers[$i];
					$j++;
				}

				foreach($users as $entry => $userArray){
					foreach($userArray as $k => $user){
						if($k == 'uid'){
							$params['tx_'.$this->persExtKey.'_pi1[showUid]'] = $user;
						} else {
							$out .= $user.', ';
						}
					}

					$persLinkText = trim(substr($out,0,-2));
					unset($out);
					array_push($personArray,$this->pi_linkTP($persLinkText,$params,1,$this->personSingleUid));
				}
				if(!empty($personArray)){
					if($this->detailview){
						$values .= implode('<br />',$personArray);
					} else {
						$values .= implode('; ',$personArray);
					}
				}else{
					$values .= '&nbsp;';
				}
			break;

			case 'lastname':
				$values .= $this->internal['currentRow']['lastname'].', '.$this->internal['currentRow']['firstname'];
			break;

			case 'type':
				$this->getCategories();
				foreach($this->types as $t) {
					if ($t['uid'] == $this->internal['currentRow'][$key]) {
						$values .= $t['name'];
					}
				}
			break;

			case 'finished':
					if($this->internal['currentRow'][$key] == '' || $this->internal['currentRow'][$key] == '-----' || $this->internal['currentRow'][$key]==0){
						$values = '';
					} else {
						$values = $this->internal['currentRow'][$key];
					}
			break;

			case 'abortet':
					if($this->internal['currentRow'][$key] == '' || $this->internal['currentRow'][$key] == '-----'){
						$values = '';
					} else {
						$values = $this->internal['currentRow'][$key];
					}
			break;

			case 'pictures':
					if($this->internal['currentRow'][$key] == ''){
						$values = '';
					} else {
						$imgTSConfig = $this->conf['images.'];
						$imgArr = explode(',',$this->internal['currentRow']['pictures']);
						foreach($imgArr as $img){
							$imgTSConfig['file'] = 'uploads/tx_x4equalificationgeneral/'.$img;
							$values .= $this->cObj->IMAGE($imgTSConfig);
						}
					}
			break;

			case 'email':
					if($this->internal['currentRow'][$key] == ''){
						$values = '';
					} else {
						$values .= $this->cObj->getTypoLink($this->internal['currentRow'][$key], $this->internal['currentRow'][$key]);
					}
			break;

			default:
				$values .= $this->internal['currentRow'][$key];
			break;
		}
		return $values;
	}
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
		switch($fN) {

			default:
				return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}

	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
		$sortArr = explode(':',$this->piVars['sort']);
		if($fN == $sortArr[0]){
			$params['tx_x4equalificationgeneral_pi1[sort]'] = $fN.':'.($sortArr[1]?0:1);
		} else {
			$params['tx_x4equalificationgeneral_pi1[sort]'] = $fN.':1';
		}
		$params['tx_x4equalificationgeneral_pi1[showUid]'] = $this->internal['currentRow'][$key];
		$params['tx_'.$this->persExtKey.'_pi1[showUid]'] = $_GET['tx_'.$this->persExtKey.'_pi1']['showUid'];

		$params['tx_x4equalificationgeneral_pi1[sword]'] = $this->piVars['sword'];
		$params['tx_x4equalificationgeneral_pi1[type]'] = $this->piVars['type'];
		$params['tx_x4equalificationgeneral_pi1[status]'] = $this->piVars['status'];
		$params['tx_x4equalificationgeneral_pi1[organizerSearchWord]'] = $this->piVars['organizerSearchWord'];
		$params['tx_x4equalificationgeneral_pi1[authorSearchWord]'] = $this->piVars['authorSearchWord'];
		$params['tx_x4equalificationgeneral_pi1[year]'] = $this->piVars['year'];


		if($this->piVars['sort'] == '' && $fN == 'lastname'){
			$params['tx_x4equalificationgeneral_pi1[sort]'] = $fN.':0';
		}
		return $this->pi_linkTP($this->getFieldHeader($fN),$params,0);
	}

	/**
	 * [Put your description here]
	 */
	function pi_list_row($c) {
		$this->cols = 0;
		$values = '';
		foreach($this->fields as $v){
			$mArr['###content###'] = $this->getFieldContent($v);
			$mArr['###uid###'] = $this->internal['currentRow']['lUid'];
			$values .= $this->cObj->substituteMarkerArray($this->cellT[$c%2],$mArr);
		}
			// add edit and remove columns
		if ($this->editMode) {
				// add edit and remove cols
			$editT = $this->cObj->getSubpart($this->rowT[c%2],'###editCol###');
			$mArr['###editRecord###'] = $this->addEditCol();
			$mArr['###removeRecord###'] = $this->addRemoveCol();
			$subPart['###editCol###'] = $this->cObj->substituteMarkerArray($editT,$mArr);
			unset($editT,$mArr);
		} else {
			$subPart['###editCol###'] = '';
		}

		$this->colCount = $this->cols;
		$subPart['###cell###'] = $values;
		return $this->cObj->substituteMarkerArrayCached($this->rowT[$c%2],array(),$subPart);
		return $this->cObj->substituteSubpart($this->rowT[$c%2],'###cell###',$values);
	}

	/*
	 * Add remove column
	 */
	function addRemoveCol() {
		return '<a href="javascript:removeQualification('.$this->internal['currentRow']['lUid'].')">'.$this->pi_getLL('removeRecord').'</a>';
	}

	/*
	 * Add edit colum
	 */
	function addEditCol() {
		if ($_GET['tx_'.$this->persExtKey.'_pi1']['showUid']>0) {
			$param['tx_'.$this->persExtKey.'_pi1[showUid]'] = intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid']);
		}
		$param['tx_x4equalificationgeneral_pi2[uid]'] = $this->internal['currentRow']['lUid'];
		$param['tx_x4equalificationgeneral_pi2[action]'] = 'edit';
		foreach($this->piVars as $k=>$v) {
			$param[$this->prefixId.'['.$k.']'] = $v;
		}
		return $this->pi_linkToPage($this->pi_getLL('editRecord'),$this->pi_getFFvalue($this->cObj->data['pi_flexform'],'editPageUid'),'',$param);
	}

	/**
	 * [Put your description here]
	 */
	function pi_list_searchBox($tableParams='')	{
		$tmpl = $this->cObj->getSubpart($this->template,'###searchBox###');
		$statusTmpl = $this->cObj->getSubpart($this->template,'###status###');
		$statusTmplOption = $this->cObj->getSubpart($this->template,'###statusOptions###');
		$typeTmpl = $this->cObj->getSubpart($this->template,'###typeOptions###');


		$statusArray = array(	0 => array('###value###' => '0','###label###' => $this->pi_getLL('optionCurrent'),'###selected###' => ''),
								1 => array('###value###' => '1','###label###' => $this->pi_getLL('optionFinished'),'###selected###' => ''),
								2 => array('###value###' => '2','###label###' => $this->pi_getLL('optionAll'),'###selected###' => ''));

		if(($this->piVars['status'] != '') && ($this->piVars['status'] != 'Status')){
			$statusArray[intval($this->piVars['status'])]['###selected###'] = 'selected="selected"';
		}


		$status['###statusOptions###'] .= $this->cObj->substituteMarkerArray($statusTmplOption,$statusArray[0]);
		$status['###statusOptions###'] .= $this->cObj->substituteMarkerArray($statusTmplOption,$statusArray[1]);
		$status['###statusOptions###'] .= $this->cObj->substituteMarkerArray($statusTmplOption,$statusArray[2]);

			// display the mode select-box only if mode = all workings
		if ($this->mode == 0 || $this->mode == 1 || $this->mode == 4){
			$sArr['###status###'] = '';
		} else {
			$sArr['###status###'] = $this->cObj->substituteSubpart($statusTmpl,'###statusOptions###',$status['###statusOptions###']);
		}

		$mArr['###typeOptions###'] = $this->generateOptionsFromTable('<option value="###value###" ###selected###>###label###</option>','tx_x4equalificationgeneral_cat',$this->piVars['type'],true);

		$mArr['###formAction###'] = $this->pi_linkTP_keepPIvars_url();
		$mArr['###searchWord###'] = $this->piVars['sword'];
		$mArr['###organizerSearchWord###'] = $this->piVars['organizerSearchWord'];

			// get Year selectbox
		if(($this->piVars['year'] != '') && ($this->piVars['year'] != '0')){
			$selectedYear = $this->piVars['year'];
		} else {
			$selectedYear = '';
		}

		$sArr['###yearOptions###'] = $this->generateOptionsFromArray($this->cObj->getSubpart($tmpl,'###yearOptions###'),$this->getYears(),0,1,$selectedYear);

		$mArr['###authorSearchWord###'] = $this->piVars['authorSearchWord'];

		$mArr['###submit###'] = $this->pi_getLL('pi_list_searchBox_search','Search',TRUE);
		
		if($this->mode == 3){
			$mArr['###profListLink###'] = '&nbsp;';
		} else {
		$params = array();
			$mArr['###profListLink###'] = $this->pi_linkTP($this->pi_getLL('profListLink'),$params,1,$this->pi_getFFvalue($this->cObj->data['pi_flexform'],'listByProfUid')).'<br/>&nbsp;';
		}
		
		$sArr['###newRecordLink###'] = '';
		if ($this->editMode) {
			$sArr['###newRecordLink###'] = $this->getNewRecordLink();
		}
		return $this->cObj->substituteMarkerArrayCached($tmpl,$mArr,$sArr);
	}

	/**
	 * Hides record
	 */
	function hideRecord() {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_x4equalificationgeneral_list','uid='.intval($this->piVars['removeUid']),array('deleted'=>1));
	}

	/**
	 * [Put your description here]
	 */
	function pi_list_makelistPersonel($res)	{
		// get all templates
		if ($this->manualFieldOrder_list == ''){
			$this->manualFieldOrder_list = $this->fields;
		}
		if ($this->listTP == '') {
			$this->listTP = $this->cObj->getSubpart($this->template,'###listPersonel###');
			$this->listT = $this->listTP;
		}

		// remove edit-columsn
		if (!$this->editMode) {
			$this->listT = $this->cObj->substituteSubpart($this->listT,'###editCol###','');
		}

		$this->rowSet = $this->cObj->getSubpart($this->listTP,'###rowSet###');
		$this->rowsT = $this->cObj->getSubpart($this->rowSet,'###rows###');
		$this->rowT[0] = $this->cObj->getSubpart($this->rowsT,'###row0###');
		$this->rowT[1] = $this->cObj->getSubpart($this->rowsT,'###row1###');
		$this->cellT[0] = $this->cObj->getSubpart($this->rowT[0],'###cell###');
		$this->cellT[1] = $this->cObj->getSubpart($this->rowT[1],'###cell###');

		// put the link-fields in appropriate array
		if (!is_array($this->conf['listView.']['detailLinkFields'])) {
			$this->conf['listView.']['detailLinkFields'] = t3lib_div::trimExplode(',',$this->conf['listView.']['detailLinkFields']);
		}

		// Make list table header:
		$tRows=array();
		$this->internal['currentRow']='';
		// get header and replace marker
		$out = $this->cObj->substituteSubpart($this->rowSet,'###headRow###',$this->pi_list_header());
			// Make list table rows
		$c=0;
		$rows = '';
		while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows .= $this->pi_list_row($c);
			$c++;
		}

		$rowsNout['rows'] = $rows;
		$rowsNout['out'] = $out;
		return $rowsNout;
	}


	/**
	 * [Put your description here]
	 */
	function pi_list_makelistProfs($res)	{
		// get all templates
		if ($this->manualFieldOrder_list == ''){
			$this->manualFieldOrder_list = $this->fields;
		}
		if ($this->listTP == '') {
			$this->listTP = $this->cObj->getSubpart($this->template,'###listProfs###');
		}
		$this->prof = $this->cObj->getSubpart($this->listTP,'###prof###');
		$this->rowSet = $this->cObj->getSubpart($this->listTP,'###rowSet###');
		$this->rowsT = $this->cObj->getSubpart($this->rowSet,'###rows###');
		$this->rowT[0] = $this->cObj->getSubpart($this->rowsT,'###row0###');
		$this->rowT[1] = $this->cObj->getSubpart($this->rowsT,'###row1###');
		$this->cellT[0] = $this->cObj->getSubpart($this->rowT[0],'###cell###');
		$this->cellT[1] = $this->cObj->getSubpart($this->rowT[1],'###cell###');

		// put the link-fields in appropriate array
		if (!is_array($this->conf['listView.']['detailLinkFields'])) {
			$this->conf['listView.']['detailLinkFields'] = t3lib_div::trimExplode(',',$this->conf['listView.']['detailLinkFields']);
		}

		// Make list table header:
		$tRows=array();
		$this->internal['currentRow']='';
		// get header and replace marker
		$out = $this->cObj->substituteSubpart($this->rowSet,'###headRow###',$this->pi_list_header());
			// Make list table rows
		$c=0;
		$rows = '';
		while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows .= $this->pi_list_row($c);
			$c++;
		}

		$rowsNout['rows'] = $rows;
		$rowsNout['out'] = $out;
		return $rowsNout;

	}

	/**
	 * [Put your description here]
	 */
	function makePersonelList($pUid){

			//t3lib_div::print_array(get_object_vars($GLOBALS['TSFE']));
		$this->internal['maxPages'] = 999;
		$this->internal['results_at_a_time']=99;
		$listArray = array();
		//$types = array(0 => 'type_liz_plur',1 => 'type_ma_plur',2 => 'type_diss_plur');
		$types = $this->getCategories();

			// Get current workings
			// loop for each type
		foreach($types as $k => $v){
			$WHERE = " AND ((list.finished = '' OR list.finished = '0') AND (list.abortet = '' OR list.abortet = '0')) ";
			$WHERE .= ' AND list.type = '.$v['uid'].' AND FIND_IN_SET('.$pUid.',list.organizer)';
			$WHERE .= ' AND list.deleted = "0" AND list.hidden = "0" ';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s','list.student = s.uid'.$WHERE,'',$this->getSorting());
			$listRows = $this->pi_list_makelistPersonel($res);
			//t3lib_div::debug($listRows);
			if($listRows['rows'] != ''){
				$list = $this->cObj->substituteSubpart($listRows['out'],'###rows###',$listRows['rows']);
				//fill header and colspan marker
				$markerArray['###span###'] = $this->colCount;
				$markerArray['###header###'] = $this->pi_getLL('type_running').$v['plural'];
				$markerArray['###span###'] = $this->colCount;
				$listArray[$v['uid']] = $this->cObj->substituteMarkerArray($list,$markerArray);
			}
		}
			// Get terminated workings
			// loop for each type
		foreach($types as $k => $v){
			//$WHERE = $WHERE = " AND ((finished != '' AND finished != '0') OR (abortet != '' AND abortet != '0')) ";
			$WHERE = $WHERE = " AND ((finished != '' AND finished != '0') AND (list.abortet = '' OR list.abortet = '0')) ";
			$WHERE .= ' AND list.type = '.$v['uid'].' AND FIND_IN_SET('.$pUid.',list.organizer)';
			$WHERE .= ' AND list.deleted = "0" AND list.hidden = "0" ';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s','list.student = s.uid'.$WHERE,'',$this->getSorting());
			$listRows = $this->pi_list_makelistPersonel($res);
			if($listRows['rows'] != ''){
				$list = $this->cObj->substituteSubpart($listRows['out'],'###rows###',$listRows['rows']);
				//fill header and colspan marker
				$markerArray['###span###'] = $this->colCount;
				$markerArray['###header###'] = $this->pi_getLL('type_terminated').$v['plural'];
				$markerArray['###span###'] = $this->colCount;
				array_push($listArray,$this->cObj->substituteMarkerArray($list,$markerArray));
			}
		}

			// insert rows
		$list = $this->cObj->substituteSubpart($this->listTP,'###rowSet###',implode($listArray));
		// get persons name
		$name = '';
		if (intval($pUid)>0) {
			$pRecord = tslib_pibase::pi_getRecord('tx_'.$this->persExtKey.'_person',intval($pUid));
			if($pRecord['title'] != ''){
				$name = $pRecord['title'].' ';
			}
			if($pRecord['firstname'] != ''){
				$name .=	$pRecord['firstname'].' ';
			}
			if($pRecord['lastname'] != ''){
				$name .=	$pRecord['lastname'];
			}
			if($pRecord['title_after'] != ''){
				$name .=	', '.$pRecord['title_after'];
			}
			$pageTitle = $this->pi_getLL('qualification_workings_of').$name;
			unset($pRecord);
		}
		$markerArray = array();
		$markerArray['###title###'] = $pageTitle;

			// add "create new qualification link if edit mode is set
		if ($this->editMode) {
			$subP['###newRecordLink###'] = $this->getNewRecordLink();
		} else {
			$subP['###newRecordLink###'] = '';
		}
		$list = $this->cObj->substituteMarkerArrayCached($list,$markerArray,$subP);

		$GLOBALS['TSFE']->page['title'] = $pageTitle;

		return $list;
	}

	/**
	 *
	 */
	function getNewRecordLink() {
		$newQualiT = $this->cObj->getSubpart($this->template,'###newRecordLink###');
			// add create new publication link
		$personId = intval($_GET['tx_'.$this->persExtKey.'_pi1']['showUid']);
		if($personId == 0) {
			$person = $this->getCurrentPerson();
			$personId = $person['uid'];
		}
		$p['tx_'.$this->persExtKey.'_pi1[showUid]'] = $personId;
		$link = $this->pi_linkToPage($this->pi_getLL('createQualification'),$this->pi_getFFvalue($this->cObj->data['pi_flexform'],'newRecordPageUid'),'',$p);
		return $this->cObj->substituteMarker($newQualiT,'###link###',$link);
	}

	/**
	 * checks which prof organizes workings
	 */
	function profsWithWorkings(){
		$WHERE = $this->cObj->enableFields('tx_x4equalificationgeneral_list');
		$res = $this->pi_exec_query('tx_x4equalificationgeneral_list',0,$WHERE);
		$result = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			array_push($result,$row);
    	};
    	return $result;
	}

	/**
	 * generates the workings list sorted by profs
	 */
	function listByProfs(){
			//t3lib_div::print_array(get_object_vars($GLOBALS['TSFE']));
		$this->internal['maxPages'] = 999;
		$this->internal['results_at_a_time']=99;
		$result = $this->profsWithWorkings();
		
		$organizer = array();
    	
    	$tmpVar = $_GET['tx_x4epersdb_pi1'];
    	if(isset($tmpVar['showUid'])){
    		$organizer[0] = $tmpVar['showUid'];
    	} else {
	    	foreach($result as $working){
	    		array_push($organizer,$working['organizer']);
	    	}
    	}
    	
    	$namesArray = array();
    	$organizer = array_unique($organizer);
			//t3lib_div::print_array($organizer);
		if(implode(',',$organizer)==''){
			$organizer[0] = 0;
		}
		$WHERE = ' uid IN('.implode(',',$organizer).')';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_'.$this->persExtKey.'_person',$WHERE,'','lastname');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
    			array_push($namesArray,$row);
    	};
    	
    	// loop for each prof
		$types = $this->getCategories();
		foreach($namesArray as $prof){
			$uid = $prof['uid'];
			$listArray = array();
				// loop for each type

			foreach($types as $t) {
					// running workings
				$WHERE = ' AND list.type = '.$t['uid'].' AND FIND_IN_SET('.$uid.',list.organizer)';
				$WHERE .= " AND ((finished = '' OR finished = '0')  AND (abortet = '' OR abortet = '0')) ";
				$WHERE .= " AND list.deleted = '0' AND list.hidden = '0' ";
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s','list.student = s.uid'.$WHERE,'',$this->getSorting());
				
				$listRows = $this->pi_list_makelistProfs($res);

				if($listRows['rows'] != ''){
					$markerArray = array();
					$list = $this->cObj->substituteSubpart($listRows['out'],'###rows###',$listRows['rows']);
					//fill header and colspan marker
					$markerArray['###span###'] = $this->colCount;
					$markerArray['###header###'] = $this->pi_getLL('type_running').$t['plural'];
					$markerArray['###span###'] = $this->colCount;
					array_push($listArray,$this->cObj->substituteMarkerArray($list,$markerArray));
				}

				// finished workings
				$WHERE = ' AND list.type = '.$t['uid'].' AND FIND_IN_SET('.$uid.',list.organizer)';
				$WHERE .= " AND ((finished != '' AND finished != '0')  AND (abortet != '' OR abortet != '0')) ";
				$WHERE .= " AND list.deleted = '0' AND list.hidden = '0' ";
				
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('list.*,s.*,s.uid as sUid,list.uid as lUid','tx_x4equalificationgeneral_list list, tx_x4equalificationgeneral_student s','list.student = s.uid'.$WHERE,'',$this->getSorting());
				$listRows = $this->pi_list_makelistProfs($res);
				//t3lib_div::print_array($listRows);

				if($listRows['rows'] != ''){
					$markerArray = array();
					$list = $this->cObj->substituteSubpart($listRows['out'],'###rows###',$listRows['rows']);
					//fill header and colspan marker
					$markerArray['###span###'] = $this->colCount;
					$markerArray['###header###'] = $this->pi_getLL('type_terminated').$t['plural'];
					$markerArray['###span###'] = $this->colCount;
					array_push($listArray,$this->cObj->substituteMarkerArray($list,$markerArray));
				}
			}
			$markerArray['###profName###'] = $this->getNameOfProf($uid);
			$markerArray['###span###'] = $this->colCount;
			$tmp = $this->cObj->substituteSubpart($this->prof,'###rowSet###',implode($listArray));
			$professor = $this->cObj->substituteMarkerArray($tmp,$markerArray);
			$profList .= $professor;

			//$profs = $this->cObj->substituteMarkerArray($profList,$markerArray);
		}

		return $this->cObj->substituteSubpart($this->listTP,'###prof###',$profList);

	}

	/**
	 * [Put your description here]
	 */
	function getLimit() {
		// add limit parameter
		$from = $this->piVars['pointer'] * $this->internal['results_at_a_time'];
		$to = $this->internal['results_at_a_time'];
		return $from.','.$to;
	}

	/**
	 * [Put your description here]
	 */
	function getSorting() {
			// if sorting parameter is orgnizer
			// sort after name of fe_user
		if($this->piVars['sort'] == 'organizer:1'){
			return 'firstname ASC';
		}
		if($this->piVars['sort'] == 'organizer:0'){
			return 'firstname DESC';
		}
		if($this->piVars['sort'] == ''){
			return 'lastname ASC';
		} else {
			$sortArr = explode(':',$this->piVars['sort']);
			$sort = $sortArr[0];
			if($sortArr[1] == 0){
				$sort .= ' DESC';
			} else {
				$sort .= ' ASC';
			}
			return $sort;
		}
	}


	/**
	 * [Put your description here]
	 */
	function getNameOfProf($uid) {
		$pRecord = $this->pi_getRecord('tx_'.$this->persExtKey.'_person',intval($uid));
		if($pRecord['title'] != ''){
			$name = $pRecord['title'].' ';
		}
		if($pRecord['firstname'] != ''){
			$name .=	$pRecord['firstname'].' ';
		}
		if($pRecord['lastname'] != ''){
			$name .=	$pRecord['lastname'];
		}
		if($pRecord['title_after'] != ''){
			$name .=	', '.$pRecord['title_after'];
		}
		unset($pRecord);

		return $name;
	}


	/**
	 * [Put your description here]
	 */
	function generateSearchQuery() {

		$addWhere = '';
			// Generate additional where statement
		if (($this->piVars['status'] != '') && ($this->piVars['status'] != 'Status')) {
			if ($this->piVars['status'] == '0'){
				$addWhere .= " AND finished = '0'";
			}
			elseif ($this->piVars['status'] == '1'){
				$addWhere .= " AND finished != '0'";
			}
		}

		if (($this->piVars['year'] != '') && ($this->piVars['year'] != '0')){
			$addWhere .= 'AND finished = "'.$this->piVars['year'].'"';
		}

		if (($this->piVars['type'] != '') && ($this->piVars['type'] != 'Typ') && ($this->piVars['type'] != '-----')) {
			$addWhere .= ' AND type = '.$this->piVars['type'];
		}

		if ($this->piVars['sword'] != '') {
			$likeQuery2 = t3lib_div::trimExplode(' ',$this->piVars['sword']);
			foreach($likeQuery2 as $key => $val) {
				$likeQuery2[$key] = '(list.title LIKE "%'.$val.'%")';
			}
			unset($key,$val);
			$subQ1 = implode(' AND ',$likeQuery2);
			unset($likeQuery2);
			$addWhere .= ' AND '.$subQ1;
		}

		if ($this->piVars['organizerSearchWord'] != '') {
			$likeQuery = t3lib_div::trimExplode(' ',$this->piVars['organizerSearchWord']);
			foreach($likeQuery as $key => $val) {
				$likeQuery[$key] = '(lastname LIKE "%'.$val.'%" OR firstname LIKE "%'.$val.'%")';
			}
			unset($key,$val);
			$subQ1 = $GLOBALS['TYPO3_DB']->SELECTquery('uid','tx_'.$this->persExtKey.'_person',implode(' AND ',$likeQuery));
			unset($likeQuery);
			$addWhere .= ' AND organizer IN ('.$subQ1.')';
		}

		if ($this->piVars['authorSearchWord'] != '') {
				// make two subqueries => 1. get user with matching name/firstname, 2. get publications of this user
			$likeQuery = t3lib_div::trimExplode(' ',$this->piVars['authorSearchWord']);
			foreach($likeQuery as $key => $val) {
				$likeQuery[$key] = '(firstname LIKE "%'.$val.'%" OR lastname LIKE "%'.$val.'%")';
			}
			unset($key,$val);
			$subQ1 = $GLOBALS['TYPO3_DB']->SELECTquery('uid','tx_x4equalificationgeneral_student',implode(' AND ',$likeQuery));
			unset($likeQuery);
			$addWhere .= ' AND student IN ('.$subQ1.')';
		}
		$addWhere .= ' AND list.deleted="0" AND list.hidden = "0" ';

		return $addWhere;
	}

	/**
	 * Returns the link to the pdf-version
	 */
	function getPdfLink() {
			// don't show get pdf-link if in pdf mode
		if ($_GET['pdf']) {

			return 'Stand: '.strftime('%d.%m.%Y',time());
		} elseif($this->editMode) {
			return '';
		} else {
			$p = array();
			foreach($this->piVars as $k => $v) {
				$p['tx_x4equalificationgeneral_pi1['.$k.']'] = $v;
			}
			if (isset($_GET['tx_'.$this->extKey.'_pi1']['showUid'])) {
				$p['tx_'.$this->extKey.'_pi1[showUid]'] = $_GET['tx_'.$this->extKey.'_pi1']['showUid'];
			} else {
				$p['tx_x4equalificationgeneral_pi1[showAll]'] = 1;
			}
			$p['tx_x4equalificationgeneral_pi1[pdf]'] = 1;
			// add show all
			//$p['tx_publics_pi1[showAll]'] = 1;
			$p['type'] = 4445;
			//return $this->pi_linkTP($this->pi_getLL('pdfLinkText'),$p,0);
			return $this->pi_linkToPage($this->pi_getLL('pdfLinkText'),$GLOBALS['TSFE']->id,'_blank',$p);
		}
	}

	/**
	 * List header row, showing column names:
	 *
	 * @return	string		HTML content; a Table row, <tr>...</tr>
	 */
	function pi_list_header()	{
		$headT = $this->cObj->getSubpart($this->listT,'###headRow###');
		$cellT = $this->cObj->getSubpart($headT,'###cell###');
		$cells = '';
		foreach($this->manualFieldOrder_list as $fieldName)	{
			$cells .= $this->cObj->substituteMarker($cellT,'###content###',$this->getFieldHeader_sortLink($fieldName));
		}
		if (!$this->editMode) {
			$sub['###editCol###'] = '';
		}
		$sub['###cell###'] = $cells;
		return $this->cObj->substituteMarkerArrayCached($headT,array(),$sub);
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
    			$mArr['###selected###'] = 'selected="selected"';
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

		$years = $TCA['tx_x4equalificationgeneral_list']['columns']['finished']['config']['items'];

		$years[0][1] = '---';

		$year = date('Y');
		for($i = 1; $i < (2011 - intval($year)) ; $i++){
			unset($years[$i]);
		}

		//t3lib_div::debug($this->years);
		return $years;
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

    /**
     * Returns an array of the categories
     *
     * @author Markus Stauffiger (markus@4eyes.ch)
     */
    function getCategories() {
    	if (count($this->types) == 0) {
    		$this->types = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,name,plural','tx_'.$this->extKey.'_cat','1 '.$this->cObj->enableFields('tx_'.$this->extKey.'_cat'));
    	}

    	return $this->types;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/pi1/class.tx_x4equalificationgeneral_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/pi1/class.tx_x4equalificationgeneral_pi1.php']);
}

?>