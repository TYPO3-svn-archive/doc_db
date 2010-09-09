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
 * Class/Function
 * 
 * $Id: class.tx_docdb_model_document.php 209 2010-05-04 10:49:23Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 12:49:23 +0200 (mar 04 mai 2010) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */

if(version_compare(TYPO3_branch, '4.3', '<')) {
    // helpers
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/class.tx_docdb_div.php');
    // view
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/model/class.tx_docdb_model_document.php');
}


class tx_docdb_xmllink extends tx_docdb_model_document
{
	
	/**
	 * Local cObj for typolink,parseFuncRTE, ...
	 * 
	 * @var object
	 */ 
	protected $_cObj     = NULL;
	
	/**
	 * devLog debug
	 * 
	 * @var boolean
	 */
	protected $_debug   = FALSE;

    protected $_langParam = array();
	
	/**
	 * Store sql Clause keys: where,order, limit
	 * 
	 * @var array
	 */
	protected $_sqlClause = array();
	
	
	
	
	/**
	 * Get xmllink related to the query selection parameters
	 * 
	 * @remotable
     * @remoteName getLink
	 * @return string
	 */
	public function get($params) {

		// build clause of query
		$this->_setSqlClause($params);

        $this->_setAddLangParam($params);

        $totalCountAndUids = tx_docdb_div::exec_SELECTcountAndGetRows('p.uid', $this->_sqlClause['from'], $this->_sqlClause['where']);

		$out = array(
			'success'    => TRUE,
            'xmlLink'    => $this->_getXmlLink($GLOBALS['TSFE']->id, $params, $totalCountAndUids['rows'])
		);

        unset($totalCountAndUids);
		return $out;
	}


    /**
     * Funtion to store the md5 hash related to params of document query and used as params value for the xml link.
     * But before to store it, clean the unwanted params to minimize the possibilities and avoid table overload.
     *
     * @param   <integer>   $pageId
     * @param   <stdClass>  $linkParams
     * @param   <array>     $resultUids     list of uids returned by the document query
     * @return  <string>    link to get XML export
     */
    protected function _getXmlLink($pageId, $linkParams, $resultUids) {

        // clean some parameters to limit numbers of possibilities
        unset(  $linkParams->filter,
                $linkParams->groupBy,
                $linkParams->groupDir,
                $linkParams->grouping,
                $linkParams->field,
                $linkParams->direction,
                $linkParams->start,
                $linkParams->limit
        );

        if($linkParams->sort !== 'date' || $linkParams->sort !== 'owner') {
            $linkParams->sort = 'date';
        }

        tx_docdb_div::sortListOfInt($linkParams->selNodes);
        tx_docdb_div::sortListOfInt($linkParams->owner);
        tx_docdb_div::sortListOfInt($linkParams->type);
        tx_docdb_div::sortListOfInt($linkParams->status);

        // sort beafore md5-ize
        sort($resultUids);
        // prepare to store
        $serializedUidsResults = json_encode($resultUids);
        $md5hashUidsResults    = md5($serializedUidsResults);

        // prepare to store. serialize not required
        $serializedParams = json_encode($linkParams);
        $md5hashParams    = md5($serializedParams);

        // store values
        $GLOBALS['TYPO3_DB']->sql_query('
          REPLACE INTO tx_docdb_cache_xmllink (pid, hash_params, hash_results, params)
          VALUES (\'' . (int)$pageId . '\', \'' . $md5hashParams . '\', \'' . $md5hashUidsResults . '\', \'' . $serializedParams . '\')
        ');

        return tx_docdb_div::siteUrl() . 'index.php?eID=docdbxml&hash=' . $md5hashParams;
    }
}





// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_xmllink.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_xmllink.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_xmllink.php']);
}

}

