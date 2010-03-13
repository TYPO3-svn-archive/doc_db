<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// get extConf from extManager (ext_conf_template.txt)
$confArr = unserialize($GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] );


// add processing for selectbox of tables tx_docdb_[owner|type|status]
include_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'classes/class.tx_docdb_tceforms.php' );

include_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'classes/class.tx_docdb_itemsProcFunc.php' );

// class for displaying the category tree in BE forms.
include_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'classes/class.tx_docdb_treeview.php' );


$TCA[ 'tx_docdb_type' ] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_type',
		'label'     => 'type',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete'    => 'deleted',
		'default_sortby'    => 'ORDER BY type ASC',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) .'icon_tx_docdb_type.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden,type'
	)
);

$TCA[ 'tx_docdb_status' ] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_status',
		'label'     => 'status',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete'    => 'deleted',
		'default_sortby'    => 'ORDER BY status ASC',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . "icon_tx_docdb_status.gif",
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden,status'
	)
);

$TCA[ 'tx_docdb_owner' ] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_owner',
		'label'     => 'owner',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete'    => 'deleted',
		'default_sortby'    => 'ORDER BY owner ASC',
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_docdb_owner.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden,owner'
	)
);

$TCA[ 'tx_docdb_descriptor' ] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_descriptor',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete'    => 'deleted',
		'treeParentField'   => 'dscr_pid',
		'default_sortby'    => 'ORDER BY title',
		'dividers2tabs'     => 1,
		'enablecolumns'     => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_docdb_descriptor.gif',
		'treeViewInBrowseWindow' => 1,
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden,title, dscr_pid, dscr_related'
	)
);


$tempColumns = array(
	'tx_docdb_doc_type' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_type',
		'config'  => array(
			'type' => 'select',
			'items' => array(
				array('----------------------------------------------------------------------', 0 ),
			),
			'itemsProcFunc'       => 'tx_docdb_itemsProcFunc->sortItems',
			'foreign_table'       => 'tx_docdb_type',
			'foreign_table_where' => 'ORDER BY tx_docdb_type.type',
			'size'                => 1,
			'minitems'            => 1,
			'maxitems'            => 1,
		)
	),
	'tx_docdb_doc_status' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_status',
		'config'  => array(
			'type'  => 'select',
			'items' => array(
				array('----------------------------------------------------------------------', 0 ),
			),
			'itemsProcFunc'       => 'tx_docdb_itemsProcFunc->sortItems',
			'foreign_table'       => 'tx_docdb_status',
			'foreign_table_where' => 'ORDER BY tx_docdb_status.status',
			'size'                => 1,
			'minitems'            => 1,
			'maxitems'            => 1,
		)
	),
	'tx_docdb_doc_owner' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_owner',
		'config'  => array(
			'type' => 'select',
			'items' => array(
				array('----------------------------------------------------------------------', 0 ),
			),
			'itemsProcFunc'       => 'tx_docdb_itemsProcFunc->sortItems',
			'foreign_table'       => 'tx_docdb_owner',
			'foreign_table_where' => 'ORDER BY tx_docdb_owner.owner',
			'size'                => 1,
			'minitems'            => 1,
			'maxitems'            => 1,
		)
	),
	'tx_docdb_doc_key' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_key',
		'config'  => array(
			'type' => 'input',
			'size' => '30',
		)
	),
	'tx_docdb_doc_descriptor' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_descriptor',
		'config'  => array(
			'type'             => 'select',
			'foreign_field'    => 'dscr_pid',
			'form_type'        => 'user',
			'userFunc'         => 'tx_docdb_treeview->displayCategoryTree',
			'treeView'         => 1,
			'foreign_table'    => 'tx_docdb_descriptor',
			'foreign_selector' => 'tx_docdb_doc_descriptor',
			'size'             => 6,
			'autoSizeMax'      => 30,
			'minitems'         => 0,
			'maxitems'         => 500,
			'MM'               => 'tx_docdb_pages_doc_descriptor_mm',
			'wizards'          => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'_VALIGN'   => 'top',
				'add'       => array(
					'type'   => 'script',
					'title'  => 'LLL:EXT:tt_news/locallang_tca.php:tt_news.createNewCategory',
					'icon'   => 'add.gif',
					'params' => array(
						'table'    =>'tx_docdb_descriptor',
						'pid'      => $confArr[ 'dscrStoragePid' ],
						'setValue' => 'append'
					),
					'script' => 'wizard_add.php'
				),
				'edit' => array(
					'type'         => 'popup',
					'title'        => 'LLL:EXT:tt_news/locallang_tca.php:tt_news.editCategory',
					'script'       => 'wizard_edit.php',
					'icon'         => 'edit2.gif',
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					'popup_onlyOpenIfSelected' => 1
				),
				'search' => array(
					'type'         => 'popup',
					'title'        => 'Search descriptor',
					'script'       => 'EXT:doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php',
					'icon'         => 'zoom.gif',
					'JSopenParams' => 'height=360,width=780,status=0,menubar=0,scrollbars=1',
					'params' => array(
						'fieldName' => 'tx_docdb_doc_descriptor',
					),
					'popup_onlyOpenIfSelected' => 0
				)
			)
		)
	),
	'tx_docdb_doc_related_pages' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:pages.tx_docdb_doc_related_pages',
		'config' => array(
			'type'          => 'group',
			'internal_type' => 'db',
			'allowed'       => 'pages',
			'size'          => 6,
			'minitems'      => 0,
			'maxitems'      => 20,
			'MM'            => 'tx_docdb_pages_doc_related_pages_mm',
		)
	),
);


t3lib_div::loadTCA( 'pages' );

// Add the document type at 7th position in the select list for page types:
array_splice(
		$TCA[ 'pages' ][ 'columns' ][ 'doktype' ][ 'config' ][ 'items' ],
		7,
		0,
		array(
			array( 'LLL:EXT:doc_db/configuration/llang/locallang.xml:pages.doktype.I.198', '198' ),
		)
	);

t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );

// Add the fields for the document type
$TCA[ 'pages' ][ 'types' ][ '198' ] = array(
	'showitem' => 'hidden;;;;1-1-1, doktype;;2;button, title;;3;;3-3-3, subtitle, nav_hide, abstract;;5;;3-3-3, tx_docdb_doc_owner;;;;1-1-1, tx_docdb_doc_type, tx_docdb_doc_status, tx_docdb_doc_key, tx_docdb_doc_descriptor, tx_docdb_doc_related_pages, TSconfig;;6;nowrap;5-5-5, storage_pid;;7, l18n_cfg'
	);
	
// icon for pages type 198
$GLOBALS[ 'PAGES_TYPES' ][ '198' ][ 'icon' ] = '../' . t3lib_extMgm::siteRelPath( $_EXTKEY ) . 'icon_page.gif';



// Add plugin FlexForm
t3lib_div::loadTCA( 'tt_content' );
//$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key';

t3lib_extMgm::addPlugin( array(
        'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1',
        t3lib_extMgm::extRelPath( $_EXTKEY ) . 'ext_icon.gif'
    ), 'list_type'
);

// flexform in doc_db
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ] = 'pi_flexform';

t3lib_extMgm::addPiFlexFormValue( $_EXTKEY . '_pi1', 'FILE:EXT:doc_db/configuration/flexform/docDBflexform_ds.xml' );
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';

// add static TS
t3lib_extMgm::addStaticFile( $_EXTKEY, 'configuration/typoscript/', 'Document DB' );



if ( TYPO3_MODE == 'BE' ) {

// add context sensitive help for tca
t3lib_extMgm::addLLrefForTCAdescr( 'tx_docdb', 'EXT:doc_db/configuration/llang/locallang_csh_tx_docdb.xml' );

// add wizard for new CE
$TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_docdb_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_docdb_pi1_wizicon.php';

// add custom wizard to search descriptors in be forms
t3lib_extMgm::addModulePath( 'xMOD_txdocdbwizard', t3lib_extMgm::extPath( $_EXTKEY ) . 'wizard/' );

}

?>