<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Laurent Cherpit <laurent.cherpit@gmail.com>
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

if(version_compare(TYPO3_branch, '4.3', '<')) {
    require_once (PATH_typo3 . 'interfaces/interface.backend_cacheActionsHook.php');
}

/**
 * Class to render the backend menu for the clear cache
 * Come from pmkShadowBox tx Stefan
 *
 * $Id: class.tx_docdb_clearcachemenu.php 209 2010-05-04 10:49:23Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 12:49:23 +0200 (mar 04 mai 2010) $
 *
 * @author	Stefan Galinski <stefan.galinski@gmail.com>
 * @author	Laurent Cherpit <laurent.cherpit@gmail.com>
 * @package	TYPO3
 * @subpackage doc_db
 */
class tx_docdb_clearcachemenu implements backend_cacheActionsHook {
    
	/**
	 * Adds a new entry to the cache menu items array
	 *
	 * @param array array Cache menu items
	 * @param array array of access configuration identifiers (typically used by userTS with options.clearCache.identifier)
	 * @return void
	 */
	 public function manipulateCacheActions(&$cacheActions, &$optionValues) {
         
	 	if ($GLOBALS['BE_USER']->isAdmin()) {
			$title = $GLOBALS['LANG']->sL('LLL:EXT:doc_db/configuration/llang/locallang.xml:clearCacheTitle');
			$cacheActions[] = array (
				'id'    => 'clearDocdbXmlCache',
				'title' => $title,
				'href'  => $GLOBALS['BACK_PATH'] . 'ajax.php?ajaxID=docdb::clearDocdbXmlCache',
				'icon'  => '<img src="' . t3lib_extMgm::extRelPath('doc_db') .
					'ext_icon.gif" width="16" height="16" title="' . htmlspecialchars($title) . '" alt="" />'
			);

			$optionValues[] = 'clearDocdbXmlCache';
		}
	 }
}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_clearcachemenu.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_clearcachemenu.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_clearcachemenu.php']);
}

}
?>