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
    // view
    require_once(t3lib_extMgm::extPath('doc_db') . 'classes/model/class.tx_docdb_model_document.php');
}

@ini_set('max_execution_time',600);
@ini_set('memory_limit','512m');

 /**
 * Class/Function : tx_docdb_xml_view
 * eID controller
 *
 * $Id: class.tx_docdb_xml_view.php 209 2010-05-04 10:49:23Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 12:49:23 +0200 (mar 04 mai 2010) $
 *
 * @author      laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  doc_db
 */

class tx_docdb_xml_view extends tx_docdb_model_document {

    /**
     * Dom Handler
     *
     * @var <DOMDocument>
     */
    protected $_dom = NULL;

    public function __construct() {

        parent::__construct();
    }


    /**
	 * Get all owners with existing related documents
	 *
	 * @remotable
	 * @return array to by encoded in JSON format
	 */
	public function get(stdClass $confObj) {

        if(($xmlContent = $this->_checkError($confObj))) {

            return $xmlContent;
        }
        
		// build clause of query
		$this->_setSqlClause($confObj->params);
        $this->_setAddLangParam($confObj->params);

		// store
		$rows = array();

		$select  = 'p.uid, p.title, p.lastUpdated AS date, p.abstract AS dAbs, p.tx_docdb_doc_descriptor AS dscr,p.tx_docdb_doc_related_pages pages,';
		$select .= 'p.tx_docdb_doc_key AS dkey, o.owner, t.type, s.status';
        //  p.tx_templavoila_flex AS flex,
		$groupBy = 'p.uid';
		$limit   = '20000'; // limit

        if($this->_debug) {

			t3lib_div::devLog('class model_document sql clause', 'doc_db', 0, array($select, $from, $this->_sqlClause['where'], $groupBy, $this->_sqlClause['order'], $limit));
		}

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            $select,
            $this->_sqlClause['from'],
            $this->_sqlClause['where'],
            $groupBy,
            $this->_sqlClause['order'],
            $limit
        );


        if($res) {

            $this->_setXmlElements($res, $this->_getDocumentStart());
            $xmlContent = $this->_getXml();

            $GLOBALS['TYPO3_DB']->sql_free_result($res);
            unset($this->_dom);

        } else {

            $rootNode = $this->_getDocumentStart();
            // document node
            $doc = $this->_dom->createElement('doc');
            $doc->setAttribute('id', '0');
            $rootNode->appendChild($doc);
            $xmlContent = $this->_getXml();
            unset($this->_dom);
        }

		return $xmlContent;
	}


    protected function _getDocumentStart() {

        $this->_dom = new DOMDocument('1.0', 'UTF-8');
        $this->_dom->formatOutput = true;

        // root node
        $root = $this->_dom->createElement('documents');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:noNamespaceSchemaLocation', tx_docdb_div::extSiteUrl(TRUE) . 'res/docdb.xsd');
        
        return $this->_dom->appendChild($root);
    }

    
    protected function _getXml() {

        return $this->_dom->saveXML();
    }


    protected function _setXmlElements($res, $rootNode) {

		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

//            t3lib_div::devLog('uid', 'docdb', 0, array($row['uid']));
            
            if(!empty($row['uid'])) {


                if($row['owner'] === 'zzz_none') {$row['owner'] = '';}
                if($row['type'] === 'zzz_none') { $row['type'] = ''; }
                if($row['status'] === 'zzz_none') { $row['status'] = ''; }
                $row['docPageURL'] = '';
                if($row['title']) {
                    /**
                     * @todo add option config to enable or not linkAccessRestrictedPages
                     */
                    $addParams = array_merge(array('parameter' => $row['uid'], 'returnLast' => 'url', 'linkAccessRestrictedPages' => '1'), $this->_langParam);
                    $url = $this->_cObj->typoLink('', $addParams);

                    $addParams['forceAbsoluteUrl'] = '1';
                    $row['docPageURL'] = tx_docdb_div::forceAbsoluteUrl($url, $addParams );
                }

                // document node
                $doc = $this->_dom->createElement('doc');
                $doc->setAttribute('id', $row['uid']);

                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'date', $row['date'], array('f' => 'unixtime'));
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'title', $row['title']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'url', $row['docPageURL']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'key', $row['dkey']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'owner', $row['owner']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'type', $row['type']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'status', $row['status']);
                $this->_createSimpleNodeAndContent($this->_dom, $doc, 'abstract', $row['dAbs'], array(), TRUE);

                // related descriptors if any
                $row['dscr'] = $row['dscr'] ? $this->_getRelatedDescriptors($row['uid']) : array();
                $this->_createSequence($this->_dom, $doc, 'descriptors', 'descr', $row['dscr']);

                // related pages if any
                $row['pages'] = $row['pages'] ? $this->_getRelatedPages($row['uid']) : array();
                $this->_createSequence($this->_dom, $doc, 'pages', 'page', $row['pages']);

                $rootNode->appendChild($doc);
            }
		}
    }


    /**
     *
     * @param <DOMDocument> $dom
     * @param <DOMElement>  $pNode
     * @param <string>      $node
     * @param <string>      $text
     * @param <array>       $attr   key as attribute name, val as attribute value
     * @param <boolean>     $cdata  create cdata section. Default: FALSE
     */
    protected function _createSimpleNodeAndContent(&$dom, &$pNode, $node, $text, $attr=array(), $cdata=FALSE) {

        $newNode = $dom->createElement($node);
        if(!empty($text)) {
            if($cdata) {
                $newNode->appendChild($dom->createCDATASection("\n\t" . $text ."\n"));
            } else {
                $newNode->appendChild($dom->createTextNode($text));
            }
        }

        if(is_array($attr) && count($attr)) {

            foreach($attr as $name => $val) {
                $newNode->setAttribute($name, $val);
            }
        }
        
        $pNode->appendChild($newNode);
    }


    protected function _createSequence(&$dom, &$pNode, $node, $childrenName, $childNodes=array()) {

        $nbChildren = count($childNodes);

        $newNode = $dom->createElement($node);

        if(is_array($childNodes) && $nbChildren) {

            foreach($childNodes as $k => $nodeObj) {

                $attrs = array();
                foreach($nodeObj as $prop => $value) {
                    if($prop !== 'title') {
                        $attrs[$prop] = $value;
                    }
                }

                $this->_createSimpleNodeAndContent($dom, $newNode, $childrenName, $nodeObj->title, $attrs);
            }
        }

        $newNode->setAttribute('n', $nbChildren);
        $pNode->appendChild($newNode);
    }
    
    protected function _countElements() {

        return tx_docdb_div::exec_SELECTcountRows('DISTINCT p.uid', $this->_sqlClause['from'], $this->_sqlClause['where']);
    }

    
    protected function _checkError(stdClass $confObj) {

        if(isset($confObj->error) && $confObj->error) {
            
            $rootNode = $this->_getDocumentStart();
            // document node
            $this->_createSimpleNodeAndContent($this->_dom, $rootNode, 'error', $confObj->errorMsg);
            return $this->_getXml();
        }
        return FALSE;
    }
}

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/view/class.tx_docdb_xml_view.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/view/class.tx_docdb_xml_view.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/view/class.tx_docdb_xml_view.php']);
}

}

