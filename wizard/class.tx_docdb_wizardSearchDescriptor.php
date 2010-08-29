<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Sk�rh�j (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Search keyword wizard
 *
 * @author Olivier Schopfer <ops@wcc-coe.org>
 * 
 * $Id: class.tx_docdb_wizardSearchDescriptor.php 208 2010-03-13 04:52:54Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-03-13 05:52:54 +0100 (sam 13 mar 2010) $
 * 
 * Rewrite to make it more friendly
 * @author     Laurent Cherpit  <laurent@eosgarden.com>
 * @version    2.0.0
 * @package    TYPO3
 * @subpackage doc_db
 */

unset($MCONF);
include ('conf.php');

require_once($GLOBALS['BACK_PATH'].'init.php');
require_once($GLOBALS['BACK_PATH'].'template.php');

require_once(t3lib_extMgm::extPath('doc_db') . 'classes/class.tx_docdb_div.php');
require_once(t3lib_extMgm::extPath('ta_xajaxwrapper') . 'class.tx_taxajaxwrapper.php');
$GLOBALS['LANG']->includeLLFile('EXT:lang/locallang_wizards.xml');

/**
 * Script Class for wizard
 *
 * @author	Laurent Cherpit
 * 
 * @todo make it better, i have not taken a good way :/
 * no time to do it now
 */
class tx_docdb_wizardSearchDescriptor
{
	
	/**
	 * Wizard parameters, coming from TCEforms linking to the wizard.
	 * 
	 * @var $_P
	 */
	private $_P = array();
	
	/**
	 * Serialized functions for changing the field
	 * Necessary to call when the value is transferred to the parent TCEform
	 * 
	 * @var $_fieldChangeFunc
	 */
	private $_fieldChangeFunc = '';
	
	/**
	 * Field name (from opener script)
	 * 
	 * @var $_itemName
	 */
	private $_itemName = '';
	
	/**
	 * Form name (from opener script)
	 * 
	 * @var $_formName
	 */
	private $_formName = '';
	
	/**
	 * ID of element in opener script for which to set keyword.
	 * 
	 * @var $_md5ID
	 */
	private $_md5ID = '';
	
	/**
	 * current existing value in opener selectbox
	 * 
	 * @var $_currentValue
	 */
	private $_currentValue = '';

    /**
     * uids of descriptors related to pages: use from plugin
     *
     * @var $_dscrRelToPage
     */
    private static $_dscrRelToPage = array();

    /**
     * uids of descriptors are leaf: use from page
     *
     * @var $_dscrAreLeaf
     */
    private static $_dscrAreNotLeaf = array();

	/**
	 * New Line char
	 * 
	 * @var $_NL
	 */
	protected static $_NL = '';

	/**
	 * defaut beCharset
	 * 
	 * @var $_charset
	 */
	protected static $_charset = '';
	
	/**
	 * Script backPath to typo3
	 * 
	 * @var $_backPath
	 */
	protected $_backPath = '';
	
	/**
	 * document template object
	 *
	 * @var mediumDoc
	 */
	protected $_doc     = NULL;

	/**
	 * instance of ta_xajaxwrapper Object
	 *
	 * @var mediumDoc
	 */ 
	protected $_xajax   = NULL;

	/**
	 * Accumulated content.
	 *
	 * @var mediumDoc
	 */
	protected $_content = '';
	
	
	/**
	 * Initialises the Class
	 *
	 * @return	void
	 */
	public function init() {
		
		
		$this->_backPath = $GLOBALS['BACK_PATH'];
		
		// Sets the new line character
		self::$_NL = chr(10);
		
		// Initialize document object:
		$this->_doc = t3lib_div::makeInstance('bigDoc');
		
		// Setting GET vars (used in frameset script):
		$this->P = t3lib_div::_GP('P');

		// Setting GET vars (used in script):
		$this->_fieldChangeFunc = $this->P['fieldChangeFunc'];
		$this->_itemName        = $this->P['itemName'];
		$this->_formName        = $this->P['formName'];
		$this->_md5ID           = $this->P['md5ID'];
		$this->_currentValue    = rawurldecode($this->P['currentValue']);

        // test if from flexform referrer
		if(strpos($this->_itemName, 'pi_flexform')) {

            self::$_dscrRelToPage = $this->_getDescrLeafRelatedDoc();

        } else if(strpos($this->_itemName, 'pages')) {

            self::$_dscrAreNotLeaf = $this->_getDescrAllNotLeaf();
        }

		// init ta_xajax wrapper
		$this->_xajax = t3lib_div::makeInstance('tx_taxajaxwrapper');
		
		if(isset($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'])) {
			// mmm look that in depth, please L.
			self::$_charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
			$this->_xajax->setCharEncoding('UTF-8');
			$this->_xajax->decodeUTF8InputOn();
			
		} else {
			self::$_charset = 'iso-8859-1';
			$this->_xajax->setCharEncoding('ISO-8859-1');
			$this->_xajax->encodeUTF8InputOn();
		}
		
		$this->_xajax->setWrapperPrefix('xajax_tx_doc_db_');
		$this->_xajax->statusMessagesOn();

		
		/**
		 *	Register the names of the PHP functions you want to be able to call through xajax
		 *
		 * $xajax->registerFunction(array('functionNameInJavescript', &$object, 'methodName'));
		 */
		
		$this->_xajax->registerFunction(array('mySearchFunction', $this, 'mySearchFunction'));

		$this->_doc->backPath           = $this->_backPath;
		$this->_doc->docType            = 'xhtml_trans';
		$this->_doc->inDocStylesArray[] = '
			#containerL {float:left;width:58%;margin-right:2%;}
			#containerR {float:left;width:40%;}
			#MainForm {margin: 0 0 15px 0;}
			DIV#insertResults TABLE TR TD,
			TABLE#listSelectedItems TR TD {border-bottom: 1px dashed #ccc;}
			.clear{ clear:both;}';
		$this->_doc->JScode             = $this->_doc->wrapScriptTags('
			
			window.onload = function() { 
				for(i = 0; i < FuncOL.length; i++) { FuncOL[i](); }
			}
			// Global app
			var TX_DOCDB = {};
			
			// store sel.
			TX_DOCDB.items = {};
			
			TX_DOCDB.items = function() {
				
				// private array
				var store = [];
				
				var sortByTitle = function (a, b) {
					
					var a = a.prop.iTitle.toLowerCase(),
					b = b.prop.iTitle.toLowerCase();
					
					return ((a < b) ? -1 : ((a > b) ? 1 : 0));
				};
				
				return {
					
					add : function(item) {
						
						store[item.prop.iId] = item;
					},
					remove : function(item) {
						
						delete store[item.prop.iId];
					},
					get : function() {
						
						var nStore = TX_DOCDB.cleanArray(store);
						nStore.sort(sortByTitle);
						
						return nStore;
					}
				};
			}();
			
			TX_DOCDB.cleanArray = function(arr) {
				
				var newArray = [], el;
				
				for(el in arr) {
					
					if(arr[el] !== undefined && arr[el] !== null && typeof arr[el] !== \'function\') {
						
						newArray.push(arr[el]);
					}
				}
				
				return newArray;
			};
			
			TX_DOCDB.search = function() {
				
				var t = false;
				return function() {
					
					if(t) {
						
						clearTimeout(t);
					}
					
					t = setTimeout(TX_DOCDB.execSearch, 700);
				};
			}();
			
			TX_DOCDB.execSearch = function() {
				return function() {
					
					xajax_tx_doc_db_mySearchFunction(xajax.getFormValues(\'MainForm\'));
				}
			}();
			
			TX_DOCDB.addEventOnItems = function(chkboxGroupName) {
				
				var ckBox = document.getElementsByName(chkboxGroupName);
				
				for(var i=0; i < ckBox.length; i++) {
					
					var id = ckBox[i].value,
					// add on click to a
					dLink = document.getElementById(\'a_\' + id),
					tr   = document.getElementById(\'tr_\' + id),
                    prop = {
						iId    : id,
						iName  : document.getElementById(\'i_\' + id).value,
						iTitle : document.getElementById(\'t_\' + id).value
					};
					
					// ref to properties
					tr.prop       = prop;
					ckBox[i].tr   = tr;
					dLink.prop    = prop;
					
					dLink.onclick = function(i) {
						
						return function(e) {
							
							TX_DOCDB.setFormValueFromBrowseWin(this.prop);
							window.close();
							return false;
						}
					}(i);
					
					// onclick checkBox, add node to selection
					ckBox[i].onclick = function(i) {
						
						return function(e) {
							
							TX_DOCDB.items.add(this.tr);
							TX_DOCDB.listItemsInContainer(\'listSelectedItems\');
							
							// change onclick event to remove node
							this.title = \'Remove from selection\';
							this.onclick = function() {
								
								return function(e) {
									
									TX_DOCDB.items.remove(this.tr);
									TX_DOCDB.listItemsInContainer(\'listSelectedItems\')
								}
							}();
						}
					}(i);
				}
			};
			
			TX_DOCDB.listItemsInContainer = function(newContainer) {
				
				var nCont = document.getElementById(newContainer),
				tbody = document.createElement(\'tbody\'),
				items = TX_DOCDB.items.get();
				
				for(var i=0; i< items.length; i++){
					
					tbody.appendChild(items[i]);
				}
				
				if(nCont.hasChildNodes()){
					
					nCont.replaceChild(tbody, nCont. firstChild);
					
				} else {
					
					nCont.appendChild(tbody);
				}
			};
			
			TX_DOCDB.importSelection = function() {
				
				var items = TX_DOCDB.items.get();
				
				for(var i=0; i< items.length; i++) {
					TX_DOCDB.setFormValueFromBrowseWin(items[i].prop);
				}
				window.close();
				return false;
			};
			
			TX_DOCDB.setFormValueFromBrowseWin = function(iEl) {
				
				parent.window.opener.setFormValueFromBrowseWin(iEl.iName, iEl.iId, iEl.iTitle);
			};
			
			window.onload= function() { document.MainForm.textSearch.focus(); }
			');
		
		// Add xajax javascriptcode to the header'
		$this->_doc->JScode .= $this->_xajax->getJavascript($this->_backPath);
		
		// Start page:
		$this->_content .= $this->_doc->startPage('Search a descriptor');
		
		$this->_content .= '<div style="position:absolute;top:9px;left:170px;"><em>Autosearch: min. 4 c. or 2 c. and press enter</em></div>';
	}

	/**
	 * Main Method, rendering content
	 *
	 * @return	void
	 */
	public function main() {
		
		// store
		$_content = array();
		
		$formElements = array('<form id="MainForm" name="MainForm" action="" method="post" onSubmit="return false;">', '</form>');
		
		// form
		$_content[] = $formElements[0];
		
		// Table with the search box:
		$_content[] = '
				<!-- Search box: -->
				<table border="0" cellpadding="3" cellspacing="0" class="bgColor4">
					<tr>
						<td> ' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.enterSearchString',1) .
							'<input' .
							' type="text"' .
							' name="textSearch"' .
							' value="" ' . $GLOBALS['TBE_TEMPLATE']->formWidth(20) .
							' onchange="if(this.value.length > 1 && this.value.length < 4){TX_DOCDB.search();}"' .
							' onblur="return false;"' .
							' onkeyup="if(this.value.length > 3){TX_DOCDB.search();} else {return false;}" />&nbsp;' .
							'<input' .
							' type="checkbox"' .
							' name="textStartBy"' .
							' value="1" />&nbsp;at the beginning&nbsp;
						</td>
					</tr>
				</table>
				<!-- Hidden fields with values that has to be kept constant -->
					<input type="hidden" name="fieldChangeFunc" value="' . htmlspecialchars($this->_fieldChangeFunc['TBE_EDITOR_fieldChanged']).'" />
					<input type="hidden" name="itemName" value="'.htmlspecialchars($this->_itemName).'" />
					<input type="hidden" name="formName" value="'.htmlspecialchars($this->_formName).'" />
					<input type="hidden" name="md5ID" value="'.htmlspecialchars($this->_md5ID).'" />';
			
			// End form
			$_content[] = $formElements[1];
			
			// Placeholder for actual search results
			$_content[] = '<div id="containerL"><strong>Search results</strong>';
			$_content[] = '<table border="0" width="100%" cellspacing="0" cellpadding="3" bgcolor="' . $this->_doc->bgColor4 . '">';
			$_content[] = '<tr><th colspan="2" align="left">Descriptors</th><th width="8%">-&gt;</th></tr>';
			$_content[] = '</table>';
			$_content[] = '<div id="insertResults"></div></div>';

			$_content[] = '<div id="containerR"><strong>Selected items</strong>';
			$_content[] = '<div style="float:right;"><a href="#" onclick="TX_DOCDB.importSelection();">Import selection&nbsp;';
			$_content[] = '<img ' . t3lib_iconWorks::skinImg($this->_backPath, 'gfx/import.gif', '') . ' alt="" hspace="0" vspace="0" border="0" align="top"></a></div>';
			$_content[] = '<div class="clear"></div><table border="0" width="100%" cellspacing="0" cellpadding="3" bgcolor="' . $this->_doc->bgColor4 . '">';
			$_content[] = '<tr><th colspan="2" align="left">Descriptors</th><th width="20%">';
			
			$_content[] = '</th></tr>';
			$_content[] = '</table>';
			
			$_content[] = '<div><table id="listSelectedItems" border="0" width="100%" cellspacing="0" cellpadding="3" bgcolor="' . $this->_doc->bgColor2 . '"></table></div></div>';
			$_content[] = '<div class="clear"></div>';

			// Output:
			$this->_content.=$this->_doc->section('Search a descriptor', implode(self::$_NL, $_content), 0,1);
	}
	
	
	public function mySearchFunction($arg) {
		
		// store
		$_content  = array();
        $_addWhere = '';
		$_startBy  = '';



		if($arg['textSearch']) {

            if(count(self::$_dscrRelToPage)) { // if come from flexform (tt_content)

                $_addWhere = ' AND uid IN(' . implode(',', self::$_dscrRelToPage) .')';

            } else if(count(self::$_dscrAreNotLeaf)) { // if come from page properties

                $_addWhere = ' AND uid NOT IN(' . implode(',', self::$_dscrAreNotLeaf) .')';
            }

			$_startBy = ($arg['textStartBy'] ? '' : '%');
			
			$res = tx_docdb_div::_sqlGetRows(
				'tx_docdb_descriptor',
				'*',
				'title LIKE \'' . $_startBy . $GLOBALS['TYPO3_DB']->quoteStr(
					$GLOBALS['TYPO3_DB']->escapeStrForLike(
						$arg['textSearch'], 'tx_docdb_descriptor'
					),
					'tx_docdb_descriptor') .'%\'' . $_addWhere,
				'',
				' title ASC ',
				'50'
			);
			
			if(is_array($res) && count($res) > 0) {
				
				$formEls = array('<form id="resultForm" name="resultForm" action="" method="post" onSubmit="return false;">', '</form>');
				
				$_content[] = $formEls[0];
				$_content[] = '<table border="0" width="100%" cellspacing="0" cellpadding="3" bgcolor="' . $this->_doc->bgColor2 . '">';
				
				foreach($res as $k => $row) {
					
					$title = htmlspecialchars($row['title'], ENT_QUOTES, self::$_charset);
					
					// Search related descriptor-s
					if($row['dscr_related']) {
						
						$relRows = tx_docdb_div::_sqlGetRows(
							'tx_docdb_descriptor',
							'uid,title',
							'uid IN (' . $row['dscr_related'] . ')' . $_addWhere,
							'',
							'title ASC',
							'50'
						);
						
						// tooltip
						$related = tx_docdb_div::_getCssTooltip($relRows, $this->_backPath);
						
					}
					
					$_content[] = '<tr id="tr_' . $row['uid'] . '"><td>';
					$_content[] = '<a href="#"' .
												' id="a_' . $row['uid'] . '"' .
												' title="import this descriptor"' .
												'>';
					
					$_content[] = $title .'</a></td>';
					
					$_content[] = '<td width="4%" valign="top">' . $related . '</td>';
					
					$_content[] = '<td width="4%" valign="top" align="right">';
					
					// checkBox to select multiple
					$_content[] = '<input type="checkbox" name="dscrItems"' .
												' id="d_' . $row['uid'] . '"' .
												' value="' . $row['uid'] . '" />';
												
					$_content[] = '<input type="hidden"' .
												' id="t_' . $row['uid'] . '"' .
												' value="' . $title . '" />';
					
					$_content[] = '<input type="hidden"' .
												' id="i_' . $row['uid'] . '"' .
												' value="' . $this->_itemName . '" />';
					
					$_content[] = '</td></tr>';
				}
				
				$_content[] = '</table>';
				
				$_content[] = $formEls[1];
				
			} else {
				
				$_content[] = '<strong>No result</strong>';
			}
		}
		
		$objResponse = $this->_xajax->getResponseObj();
		$objResponse->addAssign('insertResults', 'innerHTML', implode(self::$_NL, $_content));
		$objResponse->addScript('TX_DOCDB.addEventOnItems(\'dscrItems\');');
		
		return $objResponse->getXML();
	}


	/**
	 * Returnes the sourcecode to the browser
	 *
	 * @return	void
	 */
	public function printContent() {
		
		$this->_xajax->processRequests();
		
		$this->_content.= $this->_doc->endPage();
		$this->_content = $this->_doc->insertStylesAndJS($this->_content);
		
		echo $this->_content;
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
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);

		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

			$dscrPidList[] = $row['uid'];
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		unset($row, $select, $from, $where, $groupBy, $orderBy, $limit);

		return $dscrPidList;
	}


	/**
	 * Get uid of matching descriptors, only those are leaf.
	 * and only descriptors actually related to documents
	 *
	 * @return : array  : ids of descriptors leaf related to doc
	 */
	private function _getDescrLeafRelatedDoc() {

		// dscr_pid unique list
		$dscrPidList  = array();

		$select  = 'd.uid';
		$from    = 'tx_docdb_descriptor d,pages p,tx_docdb_pages_doc_descriptor_mm mm';

		$where   = '(mm.uid_local=p.uid AND mm.uid_foreign=d.uid)';
		$where  .= ' AND (d.deleted=0 AND d.hidden=0) AND ((p.deleted=0 AND p.hidden=0) and p.doktype=198)';
		$where  .= ' AND d.uid NOT IN(SELECT dscr_pid FROM tx_docdb_descriptor)';

		$groupBy = 'd.uid';
		$orderBy = '';
		$limit   = '';

		// get
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);

		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

			$dscrPidList[] = $row['uid'];
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		unset($row, $select, $from, $where, $groupBy, $orderBy, $limit);

		return array_unique($dscrPidList);
	}


	/*
	 * sets dscr_pid list to be selected in rootline
	 *
	 * @param : array $childrenPids : children ids pass by ref
	 * @param : array $tempPids : parent level to process
	 */
//	private function _setFilteredRootline(&$childrenPids, $tempPids=array()) {
//
//		$tempPids = count($tempPids) > 0 ? $tempPids : $childrenPids;
//
//		$select  = 'dscr_pid';
//		$from    = 'tx_docdb_descriptor';
//		$where   = 'uid IN (' . implode(',', $tempPids) . ')';
//		$groupBy = 'dscr_pid';
//		$orderBy = '';
//		$limit   = '';
//
//
//		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
//			$select,
//			$from,
//			$where, $groupBy, $orderBy, $limit
//		);
//
//		$rows = array();
//		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res))) {
//
//			$rows[] = $row[0];
//		}
//
//		$GLOBALS['TYPO3_DB']->sql_free_result($res);
//
//		// store result in ref array
//		$childrenPids = array_merge($childrenPids, $rows);
//
//		// while not in root
//		if(! in_array('0', $rows)){
//
//			$this->_setFilteredRootline($childrenPids, $rows);
//		}
//        unset($rows);
//	}

    
	/*
	 * not used -- maybe in the next version and make it all client server com. in JSON
	 * and build table content with javascript only
	 */
	private function _addCurrentValue() {
//		$this->_currentValue
		$relRows = tx_docdb_div::_sqlGetRows(
			'tx_docdb_descriptor',
			'uid,title',
			'uid IN (' . $this->_currentValue . ')',
			'',
			'title ASC',
			'50'
		);
		
	}
}

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/wizard/class.tx_docdb_wizardSearchDescriptor.php']);
}

}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_docdb_wizardSearchDescriptor');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>