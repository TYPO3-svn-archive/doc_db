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
 * Class/Function
 * 
 * $Id: class.tx_docdb_model_status.php 156 2009-12-06 22:43:41Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-06 23:43:41 +0100 (dim 06 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


class tx_docdb_model_status
{

//	public $conf=null;
//	public $cObj=null;
//	
//	public function init( &$conf, $cObj ) {
//		
//		$this->conf = $conf;
//		$this->cObj = $cObj;
//}

	/**
	 * Get all owners with existing related documents
	 * 
	 * @remotable
	 * @return array to by encoded in JSON format
	 */
	public function get( $params ) {
		
		$rows = array();
		
		$select  = 's.uid AS id,s.status';
		$from    = 'tx_docdb_status s INNER JOIN pages p ON s.uid = p.tx_docdb_doc_status';
		$where   = '((s.status!=\'zzz_none\' AND (s.deleted=0 AND s.hidden=0)) AND (p.doktype=198 AND (p.deleted=0 AND p.hidden=0)))';
		$groupBy = 's.uid';
		
		if( isset( $params->ownerfk ) && $params->ownerfk !== '0' ) {
			
			$from .= ' AND (p.tx_docdb_doc_owner IN(' . $params->ownerfk . '))';
		}
		
		if( isset( $params->typefk ) && $params->typefk !== '0' ) {
			
			$from .= ' AND (p.tx_docdb_doc_type IN(' . $params->typefk . '))';
		}
		
		if( $params->sort !== 'status' || ( $params->dir !== 'ASC' || $params->dir !== 'DESC' ) ) {
			
			$orderBy = 's.status ASC';
			
		} else {
			
			$orderBy = trim( 's.status '. $params->dir );
		}
		
		$limit = $params->limit;
		
		$rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);
		
		// add all in first
		array_unshift( $rows, array( 'id' => 0, 'status' => 'All' ) );
		
		$out = array(
			'success'    => true,
			//'totalCount' => $numRows['num'],
			'result'     => $rows
		);
		
		return $out;
	}
}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_status.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_status.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_status.php']);
}

}

