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
 * $Id: class.tx_docdb_model_document.php 156 2009-12-06 22:43:41Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2009-12-06 23:43:41 +0100 (dim 06 déc 2009) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


// tslib_cObj
require_once( PATH_tslib . 'class.tslib_content.php' );

// helpers
require_once( t3lib_extMgm::extPath( 'doc_db' ) . 'classes/class.tx_docdb_div.php' );


class tx_docdb_model_document
{
	
	/**
	 * Local cObj for typolink,parseFuncRTE, ...
	 * 
	 * @var $cObj 
	 */ 
	public $cObj     = NULL;
	
	/**
	 * devLog debug
	 * 
	 * @var $_debug
	 */
	protected $_debug   = FALSE;
	
	/**
	 * Store sql Clause keys: where,order, limit
	 * 
	 * @var
	 */
	private $_sqlClause = array();
	
	
	/**
	 * tx_docdb_model_document constructor: init
	 * 
	 * @return void 
	 */
	public function __construct() {
		
		/**
		 * @todo: enable debug from ext_template.txt and noot manualy 
		 */
		if( TYPO3_DLOG && $this->_debug ) {
			
			$this->_debug = TRUE;
		}
		
		if( isset( $GLOBALS[ 'TSFE' ]->cObj ) && is_object( $GLOBALS[ 'TSFE' ]->cObj ) ) {
		
			$this->cObj = $GLOBALS[ 'TSFE' ]->cObj;
		
		} elseif( !is_object( $this->cObj ) ) {
		
			// Creates a new instance of tslib_cObj
			$this->cObj = t3lib_div::makeInstance( 'tslib_cObj' );
		}
	}
	
	
	/**
	 * Get all owners with existing related documents
	 * 
	 * @remotable
	 * @return array to by encoded in JSON format
	 */
	public function get( $params ) {
		
		// build clause of query
		$this->_setSqlClause( $params );
		
		if( $this->_debug ) {
			
			t3lib_div::devLog( 'class model_document sql clause', 'doc_db', 0, $this->_sqlClause );
		}
		// store
		$rows = array();
		
		$select  = 'p.uid, p.title, p.lastupdated AS date, p.tx_docdb_doc_descriptor AS dscr,p.tx_docdb_doc_related_pages pages,';
		$select .= 'p.tx_docdb_doc_key AS dkey, o.owner, t.type, s.status';

        //  p.tx_templavoila_flex AS flex,
		
		$from    = $this->_sqlClause[ 'from' ];
		
		$where   = '(p.doktype=198 AND (p.deleted=0 AND p.hidden=0))';
		$where  .= $this->_sqlClause[ 'where' ];
		
		$groupBy = 'p.uid';
		
		
		$orderBy = $this->_sqlClause[ 'order' ];

		$limit = $this->_sqlClause[ 'limit' ];
		
		
		$res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
			$select,
			$from,
			$where,
			$groupBy,
			$orderBy,
			$limit
		);
		
		$rows = array();
		///$k = 0;
		while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) ) {
			
			if( $row[ 'owner' ] === 'zzz_none' ) { $row[ 'owner' ] = ''; }
			if( $row[ 'type' ] === 'zzz_none' ) { $row[ 'type' ] = ''; }
			if( $row[ 'status' ] === 'zzz_none' ) { $row[ 'status' ] = ''; }
			
			if( $row[ 'title' ] ) {
				
				$row[ 'docPageURL' ] = $this->cObj->typoLink( '', array('parameter' => $row[ 'uid' ], 'returnLast' => 'url' ) ); 
			}

            // related descriptors if any
            $row[ 'dscr' ] = $row[ 'dscr' ] ? $this->_getRelatedDescriptors( $row[ 'uid' ] ) : array();
            
            // related pages if any
            $row[ 'pages' ] = $row[ 'pages' ] ? $this->_getRelatedPages( $row[ 'uid' ] ) : array();
            
            /*
			if( $row[ 'flex' ] ) {
				
				$ttC = $this->_getContentPreview( $row[ 'flex' ], 'field_content' );
				
				unset( $row[ 'flex' ] );
				$row[ 'prevH' ] = $ttC[ 'header' ];
				$row[ 'prevC' ] = $this->_RTEcssText( $ttC[ 'bodytext' ] );
				
			}
			*/
            
			$rows[] = $row;
		}
		
		$GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $res );
		
		$totalCount = tx_docdb_div::exec_SELECTcountRows( 'p.uid', $from, $where );
		
		$out = array(
			'success'    => TRUE,
			'totalCount' => $totalCount,
			'rows'       => $rows
		);
		
		return $out;
	}
	
	/**
     * Set the sql clause from, where, sort, order, limit
     * @param object $params
     */
	protected function _setSqlClause( $params )
	{
		
		// store query
		$from  = '';
		$where = '';
		
		// paging
		$start     = isset( $params->start ) ? (int)$params->start : 0;
		$count     = isset( $params->limit ) ? (int)$params->limit : 10;
		// group sorting order
		$gSort     = isset( $params->groupBy ) ? $params->groupBy : '';
		$gDir      = isset( $params->groupDir ) ? $params->groupDir : 'ASC';
		// rows sorting order
		$sort      = isset( $params->sort ) ? $params->sort : 'title';
		$dir       = isset( $params->dir ) ? $params->dir : 'ASC';
		// doc def selection
		$ownerIds  = isset( $params->owner ) && $params->owner !== '0' ? $params->owner : '';
		$typeIds   = isset( $params->type ) && $params->type !== '0' ? $params->type : '';
		$statusIds = isset( $params->status ) && $params->status !== '0' ? $params->status : '';
		// decriptors selection
		$dscrIds     = isset( $params->selNodes ) ? $params->selNodes : '';
		$dscrSelType = isset( $params->selType ) ? $params->selType : 'AND';
		
		// substitution
        if( $sort === 'date' ) {

            $sort = 'lastupdated';

        } else if( $sort === 'dkey' ) {

            $sort = 'tx_docdb_doc_key';
        }

        if( $gSort === 'date') {

            $gSort = 'lastUpdated';
        }


		if( isset( $params->filter ) ) {
            
            // map search field
            $fieldMap = array(
                'title'  => 'p.title',
                'owner'  => 'o.owner',
                'type'   => 't.type',
                'status' => 's.status',
                'date'   => 'p.lastUpdated'
            );


        $filters = json_decode( $params->filter );
            
            // buid query from grid filter field -> value
            if( is_array( $filters ) ) {

                foreach( $filters as $filter ) {

                    switch( $filter->type ) {

                        case 'string' :
                            $where .= ' AND ' . $fieldMap[ $filter->field ] . ' LIKE \'%' . $filter->value .'%\'';
                            break;

                        case 'date' :
                            // buid tstamp
                            $sDate = preg_split( '/\//', $filter->value );
                            $sTime = mktime( 0, 0, 0 , $sDate[ 1 ], $sDate[ 0 ], $sDate[ 2 ] );

                            switch( $filter->comparison ) {

                                case 'eq' : $where .= ' AND ' . $fieldMap[ $filter->field ] .' > \'' . $sTime . '\' AND ' . $fieldMap[ $filter->field ] .' < \'' . ( $sTime + 86400 ) . '\'';
                                break;

                                case 'lt' : $where .= ' AND ' . $fieldMap[ $filter->field ] . ' < \''. ( $sTime + 86400 ) .'\'';
                                break;

                                case 'gt' : $where .= ' AND ' . $fieldMap[ $filter->field ] . ' > \''. $sTime .'\'';
                                break;
                            }
                        Break;
                    }
                }
            }
        } // eo isset filter
		
		
		$from  .= 'pages p, tx_docdb_owner o, tx_docdb_type t, tx_docdb_status s';
		$where .= 'AND ((p.tx_docdb_doc_owner=o.uid) AND (p.tx_docdb_doc_type=t.uid) AND ( p.tx_docdb_doc_status=s.uid))';
		
		if( $dscrIds ) {
			
			$from  .= ',tx_docdb_pages_doc_descriptor_mm mm, tx_docdb_descriptor d';
			$where .= ' AND ((mm.uid_local=p.uid AND mm.uid_foreign=d.uid)';
			
			if( $dscrSelType === 'OR' ) {
				
				$where .= ' AND (mm.uid_foreign IN(' . $dscrIds . ')))';
				
			} else {
				
				$dscrIds = explode( ',', $dscrIds );
				$dscrIds = implode( ' AND mm.uid_foreign=', $dscrIds );
				$where  .= ' AND (mm.uid_foreign=' . $dscrIds .'))';
			}
		}
		
		if( $ownerIds && $typeIds && $statusIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_owner IN(' . $ownerIds . ')';
			$where .= ' AND p.tx_docdb_doc_type IN(' . $typeIds . ')';
			$where .= ' AND p.tx_docdb_doc_status IN(' . $statusIds . '))';
			
		} else if( $ownerIds && $typeIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_owner IN(' . $ownerIds . ')';
			$where .= ' AND p.tx_docdb_doc_type IN(' . $typeIds . '))';
			
		} else if( $typeIds && $statusIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_type IN(' . $typeIds . ')';
			$where .= ' AND p.tx_docdb_doc_status IN(' . $statusIds . '))';
			
		} else if( $ownerIds && $statusIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_owner IN(' . $ownerIds . ')';
			$where .= ' AND p.tx_docdb_doc_status IN(' . $statusIds . '))';
			
		} else if( $ownerIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_owner IN(' . $ownerIds . '))';
			
		} else if( $typeIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_type IN(' . $typeIds . '))';
			
		} else if( $statusIds ) {
			
			$where .= ' AND (p.tx_docdb_doc_status IN(' . $statusIds . '))';
		}

        
		// fill sql clause array keys
		$this->_sqlClause[ 'from' ]  = $from;
		
		$this->_sqlClause[ 'where' ] = $where;
		
		if( $gSort ) {
			
			$this->_sqlClause[ 'order' ] = $gSort . ' ' . $gDir . ',' . $sort . ' ' . $dir;
		
		} else {
			
			$this->_sqlClause[ 'order' ] = $sort . ' ' . $dir;
		}
		
		$this->_sqlClause[ 'limit' ] = $start . ',' . $count;
		
		unset( $from, $where, $gSort, $gDir, $sort, $dir, $start, $count );
			//       var_dump( $this->_sqlClause ); exit;
	}


    protected function _getRelatedDescriptors( $pageId ) {

        // store result array
        $rows = array();
        
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECT_mm_query(
            'tx_docdb_descriptor.uid,tx_docdb_descriptor.title',
            'pages',
            'tx_docdb_pages_doc_descriptor_mm',
            'tx_docdb_descriptor',
            'AND pages.uid=' . $pageId . ' ' . $this->cObj->enableFields( 'tx_docdb_descriptor' ),
            'uid',
            'title',
            '20'
        );

        while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) ) {

            $dscrObj = new stdClass();
            $dscrObj->did    = $row[ 'uid' ];
            $dscrObj->dtitle = $row[ 'title' ];

            $rows[] = $dscrObj;
        }

        //print_r( $rows ); exit;
        return $rows;
    }


    protected function _getRelatedPages( $pageId ) {

//        // store result array
//        $rows = array();
//
//        $rows = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
//			'uid,title',
//			'pages',
//			'uid IN( SELECT uid_foreign FROM tx_docdb_pages_doc_related_pages_mm where tx_docdb_pages_doc_related_pages_mm.uid_local=' . $pageId . ') ' .
//            $this->cObj->enableFields( 'pages' ),
//			'uid',
//			'title',
//			'20'
//		);
        // store result array
        $rows = array();

        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            'uid,title',
            'pages',
            'uid IN( SELECT uid_foreign FROM tx_docdb_pages_doc_related_pages_mm where tx_docdb_pages_doc_related_pages_mm.uid_local=' . $pageId . ') ' .
            $this->cObj->enableFields( 'pages' ),
            'uid',
            'title',
            '20'
        );

        while( ( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) ) {

            $relPageObj = new stdClass();
            $relPageObj->pUrl   = $this->cObj->typoLink( '', array('parameter' => $row[ 'uid' ], 'returnLast' => 'url' ) );
            $relPageObj->pTitle = $row[ 'title' ];

            $rows[] = $relPageObj;
        }

        $GLOBALS[ 'TYPO3_DB' ]->sql_free_result( $res );
        unset( $row );
        
        return $rows;
    }


	/**
     * (not used)
     * Get the first content from mapped content in tv 
     * @param <type> $tvFlex
     * @param <type> $contentDsFielName
     * @return <type>
     */
	private function _getContentPreview( $tvFlex, $contentDsFielName='' ) {
		
		
		$tvDS = t3lib_div::xml2array( $tvFlex );
//		return print_r( $tvDS, TRUE );
		
		if( isset( $tvDS[ 'data' ][ 'sDEF' ][ 'lDEF' ][ $contentDsFielName ] ) ) {
			
			$ttContentUids = $tvDS[ 'data' ][ 'sDEF' ][ 'lDEF' ][ $contentDsFielName ][ 'vDEF' ];
			
		}
		
		$row = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTgetRows(
			'header,bodytext',
			'tt_content',
			'uid IN(' . $ttContentUids . ') AND (deleted=0 AND hidden=0)',
			$groupBy,
			$orderBy,
			'1'
		);
		
		return $row[ 0 ]; //print_r( $row[ 0 ], TRUE );;
		
	}
	
	
	/**
	* Parse RTE content
	*
	* @param string $str
	* @return RTE parsed string
	*/
	private function _RTEcssText( $str ) {
	
		// use default config
		$parseFunc =& $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'lib.' ][ 'parseFunc_RTE.' ];
		
		if( is_array( $parseFunc ) ) {
		
			$str = $this->cObj->parseFunc( $str, $parseFunc );
		
		}
		
		return $str;
	}
	
}





// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_document.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_document.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/model/class.tx_docdb_model_document.php']);
}

}

