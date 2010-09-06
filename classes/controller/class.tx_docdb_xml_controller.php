<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 cherpit laurent <laurent@eosgarden.com>
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

if(version_compare(TYPO3_branch, '4.3', '<')) {
    // helpers
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/class.tx_docdb_div.php');
    // cache handler class
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/class.tx_docdb_xmlcache.php');
    // view
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/view/class.tx_docdb_xml_view.php');
}

 /**
 * Class/Function : tx_docdb_xml_controller
 * eID controller
 *
 * $Id: class.tx_docdb_xml_controller.php 209 2010-05-04 10:49:23Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 12:49:23 +0200 (mar 04 mai 2010) $
 *
 * @author      laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  doc_db
 */
class tx_docdb_xml_controller {

    /**
     * parameters object
     * 
     * @var <stdClass>
     */
    protected $_confObj      = NULL;

    /**
     *
     * @var <integer>
     */
    protected $_pageId       = 0;

    /**
     * XML Cache handler
     *
     * @var <tx_docdb_xmlcache>
     */
    protected $_cacheHandler = NULL;

    /**
     * 
     */
    public function init($cacheHandler=NULL) {
        
        $this->_confObj = new stdClass();
        
        if($cacheHandler === NULL) {
			try {

//                $this->_cacheHandler = new tx_docdb_xmlcache('typo3temp/docdb/');
				$this->_cacheHandler = t3lib_div::makeInstance(
					'tx_docdb_xmlcache',
					'typo3temp/docdb/'
				);

			} catch (Exception $exception) {

				t3lib_div::sysLog(
					$exception->getMessage(),
					'docdb',
					t3lib_div::SYSLOG_SEVERITY_ERROR
				);
				throw $exception;
			}

		} else {
            
			$this->_cacheHandler = $cacheHandler;
		}

        try {

            $hash = $this->_getHashParam();
            
        } catch (Exception $exception) {

            t3lib_div::sysLog(
                $exception->getMessage(),
                'docdb',
                t3lib_div::SYSLOG_SEVERITY_ERROR
            );

            $this->_confObj->error = TRUE;
            $this->_confObj->errorMsg = 'Bad request. unknown parameters.';
            // the xml view
            $xmlView = t3lib_div::makeInstance('tx_docdb_xml_view');

            echo $xmlView->get($this->_confObj);
            tx_docdb_div::flush();
            exit;
        }
        
        
        // init database connection
		tslib_eidtools::connectDB();
        
        // get params from DB of related incoming hash data
        $row = tx_docdb_div::sqlGetRows(
            'tx_docdb_cache_xmllink',
            'pid, params, hash_results',
            'hash_params=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($hash, 'tx_docdb_xmllink'),
            '',
            '',
            '1'
        );

        if(count($row)) {

            $this->_confObj->params       = json_decode($row[0]['params']);
            $this->_confObj->hashFileName = $row[0]['hash_results'];

            $this->_pageId = $row[0]['pid'];

            if($this->_pageId) {

                $this->_createTSFE();
            }
        }
	}


	/**
	 * Main processing function of eID script
     *
     *
	 * @todo use xmlwriter to create XML. to support larger file
	 * @return	void
	 */
    public function main() {

        header("content-type: text/xml; charset=UTF-8");
//        header("content-type: text/html; charset=UTF-8");
        
        // check if file exists
        if(! $this->_cacheHandler->isFileInCache($this->_confObj->hashFileName)) {

            // Used to have time to write cache after flush output
            ignore_user_abort(TRUE);

            // the xml view
            $xmlView = t3lib_div::makeInstance('tx_docdb_xml_view');
//            $xmlView = new tx_docdb_xml_view();

            $xml = $xmlView->get($this->_confObj);

            // send data to the client
            echo $xml;
            tx_docdb_div::flush();

            try {

                $this->_cacheHandler->writeCacheFile($this->_confObj->hashFileName, $xml);
                
            } catch(Exception $exception) {

                t3lib_div::sysLog(
					$exception->getMessage(),
					'docdb',
					t3lib_div::SYSLOG_SEVERITY_ERROR
				);
				throw $exception;
            }

        } else {

            $xml = $this->_cacheHandler->getCacheFileContents($this->_confObj->hashFileName);

            echo $xml;
            tx_docdb_div::flush();
        }

        // clear
        unset($xml);
	}

    
	/**
	 * Initializes TSFE. This is necessary to have proper environment for typoLink.
     *
     * This class create frontend page address from the page id value and parameters.
     * tx_pagepath
     * @author	Dmitry Dulepov <dmitry@typo3.org>
	 * @return	void
	 */
	protected function _createTSFE() {
        
		require_once(PATH_tslib . 'class.tslib_fe.php');
		require_once(PATH_t3lib . 'class.t3lib_page.php');
		require_once(PATH_tslib . 'class.tslib_content.php');
		require_once(PATH_t3lib . 'class.t3lib_userauth.php' );
		require_once(PATH_tslib . 'class.tslib_feuserauth.php');
		require_once(PATH_t3lib . 'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib . 'class.t3lib_cs.php');

		$GLOBALS['TSFE']  = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $this->_pageId, '');

		$initCache = isset($GLOBALS['typo3CacheManager']) && tx_docdb_div::isTypo3V43min();
		if ($initCache) {
			require_once(PATH_t3lib . 'class.t3lib_cache.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_abstractbackend.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_abstractcache.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_exception.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_factory.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_manager.php');
			require_once(PATH_t3lib . 'cache/class.t3lib_cache_variablecache.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_classalreadyloaded.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_duplicateidentifier.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invalidbackend.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invalidcache.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invaliddata.php');
			require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_nosuchcache.php');
			$GLOBALS['typo3CacheManager'] = t3lib_div::makeInstance('t3lib_cache_Manager');
			$GLOBALS['typo3CacheFactory'] = t3lib_div::makeInstance('t3lib_cache_Factory', $GLOBALS['typo3CacheManager']);

			$GLOBALS['TSFE']->initCaches();
		}
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->getCompressedTCarray();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->cObj = t3lib_div::makeInstance('tslib_cObj');
        $GLOBALS['TSFE']->cObj->start(array());
//        $GLOBALS['TSFE']->config['config']['typolinkLinkAccessRestrictedPages'] = 1;

		// Set linkVars, absRefPrefix, etc
		require_once(PATH_tslib . 'class.tslib_pagegen.php');
		TSpagegen::pagegenInit();
	}

    /**
     * Check is valid md5 hash and return it, otherwise throw an exception.
     *
     * @return <string> md5 hash parameter
     */
    protected function _getHashParam() {

        // Sanity check
		$hash = trim(t3lib_div::_GET('hash'));

		if(empty($hash) || !preg_match('/^[a-f0-9]{32}$/', $hash)) {

            // error Not valid link hash
            $message = 'tx_docdb_xml_controller->_getHashParam: Not valid URL parameters "';
            throw new Exception($message);
		}

        return $hash;
    }
}

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_xml_controller.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_xml_controller.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_xml_controller.php']);
}

}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_docdb_xml_controller');
$SOBE->init();
$SOBE->main();
exit;

?>