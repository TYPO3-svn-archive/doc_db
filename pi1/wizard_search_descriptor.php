<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skårhøj (kasperYYYY@typo3.com)
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
* 
 *
 * @author	Olivier Schopfer <ops@wcc-coe.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 
 *
 */


define('TYPO3_MOD_PATH', '../typo3conf/ext/doc_db/pi1/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='xMOD_tx_doc_db_cm1';
require($BACK_PATH.'init.php');
require($BACK_PATH.'template.php');
require_once(PATH_t3lib.'class.t3lib_stdgraphic.php');
require (t3lib_extMgm::extPath('xajax') . 'class.tx_xajax.php');
$LANG->includeLLFile('EXT:lang/locallang_wizards.xml');

/**
 * Script Class for wizard
 *
 * @author	Olivier Schopfer <ops@wcc-coe.org>
 */
class SC_wizard_search_descriptor {
      var $pageinfo;
	var $xajax;
		// GET vars:
	var $P;				// Wizard parameters, coming from TCEforms linking to the wizard.
	var $descriptorValue;	// Value of the current keyword picked.
	var $fieldChangeFunc;	// Serialized functions for changing the field... Necessary to call when the value is transferred to the TCEform since the form might need to do internal processing. Otherwise the value is simply not be saved.
	var $fieldName;		// Form name (from opener script)
	var $formName;		// Field name (from opener script)
	var $md5ID;			// ID of element in opener script for which to set keyword.
	var $showPicker;	// Internal: If false, a frameset is rendered, if true the content of the picker script.
	var $searchText;   // Text to be searched


	/**
	 * document template object
	 *
	 * @var smallDoc
	 */
	var $doc;
	var $content;				// Accumulated content.


	/**
	 * Initialises the Class
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		// parent::init();
		$this->xajax = new tx_xajax();
		$this->xajax->setCharEncoding('ISO-8859-1');
		$this->xajax->decodeUTF8InputOff();
		//$this->xajax->debugOn();
		/**
		 *	Register the names of the PHP functions you want to be able to call through xajax
		 *
		 * $xajax->registerFunction(array('functionNameInJavescript', &$object, 'methodName'));
		 */
		$this->xajax->registerFunction(array("tx_doc_db_mySearchFunction",&$this,"mySearchFunction"));

		// Setting GET vars (used in frameset script):
		$this->P = t3lib_div::_GP('P');

		// Setting GET vars (used in script):
		$this->descriptorValue = t3lib_div::_GP('descriptorValue');
		$this->fieldChangeFunc = $this->P['fieldChangeFunc'];
		$this->fieldName = $this->P['itemName'];
		$this->formName = $this->P['formName'];
		$this->md5ID = $this->P['md5ID'];
		$this->searchText = $this->P['textSearch'];

		// Setting field-change functions:
		$fieldChangeFuncArr = unserialize($this->fieldChangeFunc);
		$update = '';
		if (is_array($fieldChangeFuncArr))	{
			unset($fieldChangeFuncArr['alert']);
			foreach($fieldChangeFuncArr as $v)	{
				$update.= '
				parent.'.$v;
			}
		}

		// Initialize document object:
		$this->doc = t3lib_div::makeInstance('smallDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';
		$this->doc->JScode='<script type="text/javascript">
		      /*<![CDATA[*/
			function jumpToUrl(URL,formEl)	{	//
				alert(URL);
                        document.location = URL;
			}
			function checkReference()	{	//
				if (window.opener && window.opener.document && window.opener.document.'.$this->formName.' && window.opener.document.'.$this->formName.'["'.$this->fieldName.'"])	{
					return window.opener.document.'.$this->formName.'["'.$this->fieldName.'"];
				} else {
					alert(\'error\');
				}
			}
			function setValue(input)	{	//
				var field = checkReference();
				if (field)	{
				      alert(field);
					field.value = input;
					'.$update.'
				}
			}
			function getValue()	{	//
				var field = checkReference();
				alert(field);
				return field.value;
			}
			/*]]>*/
			</script>
		' . $this->xajax->getJavascript(t3lib_div::resolveBackPath($BACK_PATH . '../' . t3lib_extMgm::siteRelPath('xajax')));	// Add xajaxs javascriptcode to the header'
		$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
						if (top.fsMod) top.fsMod.recentIds["web"] = ' . intval($this->id) . ';
					</script>
				';

		// Start page:
		$this->content.=$this->doc->startPage('Search a descriptor');
	}

	/**
	 * Main Method, rendering either colorpicker or frameset depending on ->showPicker
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;

		// Setting form-elements, if applicable:
		$formElements = array('', '');
		//$formAction = $this->linkThisScriptSel($this->addParams);
		$formAction = 'wizard_search_descriptor.php';
		$formElements = array('<form id="MainForm" name="MainForm" action="" method="post" onSubmit="return false;">', '</form>');

		// Table with the search box:
		$content.= $formElements[0].'
				<!--
					Search box:
				-->
				<table border="0" cellpadding="2" cellspacing="0" class="bgColor4">
					<tr>
						<td> '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.enterSearchString',1).' <input type="text" name="textSearch" value="'.htmlspecialchars($this->searchText).'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(10).' /></td>
						<td><input type="button" name="search" value="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.search',1).'" onclick="xajax_tx_doc_db_mySearchFunction(xajax.getFormValues(\'MainForm\'));" /><input type="hidden" name="descriptorValue" value="'.htmlspecialchars($this->descriptorValue).'" /></td>
					</tr>
				</table>
				<!-- Hidden fields with values that has to be kept constant -->
					<input type="hidden" name="showPicker" value="1" />
					<input type="hidden" name="fieldChangeFunc" value="'.htmlspecialchars($this->fieldChangeFunc).'" />
					<input type="hidden" name="fieldName" value="'.htmlspecialchars($this->fieldName).'" />
					<input type="hidden" name="formName" value="'.htmlspecialchars($this->formName).'" />
					<input type="hidden" name="md5ID" value="'.htmlspecialchars($this->md5ID).'" />
					<input type="hidden" name="exampleImg" value="'.htmlspecialchars($this->exampleImg).'" />
			'.$formElements[1];

			
			// Placeholder for actual search
			$content .= '<div id="insertAjax"></div>';

			// Output:
			$this->content.=$this->doc->section('Search a descriptor', $content, 0,1);
	}
	
	function mySearchFunction($arg) {
			if ($arg['textSearch']) {
			     $res = $this->getCategories('tx_docdb_descriptor','title LIKE \'%'.htmlspecialchars($arg['textSearch']).'%\'','',' title ASC ', '50');
			     if (is_array($res)) {
			          $content = '<h3>Search results</h3><table>';
			          foreach ($res as $row) {
			              $syntext=$row['title'];
			              if ($row['dscr_related']) { // If this keyword is a synonym, get the main one
			                  $synonyms = $this->getCategories('tx_docdb_descriptor',' uid = '.$row['dscr_related'] ,'',' title ASC ', '50');
			                  $syntext .= ' (See: '.$synonyms[$row['dscr_related']]['title'].')';
			                  // substitute the values
			                  $row['uid']=  $row['dscr_related'];
			                  $row['title'] = $synonyms[$row['dscr_related']]['title'];
			              }
                                $content.='<tr><td><a href="#" onclick="parent.window.opener.setFormValueFromBrowseWin(\'data[pages]['.$this->P['uid'].'][tx_docdb_doc_descriptor]\','.$row['uid'].',\''.htmlspecialchars(str_replace('\'','&#039;',$row['title'])).'\');window.close();return false;">';
                                $content.= str_replace('\'','&#039;',$syntext);
                                
                                $content.='</a></td></tr>'; 
                                
			          }
			          $content.='</table>';
			     }
			}
			//$content = '<pre>'.print_r($arg,true).'</pre>'.$content;
			$objResponse = new tx_xajax_response();
			$objResponse->setCharEncoding('ISO-8859-1');
		      $objResponse->addAssign('insertAjax', 'innerHTML', $content);
		      return $objResponse->getXML();
	}

       /**
	 * Will select all records from the "category table", $table, and return them in an array.
	 *
	 * @param	string		The name of the category table to select from.
	 * @param	string		Optional additional WHERE clauses put in the end of the query. DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	array		The array with the category records in.
	 */
	function getCategories($table,$whereClause='',$groupBy='',$orderBy='',$limit='')	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$table,
					$whereClause,	// whereClauseMightContainGroupOrderBy
					$groupBy,
					$orderBy,
					$limit
				);
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[$row['uid']] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr;
	}
      
      /**
	 * Returnes the sourcecode to the browser
	 *
	 * @return	void
	 */
	function printContent()	{
	      global $BACK_PATH;
		$this->xajax->processRequests();
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}
}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['/ext/doc_db/wizard_search_descriptor.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['/ext/doc_db/wizard_search_descriptor.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('SC_wizard_search_descriptor');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>