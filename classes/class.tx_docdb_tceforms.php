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
 * $Id: class.tx_docdb_itemsProcFunc.php 95 2009-11-22 02:47:22Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-11-22 03:47:22 +0100 (dim 22 nov 2009) $
 *
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


class tx_docdb_tceforms
{

	/**
	 *
	 * @var $_TAB
	 */
	protected $_TAB = '';


	function __construct() {

		$this->_TAB = chr(9);
	}

	/**
    * REnder
    *
    * @param array $PA
    * @return
    */
	public function inputRo(array &$PA, t3lib_TCEforms $fobj) {

		// storage
		$html = array();

		$readonly   = '';
		$roText     = '';
		$fieldStyle	= '';
		if($PA['row'][$PA['field']] === 'zzz_none') {

			$readonly    = ' readonly="readonly"';
			$roText      = '<div style="margin: 5px 0;padding: 5px 0;font-weight:bold;">' . $GLOBALS['LANG']->sL('LLL:EXT:doc_db/configuration/llang/locallang_db.xml:tx_docdb_readonly_field') . '</div>';
			$fieldStyle  = ' style="background-color:#aaa;"';
		}
		$html[] = '<div style="';

 		$html[] = $this->_TAB . 'width : 100%;';

		$html[] = $this->_TAB . 'margin: 5px';

		$html[] = $this->_TAB . 'padding: 5px;"';

		$html[] = $this->_TAB . '>';

		$html[] = $this->_TAB . '<strong>';

		$html[] = $GLOBALS['LANG']->sL($PA['fieldConf']['label']) . '</strong><br />';

		$html[] = $this->_TAB . '<input ';

		$html[] = $this->_TAB . $this->_TAB . 'size="'. $PA['fieldConf']['config']['size'] .'" ';

		$html[] = $this->_TAB . $this->_TAB . 'name="' . $PA['itemFormElName'] . '" ';

		$html[] = $this->_TAB . $this->_TAB . 'value="' .  htmlspecialchars($PA['itemFormElValue']) . '" ';

		$html[] = $this->_TAB . $this->_TAB . 'onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '" ';

		$html[] = $this->_TAB . $this->_TAB . $PA['onFocus'];

		$html[] = $this->_TAB . $readonly . $fieldStyle . ' />';

		$html[] = '</div>';

		$html[] = $roText;

		return implode(chr(10), $html);
	}
}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceforms.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceforms.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tceforms.php']);
}

}
