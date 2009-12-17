<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Laurent Cherpit (laurent@eosgarden.com)
 *  adapted from version of tt-news_tceFunc_selectTreeView
 *  (c) 2005-2007 Rupert Germann <rupi@gmx.li>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
 * 
 * $Id: class.tx_docdb_treeview.php 24 2009-11-02 13:50:52Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-11-02 14:50:52 +0100 (lun 02 nov 2009) $
 * 
 * @author     Laurent Cherpit  <laurent@eosgarden.com>
 * @version    2.0.0
 * @package    TYPO3
 * @subpackage doc_db
 */

require_once ( t3lib_extMgm::extPath( 'doc_db' ) . 'classes/class.tx_docdb_div.php' );
require_once ( PATH_t3lib . 'class.t3lib_treeview.php' );

/**
 * extend class t3lib_treeview to change function wrapTitle().
 *
 */
class tx_docdb_tceFunc_selectTreeView extends t3lib_treeview
{
	
	/**
	 * Can'be protected because parent is public
	 * @var
	 */
	public $TCEforms_itemFormElName          = '';
	public $TCEforms_nonSelectableItemsArray = array();
	public $TCEforms_selectedItemsArray      = array();
	public $selectedItemsArrayParents        = array();

	
	/**
	 * wraps the record titles in the tree with links or not depending on if they are in the TCEforms_nonSelectableItemsArray.
	 *
	 * @param    string   $title: the title
	 * @param    array    $v: an array with uid and title of the current item.
	 * @return   string   the wrapped title
	 */
	public function wrapTitle( $title, $row, $bank=0 ) {

		$_style = $this->_getTitleStyles( $row );
		$_id    = $row[ 'uid' ];
		
		if( $_id > 0 ) {
			
			$_aStyle      = '';
			$_related     = '';
			
			// ** OS 28.4.2006 Add related info
			// ** LC 30.10.2009
			if( $row[ 'dscr_related' ] > 0 ) {
				
				// get related descriptors
				$relRows = tx_docdb_div::_sqlGetRows(
					'tx_docdb_descriptor',
					'uid,title',
					'tx_docdb_descriptor.uid IN (' . $row[ 'dscr_related' ] .')',
					'',
					'title ASC',
					''
				);
				
				// tooltip
				$_related = tx_docdb_div::_getCssTooltip( $relRows );
				
				$_aStyle  = ' style="padding-left:13px;"';
				
			}
			
			$_hrefTitle = htmlentities( '[id=' . $_id . '] ' . $title );
			
			// Children of descriptor or in page they are not leaf
			if( in_array( $_id, $this->TCEforms_nonSelectableItemsArray ) ) {
				
				return $_related . '<a href="#" title="' . $_hrefTitle . '"' . $_aStyle . '>
								<span style="color:#999;cursor:default;'
							. $_style . '">' . $title . '</span></a>';
			
			} else {
				
				// Is selectable
				$aOnClick = 'setFormValueFromBrowseWin(\'' . $this->TCEforms_itemFormElName
									. '\',' . $_id . ',\'' . t3lib_div::slashJS( $title ) . '\'); return false;';
				
				return $_related . '<a href="#" onclick="' . htmlspecialchars( $aOnClick )
						. '" title="' . $_hrefTitle . '"' . $_aStyle . '><span style="' . $_style . '">'
						. $title . '</span></a>';
			}
			
		} else {
			
			// rootNode
			return $title;
		}
	}
	
	


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access define as private in parent, in fact no and should be protected
	 */
	public function PM_ATagWrap( $icon, $cmd, $bMark = '' ) {
		
		$_cmdParts = explode( '_', $cmd );
		$_title    = 'collapse';
		
		if( $_cmdParts[ 1 ] === '1' ) {
			
			$_title = 'expand';
		}
		
		return '<span onclick="tx_docdb_sendResponse(\'' . $cmd . '\');" style="cursor:pointer;" title="' . $_title . '">' . $icon . '</span>';
	}
	
	
	/**
	 * _getTitleStyles:. make bold it's leaf node and underline if parent node 
	 * 
	 * @param array    $v: an array with uid and title of the current item.
	 * @return 
	 */
	protected function _getTitleStyles( $v ) {
	
		$_style = '';
		
		// current selected descriptor
		if( in_array( $v[ 'uid' ], $this->TCEforms_selectedItemsArray ) ) {
			
			$_style .= 'font-weight:bold;';
		}
		
		// descriptor in the rootline
		if( is_array( $this->selectedItemsArrayParents ) && in_array( $v[ 'uid' ], $this->selectedItemsArrayParents ) ) {
				
			$_style .= 'text-decoration:underline;';
		}
		
		return $_style;
	}
	
	

}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceFunc_selectTreeView.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceFunc_selectTreeView.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceFunc_selectTreeView.php']);
}

}
