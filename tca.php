<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
#$persExtKey = 'x4ekunsthistpersdb';
$persExtKey = 'x4epersdb';
$persSysFolderUid = 21381;
$TCA["tx_x4equalificationgeneral_list"] = Array (
	"ctrl" => $TCA["tx_x4equalificationgeneral_list"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,organizer,title,type,abstract,pictures,finished,abortet,student"
	),
	"feInterface" => $TCA["tx_x4equalificationgeneral_list"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"organizer" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.organizer",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_".$persExtKey."_person",
				/* "foreign_table_where" => "AND tx_".$persExtKey."_person.pid = ".$persSysFolderUid." ORDER BY lastname, firstname", */
				"foreign_table_where" => "AND tx_".$persExtKey."_person.pid = ###PAGE_TSCONFIG_ID### ORDER BY lastname, firstname",
				"size" => 8,
				"minitems" => 1,
				"maxitems" => 5,
				"itemsProcFunc" => "tx_x4equalificationgeneral_tx_x4equalificationgeneral_tca_proc->main",
				"eval" => "required",
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"type" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.type",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_x4equalificationgeneral_cat",
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"abstract" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.abstract",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"pictures" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.pictures",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],
				"max_size" => 500,
				"uploadfolder" => "uploads/tx_x4equalificationgeneral",
				"show_thumbs" => 1,
				"size" => 2,
				"minitems" => 0,
				"maxitems" => 2,
			)
		),
		"finished" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.finished",
			"config" => Array (
				"type" => "select",
				/*"items" => Array (
					Array("---", ""),
				),*/
				"items" => Array (
					Array("0","0"),
					Array("2010/2011", "2010/2011"),
					Array("2009/2010", "2009/2010"),
					Array("2008/2009", "2008/2009"),
					Array("2007/2008", "2007/2008"),
					Array("2006/2007", "2006/2007"),
					Array("2005/2006", "2005/2006"),
					Array("2004/2005", "2004/2005"),
					Array("2003/2004", "2003/2004"),
					Array("2002/2003", "2002/2003"),
					Array("2001/2002", "2001/2002"),
					Array("2000/2001", "2000/2001"),
					Array("1999/2000", "1999/2000"),
					Array("1998/1999", "1998/1999"),
					Array("1997/1998", "1997/1998"),
					Array("1996/1997", "1996/1997"),
					Array("1995/1996", "1995/1996"),
				),
				//"itemsProcFunc" => "tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years->main",
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"abortet" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.abortet",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("0", "0"),
					Array("2010/2011", "2010/2011"),
					Array("2009/2010", "2009/2010"),
					Array("2008/2009", "2008/2009"),
					Array("2007/2008", "2007/2008"),
					Array("2006/2007", "2006/2007"),
					Array("2005/2006", "2005/2006"),
					Array("2004/2005", "2004/2005"),
					Array("2003/2004", "2003/2004"),
					Array("2002/2003", "2002/2003"),
					Array("2001/2002", "2001/2002"),
					Array("2000/2001", "2000/2001"),
					Array("1999/2000", "1999/2000"),
					Array("1998/1999", "1998/1999"),
					Array("1997/1998", "1997/1998"),
					Array("1996/1997", "1996/1997"),
					Array("1995/1996", "1995/1996"),
				),
				//"itemsProcFunc" => "tx_x4equalificationgeneral_tx_x4equalificationgeneral_list_years->main",
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"student" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_list.student",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_x4equalificationgeneral_student",
				"foreign_table_where" => "ORDER BY tx_x4equalificationgeneral_student.lastname",
				"size" => 7,
				"minitems" => 1,
				"maxitems" => 5,
				"eval" => "required",
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_x4equalificationgeneral_student",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "List",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tx_x4equalificationgeneral_student",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, organizer, title;;;;2-2-2, type;;;;3-3-3, abstract;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], pictures, finished, abortet, student")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_x4equalificationgeneral_student"] = Array (
	"ctrl" => $TCA["tx_x4equalificationgeneral_student"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,name,address,phone,email"
	),
	"feInterface" => $TCA["tx_x4equalificationgeneral_student"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"firstname" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student.firstname",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"lastname" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student.lastname",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"address" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student.address",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "4",
			)
		),
		"phone" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student.phone",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"email" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_student.email",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, firstname, lastname, address, phone, email")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);


$TCA["tx_x4equalificationgeneral_cat"] = Array (
	"ctrl" => $TCA["tx_x4equalificationgeneral_cat"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "name"
	),
	"feInterface" => $TCA["tx_x4equalificationgeneral_cat"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_cat.name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"plural" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:x4equalificationgeneral/locallang_db.php:tx_x4equalificationgeneral_cat.plural",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "name,plural")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

?>