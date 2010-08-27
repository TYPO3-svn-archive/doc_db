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
 * $Id: class.tx_docdb_model_descriptor.php 210 2010-05-04 11:00:03Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 13:00:03 +0200 (mar 04 mai 2010) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */

// tslib_cObj
require_once(PATH_tslib . 'class.tslib_content.php');

class tx_docdb_model_descriptor
{

	/**
	 * Local cObj for typolink,parseFuncRTE, ...
	 *
	 * @var $cObj
	 */
	public $cObj      = NULL;

	/**
	 * devLog debug
	 * 
	 * @var $_debug
	 */
	protected $_debug = FALSE;

	/**
	 * local: result of tree
	 * 
	 * @var $_resultArTree
	 */
	private static $_resultArTree = array();


	public function __construct() {
		
		/**
		 * @todo: enable debug from ext_template.txt and noot manualy 
		 */
		if(TYPO3_DLOG && $this->_debug) {
			
			$this->_debug = TRUE;
		}

//		if(isset($GLOBALS['TSFE']->cObj) && is_object($GLOBALS['TSFE']->cObj)) {
//
//			$this->cObj = $GLOBALS['TSFE']->cObj;
//
//		} elseif(!is_object($this->cObj)) {
//
//			// Creates a new instance of tslib_cObj
//			$this->cObj = t3lib_div::makeInstance('tslib_cObj');
//		}
	}


	/**
	* @param    int    : $id     : parent node id / root node
	* @param    string : $needle : search filter
	* @return   array  : $out    : array to load in direct store
	* @remotable
	* @remoteName get
	*/
	public function get($id, $params) {
		
		if($id === 'root'){
			$id = isset($this->conf['tree.']['rootPageId']) ? $this->conf['tree.']['rootPageId'] : 0;
		}

        session_start();

		if($id === 0 &&  (isset($params->reset) || isset($params->needle) || isset($params->ownerfk) || isset($params->typefk) || isset($params->statusfk))) {

			if(isset($params->needle) && strlen($params->needle) < 2) {
				// continue
			} else {

                $matchChecked = $this->_getCheckedNodes();
                if(count($matchChecked)) {

                    $params->checkedNodes = $matchChecked;
                }

				$out = $this->_getFilteredTree($params);

				return $out;
			}
		}

		/**
		 * @toto: check if better to do that here , therefore not doing it twice.
		 */
		$_SESSION['docdb-dscrtree-nodes-status'][$id] = 'expand';

		$matchDescr = $this->_getDescrLeafRelatedDoc($params);
		
		/**
		 * @todo : not realy needed
		 */
		if(count($matchDescr) < 1) {
			
			return $this->_getNoResultReponse();
			// print_r($matchDescr);exit;
		}

		// add uid of matching parent in rootline
		$this->_setFilteredRootline($matchDescr);
		
		$out = $this->_getDescritpors($id, $matchDescr);
		
		return $out;
	}

	
	/**
	 * method setSessionNode: register state of nodes in session array
	 * 
	 * @param   int     $id: node id
	 * @param   string  $status: current state of node (expand or collapse and checked or uncheched)
	 * @return  array
	 * @remotable
	 */
	public function setSessionNode($id, $status) {

		session_start();
		
		if($status === 'checked') {
			
			// checked leaf
			$_SESSION['docdb-dscrtree-nodes-status'][$id] = $status;
			
		} else if($status === 'expand') {
			
			// expand chidren
			$_SESSION['docdb-dscrtree-nodes-status'][$id] = $status;
			
		} else if($status === 'unchecked' || $status === 'collapse') {
			
			// remove node id from session array
			unset($_SESSION['docdb-dscrtree-nodes-status'][$id]);
		}
		
		return array('success' => TRUE);
	}


	/**
	 * getSessionNodes : called on page load to restore state
	 * 
	 * @param $id start node of tree
	 */
	public function getSessionNodes($id) {
		
		session_start();

		$matchDescr = $this->_getDescrLeafRelatedDoc();
		
		if(count($matchDescr) < 1) {
			
			return $this->_getNoResultReponse();
			print_r($matchDescr);exit;
		}

		// add uid of matching parent in rootline
		$this->_setFilteredRootline($matchDescr);

		$this->_setFilteredTreeNodes('root', NULL, $matchDescr, TRUE);
		
		return self::$_resultArTree;
	}


	/**
	 * treeNodes related to existing documents and corresponding
	 * to the search needle
	 * 
	 * @param : object : params object with needle, ownerfk, typefk, statusfk properties 
	 */
	private function _getFilteredTree($params) {

		$matchDescr = $this->_getDescrLeafRelatedDoc($params);
		
		if(count($matchDescr) < 1) {
			
			return $this->_getNoResultReponse();
			print_r($matchDescr);exit;
		}
		
		// add uid of matching parent in rootline
		$this->_setFilteredRootline($matchDescr);
		
		if(isset($params->needle)) {

			$this->_setFilteredTreeNodes('root', NULL, $matchDescr);

		} else {
			// session
			$this->_setFilteredTreeNodes('root', NULL, $matchDescr, TRUE);
		}
		return self::$_resultArTree;
	}


	/**
	 * Get uid of matching search needle descriptors but only those are leaf.
	 * and only descriptors actually related to documents
	 * 
	 * @param : object : $params object with needle, ownerfk, typefk, statusfk properties
	 * @return : array  : ids of descriptors leaf related to doc
	 */
	private function _getDescrLeafRelatedDoc($params=NULL) {

		// dscr_uid unique list
		$dscrUidList      = array();
		$searchNeedle     = '';
		$addWhere         = '';
		$whereRelOwner    = '';
		$whereRelType     = '';
		$whereRelStatus   = '';
        $remNotRelChkNode = FALSE;
		
		if(isset($params->needle)) {
			
			$searchNeedle = ' AND (d.title LIKE \'%' .
					$GLOBALS['TYPO3_DB']->quoteStr(
					$GLOBALS['TYPO3_DB']->escapeStrForLike(
						$params->needle, 'tx_docdb_descriptor'
					),
					'tx_docdb_descriptor') .
				'%\')';
		}

		// build whereClause to filter by descriptor related to owner-type-status
		if((isset($params->ownerfk) && (int)$params->ownerfk !== 0) ||
				(isset($params->typefk) && (int)$params->typefk !== 0) ||
				(isset($params->statusfk) && (int)$params->statusfk !== 0)) {


			$remNotRelChkNode = TRUE;
            
			if(isset($params->ownerfk) && (int)$params->ownerfk !== 0) {
				
				$whereRelOwner = ' AND p.tx_docdb_doc_owner IN(' . $params->ownerfk . ')';
			}
			
			if(isset($params->typefk) && (int)$params->typefk !== 0) {
				
				$whereRelType = 'AND p.tx_docdb_doc_type IN(' . $params->typefk . ')';
			}
			
			if(isset($params->statusfk) && (int)$params->statusfk !== 0) {
				
				$whereRelStatus = 'AND p.tx_docdb_doc_status IN(' . $params->statusfk . ')';
			}
			
			$addWhere .= $whereRelOwner . $whereRelType . $whereRelStatus;
		}
		
		
		$select  = 'd.uid';
		$from    = 'pages p ';
        $from   .= 'INNER JOIN tx_docdb_pages_doc_descriptor_mm mm ON p.uid = mm.uid_local ';
        $from   .= 'INNER JOIN tx_docdb_descriptor d ON d.uid = mm.uid_foreign ';

		$where  .= '(d.deleted=0 AND d.hidden=0) AND ((p.deleted=0 AND p.hidden=0) and p.doktype=198)';
		$where  .= ' AND d.uid NOT IN(SELECT dscr_pid FROM tx_docdb_descriptor)' . $addWhere;
		$where  .= $searchNeedle;
		
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
			
			$dscrUidList[] = $row['uid'];
		}
		
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		
		if($this->_debug) {
			
			t3lib_div::devLog('DESCRIPTOR','doc_db',0,
                array('SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where . ' GROUP BY ' . $groupBy)
			);
		}
		
		unset($row, $select, $from, $where, $groupBy, $orderBy, $limit);

        // uid list of checked nodes
        if(isset($params->checkedNodes)) {

            // Remove checked nodes not related to the selection of owner|type|status if any.
            // Otherwise no search result of document will be displayed
            if($remNotRelChkNode && ! isset($params->needle)) {
                
                $rmNodes = array_diff($params->checkedNodes, $dscrUidList);
                foreach($rmNodes as $id) {
                    unset($_SESSION['docdb-dscrtree-nodes-status'][$id]);
                }
                $params->checkedNodes = array_intersect($params->checkedNodes, $dscrUidList);

                $dscrUidList = array_merge($dscrUidList, $params->checkedNodes);
            } else {
                $dscrUidList = array_merge($dscrUidList, $params->checkedNodes);
            }
        }

		return array_unique($dscrUidList);
	}


	/**
	 * build objects nodes array for the current level.
	 * In fact the chilNodes of id passed as parameter
	 * 
	 */
	private function _getDescritpors($pDscrPid, $selIdsFilter = NULL) {

		// store result
		$out = array();
		
		$dscrItems = $this->_getDescriptors4Level($pDscrPid, FALSE, $selIdsFilter);
		
		foreach($dscrItems as $k => $dscrItem) {
			
			// make new node object
			$nodeObj = new stdClass();
			
			// count children
			$hasChildren = $this->_getDescriptors4Level($dscrItem['uid'], TRUE, $selIdsFilter);
			
			if($hasChildren[0]['count'] > 0) {
				
				$nodeObj->leaf = FALSE;
				
			} else {
				
				$nodeObj->leaf    = TRUE;
				$nodeObj->checked = $_SESSION['docdb-dscrtree-nodes-status'][$dscrItem['uid']] === 'checked' ? TRUE : FALSE;

                if($dscrItem['dscr_related']) {

                    $nodeObj->relDscr = $this->_getRelatedDescriptors($dscrItem['dscr_related'], $selIdsFilter);
                }
			}
			
			$nodeObj->id   = $dscrItem['uid'];
			$nodeObj->text = $dscrItem['title'];
			
			$out[$k] = $nodeObj;
			
		} // eo foreach
		
		
		//t3lib_div::debug($out,'_getDescritpors'); exit;
		return $out;
	}

	/**
	 * Get descriptors for the current level
	 * 
	 * @param  : int     : $pDscrPid : parent descriptors id
	 * @param  : boolean : $count : sets to return count greater than 0 if haschildren
	 * @param  : array   : $selIdFilter : ids in rootline of descriptors with related doc.
	 * @return : hash array of descriptors 
	 */
	private  function _getDescriptors4Level($pDscrPid, $count=FALSE, $selIdsFilter=NULL) {

		$rows = array();
		
		$select  = 'd.uid,d.dscr_pid,d.dscr_related,d.title';
		$from    = 'tx_docdb_descriptor d';
		$where   = 'd.dscr_pid=' . $pDscrPid;
		$groupBy = 'd.uid';
		$orderBy = 'd.title';
		$limit   = '';
		
		// filter needed uid to build filtered tree
		if(is_array($selIdsFilter)) {
			
			$where .= ' AND d.uid IN(' . implode(',', $selIdsFilter) . ')';
		}
		
		if($count) {
			
			$select  = 'd.uid AS count';
			$groupBy = '';
			$orderBy = '';
			$limit   = '1';
		}
		
		// addWhere
		$where .= ' AND (d.deleted=0 AND d.hidden=0)';
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);
//t3lib_div::debug($rows,'rows');exit;
		
		return $rows;
	}


    private  function _getRelatedDescriptors($dIds, $selIdsFilter) {

        // store result array
        $rows = array();

        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'd.uid,d.title',
			'tx_docdb_descriptor d',
			'd.uid IN(' . $GLOBALS['TYPO3_DB']->cleanIntList($dIds) . ')' .
            ' AND d.uid IN(' . implode(',', $selIdsFilter) . ') AND (d.deleted=0 AND d.hidden=0)',
			'd.uid',
			'd.title ASC',
			'20'
		);

        return $rows;
    }


	private function _getNoResultReponse() {
		
		// make new node object
			$nodeObj = new stdClass();
			
			$nodeObj->leaf    = TRUE;
			//$nodeObj->checked = 
			
			$nodeObj->id   = 'nores';
			$nodeObj->text = 'No result';
			
			return array($nodeObj);
	}


	/*
	 * sets dscr_pid list to be selected in rootline
	 * 
	 * @param : array $childrenPids : children ids pass by ref
	 * @param : array $tempPids : parent level to process
	 */
	private function _setFilteredRootline(&$childrenPids, $tempPids=array()) {

        static $firstCall = TRUE, $isLeaf;

        if($firstCall) {
            $isLeaf = $childrenPids;
            $firstCall = FALSE;
        }
            
		$tempPids = count($tempPids) > 0 ? $tempPids : $childrenPids;
		
		$select  = 'uid,dscr_pid';
		$from    = 'tx_docdb_descriptor';
		$where   = 'uid IN (' . implode(',', $tempPids) . ')';
		$groupBy = 'dscr_pid';
		$orderBy = '';
		$limit   = '';
		
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select,
			$from,
			$where, $groupBy, $orderBy, $limit
		);
		
		$rows = array();
		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

            // if the first level is leaf
            if(in_array($row['uid'], $isLeaf) && (int)$row['dscr_pid'] == 0            ) {
                continue;
            }
            $rows[] = $row['dscr_pid'];
		}
		
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		
		// store result in ref array
		$childrenPids = array_merge($childrenPids, $rows);

        rsort($childrenPids, SORT_NUMERIC);

		// while not in root
		if(! in_array('0', $rows)){
			
			$this->_setFilteredRootline($childrenPids, $rows);
		}

        unset($rows);
	}


    private function _getCheckedNodes() {

        // get array of uid of checked nodes
        return array_keys(
            array_filter(
                $_SESSION['docdb-dscrtree-nodes-status'],
                create_function(
                    '$a','return ($a === "checked");'
               )
           )
       );
    }

	/*
	 * build treeNodes related to the searh filter   $this->_getDescrByTitle($needle);
	 * 
	 * @param : mixed         $id : rootIds of tree
	 * @param : object        $parent : parent level object
	 * @param : array         $selIdsFilter : ids existing in rootline of descriptors leaf with relation in document.
	 * @param : mixed/boolean $session : TRUE if call from session restore
	 */
	private function _setFilteredTreeNodes($id, $parent=NULL, $selIdsFilter=array(), $session=NULL) {

		static $tempNodes = array(),
					 $pathIndex = array();
		static $level     = 0,
               $lastLevel = 0;
		
		if($id === 'root') {
			$id = $this->conf['tree.']['rootPageId'] ? $this->conf['tree.']['rootPageId']:'0';
		}
		
		if($parent !== NULL) {
			
			$tempNodes[$lastLevel] = $parent;
		}
		
		// get childs nodes by parent id
		$currentNodes = $this->_getDescritpors($id, $selIdsFilter);
		
		$tempResult = array();
		
		foreach($currentNodes as $k => $node) {
			
			$level++;
			
			// if session restore
			if($session !== NULL) {
				
				if(isset($node->expanded)) { unset($node->expanded); }
				
                // remove expand node status if node not in result
                if(! in_array($node->id, $selIdsFilter)) {
                    unset($_SESSION['docdb-dscrtree-nodes-status'][$node->id]);
                }

				// if node id is registered in session
				$expand = (isset($_SESSION['docdb-dscrtree-nodes-status'][$node->id]) && $_SESSION['docdb-dscrtree-nodes-status'][$node->id] == 'expand') ? TRUE : FALSE;
				
				if($expand) {
					
					$node->expanded = TRUE;
				}
				
			} else {
				
				// not session
				$node->expanded = TRUE;
			}
			// eo session restore
			
			if(! $node->leaf && $node->expanded) {
				
				$lastLevel    = $level;
				
				$node->hasChildren = TRUE;
				
				$tempResult[] = $node;
				
				// get next level
				$this->_setFilteredTreeNodes($node->id, $node, $selIdsFilter, $session);
				
			} else {
				
				// leaf or not expanded node
				$tempResult[] = $node;
			}
			
			$level--;
		
		} // eo foreach
		
		if(count($tempNodes) && $parent->hasChildren) {
			
			unset($parent->hasChildren);
			// add children to parent node
			$tempNodes[$level]->children = $tempResult;
		
		} else {
		
			$tempNodes[$level] = $tempResult;
		
		}
		
		// on the end
		if($level == 0) {
		
		// store result tree
			self::$_resultArTree  = $tempNodes[0];
		}
		
		array_pop($tempNodes);
	} // eo _setFilteredTreeNodes

} // eo class

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_descriptor.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_descriptor.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_descriptor.php']);
}

}

