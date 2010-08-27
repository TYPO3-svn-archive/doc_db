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
 * Class/Function to check on preprocess saving record
 * 
 * $Id: class.tx_docdb_tcemain.php 155 2009-12-06 22:40:57Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-06 23:40:57 +0100 (dim 06 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


class tx_docdb_tceMain
{
	
	/**
	* This method is called by a hook in the TYPO3 Core Engine (TCEmain) when a record is saved. 
	* We use it to disable saving of the current record if the required values of tx_docdb_doc_owner,tx_docdb_doc_type,tx_docdb_doc_status
	* are not different of 0.
	* In fact TCA config seems to not provide a mecanism to had a blank values select by default and not to allow this value to be selected
	* 
	* @param   array       $fieldArray: The field names and their values to be processed (passed by reference)
	* @param   string      $table: The table TCEmain is currently processing
	* @param   string      $id: The records id (if any)
	* @param   object      $pObj: Reference to the parent object (TCEmain)
	* @return  void
	* @access public
	*/
	public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, t3lib_tceMain $pObj) {
		
		if($table === 'pages' && $fieldArray['doktype'] === '198') {
			
			$required  = FALSE;
			$fieldName = array();
			$s         = '';
			$reqText   = 'is';
			
			if($fieldArray['tx_docdb_doc_owner'] === '0') {
			
				$fieldName[] = 'Owner';
				$required    = TRUE;
			}
			
			if($fieldArray['tx_docdb_doc_type'] === '0') {
			
				$fieldName[] = 'Type';
				$required    = TRUE;
			}
			
			if($fieldArray['tx_docdb_doc_status'] === '0') {
			
				$fieldName[] = 'Status';
				$required    = TRUE;
			}
			
			if($required) {
			
				// plurial
				if(count($fieldName) > 1) {
				
					$s       = 's';
					$reqText = 'are';
				}
				
				$pObj->log($table,
					$id,
					2,
					0,
					1,
					"Attempt to save or update page '%s' (%s):" .
					"but field". $s . " %s " . $reqText . " required.",
					1,
					array($fieldArray['title'], $id, implode(' and ' , $fieldName)),
					$fieldArray['pid']
				);
				
				//unset fieldArray to prevent saving of the record
				$fieldArray = array();
				
			}
		}
	}
}




class tx_docdb_tceMain_cmdMap
{
	
	/**
	* not allow to delete default values 'zzz_none'
	*
	* @param   String      $command: ...
	* @param   [type]      $table: ...
	* @param   [type]      $srcId: ...
	* @param   [type]      $destId: ...
	* @param   [type]      $pObj: ...
	* @return  [type]      ...
	*/
	public function processCmdmap_preProcess(&$command, $table, $srcId, $destId, t3lib_tceMain $pObj) {
		
		// 
		if($command === 'delete') {
			
			if($table === 'tx_docdb_owner' || $table === 'tx_docdb_type' || $table === 'tx_docdb_status') {
				
				$field = substr($table, 9, strlen($table));
				
				// get parent uid
				$rec  = t3lib_BEfunc::getRecord($table, $srcId, $field);
				
				if(trim($rec[$field]) === 'zzz_none') {
					
					$pObj->log($table,
						'',
						3,
						0,
						1,
						"Attempt to delete record '%s' (field:%s:%s) from table %s, " .
						"but it's not allowed, this value is required by doc_db extension.",
						1,
						array($rec[$field], $field, $srcId, $table)
					);
					// 'unset' delete cmd
					$command = '';
				}
			} // enf table
		} // end if cmd == delete
	}

}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tcemain.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tcemain.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tcemain.php']);
}

}

