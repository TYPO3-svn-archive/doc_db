<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$TCA['tx_docdb_type']  = array(
	'ctrl' => $TCA['tx_docdb_type']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,type'
	),
	'feInterface' => $TCA['tx_docdb_type']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'type' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_type.type',
			'config' => array(
					'type' => 'user',
					'userFunc' => 'tx_docdb_tceforms->inputRo',
				'size'       => '35',
				'eval'       => 'required,unique'
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'type;;;;1-1-1')
	),
	'palettes' => array(
		'1' => array('showitem' => 'hidden')
	)
);



$TCA['tx_docdb_status'] = array(
	'ctrl' => $TCA['tx_docdb_status']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => "hidden,status"
	),
	'feInterface' => $TCA['tx_docdb_status']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'status' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_status.status',
			'config'  => array(
				'type'     => 'user',
				'userFunc' => 'tx_docdb_tceforms->inputRo',
				'size'     => '30',
				'eval'     => 'required,unique'
			)
		),
	),
	'types' => array(
		'0' => array("showitem" => "status;;;;1-1-1")
	),
	'palettes' => array(
		'1' => array("showitem" => "")
	)
);

$TCA['tx_docdb_owner'] = array(
	'ctrl' => $TCA['tx_docdb_owner']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => "hidden,owner"
	),
	'feInterface' => $TCA['tx_docdb_owner']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'owner' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_owner.owner',
			'config' => array(
				'type'     => 'user',
				'userFunc' => 'tx_docdb_tceforms->inputRo',
				'size'     => '45',
				'eval'     => 'required,unique'
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'owner;;;;1-1-1')
	),
	'palettes' => array(
		'1' => array('showitem' => 'hidden')
	)
);

$TCA['tx_docdb_descriptor'] = array(
	'ctrl' => $TCA['tx_docdb_descriptor']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => "hidden,title,dscr_pid,dscr_related"
	),
	'feInterface' => $TCA['tx_docdb_descriptor']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_descriptor.title',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'eval' => 'required',
			)
		),
		'dscr_pid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_descriptor.dscr_pid',
			'config' => array(
				'type'               => 'select',
				'foreign_table'      => 'tx_docdb_descriptor',
				'foreign_selector'   => 'dscr_pid',
				'prepend_tname'      => 0,
				'form_type'          => 'user',
				'userFunc'           => 'tx_docdb_treeview->displayCategoryTree',
				'treeView'           => 1,
				'size'               => 1,
				'autoSizeMax'        => 30,
				'minitems'           => 0,
				'maxitems'           => 2,
				'wizards'            => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'_VALIGN'   => 'top',
				'search' => array(
					'type'         => 'popup',
					'title'        => 'Search descriptor',
					'script'       => 'EXT:doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php',
					'icon'         => 'zoom.gif',
					'JSopenParams' => 'height=360,width=780,status=0,menubar=0,scrollbars=1',
					'params' => array(
						'fieldName' => 'dscr_pid',
					),
					'popup_onlyOpenIfSelected' => 0
				)
			)
			)
		),
		'dscr_related' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_descriptor.dscr_related',
			'config'  => array(
				'type'             => 'select',
				'foreign_table'    => 'tx_docdb_descriptor',
				'foreign_selector' => 'dscr_related',
				'prepend_tname'    => 0,
				'form_type'        => 'user',
				'userFunc'         => 'tx_docdb_treeview->displayCategoryTree',
				'treeView'         => 1,
				'size'             => 5,
				'autoSizeMax'      => 30,
				'minitems'         => 0,
				'maxitems'         => 10,
				'wizards'          => array(
				'_PADDING'  => 2,
				'_VERTICAL' => 1,
				'_VALIGN'   => 'top',
				'search' => array(
					'type'         => 'popup',
					'title'        => 'Search descriptor',
					'script'       => 'EXT:doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php',
					'icon'         => 'zoom.gif',
					'JSopenParams' => 'height=360,width=780,status=0,menubar=0,scrollbars=1',
					'params' => array(
						'fieldName' => 'dscr_related',
					),
					'popup_onlyOpenIfSelected' => 0
				)
			)
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'title;;1;;1-1-1,
										dscr_pid,
										--div--;LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_descriptor.dscr_related,
										dscr_related'
		)
	),
	'palettes' => array(
		'1' => array('showitem' => 'hidden')
	)
);


//$TCA['tx_docdb_xmllink']  = array(
//	'ctrl' => $TCA['tx_docdb_xmllink']['ctrl'],
//	'interface' => array(
//		'showRecordFieldList' => 'type'
//	),
//	'feInterface' => $TCA['tx_docdb_type']['feInterface'],
//	'columns' => array(
//        'pId' => array(
//			'label'   => 'page id',
//			'config' => array(
//					'type' => 'passthrough'
//			)
//		),
//		'params' => array(
//			'label'   => 'params',
//			'config' => array(
//					'type' => 'passthrough'
//			)
//		),
//        'hash' => array(
//			'exclude' => 1,
//			'label'   => 'hash',
//			'config'  => array(
//				'type'    => 'passthrough'
//			)
//		),
//	),
//	'types' => array(
//		'0' => array('showitem' => 'type;;;;1-1-1')
//	),
//	'palettes' => array(
//		'1' => array('showitem' => 'pId,params,hash')
//	)
//);
?>