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
 * Class/Function with div util static methods 
 * 
 * $Id: class.tx_docdb_div.php 156 2009-12-06 22:43:41Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-06 23:43:41 +0100 (dim 06 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */

class tx_docdb_div
{
	
	/**
	 * Return related Descriptor in CSS tooltip (not usefull)
	 * 
	 * @param int $id: related descriptor uid list
	 * @return String: html tooltip
	 */
	public static function _getCssTooltip( $rows, $backPath='' ) {
		
		$_backPath = isset( $backPath ) ? t3lib_div::resolveBackPath( $backPath ) : '';
		// store
		$_tt = array();
		
		$_tt[] = '<div style="display:inline;position:relative;">';
		$_tt[] = '<div style="position:absolute;top:0;left:0;">';
		$_tt[] = '<a href="#" class="typo3-csh-link">';
		$_tt[] = '<img width="16" height="16" border="0" alt="" style="cursor:context-menu;" src="' . $_backPath . 'gfx/icon_note.gif"/>';
		$_tt[] = '<span class="typo3-csh-inline">';
		$_tt[] = '<span class="header">See related</span><br/>';
		$_tt[] = '<span class="paragraph">';
		
		foreach( $rows as $k => $row ) {
			
			$_hr = ($cpt++ > 0) ? '<hr style="border:none" />' : '';
			$_tt[] = $_hr . 'Id[' . $row[ "uid" ] . '] :<br /><i>(' . $row[ 'title' ] . ')</i>';
			
		}
		
		$_tt[] = '</span></span>';
		$_tt[] = '</a></div></div>';
		
		return implode( "\n", $_tt );
	}
	
	
	
	/**
	 * _sqlGetRows, from one $table, and return them in an array.
	 *
	 * @param	string		The name of the table to select from.
	 * @param	string		Fields list to select default *: all
	 * @param	string		Optional additional WHERE clauses put in the end of the query. DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	array		The array with the category records in.
	 */
	public static function _sqlGetRows( $table, $fields='*', $whereClause='', $groupBy='', $orderBy='', $limit='' ) {
		
		$rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
			$fields,
			$table,
			$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);
		
//		$outArr = array();
//		while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) ) {
//			
//			$outArr[] = $row;
//		}
		
//		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result($res);
		
		return $rows;
	}
	
	/**
	* Counts the number of rows in a table.
	*
	* @param   string      $field: Name of the field to use in the COUNT() expression (e.g. '*')
	* @param   string      $table: Name of the table to count rows for
	* @param   string      $where: (optional) WHERE statement of the query
	* @return  mixed       Number of rows counter (integer) or false if something went wrong (boolean)
	*/
	public static function exec_SELECTcountRows($field, $table, $where = '') {
		$count = false;
		
		$resultSet = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery('COUNT(' . $field . ')', $table, $where);
		
		if ($resultSet !== false) {
		
			list($count) = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_row($resultSet);
			
			$GLOBALS[ 'TYPO3_DB' ]->sql_free_result($resultSet);
		}
		
		return $count;
	}
	

}



// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php']);
}

}

