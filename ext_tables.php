<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=="BE"){
	include_once(t3lib_extMgm::extPath("x4equalificationgeneral")."class.tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years.php");
	include_once(t3lib_extMgm::extPath("x4equalificationgeneral")."class.tx_x4equalificationgeneral_tx_x4equalificationgeneral_tca_proc.php");
}

if (TYPO3_MODE=="BE")    {

    t3lib_extMgm::addModule("web","txx4equalificationgeneralM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}

$TCA["tx_x4equalificationgeneral_list"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4equalificationgeneral_list.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, organizer, title, type, finished, abstract, pictures, abortet, student",
	)
);

$TCA["tx_x4equalificationgeneral_student"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student",
		"label" => "lastname",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY lastname",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4equalificationgeneral_student.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, firstname, lastname, address, phone, email",
	)
);

$TCA["tx_x4equalificationgeneral_cat"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_cat",
		"label" => "name",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_x4equalificationgeneral_student.gif",
	),

	"feInterface" => Array (
		"fe_admin_fieldList" => "name",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(Array('LLL:EXT:x4equalificationgeneral/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';


t3lib_extMgm::addPlugin(Array('LLL:EXT:x4equalificationgeneral/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Qualification workings');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Qualification workings entry");
// flexform stuff
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:x4equalificationgeneral/pi1/flexform_ds_pi1.xml');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:x4equalificationgeneral/pi2/flexform_ds_pi2.xml');


?>