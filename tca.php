<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_docdb_type"] = Array (
	"ctrl" => $TCA["tx_docdb_type"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "type"
	),
	"feInterface" => $TCA["tx_docdb_type"]["feInterface"],
	"columns" => Array (
		"type" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_type.type",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "type;;;;1-1-1")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_docdb_status"] = Array (
	"ctrl" => $TCA["tx_docdb_status"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "statuts"
	),
	"feInterface" => $TCA["tx_docdb_status"]["feInterface"],
	"columns" => Array (
		"statuts" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_status.statuts",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "statuts;;;;1-1-1")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA["tx_docdb_owner"] = Array (
	"ctrl" => $TCA["tx_docdb_owner"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "owner"
	),
	"feInterface" => $TCA["tx_docdb_owner"]["feInterface"],
	"columns" => Array (
		"owner" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_owner.owner",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "owner;;;;1-1-1")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA["tx_docdb_descriptor"] = Array (
	"ctrl" => $TCA["tx_docdb_descriptor"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "title,dscr_pid,dscr_related"
	),
	"feInterface" => $TCA["tx_docdb_descriptor"]["feInterface"],
	"columns" => Array (
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_descriptor.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "50",
				"eval" => "required",
			)
		),
		"dscr_id" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_descriptor.dscr_id",		
			"config" => Array (
				"type" => "none",	
				"size" => "30",	
				"eval" => "int",
			)
		),
		"dscr_pid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_descriptor.dscr_pid",		
			/* "config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_docdb_descriptor",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			) */
			'config' => Array (
			   'type' => 'select',
			   'foreign_table' => 'tx_docdb_descriptor',
			   'foreign_selector' => 'dscr_pid',
			   'foreign_unique' => 'dscr_pid',
			   'form_type' => 'user',
			   'userFunc' => 'tx_docdb_treeview->displayCategoryTree',
		    	   'treeView' => 1,
			   'size' => 3,
			   'autoSizeMax' => '30',
			   'minitems' => 0,
			   'maxitems' => 2,
                  ),
		),
		"dscr_related" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_descriptor.dscr_related",		
			/*"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_docdb_descriptor",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)    */
			'config' => Array (
			   'type' => 'select',
			   'foreign_table' => 'tx_docdb_descriptor',
			   'foreign_selector' => 'dscr_related',
			   'foreign_unique' => 'dscr_related',
			   'form_type' => 'user',
			   'userFunc' => 'tx_docdb_treeview->displayCategoryTree',
		    	   'treeView' => 1,
			   'size' => 3,
			   'autoSizeMax' => '30',
			   'minitems' => 0,
			   'maxitems' => 2,
                  ),
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "title;;;;1-1-1, dscr_pid, dscr_related")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>
