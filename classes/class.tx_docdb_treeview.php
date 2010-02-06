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
 * This function displays a selector with nested descriptors (categories)
 * The original code is borrowed from the extension "Digital Asset Management" (tx_dam) author: René Fritz <r.fritz@colorcube.de>
 * @author    Rupert Germann <rupi@gmx.li>
 * @ Adapted  for doc_db by Olivier Schopfer
 * 
 * $Id: class.tx_docdb_treeview.php 205 2010-02-06 17:30:07Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-02-06 18:30:07 +0100 (sam 06 fév 2010) $
 * 
 * @author     Laurent Cherpit  <laurent@eosgarden.com>
 * @version    2.0.0
 * @package    TYPO3
 * @subpackage doc_db
 */



require_once ( PATH_t3lib . 'class.t3lib_flexformtools.php' );
// require_once ( t3lib_extMgm::extPath( 'tt_news' ) . 'class.tx_ttnews_div.php' );
require_once ( t3lib_extMgm::extPath( 'doc_db' ) . 'classes/class.tx_docdb_tceFunc_selectTreeView.php' );


/**
 * this class displays a tree selector with nested descriptors (categories).
 *
 */
class tx_docdb_treeview
{
	
	/**
	 * Local ext. configuration array from extManager
	 * 
	 * @var $_extConfArray
	 */
	protected $_extConfArr = array();
	
	/**
	 * Local tree name to use with ajax
	 * 
	 * @var $treeName;
	 */
	protected $_treeName = '';

	private $_PA         = '';
	
	private $_table      = '';
	
	private $_field      = '';
	
	private $_row        = '';
	
	//private static $_instances = array();
	
	/**
	 * Local instance of xajax wrapper object
	 * 
	 * @var
	 */
	private $_xjxWrapperOb = NULL;

	public function displayCategoryTree( &$PA, $fobj ) {

		$this->_PA          = &$PA;
		$this->_table       = $this->_PA[ 'table' ];
		$this->_field       = $this->_PA[ 'field' ];
		$this->_row         = $this->_PA[ 'row' ];
		$this->_pObj        = &$this->_PA[ 'pObj' ];
		$this->_tcaConfig   = $this->_PA[ 'fieldConf' ][ 'config' ];
		
		$this->_parentField = $GLOBALS[ 'TCA' ][ $this->_tcaConfig[ 'foreign_table' ] ][ 'ctrl' ][ 'treeParentField' ];
		
		// set treeName to use with xajax
		$this->_treeName    = $this->_field . '_tree';
		$this->_treeName    = str_replace( '_', '', $this->_treeName );
		
		$_content = '';
		
		// get doc_db extConf array
		if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] ) ) {
			
			$this->_extConfArr = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] );
		}
		
		if( t3lib_extMgm::isLoaded( 'ta_xajaxwrapper' ) ) {
			
			require_once ( t3lib_extMgm::extPath( 'ta_xajaxwrapper' ) . 'class.tx_taxajaxwrapper.php' );
		
		} else {
			
			// info to TYPO3 admin
			t3lib_BEfunc::typo3PrintError( 'Document db error', 'ta_xajaxwrapper Extension is not installed.' );
			exit;
		}
		
		if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'BE' ][ 'forceCharset' ] ) ) {
			
			define( 'XAJAX_DEFAULT_CHAR_ENCODING', $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'BE' ][ 'forceCharset' ] );
		
		} else {
			
			define( 'XAJAX_DEFAULT_CHAR_ENCODING', 'iso-8859-1' );
		}
		
		try {
			
			$this->_xjxWrapperObj = t3lib_div::makeInstance( 'tx_taxajaxwrapper' );
			$this->_xjxWrapperObj->setWrapperPrefix( 'tx_docdb_' );
			$this->_xjxWrapperObj->statusMessagesOn();
			//$this->_xjxWrapperObj->debugOn();
			$this->_xjxWrapperObj->outputEntitiesOn();
			$this->_xjxWrapperObj->decodeUTF8InputOn();
			$this->_xjxWrapperObj->registerFunction(
				array(
					'sendResponse', $this, 'sendResponse'
				)
			);
			
			$_content .= $this->_xjxWrapperObj->getJavascript();
			
			$this->_xjxWrapperObj->processRequests();
		
		}
		catch( exception $e ) {
			
			t3lib_BEfunc::typo3PrintError( 'Document db error', $e->getMessage() );
			exit;
		}
		
		/**
		 * @todo remove depandency to tx_ttnews_div
		 */
//		if( !is_object( $this->divObj ) ) {
//			
//			$this->divObj = t3lib_div::makeInstance( 'tx_ttnews_div' );
//		}
		$_content .= $this->renderDescriptorFields();
		
		return $_content;
	}
	
	/**
	 * send tree as response: not very efficient. in the future, it would be nice to rewrite this class
     * with the ajax framework provided by typo3 4.2 instead of xajax
	 *
	 * @param	[type]		$cmd: ...
	 * @return	[type]		...
	 */
	function sendResponse( $cmd ) {
		
		$_content = '';

		if( $cmd == 'show' || $cmd == 'hide' ) {
			
			$_content .= $this->renderCatTree( $cmd );
			
		} else {
			
			t3lib_div::_GETset( $cmd, 'PM' );
			$_content .= $this->renderCatTree( $cmd );
		}
		
		// Init response Object
		$objResponse = $this->_xjxWrapperObj->getResponseObj();
		
		// assign inner Div
		$objResponse->addAssign(
			substr( $cmd, strrpos( $cmd, '_' ) + 1, strlen( $cmd ) ),
			'innerHTML',
			$_content
		);
		
		//return the XML response
		return $objResponse->getXML();
	}
	
	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$cmd: ...
	 * @return	[type]		...
	 */
	function renderCatTree( $cmd = '' ) {

		if( !$treeViewObj instanceof tx_docdb_tceFunc_selectTreeView ) {
			
			$treeViewObj = t3lib_div::makeInstance( 'tx_docdb_tceFunc_selectTreeView' );
		}
		
		// open tceForm
		if( !$cmd ) {
			
			$treeViewObj->treeName = $this->_treeName;
		
		} else {
			
			// xajax call
			$treeViewObj->treeName = $this->_stripCmdSuffix( $cmd );
			
            if( $treeViewObj->treeName === 'dscrrelatedtree' ) {

                // little tricks to set to right selected field
                $this->_tcaConfig = $GLOBALS[ 'TCA' ][ $this->_tcaConfig[ 'foreign_table' ] ][ 'columns' ][ 'dscr_related' ][ 'config' ];
            }
		}
		
		$treeViewObj->table    = $this->_tcaConfig[ 'foreign_table' ];
//		$treeViewObj->init( $SPaddWhere . $catlistWhere, $this->_extConfArr[ 'treeOrderBy' ] );
		$treeViewObj->init( '', $this->_extConfArr[ 'treeOrderBy' ] );
		$treeViewObj->backPath    = $this->_pObj->backPath;
		$treeViewObj->parentField = $this->_parentField;
//		$treeViewObj->maxOpenLevel = $this->_maxOpenLevel;
		$treeViewObj->expandAll   = ( $cmd === 'show' ? 1 : 0 );
		$treeViewObj->expandFirst = 0;
		
		// those fields will be filled to the array $treeViewObj->tree
		$treeViewObj->fieldArray = array(
			'uid', 'title', 'dscr_pid', 'dscr_related'
		);
		$treeViewObj->useXajax = TRUE;
		
//		if( $this->includeList ) {
//			
//			$treeViewObj->MOUNTS = t3lib_div::intExplode( ',', $this->includeList );
//		}
		
		// no context menu on icons
		$treeViewObj->ext_IconMode = '1';
		$treeViewObj->title = $GLOBALS[ 'LANG' ]->sL( $GLOBALS[ 'TCA' ][ $this->_tcaConfig[ 'foreign_table' ] ][ 'ctrl' ][ 'title' ] );
		
		// set $this->_PA[ 'itemFormElName' ] to allow set select from setFormValueFromBrowseWin
		$treeViewObj->TCEforms_itemFormElName = 'data['. $this->_table .'][' .$this->_row[ 'uid' ] . '][' . $this->_tcaConfig[ 'foreign_selector' ] . ']';
		
		// apply only on descriptors in edition mode
		if( $this->_table === $this->_tcaConfig[ 'foreign_table' ] ) {
			
			// own descriptor is not selectable as parent
			$treeViewObj->TCEforms_nonSelectableItemsArray[] = $this->_row[ 'uid' ];
			
			// apply only on parent descr, not on related
			if( $treeViewObj->treeName === 'dscrpidtree' ) {
				
				$this->_setSubNonSelectableItemsArray( $treeViewObj );
			}
		}
		
		if( is_array( $notAllowedItems ) && $notAllowedItems[ 0 ] ) {
			
			foreach ( $notAllowedItems as $k ) {
				
				$treeViewObj->TCEforms_nonSelectableItemsArray[] = $k;
			}
		}
		
		// mark selected categories
		$selectedItems = array();
		
		// descriptors on a page
		if( isset( $this->_row[ 'tx_docdb_doc_descriptor' ] ) ) {
			
			$_res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECT_mm_query(
				'tx_docdb_descriptor.uid',
				'pages',
				'tx_docdb_pages_doc_descriptor_mm',
				'tx_docdb_descriptor',
				' AND pages.uid=' . (int)$this->_row[ 'uid' ],
				'tx_docdb_descriptor.uid',
				'',
				''
			);
			
			while( ($row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res ) ) ) {
				
				$selectedItems[] = $row[ 'uid' ]; 
			}
			$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );

            // add all those are not leaf to non selectable items
            $treeViewObj->TCEforms_nonSelectableItemsArray = array_merge( $treeViewObj->TCEforms_nonSelectableItemsArray, $this->_getDescrAllNotLeaf() );

		// parent descriptor-s
		} elseif( $treeViewObj->treeName === 'dscrpidtree' ) {
		
			$selectedItems[] = (int)$this->_row[ 'dscr_pid' ];
			
		// related descriptor-s
		} elseif( $treeViewObj->treeName === 'dscrrelatedtree') {
			
			$selectedItems = explode( ',', $GLOBALS[ 'TYPO3_DB' ]->cleanIntList( $this->_row[ 'dscr_related' ] ) );
			
			// plugin in tt_content
		} elseif( isset( $this->_row[ 'pi_flexform' ] ) ) {
			
			// concat right selector for itemFromElement name in flex
			$treeViewObj->TCEforms_itemFormElName .= '[data][sDEF][lDEF][descriptors][vDEF]';
			
			/**
			 * get flex array
			 */
			$cfgArr = t3lib_div::xml2array( $this->_row[ 'pi_flexform' ] );
			
			if( is_array( $cfgArr ) && is_array( $cfgArr[ 'data' ][ 'sDEF' ][ 'lDEF' ] ) &&
					is_array( $cfgArr[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'descriptors' ] )
				) {
				// has descriptors
				//$dscr = $cfgArr[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'descriptors' ][ 'vDEF' ];
				if( isset( $cfgArr[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'descriptors' ][ 'vDEF' ] ) ) {
					
					$selectedItems = explode( ',', $GLOBALS[ 'TYPO3_DB' ]->cleanIntList (
						$cfgArr[ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'descriptors' ][ 'vDEF' ] )
					);
				}
			}

            // add all those are not leaf to non selectable items
            $treeViewObj->TCEforms_nonSelectableItemsArray = array_merge( $treeViewObj->TCEforms_nonSelectableItemsArray, $this->_getDescrAllNotLeaf() );
		}
//        else {
//
//		//?????????????
//				$selectedItems = $this->_row[ 'tx_docdb_doc_descriptor' ];
//		}
        
		$treeViewObj->TCEforms_selectedItemsArray = $selectedItems;
		
		// set rootLine parent descriptor
		$this->_setDscrRootline( $selectedItems, $treeViewObj, $SPaddWhere );
		
		if( !$this->divObj->allowedItemsFromTreeSelector ) {
			$notAllowedItems = $this->getNotAllowedItems( $this->_PA, $SPaddWhere );
		} else {
			$treeIDs = $this->divObj->getCategoryTreeIDs();
			$notAllowedItems = $this->getNotAllowedItems( $this->_PA, $SPaddWhere, $treeIDs );
		}
		// render tree html
		$_treeContent = $treeViewObj->getBrowsableTree();
		
		$this->treeItemC = count( $treeViewObj->ids );
		// 		if ($cmd == 'show' || $cmd == 'hide') {
		// 			$this->treeItemC++;
		// 		}
		$this->treeIDs = $treeViewObj->ids;
		
		// $this->debug['MOUNTS'] = $treeViewObj->MOUNTS;

		return $_treeContent;
	}
	
	/**
	 * select items in the parent rootline from selected leaf
	 *
	 * @param	[type]		$selectedItems: ...
	 * @param	[type]		$SPaddWhere: ...
	 * @return	[type]		...
	 */
	protected function _setDscrRootline( $selectedItems, &$treeViewObj, $SPaddWhere='' ) {

		// not process if none selected or create new descriptor
		if( ( count( $selectedItems ) === 0 ) || substr( $treeViewObj->TCEforms_nonSelectableItemsArray[ 0 ], 0, 3 ) === 'NEW' ) {
			
			return NULL;
		}
		
		// store also the current selected Items
		if( empty( $treeViewObj->selectedItemsArrayParents ) ) {
			
			$treeViewObj->selectedItemsArrayParents = $selectedItems;
		}
		
		// select parent uid
		$_res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
			'dscr_pid',
			'tx_docdb_descriptor',
			'uid IN (' . implode( ',', $selectedItems ) . ') ' . $SPaddWhere
		);
		
		while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res ) ) ) {
				
            $treeViewObj->selectedItemsArrayParents[] = $row[ 'dscr_pid' ];
		}
		
		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
		
		arsort( $treeViewObj->selectedItemsArrayParents );


		$treeViewObj->selectedItemsArrayParents = array_unique( $treeViewObj->selectedItemsArrayParents );
		
		$selectedItems = array_diff( $treeViewObj->selectedItemsArrayParents, $selectedItems );
		
		$_lastItem = array_slice( $treeViewObj->selectedItemsArrayParents, -1, 1 );
		if( ( (int)$_lastItem[ 0 ] === 0 ) || empty( $selectedItems ) ) {
			
			// root level
			return NULL;
		}
		
		// process parent level
		$this->_setDscrRootline( $selectedItems, $treeViewObj, $SPaddWhere );
	}


	/**
	 * set $treeViewObj->TCEforms_nonSelectableItemsArray
	 * with sub descriptors from current descriptor
	 * 
	 * @param object $treeViewObj
	 * @param object $_currentNonSelItemsArray [optional]
	 * @return void
	 */
	protected function _setSubNonSelectableItemsArray( &$treeViewObj, $_currentNonSelItemsArray = '' ) {
	
		// not process if create new descriptor
		if( ( count( $_currentNonSelItemsArray ) === 0 ) ||  substr( $treeViewObj->TCEforms_nonSelectableItemsArray[ 0 ], 0, 3 ) === 'NEW' ) {
			
			return NULL;
		}
		
		// first call
		if( ! is_array( $_currentNonSelItemsArray ) ) {
			
			// store processed items
			$_currentNonSelItemsArray = $treeViewObj->TCEforms_nonSelectableItemsArray;
			
		}
		
		// get uid of sub items
		$_res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
			'uid',
			$treeViewObj->table,
			' ' . $treeViewObj->table . '.' . $treeViewObj->parentField . ' IN (' . implode( ',', $_currentNonSelItemsArray ) . ')'
		);
		
		// reset
		$_currentNonSelItemsArray = array();
		
		while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res ) ) ) {
			
			$_currentNonSelItemsArray[] = $row[ 'uid' ];
		}
		
		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $_res );
		
		// store results
		$treeViewObj->TCEforms_nonSelectableItemsArray = array_unique(
			array_merge( $treeViewObj->TCEforms_nonSelectableItemsArray, $_currentNonSelItemsArray )
		);
		
		// get sub descriptors of current level
		$this->_setSubNonSelectableItemsArray( $treeViewObj, $_currentNonSelItemsArray );
	}


	/**
	 * Get uid descriptors, those are not leaf.
	 *
	 * @return : array  : ids of descriptors not leaf
	 */
	private function _getDescrAllNotLeaf() {

		// dscr_pid unique list
		$dscrPidList  = array();

		$select  = 'd.uid';
		$from    = 'tx_docdb_descriptor d';
		$where   = 'd.uid IN(SELECT dscr_pid FROM tx_docdb_descriptor) AND d.deleted=0';

		$groupBy = 'd.uid';
		$orderBy = '';
		$limit   = '';

		// get
		$res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);

		while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) ) {

			$dscrPidList[] = $row[ 'uid' ];
		}

		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result($res);

		unset( $row, $select, $from, $where, $groupBy, $orderBy, $limit );

		return $dscrPidList;
	}



	/**
	 * Generation of TCEform elements of the type "select"
	 * This will render a selector box element, or possibly a special construction with two selector boxes. That depends on configuration.
	 *
	 * @param	array		$_PA: the parameter array for the current field
	 * @param	object		$fobj: Reference to the parent object
	 * @return	string		the HTML code for the field
	 */
	function renderDescriptorFields() {
		
		// Field configuration from TCA
		// it seems TCE has a bug and do not work correctly with '1'
		$this->_tcaConfig['maxitems' ] = ( $this->_tcaConfig[ 'maxitems' ] == 2 ) ? 1 : $this->_tcaConfig[ 'maxitems' ];
		
		// Getting the selector box items from the system
		$selItems = $this->_pObj->addSelectOptionsToItemArray(
			$this->_pObj->initItemArray( $this->_PA[ 'fieldConf' ] ),
			$this->_PA[ 'fieldConf' ],
			$this->_pObj->setTSconfig( $this->_table, $this->_row ),
			$this->_field
		);
		$selItems = $this->_pObj->addItems( $selItems, $this->_PA[ 'fieldTSConfig' ][ 'addItems.' ] );
		
		// Possibly remove some items:
		$removeItems = t3lib_div::trimExplode( ',', $this->_PA[ 'fieldTSConfig' ][ 'removeItems' ], 1 );

		foreach ( $selItems as $tk => $p ) {
			if( in_array( $p[ 1 ], $removeItems ) ) {
				unset( $selItems[ $tk ] );
			} elseif( isset( $this->_PA[ 'fieldTSConfig' ][ 'altLabels.' ][ $p[ 1 ] ] ) ) {
				$selItems[ $tk ][ 0 ] = $this->_pObj->sL( $this->_PA[ 'fieldTSConfig' ][ 'altLabels.' ][ $p[ 1 ] ] );
			}
			
			// Removing doktypes with no access:
			if( $this->_table . '.' . $this->_field == 'pages.doktype' ) {
				if( !( $GLOBALS[ 'BE_USER' ]->isAdmin() || t3lib_div::inList( $GLOBALS[ 'BE_USER' ]->groupData[ 'pagetypes_select' ], $p[ 1 ] ) ) ) {
					unset( $selItems[ $tk ] );
				}
			}
		}
		
		// Creating the label for the "No Matching Value" entry.
		$nMV_label = isset( $this->_PA[ 'fieldTSConfig' ][ 'noMatchingValue_label' ] ) ? $this->_pObj->sL( $this->_PA[ 'fieldTSConfig' ][ 'noMatchingValue_label' ] ) : '[ ' . $this->_pObj->getLL( 'l_noMatchingValue' ) . ' ]';
		$nMV_label = @sprintf( $nMV_label, $this->_PA[ 'itemFormElValue' ] );

		
		// Prepare some values:
		$maxitems = intval( $this->_tcaConfig[ 'maxitems' ] );
		$minitems = intval( $this->_tcaConfig[ 'minitems' ] );
		//$this->_tcaConfig[ 'size' ] = intval($this->_tcaConfig['size']);
		
		// If a SINGLE selector box...
		if( $maxitems < 1 AND !$this->_tcaConfig[ 'treeView' ] ) {
		
		} else {

		
			/** ******************************
			 build tree selector
			 /** *****************************/
			
			$item .= '<input type="hidden" name="' . $this->_PA[ 'itemFormElName' ] . '_mul" value="' . ( $this->_tcaConfig[ 'multiple' ] ? 1 : 0 ) . '" />';
			
			// Set max and min items:
			$maxitems = t3lib_div::intInRange( $this->_tcaConfig[ 'maxitems' ], 0 );
			if( !$maxitems ) { $maxitems = 100000; }
            
			$minitems = t3lib_div::intInRange( $this->_tcaConfig[ 'minitems' ], 0 );
			
			// Register the required number of elements:
			$this->_pObj->requiredElements[ $this->_PA[ 'itemFormElName' ] ] = array(
				$minitems, $maxitems, 'imgName' => $this->_table . '_' . $this->_row[ 'uid' ] . '_' . $this->_field
			);

			if( $this->_tcaConfig[ 'treeView' ] AND $this->_tcaConfig[ 'foreign_table' ] ) {
				// get default items
				$defItems = array(
				);
				if( is_array( $this->_tcaConfig[ 'items' ] ) && $this->_table == 'tt_content' && $this->_row[ 'CType' ] == 'list' && $this->_row[ 'list_type' ] == 9 && $this->_field == 'pi_flexform' ) {
					reset( $this->_tcaConfig[ 'items' ] );
					while( list($itemName, $itemValue) = each( $this->_tcaConfig[ 'items' ] ) ) {
						if( $itemValue[ 0 ] ) {
							$ITitle = $GLOBALS[ 'LANG' ]->sL( $itemValue[ 0 ] );
							$defItems[] = '<a href="#" onclick="setFormValueFromBrowseWin(\'data[' . $this->_table . '][' . $this->_row[ 'uid' ] . '][' . $this->_field . '][data][sDscr][lDEF][descriptors][vDEF]\',' . $itemValue[ 1 ] . ',\'' . $ITitle . '\'); return false;" style="text-decoration:none;">' . $ITitle . '</a>';
						}
					}
				}
				
				$_treeContent = '<div id="' . $this->_treeName . '" class="' . $this->_treeName . '">' . $this->renderCatTree() . '</div>';
				
				if( $defItems[ 0 ] ) { // add default items to the tree table. In this case the value [not categorized]
					$this->treeItemC += count( $defItems );
					$_treeContent .= '<table border="0" cellpadding="0" cellspacing="0"><tr>
							<td>' . $GLOBALS[ 'LANG' ]->sL( $this->_tcaConfig[ 'itemsHeader' ] ) . '&nbsp;</td><td>' . implode( $defItems, '<br />' ) . '</td>
							</tr></table>';
				}
				
				
				// find recursive categories or "storagePid" related errors and if there are some, add a message to the $errorMsg array.
				// 					$errorMsg = $this->findRecursiveCategories($this->_PA,$this->_row,$this->_table,$this->storagePid,$this->treeIDs) ;
				$errorMsg = array();
				
				$width = 300; // default width for the field with the category tree
				if( (int)$this->_extConfArr[ 'descriptorsTreeWidth' ] ) { // if a value is set in extConf take this one.
					
					$width = t3lib_div::intInRange( $this->_extConfArr[ 'descriptorsTreeWidth' ], 1, 600 );
				
				} elseif( $GLOBALS[ 'CLIENT' ][ 'BROWSER' ] === 'msie' ) { // to suppress the unneeded horizontal scrollbar IE needs a width of at least 320px
					
					$width = 330;
				}
				
				// conf autoSizeMax from tca
				$this->_tcaConfig[ 'autoSizeMax' ] = t3lib_div::intInRange( $this->_tcaConfig[ 'autoSizeMax' ], 0 );
				
				$height = $this->_tcaConfig[ 'autoSizeMax' ] ? t3lib_div::intInRange( $this->treeItemC + 2, t3lib_div::intInRange( $this->_tcaConfig[ 'size' ], 1 ), $this->_extConfArr[ 'descriptorsTreeHeigth' ] ) : $this->_tcaConfig[ 'size' ];
				// hardcoded: 16 is the height of the icons
				$height = $height * 16;
				
				$divStyle = 'position:relative; left:0px; top:0px; height:' . $height . 'px; width:' . $width . 'px;border:solid 1px;overflow:auto;background:#fff;margin-bottom:5px;';
				$thumbnails = '<div  name="' . $this->_PA[ 'itemFormElName' ] . '_selTree" id="tree-div" style="' . htmlspecialchars( $divStyle ) . '">';
				$thumbnails .= $_treeContent;
				$thumbnails .= '</div>';
			
			} else {
				
				$sOnChange = 'setFormValueFromBrowseWin(\'' . $this->_PA[ 'itemFormElName' ] . '\',this.options[this.selectedIndex].value,this.options[this.selectedIndex].text); ' . implode( '', $this->_PA[ 'fieldChangeFunc' ] );
				
				// Put together the select form with selected elements:
				$selector_itemListStyle = isset( $this->_tcaConfig[ 'itemListStyle' ] ) ? ' style="' . htmlspecialchars( $this->_tcaConfig[ 'itemListStyle' ] ) . '"' : ' style="' . $this->_pObj->defaultMultipleSelectorStyle . '"';
				$itemArray = array(
				);
				$size = $this->_tcaConfig[ 'autoSizeMax' ] ? t3lib_div::intInRange( count( $itemArray ) + 1, t3lib_div::intInRange( $this->_tcaConfig[ 'size' ], 1 ), $this->_tcaConfig[ 'autoSizeMax' ] ) : $this->_tcaConfig[ 'size' ];
				$thumbnails = '<select style="width:150px;" name="' . $this->_PA[ 'itemFormElName' ] . '_sel"' . $this->_pObj->insertDefStyle( 'select' ) . ( $size ? ' size="' . $size . '"' : '' ) . ' onchange="' . htmlspecialchars( $sOnChange ) . '"' . $this->_PA[ 'onFocus' ] . $selector_itemListStyle . '>';

				
				foreach ( $selItems as $p ) {
					
					$thumbnails .= '<option value="' . htmlspecialchars( $p[ 1 ] ) . '">' . htmlspecialchars( $p[ 0 ] ) . '</option>';
				}
				$thumbnails .= '</select>';
			
			}
			
			// Perform modification of the selected items array:
			$itemArray = t3lib_div::trimExplode( ',', $this->_PA[ 'itemFormElValue' ], 1 );
			foreach ( $itemArray as $tk=>$tv ) {
				$tvP = explode( '|', $tv, 2 );
				$evalValue = rawurldecode( $tvP[ 0 ] );
				if( in_array( $evalValue, $removeItems ) && !$this->_PA[ 'fieldTSConfig' ][ 'disableNoMatchingValueElement' ] ) {
					$tvP[ 1 ] = rawurlencode( $nMV_label );
					// 					} elseif (isset($this->_PA['fieldTSConfig']['altLabels.'][$evalValue])) {
					// 						$tvP[1] = rawurlencode($this->_pObj->sL($this->_PA['fieldTSConfig']['altLabels.'][$evalValue]));
				} else {
					$tvP[ 1 ] = rawurldecode( $tvP[ 1 ] );
				}
				$itemArray[ $tk ] = implode( '|', $tvP );
			}
			$sWidth = 150; // default width for the left field of the category select
			if( (int)$this->_extConfArr[ 'descriptorsSelectedWidth' ] ) {
				
				$sWidth = t3lib_div::intInRange( $this->_extConfArr[ 'descriptorsSelectedWidth' ], 1, 600 );
			}
			$params = array(
				'size'=>$this->_tcaConfig[ 'size' ], 'autoSizeMax'=>t3lib_div::intInRange( $this->_tcaConfig[ 'autoSizeMax' ], 0 ),
				#'style' => isset($this->_tcaConfig['selectedListStyle']) ? ' style="'.htmlspecialchars($this->_tcaConfig['selectedListStyle']).'"' : ' style="'.$this->_pObj->defaultMultipleSelectorStyle.'"',
				'style'=>' style="width:' . $sWidth . 'px;"', 'dontShowMoveIcons'=>( $maxitems <= 1 ),
						 'maxitems'=>$maxitems, 'info'=>'', 'headers'=>array(
					'selector'=>$this->_pObj->getLL( 'l_selected' ) . ':<br />', 'items'=>$this->_pObj->getLL( 'l_items' ) . ':<br />'
				), 'noBrowser'=>1, 'thumbnails'=>$thumbnails
			);
			$item .= $this->_pObj->dbFileIcons( $this->_PA[ 'itemFormElName' ], '', '', $itemArray, '', $params, $this->_PA[ 'onFocus' ] );
			// Wizards:
			$altItem = '<input type="hidden" name="' . $this->_PA[ 'itemFormElName' ] . '" value="' . htmlspecialchars( $this->_PA[ 'itemFormElValue' ] ) . '" />';
			$item = $this->_pObj->renderWizards( array(
				$item, $altItem
			), $this->_tcaConfig[ 'wizards' ], $this->_table, $this->_row, $this->_field, $this->_PA, $this->_PA[ 'itemFormElName' ], array(
			) );
		
		}

		return $this->NA_Items . $item;
	}
	
	/**
	 * This function checks if there are categories selectable that are not allowed for this BE user and if the current record has
	 * already categories assigned that are not allowed.
	 * If such categories were found they will be returned and "$this->NA_Items" is filled with an error message.
	 * The array "$itemArr" which will be returned contains the list of all non-selectable categories. This array will be added to "$treeViewObj->TCEforms_nonSelectableItemsArray". If a category is in this array the "select item" link will not be added to it.
	 *
	 * @param	array		$PA: the paramter array
	 * @param	string		$SPaddWhere: this string is added to the query for categories when "useStoragePid" is set.
	 * @param	[type]		$allowedItemsList: ...
	 * @return	array		array with not allowed categories
	 * @see tx_docdb_tceFunc_selectTreeView::wrapTitle()
	 */


	function getNotAllowedItems( &$PA, $SPaddWhere, $allowedItemsList = false ) {

		$fTable = $PA[ 'fieldConf' ][ 'config' ][ 'foreign_table' ];
		
		$itemArr = array(
		);
		if(/*$allowedItemsList*/ false ) {
			// get all categories
			$_res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( 'uid', $fTable, '1=1' . $SPaddWhere . ' ' );
			while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $_res ) ) {
				
				if( !t3lib_div::inList( $allowedItemsList, $row[ 'uid' ] ) ) { // remove all allowed categories from the category result
				
					$itemArr[] = $row[ 'uid' ];
				}
			}
			
			if( !$PA[ 'row' ][ 'sys_language_uid' ] && !$PA[ 'row' ][ 'l18n_parent' ] ) {
				$catvals = explode( ',', $PA[ 'row' ][ 'category' ] ); // get categories from the current record
				// 				debug($catvals,__FUNCTION__);
				$notAllowedCats = array(
				);
				foreach ( $catvals as $k ) {
					$c = explode( '|', $k );
					if( $c[ 0 ] && !t3lib_div::inList( $allowedItemsList, $c[ 0 ] ) ) {
						$notAllowedCats[] = '<p style="padding:0px;color:red;font-weight:bold;">- ' . $c[ 1 ] . ' <span class="typo3-dimmed"><em>[' . $c[ 0 ] . ']</em></span></p>';
					}
				}
				if( $notAllowedCats[ 0 ] ) {
					$this->NA_Items = '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src="gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">SAVING DISABLED!! <br />This record has the following categories assigned that are not defined in your BE usergroup: ' . urldecode( implode( $notAllowedCats, chr( 10 ) ) ) . '</td></tr></tbody></table>';
				}
			}
		}
		return $itemArr;
	}

	
	/**
	 * This functions displays the title field of a news record and checks if the record has categories assigned that are not allowed for the current BE user.
	 * If there are non allowed categories an error message will be displayed.
	 *
	 * @param	array		$PA: the parameter array for the current field
	 * @param	object		$fobj: Reference to the parent object
	 * @return	string		the HTML code for the field and the error message
	 */


	function displayTypeFieldCheckCategories( &$PA, &$fobj ) {
		
		/**
		 *  @todo  remove depandencies to tt_news
		 */
		
		if( !is_object( $this->divObj ) ) {
			$this->divObj = t3lib_div::makeInstance( 'tx_ttnews_div' );
		}
		
		if( $this->divObj->useAllowedCategories() ) {
			$notAllowedItems = array();
			//			if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] ) ) { // get doc_db extConf array
			//
			//				$this->_extConfArr = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'doc_db' ] );treeOrderBy
			//			}
			
			$SPaddWhere = ' AND tx_docdb_descriptor.pid IN (' . $this->_extConfArr[ 'dscrStoragePid' ] . ')';
			
			//$SPaddWhere = '';
			
			if( !$this->divObj->allowedItemsFromTreeSelector ) {
				
				$notAllowedItems = $this->getNotAllowedItems( $this->_PA, $SPaddWhere );
			
			} else {
				
				$treeIDs = $this->divObj->getCategoryTreeIDs();
				$notAllowedItems = $this->getNotAllowedItems( $this->_PA, $SPaddWhere, $treeIDs );
			}
			
			if( $notAllowedItems[ 0 ] ) {
				$uidField = $this->_row[ 'l18n_parent' ] && $this->_row[ 'sys_language_uid' ] ? $this->_row[ 'l18n_parent' ] : $this->_row[ 'uid' ];
				
				if( $uidField ) {
					// get categories of the record in db
					if( $table == 'tt_content' ) {
						
						$catres = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( 'uid, title, mmsorting', 'tx_docdb_descriptor', ' tx_docdb_descriptor.uid IN (' . $uidField . ') ' . $SPaddWhere, '', '', '' );
					} else {
						$catres = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECT_mm_query( 'tx_docdb_descriptor.uid,tx_docdb_descriptor.title AS title,tx_docdb_pages_doc_descriptor_mm.sorting AS mmsorting', 'pages', 'tx_docdb_pages_doc_descriptor_mm', 'tx_docdb_descriptor', ' AND tx_docdb_pages_doc_descriptor_mm.uid_local=' . $uidField . $SPaddWhere, '', 'title' );
					}
					$NACats = array(
					);
					if( $catres ) {
						while( $catrow = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $catres ) ) {
							if( $catrow[ 'uid' ] && $notAllowedItems[ 0 ] && in_array( $catrow[ 'uid' ], $notAllowedItems ) ) {
								
								$NACats[] = '<p style="padding:0px;color:red;font-weight:bold;">- ' . $catrow[ 'title' ] . ' <span class="typo3-dimmed"><em>[' . $catrow[ 'uid' ] . ']</em></span></p>';
							}
						}
					}
					
					if( $NACats[ 0 ] ) {
						$NA_Items = '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src="gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">SAVING DISABLED!! <br />' . ( $this->_row[ 'l18n_parent' ] && $this->_row[ 'sys_language_uid' ] ? 'The translation original of this' : 'This' ) . ' record has the following categories assigned that are not defined in your BE usergroup: ' . implode( $NACats, chr( 10 ) ) . '</td></tr></tbody></table>';
					}
				}
			}
		}
		// unset foreign table to prevent adding of categories to the "type" field
		$PA[ 'fieldConf' ][ 'config' ][ 'foreign_table' ] = '';
		$PA[ 'fieldConf' ][ 'config' ][ 'foreign_table_where' ] = '';
		if( !$this->_row[ 'l18n_parent' ] && !$this->_row[ 'sys_language_uid' ] ) { // render "type" field only for records in the default language
			$fieldHTML = $fobj->getSingleField_typeSelect( $table, $field, $this->_row, $PA );
		}
		
		return $NA_Items . $fieldHTML;
	}
	
	/**
	 * extract field name used as treeName and div
	 *
	 * @param String    $cmd
	 * @return
	 */


	protected function _stripCmdSuffix( $cmd ) {

		return substr( $cmd, strrpos( $cmd, '_' ) + 1, strlen( $cmd ) );
	}
}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_treeview.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_treeview.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_treeview.php']);
}

}

