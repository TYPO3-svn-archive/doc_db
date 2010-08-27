<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Laurent Cherpit <laurent@eosgarden.com>
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
 * Class that renders fields for the extensionmanager configuration
 * Initial class come from tt_news. tx rupi
 *
 * $Id: class.tx_docdb_tsparserext.php 168 2009-12-22 07:01:25Z lcherpit $
 *
 * @author  Rupert Germann <rupi@gmx.li>
 * @author  Laurent Cherpit
 * @package TYPO3
 * @subpackage doc_db
 */
class tx_docdb_tsparserext {

    /**
     * ref to localang labels
     *
     * @var string
     */
    private $_ll       = 'LLL:EXT:doc_db/configuration/llang/locallang.xml:extmng.';
    

	public function displayMessage(&$params, &$tsObj) {

		$out = '';

		if(t3lib_div::int_from_ver(TYPO3_version) < 4003000) {
            
			$cssPath = $GLOBALS['BACK_PATH'] . t3lib_extMgm::extRelPath('doc_db');
			$out     = '<link rel="stylesheet" type="text/css" href="' . $cssPath . 'res/compat/flashmessages.css" media="screen" />';
		}

		$out .= '
		<div style="position:absolute;top:10px;right:10px; width:300px;">
			<div class="typo3-message message-information">
   				<div class="message-header">' . $GLOBALS['LANG']->sL($this->_ll . 'updatermsgHeader') . '</div>
  				<div class="message-body">
  					' . $GLOBALS['LANG']->sL($this->_ll . 'updatermsg') . '<br />
  					<a style="text-decoration:underline;" href="index.php?&amp;id=0&amp;CMD[showExt]=doc_db&amp;SET[singleDetails]=updateModule">
  					' . $GLOBALS['LANG']->sL($this->_ll . 'updatermsgLink') . '</a>
  				</div>
  			</div>
  		</div>
  		';

		return $out;
	}
}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tsparserext.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tsparserext.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_tsparserext.php']);
}

}
?>