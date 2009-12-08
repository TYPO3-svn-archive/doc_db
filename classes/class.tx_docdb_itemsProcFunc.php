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
 * Class/Function to process items of tables tx_docdb_[owner|type|status]
 * 
 * $Id: class.tx_docdb_itemsProcFunc.php 156 2009-12-06 22:43:41Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-06 23:43:41 +0100 (dim 06 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


class tx_docdb_itemsProcFunc
{
	/**
	 * Put items with value 'zzz_none' in first position
	 * 
	 * @param array $PA
	 * @return 
	 */
	public function sortItems( &$PA ) {
		
		$items = $PA[ 'items' ];
		$item2unShift = Array();
		
		foreach( $PA[ 'items' ] as $k => $item ) {
			
			if( $item[ 0 ] === 'zzz_none' ) {
				
				$item2unShift = array_splice  ( $PA[ 'items' ] , $k, 1 );
			}
		}
		
		$item2unShift[ 0 ][ 0 ] = 'None';
		
		array_unshift( $PA[ 'items' ], $item2unShift[ 0 ] );
	}
}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_itemsProcFunc.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_itemsProcFunc.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_itemsProcFunc.php']);
}

}

