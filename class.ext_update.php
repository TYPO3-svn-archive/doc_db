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
 * Inspired from tt_news class.ext_update.
 *
 * This Class need a good cleanup
 *
 * $Id: class.ext_update.php 167 2009-12-20 17:00:10Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-20 18:00:10 +0100 (dim 20 déc 2009) $
 *
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */



class ext_update
{

	/**
	 * New line character
	 *
	 * @var string
	 */
    protected $_NL     = '';

	/**
	 * Instance of the TYPO3 document class
	 *
	 * @var object
	 */
	private $_doc      = NULL;

	/**
	 * Back path
	 *
	 * @var string
	 */
	private $_backPath = '';

	/**
	 * extension configuration array
	 *
	 * @var array
	 */
	private $_extConf  = array();

    /**
     * ref to localang labels
     *
     * @var string
     */
    private $_ll       = 'LLL:EXT:doc_db/configuration/llang/locallang.xml:updater.';

    /**
     * test result messages
     *
     * @var array
     */
    private $_resMsg   = array();

	/**
	 * columns of tables tx_docdb_status and tx_docdb_type
	 *
	 * @var $_tableFieldsDef
	 */
	private $_tableFieldsDef = array();

    /**
     * max_execution_time php.ini config value
     *
     * @var int
     */
    private static $_maxExecTime = 0;

    /**
     * tce data store
     *
     * @var array
     */
    private static $_tceData = array();

    /**
     * tce data store pages relation
     *
     * @var array
     */
    private static $_tceDataP = array();

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
		$this->_backPath = $GLOBALS['BACK_PATH'];

		// ext_conf_template.txt array
		$this->_extConf  = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['doc_db']);

		// New instance of the TYPO3 document class
		$this->_doc      = t3lib_div::makeInstance('bigDoc');

		// columns of tables status
		$this->_tableFieldsDef['tx_docdb_status'] = $GLOBALS['TYPO3_DB']->admin_get_fields('tx_docdb_status');

        self::$_bkpPrefix = '_' . date('ymdHis');

        self::$_maxExecTime = ini_get('max_execution_time');

		// Sets the new line character
		$this->_NL = chr(10);
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

        // check field statuts exists
		$this->_checkTableFieldsToRename();

        // run init default val by default
		$this->_checkAndInitDefaultNoneVal();

        // check relations
        $this->_checkAndUpdateRelToPages();

        // check old mm tables
        $this->_checkOldMmTablesAndUpdate();


        $htmlOut = array();

        if(t3lib_div::int_from_ver(TYPO3_version) < 4003000) {

            // add flashmessages styles
			$cssPath   = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('doc_db');
			$htmlOut[] = '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'res/compat/flashmessages.css" media="screen" />';
		}

        if (t3lib_div::_GP('do_update')) {

			$htmlOut[] = '<a href="' . t3lib_div::linkThisScript(array('do_update' => '', 'func' => '')) . '">' . $GLOBALS['LANG']->sL($this->_ll . 'back') . '</a><br>';

			$func = trim(t3lib_div::_GP('func'));
			if (method_exists($this, $func)) {
				$htmlOut[] = '
				<div style="padding:15px 15px 20px 0;">
				<div class="typo3-message message-ok">
   				<div class="message-header">' . $GLOBALS['LANG']->sL($this->_ll . 'updateresults') . '</div>
  				<div class="message-body">
				' . $this->$func(TRUE) . '
				</div>
				</div></div>';

			} else {

				$htmlOut[] = '
				<div style="padding:15px 15px 20px 0;">
				<div class="typo3-message message-error">
   					<div class="message-body">ERROR: ' . $func . '() not found</div>
   				</div>
   				</div>';
			}

		} else {

            // if previous actions not already performed, button is disabled true
            $disabled = FALSE;

            $htmlOut[] = '<a href="' . t3lib_div::linkThisScript(array('do_update' => '', 'func' => '')) . '">' . $GLOBALS['LANG']->sL($this->_ll . 'reload') . '
			<img style="vertical-align:bottom;" ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/refresh_n.gif', 'width="18" height="16"') . '></a><br>';

            $htmlOut[] = $this->_displayWarning();

            $htmlOut[] = '<h3>' . $GLOBALS['LANG']->sL($this->_ll . 'actions') . '</h3>';

            // table field required to be renamed
			$htmlOut[] = $this->_displayUpdateOption('checkTableFieldsToRename', count($this->_resMsg['fieldsToRename']), '_checkTableFieldsToRename', $disabled);

            if(count($this->_resMsg['fieldsToRename'])) {
                $disabled = TRUE;
            }
            $htmlOut[] = $this->_displayUpdateOption('defaultVal', $this->_resMsg['defaultVal'], '_checkAndInitDefaultNoneVal', $disabled);

            if($this->_resMsg['defaultVal']) {
                $disabled = TRUE;
            }
            $htmlOut[] = $this->_displayUpdateOption('relToPage', $this->_resMsg['relToPage'], '_checkAndUpdateRelToPages', $disabled);

            if($this->_resMsg['relToPage']) {
                $disabled = TRUE;
            }
            $htmlOut[] = $this->_displayUpdateOption('oldTableRename', $this->_resMsg['oldTableRename'], '_checkOldMmTablesAndUpdate', $disabled);
		}

        return implode($this->_NL, $htmlOut);
	}


	/**
	 * @deprecated
	 *
	 * @return msg you must create new table before update
	 */
	protected function _sayCreateNewTables() {

		// Storage
		$htmlCode = array();

		// Infos
		$htmlCode[] = '<div>' . '<img ' . t3lib_iconWorks::skinImg($this->_backPath, 'gfx/icon_warning2.gif', '') . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;' . '<strong>Important:</strong><br />';
		$htmlCode[] = 'Before, you must select information select option and click update button to create the required new tables !<br />';
		$htmlCode[] = 'And after you must coming back here to process update of the new version of the "doc_db" extension.<br />';
		$htmlCode[] = 'Please click on the select button information above.<br />' . '</div>';

		// Return content
		return implode($this->_NL, $htmlCode);
	}


	/**
	 *
	 * @return form with tables list to be renamed
	 */
	protected function _listTables() {

		// Storage
		$htmlCode = array();

		// Infos
		$htmlCode[] = '<div>' . '<img ' . t3lib_iconWorks::skinImg($this->_backPath, 'gfx/icon_note.gif', '') . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;' . '<strong>Note:</strong><br />';
		$htmlCode[] = 'The following db tables need to be updated in order to be compatible with the new version of the "doc_db" extension.<br />';
		$htmlCode[] = 'All data will be copied in the new tables and old tables will be backuped and if backup tables already exists, new bkp tables will be created.<br />';
		$htmlCode[] = 'If you have some SQL queries related (ex. in pageTS or tmplTS) to those tables, the tablenames must be adapted<br /><br />';
		$htmlCode[] = 'Please click on the button below to start the update process.<br />' . '</div>';

		// Divider
		$htmlCode[] = $this->_doc->spacer(10);

		// Divider
		$htmlCode[] = $this->_doc->spacer(10);
		$htmlCode[] = $this->_doc->divider(5);
		$htmlCode[] = $this->_doc->spacer(10);

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
		foreach(self::$_oldTablesName as $name => $val) {

			// Row color
			$color = (($counter++ % 2)===0) ? $this->_doc->bgColor3 : $this->_doc->bgColor4;

			// Starts the row
			$htmlCode[] = '<tr bgcolor="' . $color . '">';

			// tables name rename info
			$htmlCode[] = '<td align="left" valign="middle">' . $counter . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val['nbRec'] . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $name . '</td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val['NewName'] . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle">' . $val['oldRename'] . '</td>';

			// Ends the row
			$htmlCode[] = '</tr>';

		}

		// Ends the table
		$htmlCode[] = '</table>';

		// Divider
		$htmlCode[] = $this->_doc->spacer(10);
		$htmlCode[] = $this->_doc->divider(5);
		$htmlCode[] = $this->_doc->spacer(10);

		// Return content
		return implode($this->_NL, $htmlCode);
	}


	protected function _updateTablesName() {

		// result of backup
		$_affected = array();
		// backup old table
		foreach(self::$_oldTablesName as $oldname => $val) {

			// ////drop and create bkp table//// changed: no drop but use new backup name to avoid backup overwrite
			//$GLOBALS['TYPO3_DB']->sql_query('DROP TABLE IF EXISTS ' . $val['oldRename']);
			$_query = 'CREATE TABLE ' . $val['oldRename'] . ' LIKE ' . $oldname;

			$_res = $GLOBALS['TYPO3_DB']->sql_query($_query);

			// fill backup table
			$_query = 'INSERT INTO ' . $val['oldRename'] .
			'(uid_local,uid_foreign,tablenames,sorting)' .
			' SELECT uid_local,uid_foreign,tablenames,sorting' .
			' FROM ' . $oldname . ' WHERE 1=1';

			$_res = $GLOBALS['TYPO3_DB']->sql_query($_query);


			$_affected[$val['oldRename']] = $GLOBALS['TYPO3_DB']->sql_affected_rows($_res);
		}

		foreach(self::$_oldTablesName as $oldname => $val) {

			if((int)$_affected[$val['oldRename']] !== (int)$val['nbRec']) {

				return 'BKP_ERROR';
			}
		}

		// check if new table contains data
		foreach(self::$_oldTablesName as $oldname => $val){

			$_query = 'SELECT COUNT(uid_local) AS count FROM ' . $val['NewName'];

			$_res = $GLOBALS['TYPO3_DB']->sql_query($_query);

			$_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($_res);

			//free
			$GLOBALS['TYPO3_DB']->sql_free_result($_res);

			if($_row['count'] > 0) {

				return 'TABLE_EXISTS_ERROR';
			}
		}


		// process update
		$_affected = array();

		foreach(self::$_oldTablesName as $oldname => $val){

			$_query = 'INSERT INTO ' . $val['NewName'] .
			'(uid_local,uid_foreign,tablenames,sorting)' .
			' SELECT uid_local,uid_foreign,tablenames,sorting' .
			' FROM ' . $oldname . ' WHERE 1=1';

			$_res = $GLOBALS['TYPO3_DB']->sql_query($_query);

			$_affected[$val['NewName']] = $GLOBALS['TYPO3_DB']->sql_affected_rows($_res);
		}

		foreach(self::$_oldTablesName as $oldname => $val) {

			// check if number of affected rows -neq to nb rows to be processed.
			if((int)$_affected[$val['NewName']] !== (int)$val['nbRec']) {

				return 'IMPORT_ERROR';

			} else {

				$_query = 'DROP TABLE IF EXISTS ' . $oldname;

				$GLOBALS['TYPO3_DB']->sql_query($_query);

			}
		}


		// Storage
		$htmlCode = array();

		// Confirmation message
		$htmlCode[] = 'The records were successfully updated.<br />';

		$htmlCode[] = '<table cellpadding="2" cellspacing="0">';
		$htmlCode[] = '<tr><td>Update ok</td></tr>';
        $htmlCode[] = '<tr><td>You should need to check the Database Analyser from TYPO3 Install Tool and click COMPARE. and remove the zzz_ tables.</td></tr>';
		$htmlCode[] = '</table>';

		// Divider
		$htmlCode[] = $this->_doc->spacer(10);
		$htmlCode[] = $this->_doc->divider(5);
		$htmlCode[] = $this->_doc->spacer(10);

		$htmlCode[] ='<div>Thank you for using the "Document database (doc_db)" extension.' . '</div>';


		// Return content
		return implode($this->_NL, $htmlCode);
	}


	private function _checkTableFieldsToRename($updProcess = FALSE) {

        $cpt = 0;
        $msg = array();

		$this->_tableFieldsDef['tx_docdb_status']['oldFname'] = 'statuts';
		$this->_tableFieldsDef['tx_docdb_status']['newFname'] = 'status';


        if($updProcess) {

            foreach($this->_tableFieldsDef as $table => $fieldDscr) {

                // first check if rename field statuts|type to status|dtype of table tx_docdb_[type|status] is required.
                if(array_key_exists($fieldDscr['oldFname'], $this->_tableFieldsDef[$table])) {

                    // check eventually if any field status already exists . if it contains data, wich must be false, it will be renamed  with prefix and dropped after from installtool
                    if(array_key_exists($fieldDscr['newFname'], $this->_tableFieldsDef[$table])) {

                        $GLOBALS['TYPO3_DB']->sql_query(
                            'ALTER TABLE ' . $table . ' CHANGE ' . $fieldDscr['newFname'] . ' zzz_deleted' . self::$_bkpPrefix . $fieldDscr['newFname'] . ' ' . $this->_tableFieldsDef[$table][$fieldDscr['newFname']]['Type']
                       );

                        $msg[] = 'Field ' . $fieldDscr['newFname'] .' exists, was renamed to  zzz_deleted' . self::$_bkpPrefix . $fieldDscr['newFname'];
                    }

                    $_res = $GLOBALS['TYPO3_DB']->sql_query(
                        'ALTER TABLE ' . $table . ' CHANGE ' . $fieldDscr['oldFname'] . ' ' . $fieldDscr['newFname'] . ' ' . $this->_tableFieldsDef[$table][$fieldDscr['oldFname']]['Type']
                   );

                    if(! $_res) {

                        return $this->_error('RENAME_FIELDS_ERROR');
                    }

                    $msg[] = 'Field ' . $fieldDscr['oldFname'] . ' was renamed to ' . $fieldDscr['newFname'];
                    $msg[$table] = 'on table ' . $table;
                } // eo fields exists
            } // eo foreach

            return implode('<br />' . $this->_NL, $msg);

        } else {

            foreach($this->_tableFieldsDef as $table => $fieldDscr) {

                // first check if rename field statuts|type to status|dtype of table tx_docdb_[type|status] is required.
                if(array_key_exists($fieldDscr['oldFname'], $this->_tableFieldsDef[$table])) {

                   $this->_resMsg['fieldsToRename'][] = ++$cpt;

                } // eo fields exists

            } // eo foreach

        }
	}


/**
	 * Check default and update values in tables tx_docdb_owner,tx_docdb_type,tx_docdb_status.
	 * The default values are required for the consistency of the grid sorting
	 * @return boolean
	 */
	private function _checkAndInitDefaultNoneVal($updProcess = FALSE) {

		// tables definition to check if
		$docdbTablesDef = array(
			'tx_docdb_owner'  => array('field' => 'owner', 'pagesRelField' => 'tx_docdb_doc_owner'),
			'tx_docdb_type'   => array('field' => 'type', 'pagesRelField' => 'tx_docdb_doc_type'),
			'tx_docdb_status' => array('field' => 'status', 'pagesRelField' => 'tx_docdb_doc_status')
		);

		if($updProcess ) {

            $tce = t3lib_div::makeInstance('t3lib_TCEmain');
    		$tce->stripslashes_values = 0;

            $msg = array();
            foreach(self::$_tceData as $table => $uidAr) {

                if(is_array($uidAr)) {

                    $records = '';
                    foreach($uidAr as $uid => $fields) {

                        if(strpos($uid, 'NEW') !== FALSE) {

                            $tce->start(array() , array());
                            $tce->insertDB($table, $uid, $fields);

                            // inserted id
                            $uid = $tce->substNEWwithIDs[$uid];
                            $msgNewOrUpd = 'created';

                        } else {

                            $tce->start(array() , array());
                            $tce->updateDB($table, $uid, $fields);

                            $msgNewOrUpd = 'updated';
                        }

                        if(is_array($fields)) {

                            $fieldMsg = array();
                            foreach($fields as $field => $val) {

                                $fieldMsg[] = $field . '=' . $val;
                            }
                        }

                        $records .= 'uid:' . $uid . ' ' . $msgNewOrUpd . ' with ' . implode(', ', $fieldMsg) . '<br />';
                    }
                }

                $msg[] = 'Table: <b>' . $table . '</b> updated: <br />' . $records;
            }

            // reset
            self::$_tceData = array();
            unset($tce);

            return implode('<br />' . $this->_NL, $msg);

        } else {


            $this->_resMsg['defaultVal'] = 0;

            // get uids for table wich record value 'zzz_none' exists
            foreach($docdbTablesDef as $table => $val) {

                // store existing uid of table if any
                $existingUids[$table] = array();

                // check existing uids of 'zzz_none' field value and def storage Pid
                $_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                    'uid', $table, $table . '.' . $val['field'] . '=\'zzz_none\' AND pid !=\'' . $this->_extConf['dscrDefStoragePid'] . '\' '  . t3lib_BEfunc::deleteClause($table)
               );

                if($_res) {

                    $cpt = 0;
                    while(($_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($_res))) {

                        // if existing zzz_none and pid diff from dscrDefStoragePid prepare tceData array
                        self::$_tceData[$table][$_row['uid']] = array(
                            'pid' => trim($this->_extConf['dscrDefStoragePid']),
                            $val['field'] => 'zzz_none'
                       );
                        $this->_resMsg['defaultVal']++;
                        $cpt++;
                    }

                    $GLOBALS['TYPO3_DB']->sql_free_result($_res);

                }

                if($cpt < 1) {

                    // check if zzz_none lacks
                    $_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        'COUNT(uid) as count', $table, $table . '.' . $val['field'] . '=\'zzz_none\' '  . t3lib_BEfunc::deleteClause($table), 'uid'
                   );

                    if($_res) {

                        $_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($_res);

                        // if lacks, prepare to create one
                        if($_row['count'] < 1) {

                            self::$_tceData[$table]['NEW_' . substr(mt_rand(), 0, 8)] = array(
                                'pid'           => trim($this->_extConf['dscrDefStoragePid']),
                                $val['field'] => 'zzz_none',
                                'tstamp'        => time(),
                                'crdate'        => time()
                           );

                            $this->_resMsg['defaultVal']++;
                        }

                        $GLOBALS['TYPO3_DB']->sql_free_result($_res);
                    }
                }
                // eo check uids of 'zzz_none' field value deleted or not
            } // eo foreach $docdbTablesDef
        }
	}

	/**
	 * Return array of pages to update :
	 * $pages[docdbTableRef][pageUid][pagesDocdbRelField] = uid of relfield
	 *
	 * @param array $docdbTablesDef mapping
	 * @return
	 */
	private function _checkAndUpdateRelToPages($updProcess = FALSE) {

        if($updProcess) {

            $start_time = microtime(TRUE);

            $tce = t3lib_div::makeInstance('t3lib_TCEmain');
    		$tce->stripslashes_values = 0;

            $msg = array();
            foreach(self::$_tceDataP as $table => $uidAr) {

                if(is_array($uidAr)) {

                    $nbRecToProc = count($uidAr);
                    $records = '';
                    foreach($uidAr as $uid => $fields) {

                        if(is_array($fields)) {

                            $tce->start(array() , array());
                            $tce->updateDB($table, $uid, $fields);

                            $fieldMsg = array();
                            foreach($fields as $field => $val) {

                                $fieldMsg[] = $field . '=' . $val;
                            }
                        }

                        $records .= 'page uid:' . $uid . ' updated with ' . implode(', ', $fieldMsg) . ' fk<br />';

                        $nbRecToProc--;
                        // stop 5 seconds before timelimit
                        if((microtime(TRUE) - $start_time) > (self::$_maxExecTime - 5)) {

                            $msg[] = '<b>php max execution time reached, ' . $nbRecToProc . ' more records to update.<br />Click Back link above and Update button to do it.</b><br />';
                            break;
                        }
                    }
                }

                $msg[] = 'Table: <b>' . $table . '</b> updated: <br />' . $records;
            }

            // reset
            self::$_tceDataP = array();
            unset($tce);

            return implode('<br />' . $this->_NL, $msg);

        } else {

            // tables definition to check if
            $docdbTablesDef = array(
                'tx_docdb_owner'  => array('field' => 'owner', 'pagesRelField' => 'tx_docdb_doc_owner'),
                'tx_docdb_type'   => array('field' => 'type', 'pagesRelField' => 'tx_docdb_doc_type'),
                'tx_docdb_status' => array('field' => 'status', 'pagesRelField' => 'tx_docdb_doc_status')
           );
            
            foreach($docdbTablesDef as $table => $val) {

                $addWhere   = '';
                
                // get other uids of field with value != 'zzz_none'
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid',
                    $table,
                    $table . '.' . $val['field'] . '!=\'zzz_none\'' . t3lib_BEfunc::deleteClause($table),
                    'uid',
                    '',
                    ''
               );

                $rowUidOthersExists = array();
                foreach($rows as $row){

                    $rowUidOthersExists[] = $row['uid'];
                }
                unset($rows, $row);


                if(count($rowUidOthersExists)) {

                    $addWhere .= $val['pagesRelField'] . ' NOT IN (' . implode(',', $rowUidOthersExists) . ')';
                }

                $currentUid = '';
                // get current uid of field with value 'zzz_none'
                $rowUidCurrent = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid',
                    $table,
                    $table . '.' . $val['field'] . '=\'zzz_none\'' . t3lib_BEfunc::deleteClause($table),
                    'uid',
                    '',
                    '1'
               );

                if(isset($rowUidCurrent[0]['uid'])) {

                    if(count($rowUidOthersExists)) {

                        $currentUid = ' AND ';
                    }
                    $currentUid .= $val['pagesRelField'] . ' !=' . $rowUidCurrent[0]['uid'];
                }

                // get pages uid with related field not sets
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid',
                    'pages',
                    $addWhere
                    . $currentUid
                    . ' AND pages.doktype=198' . t3lib_BEfunc::deleteClause('pages'),
                    'uid',
                    '',
                    ''
               );

                // store relation fk for table pages
                foreach($rows as $page) {

                    self::$_tceDataP['pages'][$page['uid']][$val['pagesRelField']] = $rowUidCurrent[0]['uid'];
                }
            }

            $this->_resMsg['relToPage'] = count(self::$_tceDataP['pages']);
        } // eo else updateProcess
	}


    private function _checkOldMmTablesAndUpdate($updProcess = FALSE) {

        if($updProcess) {

            return $this->_updateTablesName();

        } else {

            $this->_resMsg['oldTableRename'] = 0;

            // Checks if old bkp tables exists
            $_res = $GLOBALS['TYPO3_DB']->sql_query("SHOW TABLES LIKE 'zzz_deleted%pages_tx_docdb_%_mm'");
            $cpt=0;
            while(($_row = $GLOBALS['TYPO3_DB']->sql_fetch_row($_res))) {

                $cpt++;

            } // end while sql_fetch_row
            $GLOBALS['TYPO3_DB']->sql_free_result($_res);

            if($cpt > 0) {
                $bkpTablesPrefix = self::$_bkpPrefix;
            }

            // Checks old tables names from old version of doc_db extension
            $_res = $GLOBALS['TYPO3_DB']->sql_query("SHOW TABLES LIKE 'pages_tx_docdb_doc_%_mm'");

            while(($_row = $GLOBALS['TYPO3_DB']->sql_fetch_row($_res))) {

                // check numbers of records
                $_res1 = $GLOBALS['TYPO3_DB']->sql_query(
                    'SELECT COUNT(' . $_row[0] . '.uid_local) AS count from ' . $_row[0] .
                    ' WHERE 1=1'
               );

                $_row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($_res1);

                self::$_oldTablesName[$_row[0]] = array(
                    'nbRec'     => $_row1['count'],
                    'NewName'   => str_replace('pages_tx_docdb_', 'tx_docdb_pages_', $_row[0]),
                    'oldRename' => str_replace('pages_tx_docdb_', 'zzz_deleted' . $bkpTablesPrefix . '_pages_tx_docdb_', $_row[0])

               );

                $GLOBALS['TYPO3_DB']->sql_free_result($_res1);
            } // end while sql_fetch_row

            $GLOBALS['TYPO3_DB']->sql_free_result($_res);

            // Checks old table already dropped
            if(count(self::$_oldTablesName)) {

                $this->_resMsg['oldTableRename'] = $this->_listTables();
            }
        }
    }

    /**
     *
     * @deprecated
     */
	private function _checkDocDefPid() {

		$tableDefValUid = array(
			'owner'  => 0,
			'type'   => 0,
			'status' => 0
		);

		$cpt = 0;
		foreach($tableDefValUid as $fieldName => $defValUid) {

			$_res = $GLOBALS['TYPO3_DB']->sql_query(
				'SELECT COUNT(uid) AS count from tx_docdb_' . $fieldName . ' where pid=' . $this->_extConf['dscrDefStoragePid']
			);

			if($_res) {

				$_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($_res);
				$GLOBALS['TYPO3_DB']->sql_free_result($_res);

				$cpt += $_row['count'];
			}


		}
		// if not result the pid seems to not defined.
		if($cpt < 1) {

			return TRUE;
		}
	}


	private function _displayWarning() {
		$out = '
		<div style="padding:15px 15px 20px 0;">
			<div class="typo3-message message-warning">
   				<div class="message-header">' . $GLOBALS['LANG']->sL($this->_ll . 'warningHeader') . '</div>
  				<div class="message-body">
					' . $GLOBALS['LANG']->sL($this->_ll . 'warningMsg') . '
				</div>
			</div>
		</div>';

		return $out;
	}


    private function _displayUpdateOption($k, $count, $func, $disabled) {


		$msg = $GLOBALS['LANG']->sL($this->_ll . 'msg_' . $k) . ' ';

        if(is_int($count)) {

            $msg .= '<br /><strong>' . str_replace('###COUNT###', $count, $GLOBALS['LANG']->sL($this->_ll . 'foundMsg_' . $k)) . '</strong>';

        } else {

            $msg  .= $count;
            $count = count(self::$_oldTablesName);
        }
        if($count == 0) {

			$icon = 'ok';

		} else {

			$icon = 'warning2';
		}

		$msg .= ' <img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_' . $icon . '.gif', 'width="18" height="16"') . '>';

		if($count) {

			$msg .= '<p style="margin:5px 0;">' . $GLOBALS['LANG']->sL($this->_ll . 'question_' . $k) . '<p>';
			$msg .=  '<p style="margin-bottom:10px;"><em>' . $GLOBALS['LANG']->sL($this->_ll . 'questionInfo_' . $k) . '</em><p>';
			$msg .= $this->_getButton($func, $disabled, $GLOBALS['LANG']->sL($this->_ll . 'btn_' . $k));

		} else {

			$msg .= '<br>' . $GLOBALS['LANG']->sL($this->_ll . 'nothingtodo');
		}

		$out = $this->_wrapForm($msg, $GLOBALS['LANG']->sL($this->_ll . 'lbl_' . $k), $disabled);
		$out .= '<br /><br />';

		return $out;
	}


    private function _wrapForm($content, $fsLabel, $disabled) {

        $disabledColor = $disabled ? '#aaa' : '#f4f4f4';

		$out = '<form action="">
			<fieldset style="background:' . $disabledColor . ';margin-right:15px;">
			<legend style="background:' . $disabledColor . ';">' . $fsLabel . '</legend>
			' . $content . '

			</fieldset>
			</form>';
		return $out;
	}


	private function _getButton($func, $disabled = FALSE, $lbl = 'DO IT') {

        $params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";

        if($disabled) {

            $disabled      = ' disabled';
            $disabledStyle = ' style="background:#A42929;"';

        } else {

            $disabled      = ' onclick="' . htmlspecialchars($onClick) . '"';
            $disabledStyle = '';
        }

		$button = '<input type="submit" value="' . $lbl . '"' . $disabled . $disabledStyle . '>';

		return $button;
	}


    /**
     * @todo cleanup and adapt or remove 
     *
     * @param <type> $type
     * @return <type> @
     */
	private function _error($type) {

		$icon = '<img ' . t3lib_iconWorks::skinImg($this->_backPath, 'gfx/icon_warning2.gif', '') .' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;';

		// backup table error
			if($type === 'BKP_ERROR') {

				return $icon . '<strong>Bakup error</strong>:<br /> You sould try to make the backup of the next tables manually:<br /><br />' .
							'';

			} else if($type === 'IMPORT_ERROR') {

				return $icon .'<strong>Import error</strong>:<br /> You sould try to execute the next sql query manually:<br /><br />' .
							 '<pre>INSERT INTO <br />tx_docdb_pages_doc_descriptor_mm (`uid_local`, `uid_foreign`, `tablenames`, `sorting`)<br />SELECT `uid_local`, `uid_foreign`, `tablenames`, `sorting` FROM `pages_tx_docdb_doc_descriptor_mm`<br /<br />' .
							 'INSERT INTO <br />tx_docdb_pages_doc_related_pages_mm (`uid_local`, `uid_foreign`, `tablenames`, `sorting`)<br />SELECT `uid_local`, `uid_foreign`, `tablenames`, `sorting` FROM `pages_tx_docdb_doc_related_pages_mm`<br /<br /></pre>';

			} else if($type === 'TABLE_EXISTS_ERROR') {

				return $icon . '<strong>Import error</strong>:<br />The new tables already contains data.<br />You sould make a backup of the next existing tables manually:<br /><br />' .
							'tx_docdb_pages_doc_descriptor_mm<br />' .
							'tx_docdb_pages_doc_related_pages_mm<br /><br />' .
							'And execute TRUNCATE on those tables before coming back here to update the new tables.';

			} else if($type === 'RENAME_FIELDS_ERROR'){

				return $icon . '<strong>Rename Field statuts to.status error</strong>:<br />' .
											 ' You sould make update manually with the next cmd: or rename it with phpmyadmin<br /><br />' .
											 'ALTER TABLE tx_docdb_status CHANGE statuts status TINYTEXT NOT NULL<br /><br />' .
											 'and after update file ext_tables.sql and rename field statuts to status. Otherwise the extension will not work.';

			} else if($type === 'RENAME_FIELDS_ERROR_SQL') {

				return $icon . '<strong>Rename Field statuts to.status in ext_tables.sql error</strong>:<br />' .
											 ' Update in db is ok but you should rename statuts to status in ext_tables.sql manually<br />' .
											 'Just to avoid update field message in extension manager.<br /><br />';

			} else if($type === 'DOCDEFPID_ERROR') {

				return $icon . '<strong>Document definition PID error</strong>:<br />' .
											 ' It seems that the Document Definition storage page [dscrDefStoragePid] is not already sets.<br />' .
											 ' Please, before update you must set the Document Definition storage page [dscrDefStoragePid]<br />' .
											 '';
			}
	}

}


// avoid notice
if(defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/class.ext_update.php']);
}

}
