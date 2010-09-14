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
 * Class/Function which manipulates the item-array for table/field tx_x4equalificationgeneral_list_abortet.
 *
 * @author	Andi Keller <andi@4eyes.ch>
 */



class tx_x4equalificationgeneralgeneral_tx_x4equalificationgeneral_list_years {
	function main(&$params,&$pObj=0)	{
/*		debug("Hello World!",1);
		debug("\$params:",1);
		debug($params);
		debug("\$pObj:",1);
		debug($pObj); */

		// Adding the items!
		$year = date('Y');
		for($i = $year; $i > 1995;$i--){
			$label = sprintf("%02d/%02d", $i,$i+1);
			//$label = $i.'/'.($i+1);
			$params['items'][] = array($label,$label);
		}
		//$params["items"][]=Array($pObj->sL("Added label by PHP function|Tilføjet Dansk tekst med PHP funktion"), 999);

		// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/class.tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/x4equalificationgeneral/class.tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years.php']);
}

?>