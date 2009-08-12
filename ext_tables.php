<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['doc_db']);

$TCA["tx_docdb_type"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_type",		
		"label" => "type",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY type",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_docdb_type.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "type",
	)
);

$TCA["tx_docdb_status"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_status",		
		"label" => "statuts",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY statuts",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_docdb_status.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "statuts",
	)
);

$TCA["tx_docdb_owner"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_owner",		
		"label" => "owner",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY owner",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_docdb_owner.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "owner",
	)
);

$TCA["tx_docdb_descriptor"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:doc_db/locallang_db.php:tx_docdb_descriptor",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"treeParentField" => "dscr_pid",
		"default_sortby" => "ORDER BY title",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_docdb_descriptor.gif",
		"treeViewInBrowseWindow" => 1,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "title, dscr_pid, dscr_related",
	)
);

$tempColumns = Array (
	"tx_docdb_doc_type" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_type",		
		"config" => Array (
			"type" => "select",	
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "tx_docdb_type",	
			"foreign_table_where" => "ORDER BY tx_docdb_type.type",	
			"size" => 1,	
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_docdb_doc_status" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_status",		
		"config" => Array (
			"type" => "select",	
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "tx_docdb_status",	
			"foreign_table_where" => "ORDER BY tx_docdb_status.statuts",	
			"size" => 1,	
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_docdb_doc_owner" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_owner",		
		"config" => Array (
			"type" => "select",	
			"items" => Array (
				Array("",0),
			),
			"foreign_table" => "tx_docdb_owner",	
			"foreign_table_where" => "ORDER BY tx_docdb_owner.owner",	
			"size" => 1,	
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_docdb_doc_key" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_key",		
		"config" => Array (
			"type" => "input",	
			"size" => "30",
		)
	),

	"tx_docdb_doc_descriptor" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_descriptor",		
		/* "config" => Array (
			"type" => "group",    
            		"internal_type" => "db",    
		         "allowed" => "tx_docdb_descriptor",    
            		"size" => 5,    
            		"minitems" => 0,
            		"maxitems" => 20,    
            		"MM" => "pages_tx_docdb_doc_descriptor_mm",
		) */
		
		'config' => Array (
			'type' => 'select',
			'foreign_field' => 'dscr_pid',
			'form_type' => 'user',
			'userFunc' => 'tx_docdb_treeview->displayCategoryTree',
			'treeView' => 1,
			'foreign_table' => 'tx_docdb_descriptor',
			/* 'foreign_table_where' => ' ORDER BY tx_docdb_descriptor.title', */
                  		'size' => 3,
			'autoSizeMax' => '30',
			'minitems' => 0,
			'maxitems' => 500,
			'MM' => 'pages_tx_docdb_doc_descriptor_mm', 
			'wizards' => Array(
				'_PADDING' => 2,
				'_VERTICAL' => 1,
				'add' => Array(
					'type' => 'script',
					'title' => 'LLL:EXT:tt_news/locallang_tca.php:tt_news.createNewCategory',
					'icon' => 'EXT:tt_news/res/add_cat.gif',
					'params' => Array(
						'table'=>'tx_docdb_descriptor',
						// **OS Get categories storage page from ext configuration
						'pid' => $confArr['catStoragePid'],
						'setValue' => 'append'
					),
					'script' => 'wizard_add.php',
				),
				'edit' => Array(
					'type' => 'popup',
					'title' => 'LLL:EXT:tt_news/locallang_tca.php:tt_news.editCategory',
					'script' => 'wizard_edit.php',
					'popup_onlyOpenIfSelected' => 1,
					'icon' => 'edit2.gif',
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
				'search' => Array(
				      // **OS Search keyword
					'type' => 'popup',
					'title' => 'Search descriptor',
					'script' => 'EXT:doc_db/pi1/wizard_search_descriptor.php',
					'popup_onlyOpenIfSelected' => 0,
					'params' => Array(
						'fieldName' => 'tx_docdb_doc_descriptor',
					),
					'icon' => 'zoom.gif',
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
			),
           ),
	),
	"tx_docdb_doc_related_pages" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:doc_db/locallang_db.php:pages.tx_docdb_doc_related_pages",		
		"config" => Array (
			"type" => "group",	
			//"foreign_table" => "pages",	
			"internal_type" => "db",    
            "allowed" => "pages",
			//"foreign_table_where" => "ORDER BY pages.uid",	
			"size" => 3,	
			"minitems" => 0,
			"maxitems" => 20,	
			"MM" => "pages_tx_docdb_doc_related_pages_mm",
		)
	),
);


t3lib_div::loadTCA("pages");

// Add the document type at 7th position in the selet list for page types:
array_splice(
		$TCA['pages']['columns']['doktype']['config']['items'],
		7,
		0,
		Array(
			Array('LLL:EXT:doc_db/locallang.php:pages.doktype.I.198', '198'),
		)
	);

t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);

// Add the fields for the document type
$TCA['pages']['types']['198']= Array('showitem' => 'hidden;;;;1-1-1, doktype;;2;button, title;;3;;3-3-3, subtitle, nav_hide, abstract;;5;;3-3-3, tx_docdb_doc_type;;;;1-1-1, tx_docdb_doc_status, tx_docdb_doc_owner, tx_docdb_doc_key, tx_docdb_doc_descriptor, tx_docdb_doc_related_pages, TSconfig;;6;nowrap;5-5-5, storage_pid;;7, l18n_cfg');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:doc_db/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Document DB');


// New class for displaying descriptors tree 
//if (t3lib_extMgm::isLoaded('dam')) {
//	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_SC_browse_links.php'] = t3lib_extMgm::extPath($_EXTKEY).'class.ux_browse_links.php';
//} else {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/browse_links.php'] = t3lib_extMgm::extPath($_EXTKEY).'class.ux_browse_links.php';
//}

// class for displaying the category tree in BE forms.
include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_docdb_treeview.php');

// flexform in doc_db
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:doc_db/docDBflexform_ds.xml');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';


/*if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txdocdbM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}  */
?>