<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 cherpit laurent <laurent@eosgarden.com>
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
 * Class/Function for updating the extension from older versions or fresh install
 * 
 * $Id: class.ext_update.php 162 2009-12-08 16:33:32Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-08 17:33:32 +0100 (mar 08 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */

//require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );

//$tce = t3lib_div::makeInstance( 't3lib_TCEmain' );
//$tce->clear_cacheCmd( $GLOBALS['TSFE']->id );

class ext_update
{
	
	/**
	 * New line character
	 * 
	 * @var
	 */
    protected $_NL     = '';
	
	/**
	 * Instance of the TYPO3 document class
	 * 
	 * @var $_doc
	 */
	private $_doc      = NULL;
		
	/**
	 * Back path
	 * 
	 * @var $_backPath
	 */
	private $_backPath = '';
	
	/**
	 * extension configuration array
	 * 
	 * @var $_extConf
	 */
	private $_extConf  = array();

	/**
	 * columns of tables tx_docdb_status and tx_docdb_type
	 * 
	 * @var $_tableFieldsDef
	 */
	private $_tableFieldsDef = array();
	
	/**
	 * pid of doc def ok
	 * 
	 * @var
	 */
	private static $_docDefPidOk     = TRUE;
	
	/**
	 * test if new table created
	 * 
	 * @var
	 */
	private static $_newTablesOk     = TRUE;
	
	/**
	 * 
	 * 
	 * @var
	 */
	private static $_defaultValuesOK = TRUE;
	
	private static $_oldTablesName   = array();

    private static $_bkpPrefix = '';
	
	
	/**
	 * Class constructor
	 *
	 * @return  void
	 */
	public function __construct() {
		
		// Sets the back path
		$this->_backPath = $GLOBALS[ 'BACK_PATH' ];
		
		// ext_conf_template.txt array
		$this->_extConf  = unserialize($GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] );
		
		// New instance of the TYPO3 document class
		$this->_doc      = t3lib_div::makeInstance( 'bigDoc' );
		
		// columns of tables type and status
		/*
		 * NOT needed
		$this->_tableFieldsDef[ 'tx_docdb_type' ]   = $GLOBALS[ 'TYPO3_DB' ]->admin_get_fields( 'tx_docdb_type' );
		*/
		$this->_tableFieldsDef[ 'tx_docdb_status' ] = $GLOBALS[ 'TYPO3_DB' ]->admin_get_fields( 'tx_docdb_status' );

        self::$_bkpPrefix = '_' . date( 'ymdHis' );

		// Sets the new line character
		$this->_NL = chr( 10 );
	}
	
	
	/**
	 * Checks if an update is needed.
	 *
	 * This function check if default values exists in tables owner,type and status
	 * and if mm tablesName follows to T3 CGL and need to be updated
	 * to avoid table name errors msg in ext. manager
	 * (this function is called from the extension manager)
	 *
	 * @return  boolean
	 */
	public function access() {
		
		// exec rename field statuts to status in ext_tables.sql
		$this->_checkTableFieldsToRename();
		
		// run init default val by default
		$this->_checkAndInitDefaultNoneVal();
        
		// Checks if old bkp tables exists
		$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( "SHOW TABLES LIKE 'zzz_deleted%pages_tx_docdb_%_mm'" );
		$cpt=0;
		while( ( $_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_row( $_res ) ) ) {
			
			$cpt++;
		
		} // end while sql_fetch_row
		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
		
		if( $cpt > 0 ) {
			$bkpTablesPrefix = self::$_bkpPrefix;
		}
		
		// Checks tables old tables names from old version of doc_db extension
		$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( "SHOW TABLES LIKE 'pages_tx_docdb_doc_%_mm'" );
		
		while( ( $_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_row( $_res ) ) ) {
			
			// check numbers of records
			$_res1 = $GLOBALS[ 'TYPO3_DB' ]->sql_query(
				'SELECT COUNT(' . $_row[ 0 ] . '.uid_local) AS count from ' . $_row[ 0 ] .
				' WHERE 1=1'
			);
			
			$_row1 = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res1 );
			
			self::$_oldTablesName[ $_row[ 0 ] ] = array(
				'nbRec'     => $_row1[ 'count' ],
				'NewName'   => str_replace( 'pages_tx_docdb_', 'tx_docdb_pages_', $_row[ 0 ] ),
				'oldRename' => str_replace( 'pages_tx_docdb_', 'zzz_deleted' . $bkpTablesPrefix . '_pages_tx_docdb_', $_row[ 0 ] )
				
			);
			
			$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res1 );
			
		
		} // end while sql_fetch_row
		
		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
		
		
		// Checks old table already dropped 
		if( count( self::$_oldTablesName ) < 1 ) {
			
			// No update needed
			return FALSE;
		
		} else {
			
			// check if new tables exists and display error if not. (but this should not happen)
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( "SHOW TABLES LIKE 'tx_docdb_pages_doc_%_mm'" );
			
			$newTable = array();
			while( ( $_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_row( $_res ) ) ) {
			
				$newTable[] = $_row[0];
			}
		
			$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
			
			if( count( $newTable ) < 2 ) {
				
				self::$_newTablesOk = FALSE;
			}
		}
        
		// call update
		return TRUE;
	
	}
	
	
	/**
	 * Update extension
	 *
	 *
	 * @return  The content of the class
	 */
	public function main() {
		
		
		if( t3lib_div::_POST( 'tx_docdb_update_tablesname' ) ) {
			// Update
			$_res = $this->_updateTablesName();
			
			// error
			if( $_res === 'BKP_ERROR' || $_res === 'IMPORT_ERROR' || $_res === 'TABLE_EXISTS_ERROR' ) {
				
				return $this->_error( $_res );
				
			}
			
			// well done
			return $_res;
			
		} else {
			
			// new tables not exists
			if( ! self::$_newTablesOk ) {
				
				return $this->_sayCreateNewTables();
			
			} else {
			
				// call form list table to update
				return $this->_listTables();
			}
		}
	}
	
	
	/**
	 * 
	 * 
	 * @return msg you must create new table before update 
	 */
	protected function _sayCreateNewTables() {
		
		// Storage
		$htmlCode = array();
		
		// Infos
		$htmlCode[] = '<div>' . '<img ' . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_warning2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;' . '<strong>Important:</strong><br />';
		$htmlCode[] = 'Before, you must select information select option and click update button to create the required new tables !<br />';
		$htmlCode[] = 'And after you must coming back here to process update of the new version of the "doc_db" extension.<br />';
		$htmlCode[] = 'Please click on the select button information above.<br />' . '</div>';
		
		// Return content
		return implode( $this->_NL, $htmlCode );
	}
	
	
	/**
	 * 
	 * @return form with tables list to be renamed
	 */
	protected function _listTables() {
		
		// Storage
		$htmlCode = array();
		
		// Starts the form
		$htmlCode[] = '<form
									action="' . t3lib_div::linkThisScript() . '"
									method="post"
									name="updateTablesName"
									id="updateTablesName">';
		
		$htmlCode[] = '<input
									name="tx_docdb_update_tablesname"
									id="tx_docdb_update_tablesname"
									type="hidden"
									value="true" />';
		
		// Infos
		$htmlCode[] = '<div>' . '<img ' . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_note.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;' . '<strong>Note:</strong><br />';
		$htmlCode[] = '<strong>Before update, it\'s always a good choice to make a backup of the database and of the extension</strong><br /><br />';
		$htmlCode[] = 'The following db tables need to be updated in order to be compatible with the new version of the "doc_db" extension.<br />';
		$htmlCode[] = 'All data will be copied in the new tables and old tables will be backuped and if backup tables already exists, new bkp tables will be created.<br />';
		$htmlCode[] = 'If you have some SQL queries related (ex. in pageTS or tmplTS) to those tables, the tablenames must be adapted<br /><br />';
		$htmlCode[] = 'Please click on the button below to start the update process.<br />' . '</div>';
		
		// Divider
		$htmlCode[] = $this->_doc->spacer( 10 );
		
		// Divider
		$htmlCode[] = $this->_doc->spacer( 10 );
		$htmlCode[] = $this->_doc->divider( 5 );
		$htmlCode[] = $this->_doc->spacer( 10 );
		
		// Starts the table
		$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->_doc->bgColor2 . '">';
		
		// Table headers
		$htmlCode[] = '<tr>';
		$htmlCode[] = '<th align="left" valign="middle"></td>';
		$htmlCode[] = '<th align="left" valign="middle"><strong>Nb of record-s:</strong></td>';
		$htmlCode[] = '<th align="left" valign="middle"><strong>Old table name:</strong></td>';
		$htmlCode[] = '<th align="left" valign="middle"><strong>New table name:</strong></td>';
		$htmlCode[] = '<th align="left" valign="middle"><strong>Old table renamed to:</strong></td>';
		$htmlCode[] = '</tr>';
		
		// Counter
		$counter = 0;
		
		// Process each table
		foreach( self::$_oldTablesName as $name => $val ) {
			
			// Row color
			$color = ( ( $counter++ % 2 )===0 ) ? $this->_doc->bgColor3 : $this->_doc->bgColor4;
			
			// Starts the row
			$htmlCode[] = '<tr bgcolor="' . $color . '">';
			
			// tables name rename info
			$htmlCode[] = '<td align="left" valign="middle">' . $counter . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val[ 'nbRec' ] . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $name . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val[ 'NewName' ] . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val[ 'oldRename' ] . '</td>';
			
			// Ends the row
			$htmlCode[] = '</tr>';
			
		}
		
		// Ends the table
		$htmlCode[] = '</table>';
		
		// Divider
		$htmlCode[] = $this->_doc->spacer( 10 );
		$htmlCode[] = $this->_doc->divider( 5 );
		$htmlCode[] = $this->_doc->spacer( 10 );
		
		// Submit button
		$htmlCode[] = '<div><input name="submit" type="submit" value="Copy data in the new tables and rename old tables to zzz_*" /></div>';
		
		// Ends the form
		$htmlCode[] = '</form>';
		
		// Return content
		return implode( $this->_NL, $htmlCode );

	}
    

	protected function _updateTablesName() {
		
		// result of backup
		$_affected = array();
		// backup old table
		foreach( self::$_oldTablesName as $oldname => $val ) {
			
			// ////drop and create bkp table//// changed: no drop but use new backup name to avoid backup overwrite
			//$GLOBALS[ 'TYPO3_DB' ]->sql_query( 'DROP TABLE IF EXISTS ' . $val[ 'oldRename' ] );
			$_query = 'CREATE TABLE ' . $val[ 'oldRename' ] . ' LIKE ' . $oldname;
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( $_query );
			
			// fill backup table
			$_query = 'INSERT INTO ' . $val[ 'oldRename' ] . 
			'(uid_local,uid_foreign,tablenames,sorting)' .
			' SELECT uid_local,uid_foreign,tablenames,sorting' .
			' FROM ' . $oldname . ' WHERE 1=1';
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( $_query );

			
			$_affected[ $val[ 'oldRename' ] ] = $GLOBALS[ 'TYPO3_DB' ]->sql_affected_rows( $_res );
		}
		
		foreach( self::$_oldTablesName as $oldname => $val ) {
			
			if( (int)$_affected[ $val[ 'oldRename' ] ] !== (int)$val[ 'nbRec' ] ) {
				
				return 'BKP_ERROR';
			}
		}
		
		
		// check if new table contains data
		foreach( self::$_oldTablesName as $oldname => $val ){
			
			$_query = 'SELECT COUNT(uid_local) AS count FROM ' . $val[ 'NewName' ];
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( $_query );
			
			$_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res );
			
			//free
			$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
			
			if( $_row[ 'count' ] > 0 ) {
				
				return 'TABLE_EXISTS_ERROR';
			}
		}
		
		
		// process update
		$_affected = array();
		
		foreach( self::$_oldTablesName as $oldname => $val ){
			
			$_query = 'INSERT INTO ' . $val[ 'NewName' ] .
			'(uid_local,uid_foreign,tablenames,sorting)' .
			' SELECT uid_local,uid_foreign,tablenames,sorting' .
			' FROM ' . $oldname . ' WHERE 1=1';
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query( $_query );
			
			$_affected[ $val[ 'NewName' ] ] = $GLOBALS[ 'TYPO3_DB' ]->sql_affected_rows( $_res );
		}
		
		foreach( self::$_oldTablesName as $oldname => $val ) {
			
			// check if number of affected rows -neq to nb rows to be processed.
			if( (int)$_affected[ $val[ 'NewName' ] ] !== (int)$val[ 'nbRec' ] ) {
				
				return 'IMPORT_ERROR';
				
			} else {
				
				$_query = 'DROP TABLE IF EXISTS ' . $oldname;
			
				$GLOBALS[ 'TYPO3_DB' ]->sql_query( $_query );
				
			}
		}
		
		
		// Storage
		$htmlCode = array();
		
		// Confirmation message
		$htmlCode[] = '<div>' . '<img ' . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;' . '<strong>Success:</strong>The records were successfully updated.<br />';
		
		$htmlCode[] = '<table cellpadding="2" cellspacing="0">';
		$htmlCode[] = '<tr><td>The update option won\'t appear anymore in the extension manager.</td></tr>';
        $htmlCode[] = '<tr><td>You should need to check the Database Analyser from TYPO3 Install Tool and click COMPARE. and remove the zzz_ tables.</td></tr>';
		$htmlCode[] = '</table>';
		
		// Divider
		$htmlCode[] = $this->_doc->spacer( 10 );
		$htmlCode[] = $this->_doc->divider( 5 );
		$htmlCode[] = $this->_doc->spacer( 10 );
		
		$htmlCode[] ='<div>Thank you for using the "Document database (doc_db)" extension.' . '</div>';
		
		
		// Return content
		return implode( $this->_NL, $htmlCode );
	}
	
	
	private function _error( $type ) {
		
		$icon = '<img ' . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_warning2.gif', '' ) .' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;';
		
		// backup table error
			if( $type === 'BKP_ERROR' ) {
				
				return $icon . '<strong>Bakup error</strong>:<br /> You sould try to make the backup of the next tables manually:<br /><br />' .
							'';
				
			} else if( $type === 'IMPORT_ERROR' ) {
				
				return $icon .'<strong>Import error</strong>:<br /> You sould try to execute the next sql query manually:<br /><br />' .
							 '<pre>INSERT INTO <br />tx_docdb_pages_doc_descriptor_mm (`uid_local`, `uid_foreign`, `tablenames`, `sorting`)<br />SELECT `uid_local`, `uid_foreign`, `tablenames`, `sorting` FROM `pages_tx_docdb_doc_descriptor_mm`<br /<br />' .
							 'INSERT INTO <br />tx_docdb_pages_doc_related_pages_mm (`uid_local`, `uid_foreign`, `tablenames`, `sorting`)<br />SELECT `uid_local`, `uid_foreign`, `tablenames`, `sorting` FROM `pages_tx_docdb_doc_related_pages_mm`<br /<br /></pre>';
			
			} else if( $type === 'TABLE_EXISTS_ERROR' ) {
				
				return $icon . '<strong>Import error</strong>:<br />The new tables already contains data.<br />You sould make a backup of the next existing tables manually:<br /><br />' .
							'tx_docdb_pages_doc_descriptor_mm<br />' .
							'tx_docdb_pages_doc_related_pages_mm<br /><br />' .
							'And execute TRUNCATE on those tables before coming back here to update the new tables.';
							
			} else if( $type === 'RENAME_FIELDS_ERROR' ){
				
				return $icon . '<strong>Rename Field statuts to.status error</strong>:<br />' .
											 ' You sould make update manually with the next cmd: or rename it with phpmyadmin<br /><br />' .
											 'ALTER TABLE tx_docdb_status CHANGE statuts status TINYTEXT NOT NULL<br /><br />' .
											 'and after update file ext_tables.sql and rename field statuts to status. Otherwise the extension will not work.';
											 
			} else if( $type === 'RENAME_FIELDS_ERROR_SQL' ) {
				
				return $icon . '<strong>Rename Field statuts to.status in ext_tables.sql error</strong>:<br />' .
											 ' Update in db is ok but you should rename statuts to status in ext_tables.sql manually<br />' .
											 'Just to avoid update field message in extension manager.<br /><br />';
			
			} else if( $type === 'DOCDEFPID_ERROR' ) {
				
				return $icon . '<strong>Document definition PID error</strong>:<br />' .
											 ' It seems that the Document Definition storage page [dscrDefStoragePid] is not already sets.<br />' .
											 ' Please, before update you must set the Document Definition storage page [dscrDefStoragePid]<br />' .
											 '';
			}
	}
	
	
	private function _checkTableFieldsToRename() {
		
		// field type need to be renamed, in fact userFunc not work with that name.
		/*
		 * arff NOT needed
		$this->_tableFieldsDef[ 'tx_docdb_type' ][ 'oldFname' ] = 'type';
		$this->_tableFieldsDef[ 'tx_docdb_type' ][ 'newFname' ] = 'dtype';
		*/
		$this->_tableFieldsDef[ 'tx_docdb_status' ][ 'oldFname' ] = 'statuts';
		$this->_tableFieldsDef[ 'tx_docdb_status' ][ 'newFname' ] = 'status';
		
		foreach( $this->_tableFieldsDef as $table => $fieldDscr ) {
			
			// first check if rename field statuts|type to status|dtype of table tx_docdb_[type|status] is required and doing.
			if( array_key_exists( $fieldDscr[ 'oldFname' ], $this->_tableFieldsDef[ $table ] ) ) {
				
				// check eventually if any field status already exists . if it contains data, wich must be false, it will be renamed and dropped after from installtool
				if( array_key_exists( $fieldDscr[ 'newFname' ], $this->_tableFieldsDef[ $table ] ) ) {
                    
                    $GLOBALS[ 'TYPO3_DB' ]->sql_query(
                        'ALTER TABLE ' . $table . ' CHANGE ' . $fieldDscr[ 'newFname' ] . ' zzz_deleted' . self::$_bkpPrefix . $fieldDscr[ 'newFname' ] . ' ' . $this->_tableFieldsDef[ $table ][ $fieldDscr[ 'newFname' ] ][ 'Type' ]
                    );
				}
				
				$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query(
					'ALTER TABLE ' . $table . ' CHANGE ' . $fieldDscr[ 'oldFname' ] . ' ' . $fieldDscr[ 'newFname' ] . ' ' . $this->_tableFieldsDef[ $table ][ $fieldDscr[ 'oldFname' ] ][ 'Type' ]
				);
				
				if( ! $_res ) {
					
					return $this->_error( 'RENAME_FIELDS_ERROR' );
				}
			} // eo fields exists
			
		} // eo foreach
		
		
		/*
		 * NOT NEEDED
		// only on ext_tables.sql
		foreach( $this->_tableFieldsDef as $table => $fieldDscr ) {
			
			$extTableSqlfilePath = t3lib_extMgm::extPath( 'doc_db' ) . 'ext_tables.sql';
			
			$extTablesSql = file_get_contents( $extTableSqlfilePath );
			
			if( strpos( $extTablesSql, $fieldDscr[ 'oldFname' ] ) !== FALSE ) {
				
				$extTablesSql = preg_replace(
					'/(\040|\t)+' . $fieldDscr[ 'oldFname' ] . '(\s+)/',
					'$1' . $fieldDscr[ 'newFname' ] . '$2',
					$extTablesSql
				);
				
				if( ! file_put_contents( $extTableSqlfilePath, $extTablesSql ) ) {
					
					return $this->_error( 'RENAME_FIELDS_ERROR_SQL' );
				}
			}
		}
		*/
	}
	
	

/**
	 * Check default and update values in tables tx_docdb_owner,tx_docdb_type,tx_docdb_status.
	 * The default values are required for the consistency of the grid sorting
	 * @return boolean
	 */
	private function _checkAndInitDefaultNoneVal() {
		
		$existingUids = array();
		$tceData      = array();
		
		// tables definition to check if
		$docdbTablesDef = array(
			'tx_docdb_owner'  => array( 'field' => 'owner', 'pagesRelField' => 'tx_docdb_doc_owner' ),
			'tx_docdb_type'   => array( 'field' => 'type', 'pagesRelField' => 'tx_docdb_doc_type' ),
			'tx_docdb_status' => array( 'field' => 'status', 'pagesRelField' => 'tx_docdb_doc_status' )
		);
		
		
		// get uids for table wich record value 'zzz_none' exists
		foreach( $docdbTablesDef as $table => $val ) {
			
			// store existing uid of table if any
			$existingUids[ $table ] = array();
			
			// store existing uids of 'zzz_none' field value
            $_res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                'uid', $table, $table . '.' . $val[ 'field' ] . '=\'zzz_none\'' . t3lib_BEfunc::deleteClause( $table )
            );
			
			if( $_res ) {
				
				while( ($_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res ) ) ) {
					
					$existingUids[ $table ][] = $_row[ 'uid' ];
				}
				$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
			}
			// eo check uids of 'zzz_none' field value deleted or not
			
			// if existing zzz_none prepare tceData array
			if( count( $existingUids[ $table ] ) ) {
					
				// store descriptor definition storage pid to update
				foreach( $existingUids[ $table ] as $uid ){
					
					$tceData[ $table ][ $uid ] = array( 'pid' => $this->_extConf[ 'dscrDefStoragePid' ] );
				}
				
			// else prepare tceData to insert 'zzz_none' if not exists
			} else {
				
				$tceData[ $table ][ 'NEW_' . substr( mt_rand(), 0, 8 ) ] = array(
					'pid'           => $this->_extConf[ 'dscrDefStoragePid' ],
					$val[ 'field' ] => 'zzz_none'
				);
			}
		} // eo foreach $docdbTablesDef
		
		$tce = t3lib_div::makeInstance( 't3lib_TCEmain' );
		$tce->stripslashes_values = 0;
		$tce->start( $tceData, array() );
		$tce->process_datamap();
		// reset
		$tceData      = array();
		
		$pages = $this->_preparePagesToUpdate( $docdbTablesDef );
		
		foreach( $docdbTablesDef as $table => $val ) {
			
			foreach( $pages[ $table ] as $pageUid => $relField ) {
			
				$tceData[ 'pages' ][ $pageUid ][ $val[ 'pagesRelField' ] ] = $relField[ $val[ 'pagesRelField' ] ];
			}
		}
		
		// update descriptor definition storage pid
		$tce->start( $tceData, array() );
		$tce->process_datamap();
		
		unset( $tce );
	}
	
	/**
	 * Return array of pages to update : 
	 * $pages[ docdbTableRef ][ pageUid ][ pagesDocdbRelField] = uid of relfield
	 * 
	 * @param array $docdbTablesDef mapping
	 * @return 
	 */
	private function _preparePagesToUpdate( $docdbTablesDef ) {
		
		$pages = array();
        $addWhere = '';
		foreach( $docdbTablesDef as $table => $val ) {
			
			$pages[ $table ] = array();
			
			// get all uids of field with value 'zzz_none'
			$rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
				'uid',
				$table,
				$table . '.' . $val[ 'field' ] . '=\'zzz_none\'',
				'uid',
				'',
				''
			);
			
			$rowUidExists = array();
			foreach( $rows as $row ){
				
				$rowUidExists[] = $row[ 'uid' ];
			}
			unset($rows, $row );
			
			// get current uid of field with value 'zzz_none'
			$rowUidCurrent = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
				'uid',
				$table,
				$table . '.' . $val[ 'field' ] . '=\'zzz_none\'' . t3lib_BEfunc::deleteClause( $table ),
				'uid',
				'',
				'1'
			);
			
			
			// get other uids of field with value != 'zzz_none'
			$rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
				'uid',
				$table,
				$table . '.' . $val[ 'field' ] . '!=\'zzz_none\'' . t3lib_BEfunc::deleteClause( $table ),
				'uid',
				'',
				''
			);
			
			$rowUidOthersExists = array();
			foreach( $rows as $row ){
				
				$rowUidOthersExists[] = $row[ 'uid' ];
			}
			unset($rows, $row );


            if( count( $rowUidExists ) || count( $rowUidOthersExists ) ) {

                $addWhere = ' OR (';

                if( count( $rowUidExists ) ) {

                    $addWhere .= $val[ 'pagesRelField' ] . ' IN (' . implode( ',', $rowUidExists ) . ')';
                }

                if( count( $rowUidOthersExists ) ) {

                    if( count( $rowUidExists ) ) {
                        $addWhere .= ' AND ';
                    }

                     $addWhere .= $val[ 'pagesRelField' ] . ' NOT IN (' . implode( ',', $rowUidOthersExists ) . ')';
                }

                $addWhere .= ')';

            }

			// get pages uid of relatedField value with fk uid of field
			$rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
				'uid',
				'pages',
                $val[ 'pagesRelField' ] . '=\'zzz_none\' OR '
                . $val[ 'pagesRelField' ] . '=0 '
                . $addWhere
                . ' AND pages.doktype=198' . t3lib_BEfunc::deleteClause( 'pages' ),
				'uid',
				'',
				''
			);
			
			foreach( $rows as $row ) {
				$pages[ $table ][ $row[ 'uid' ] ] = array( $val[ 'pagesRelField' ] => $rowUidCurrent[ 0 ][ 'uid' ] );
			}
		}
		
		return $pages;
	}
	
	private function _checkDocDefPid() {
		
		$tableDefValUid = array(
			'owner'  => 0,
			'type'   => 0,
			'status' => 0
		);
		
		$cpt = 0;
		foreach( $tableDefValUid as $fieldName => $defValUid ) {
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->sql_query(
				'SELECT COUNT(uid) AS count from tx_docdb_' . $fieldName . ' where pid=' . $this->_extConf[ 'dscrDefStoragePid' ]
			);
			
			if( $_res ) {
				
				$_row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res );
				$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
				
				$cpt += $_row[ 'count' ];
			}
			
			
		}
		// if not result the pid seems to not defined.
		if( $cpt < 1 ) {
			
			return TRUE;
		}
	}
	
	



}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php']);
}

}
