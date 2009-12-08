<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TYPO3_CONF_VARS[ 'FE' ][ 'eID_include' ][ 'docdb' ] ='EXT:doc_db/classes/controller/class.tx_docdb_extjsdirect_controller.php';

t3lib_extMgm::addUserTSConfig('
options.saveDocNew {
	tx_docdb_type   = 1
	tx_docdb_status = 1
	tx_docdb_owner  = 1
}
');


t3lib_extMgm::addPItoST43(
	$_EXTKEY,
	'pi1/class.tx_docdb_pi1.php',
	'_pi1',
	'list_type'
	,0
);


/**
* Register hooks in tcemain:
*/
// this hook is used to prevent saving default value for fields tx_docdb_doc_owner,tx_docdb_doc_type,tx_docdb_doc_status from table pages
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SC_OPTIONS' ][ 't3lib/class.t3lib_tcemain.php' ][ 'processDatamapClass' ][] = 'EXT:' .
     $_EXTKEY . '/classes/class.tx_docdb_tcemain.php:tx_docdb_tceMain';

// this hook is used when update, copy, delete
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SC_OPTIONS' ][ 't3lib/class.t3lib_tcemain.php' ][ 'processCmdmapClass' ][] = 'EXT:' .
     $_EXTKEY . '/classes/class.tx_docdb_tcemain.php:tx_docdb_tceMain_cmdMap';

// xclass class.tx_jwextjsdirect_sv1.php to modify a little bit the render  of the header of page.
$TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/jw_extjsdirect/sv1/class.tx_jwextjsdirect_sv1.php' ] =
     PATH_typo3conf . 'ext/doc_db/classes/class.ux_tx_jwextjsdirect_sv1.php';
     
?>