<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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

require_once ("class.display_tree.php");

/**
 * Script class for the Element Browser window.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class ux_TBE_browser_recordList extends TBE_browser_recordList {
	
	/**
	 * Creates the list of tree of records from a single table
	 *
	 * @param	string		Table name
	 * @param	integer		Page id
	 * @param	string		List of fields to show in the listing. Pseudo fields will be added including the record header.
	 * @return	string		HTML table with the listing for the record.
	 */
	function getTable($table,$id,$rowlist)	{
		global $TCA;

			// Loading all TCA details for this table:
		t3lib_div::loadTCA($table);
		
		// display tree is asked by the ctrl key of the TCA
		if ($TCA[$table]['ctrl']['treeViewInBrowseWindow']) {
		
				// Init
			$addWhere = '';
			$titleCol = $TCA[$table]['ctrl']['label'];
			$thumbsCol = $TCA[$table]['ctrl']['thumbnail'];
			$l10nEnabled = $TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'] && !$TCA[$table]['ctrl']['transOrigPointerTable'];
	
				// Place the $titleCol as reference name
			$this->fieldArray=array();
			$this->fieldArray[] = $titleCol;	// Add title column

				// Creating the list of fields to include in the SQL query:
			$selectFields = $this->fieldArray;
			$selectFields[] = 'uid';
			$selectFields[] = 'pid';		
			$selFieldList = implode(',',$selectFields);		// implode it into a list of fields for the SQL-statement.
	
				// Create the SQL query for selecting the elements in the listing:
			$queryParts = $this->makeQueryArray($table, $id,$addWhere,$selFieldList);	// (API function from class.db_list.inc)
			$this->setTotalItems($queryParts);		// Finding the total amount of records on the page (API function from class.db_list.inc)
	
				// Init:
			$dbCount = 0;
			$out = '';
	
				// If the count query returned any number of records, we perform the real query, selecting records.
			if ($this->totalItems)	{
			      //$out .= '<pre>'.print_r($queryParts, true).'</pre>';
				$result = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
				$dbCount = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
				//$out .= '<pre>Count: '.$dbCount.'</pre>';
			}
	
			$LOISmode = $this->listOnlyInSingleTableMode && !$this->table;

				// If any records was selected, render the list:
			if ($dbCount)	{
	
					// Half line is drawn between tables:
				$theData = Array();
				$theData[$titleCol] = '<img src="clear.gif" width="'.($GLOBALS['SOBE']->MOD_SETTINGS['bigControlPanel']?'230':'350').'" height="1" alt="" />';
				$out.=$this->addelement(0,'',$theData,'',$this->leftMargin);
				
				
					// Header line is drawn
				$theData = Array();
				$theData[$titleCol] = $this->linkWrapTable($table,'<span class="c-table">'.$GLOBALS['LANG']->sL($TCA[$table]['ctrl']['title'],1).'</span> ('.$this->totalItems.') <img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/'.($this->table?'minus':'plus').'bullet_list.gif','width="18" height="12"').' hspace="10" class="absmiddle" title="'.$GLOBALS['LANG']->getLL(!$this->table?'expandView':'contractView',1).'" alt="" />');
	
				$out.=$this->addelement(1,'',$theData,' class="c-headLineTable"','');

				// Initialize tree object:
				$tree = t3lib_div::makeInstance('display_tree');	
				$tree->init(' AND pid='.$GLOBALS['SOBE']->browser->expandPage.' ','title');
				$tree->title='Descriptors';
				
				// set information about the record from the incoming form in the main windows
				$tree->setSourceRecord($table,$GLOBALS['SOBE']->browser->bparams);
				
				//$out.='<pre>expandPage:'.print_r($GLOBALS['SOBE']->browser->expandPage, true).'</pre><hr />';
				$out.='<tr><td nowrap="nowrap"></td><td nowrap="nowrap">'.$tree->getBrowsableTree().'</td></tr>';
				
					// ... and it is all wrapped in a table:
				$out='
	

				<!--
					DB listing of elements:	"'.htmlspecialchars($table).'"
				-->
					<table border="0" cellpadding="0" cellspacing="0" class="typo3-dblist'.($LOISmode?' typo3-dblist-overview':'').'">
						'.$out.'
					</table>';
	
					// Output csv if...
				if ($this->csvOutput)	$this->outputCSV($table);	// This ends the page with exit.
	
			}
	
				// Return content:
			return $out;
		} else {
			// Display table in a list
			return parent::getTable($table,$id,$rowlist);
		}
	}
		
	function getSearchBox() {
		return;
	}
}

?>
