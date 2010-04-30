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
 * Class/Function frontend plugin
 *
 * $Id: class.tx_docdb_pi1.php 207 2010-03-12 21:37:26Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-03-12 22:37:26 +0100 (ven 12 mar 2010) $
 *
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */


require_once( PATH_tslib . 'class.tslib_pibase.php' );
require_once( t3lib_extMgm::extPath( 'doc_db' ) . 'classes/class.tx_docdb_div.php' );

class tx_docdb_pi1 extends tslib_pibase
{
	/**
	 * local : site relative path to ext rootDir
	 *
	 * @var string
	 */
	protected $_siteRelPath = '';

	/**
	 * local : ext TS config
	 *
	 * @var array
	 */
	protected $_conf = array();

    /**
     * Local flex store
     *
     * @var array
     */
    protected $_piFform = array();

	/**
	 * Prefix pi1
	 *
	 * @var string
	 */
	public $prefixId = 'tx_docdb_pi1';

	/**
	 * Extension key
	 *
	 * @var string
	 */
	public $extKey = 'doc_db';

	/**
	 * Path to this script relative to the extension dir.
	 *
	 * @var string
	 */
	public $scriptRelPath = 'pi1/class.tx_docdb_pi1.php';


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The		content that is displayed on the website
	 */
	public function main( $content, &$conf ) {

		// Local extjs conf array
		$_extjsConf = array();

		// Local extjsDirect obj
		$_extjs = NULL;

		$this->_conf =& $conf;

		//$this->pi_setPiVarDefaults();

        $this->pi_loadLL();

        // Init FlexForm configuration for plugin
        $this->pi_initPIflexForm();

        // Store flexform informations
        $this->_piFform     = $this->cObj->data['pi_flexform'];
        
        $this->_setConfig();

        if( ! $this->_conf['extJS.']['confInc'] ) {

            return $this->pi_wrapInBaseClass(
                '<b>' . $this->pi_getLL( 'errorMsgTsNotInc', 'Static TypoScript Template not included or if it is, try to clear cache' ) . '</b>'
            );
        }

        // resolv resources path
        tx_docdb_div::subExtPrefixPath( $this->_conf['extJS.']['resourcesPath'] );

		$this->_siteRelPath = t3lib_extMgm::siteRelPath( $this->extKey );

		if( is_object( $_extjs = t3lib_div::makeInstanceService( 'extjsDirect' ) ) ) {

			$this->_conf['extjs-ct'] = $this->_conf['extjs-ct'] ? $this->_conf['extjs-ct'] :'extjs-ct';

			$this->_conf['extjs-ct'] .= $this->cObj->data['uid'];

            // global conf
            $_extjsConf = array(
                'adapter'     => 'ext/ext-base.js',  //default
                'eID'         => 'docdb',
                'extensionId' => $this->cObj->data['uid'],
                'prefixId'    => $this->prefixId,
                //'themes.'     => array( 'wcc' )
            );

            if( $this->_conf['extJS.']['production'] ) {

                $_extjsConf['js.'] = array(

                    // all js files needed for app, cat and minified
                    $this->_siteRelPath . 'resources/js/DocDb-lib-min.js'
                );

            } else {

                // most of them is not needed for the standalone grid. but they will be downloaded only one time
                $_extjsConf['js.'] = array(
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.plugins.HeaderButtons.js',
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.form.MultiSelect.js',
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.MsgBus.js',

                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.menu.RangeMenu.js',
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.grid.GridFilters.js',
                    $this->_siteRelPath . 'resources/js/ux/filters/Filter.js',
                    $this->_siteRelPath . 'resources/js/ux/filters/StringFilter.js',
                    $this->_siteRelPath . 'resources/js/ux/filters/DateFilter.js',
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.grid.RowExpander.js',
                    $this->_siteRelPath . 'resources/js/ux/Ext.ux.PageSizePlugin.js',

                    $this->_siteRelPath . 'resources/js/DocDb.OwnersList.js',
                    $this->_siteRelPath . 'resources/js/DocDb.TypesList.js',
                    $this->_siteRelPath . 'resources/js/DocDb.StatusList.js',
                    $this->_siteRelPath . 'resources/js/DocDb.Descriptors.js',

                    $this->_siteRelPath . 'resources/js/DocDb.GridResults.js',
                );
            }

            if( $this->_getViewMode() === 'FE' ) {

                $loadingText = $this->pi_getLL( 'searchLoadingText', 'Loading ...' );

                $nodes ='';
                require_once( t3lib_extMgm::extPath( $this->extKey ) . 'classes/model/class.tx_docdb_model_descriptor.php' );
                $dscr = t3lib_div::makeInstance( 'tx_docdb_model_descriptor' );
                $nodes = $dscr->getSessionNodes( '0' );

                if( $this->_conf['extJS.']['production'] ) {

                    $_extjsConf += array(
                        'adapter' => 'merged',
                        'css.'    => array(
                                $this->_siteRelPath . 'resources/css/default-min.css'
                        )
                    );

                    // add global js DoDb.app minidied contain  DocDb.SearchForm.js and DocDb.searchMainApp.js
                    array_push( $_extjsConf['js.'],
                        $this->_siteRelPath . 'resources/js/DocDb.searchMainApp-prod-min.js'
                    );

                } else {

                    $_extjsConf['css.'] = array(
                        $this->_siteRelPath . 'resources/css/default.css'
                    );

                    // add global js DoDb.app
                    array_push( $_extjsConf['js.'],
                        $this->_siteRelPath . 'resources/js/DocDb.SearchForm.js',
                        $this->_siteRelPath . 'resources/js/DocDb.searchMainApp.js'
                    );
                } // eo else production

                // get only grouping properties from gridParams
                $this->_deleteNotWantedProp( $this->_conf['ff']->gridParam, array( 'grouping', 'groupBy', 'field', 'direction', 'dF', 'colsW', 'selNodes', 'lang' ) );
                
                $this->_setToInt( $this->_conf['ff']->treeHeight, array() );
                $this->_setToInt( $this->_conf['ff']->docDetail, array( 'divContIdWinP' ) );

                $_extjsConf['statvar.'] = array(
                    array(
                        'type'      => 'APPLY_TO',
                        'namespace' => 'DocDb',
                        'assign'    => 'DocDb.mainPanel',
                        'lang.'     => array('sort',
                            'prefix' => '',
                            'LLfile' => $this->_siteRelPath . 'resources/lang/locallang_extjs.docdb.xml',
                        ),
                        'statvar.' => array(
                            'RENDER_TO'      => $this->_conf['extjs-ct'],
                            'PAGESIZE'       => (int)$this->_conf['ff']->pageSize,
                            'gridParams'     => $this->_conf['ff']->gridParam,
                            'nodes'          => $nodes,
                            'mainPWidth'     => (int)$this->_conf['ff']->mainPWidth,
                            'gridHeight'     => (int)$this->_conf['ff']->gridHeight,
                            'docDetail'      => $this->_conf['ff']->docDetail,
                            'mSelHeight'     => (int)$this->_conf['ff']->mSelHeight,
                            'treeHeight'     => $this->_conf['ff']->treeHeight,
                            'formHeight'     => (
                                (int)$this->_conf[ 'ff' ]->mSelHeight + (int)$this->_conf[ 'ff' ]->treeHeight->min
                            )
                        )
                    )
                ); // eo $_extjsConf['statvar.']

                $divMaskH = (int)$this->_conf[ 'ff' ]->mSelHeight + (int)$this->_conf[ 'ff' ]->treeHeight->min;
                //$divMaskH = $this->_conf['ff']->gridHeight;

            /**
             * pi BE config search result in flexform
             */
            } else if( $this->_getViewMode() === 'PI' ) {

                $loadingText = $this->pi_getLL( 'gridLoadingText', 'Loading ...' );

                if( $this->_conf['extJS.']['production'] ) {

                    $_extjsConf += array(
                        'adapter' => 'merged',
                        'css.'    => array(
                                $this->_siteRelPath . 'resources/css/default-min.css'
                        )
                    );

                    // add global js DoDb.app minidied contain DocDb.gridMainApp
                    array_push( $_extjsConf['js.'],
                        $this->_siteRelPath . 'resources/js/DocDb.gridMainApp-prod-min.js'
                    );

                } else {

                    $_extjsConf['css.'] = array(
                        $this->_siteRelPath . 'resources/css/default.css'
                    );

                    // add global js DoDb.app
                    array_push( $_extjsConf['js.'],
                        $this->_siteRelPath . 'resources/js/DocDb.gridMainApp.js'
                    );
                }

                $this->_setToInt( $this->_conf['ff']->gridParam, array( 'groupBy', 'groupDir', 'field', 'direction', 'selType', 'owner', 'type', 'status', 'selNodes','dF', 'colsW' ) );
                $this->_setToInt( $this->_conf['ff']->docDetail, array( 'divContIdWinP' ) );

                $_extjsConf['statvar.'] = array(

                    array(
                        'type'      => 'APPLY_TO',
                        'namespace' => 'DocDb',
                        'assign'    => 'DocDb.mainGrid',
                        'lang.'     => array(
                            'prefix' => '',
                            'LLfile' => $this->_siteRelPath . 'resources/lang/locallang_extjs.docdb.gridStandalone.xml',
                        ),
                        'statvar.' => array(
                            'RENDER_TO'      => $this->_conf['extjs-ct'],
                            'PAGESIZE'       => (int)$this->_conf['ff']->pageSize,
                            'gridParams'     => $this->_conf['ff']->gridParam,
                            'mainPWidth'     => (int)$this->_conf['ff']->mainPWidth,
                            'gridHeight'     => (int)$this->_conf['ff']->gridHeight,
                            'docDetail'      => $this->_conf['ff']->docDetail,
                        )
                    )
                ); // eo $_extjsConf['statvar.']

                $divMaskH = $this->_conf['ff']->gridHeight;

            } // eo if PI


			$_extjs->process( $_extjsConf, $this->_conf['extJS.'] );

		} else {

			$content = '<p>Error: TYPO3 ExtJs Direct Service is not available.</p>';
		}

            $content .= '
                <div style="position:relative;margin:0;padding:0;">
                <div id="loading-mask" style="width:' . $this->_conf['ff']->mainPWidth . 'px;height:' . $divMaskH . 'px;"></div>
                    <div id="loading">

                        <div class="loading-indicator">
                                <img src="' . $this->_siteRelPath . 'resources/ExtJS/3.0.3/resources/images/wcc/ajax-loader.gif" width="128" height="15" align="absmiddle" /><br />
                                <p>' . $loadingText . '</p>
                        </div>
                    </div>
                    </div>
                ';

		$content .= '<div id="' . $this->_conf['extjs-ct'] . '"></div>';


		return $this->pi_wrapInBaseClass( $content );
	}

	/**
	 * local func _getViewMode
	 * view mode:
	 *   - FE : query defined in frontend with select form
	 *   - PI : query defined in flexform to display table list of selected documents
	 * @return view mode defined in FF ( FE or PI )
	 */
	protected function _getViewMode() {

        return $this->_conf['ff']->mode;
	}


    /**
     * build plugin TS configuration from flexform configuration.
     */
    protected function _mergeConfFlex( $mapObj, $flexRes ) {

        // Process each entry of the mapping array
        foreach( $mapObj as $key => $value ) {

            // Check if current TS object has sub objects
            if( is_object( $value ) ) {

                // Item has sub objects - Process the array
                $tempConfig->$key = $this->_mergeConfFlex(
                    $value,
                    $flexRes
                );

            } else {

                // No sub objects - Get informations about the flexform value to get
                $flexInfo = explode( ':', $value );

                // Try to get the requested flexform value
                $tempConfig->$key = $this->pi_getFFvalue(
                    $flexRes,
                    $flexInfo[ 1 ],
                    $flexInfo[ 0 ]
                );
            }
        }

        // Return configuration
        return $tempConfig;
    }


    /**
     * setConfig : all in one
     *
     * Functions to set propertie $this-conf from the merged values of TS and FF.
     */
    private function _setConfig() {

        // Mapping array for PI flexform,
        $flexConf = new stdClass();
        $flexConf->mode      = 'sDEF:mode';

        $flexConf->gridParam = new stdClass();
        $flexConf->gridParam->owner      = 'sDEF:owner';
        $flexConf->gridParam->type       = 'sDEF:docType';
        $flexConf->gridParam->status     = 'sDEF:docStatus';
        $flexConf->gridParam->grouping   = 'sDEF:grouping';
        $flexConf->gridParam->groupBy    = 'sDEF:grouping_by';
        $flexConf->gridParam->field      = 'sDEF:sorting';
        $flexConf->gridParam->direction  = 'sDEF:sorting_order';
        $flexConf->gridParam->selType    = 'sDEF:descr_combination';
        $flexConf->gridParam->selNodes   = 'sDEF:descriptors';

        $flexConf->mainPWidth = 'sDisplay:mainWidth';
        $flexConf->mSelHeight = 'sDisplay:mulSel_height';
        $flexConf->pageSize   = 'sDisplay:resGrid_pageSize';
        $flexConf->gridHeight = 'sDisplay:resGrid_height';

        $flexConf->docDetail  = new stdClass();
        $flexConf->docDetail->divContIdWinP = 'sDisplay:divContIdWinP';
        $flexConf->docDetail->pWinWidth     = 'sDisplay:pWinWidth';
        $flexConf->docDetail->pWinHeight    = 'sDisplay:pWinHeight';

        $flexConf->treeHeight = new stdClass();
        $flexConf->treeHeight->min = 'sDisplay:dscrTreeMin_height';
        $flexConf->treeHeight->max = 'sDisplay:dscrTreeMax_height';

        // setup with flexform val
        $this->_conf['ff'] = $this->_mergeConfFlex(
            $flexConf,
            $this->_piFform
        );

        if( ! $this->_conf['ff']->gridParam->grouping ) {

            $this->_conf['ff']->gridParam->groupBy = '';
        }

        // set values of columns width to array of int from string
        $this->_conf['ff']->gridParam->colsW = explode( ',', $this->_conf[ 'extJS.' ][ 'gridColsWidth' ] );

        array_walk( $this->_conf['ff']->gridParam->colsW, create_function( '&$a,$k', '$a = (int)$a;' ) );

        $dForm     = $this->_conf[ 'extJS.' ][ 'dateF.' ];
        $dFormCust = $this->_conf[ 'extJS.' ][ 'dateFCustom.' ];
        $dF        = new stdClass();
        
        if( $dFormCust['row'] !== '' ) {

            $dF->row = $dFormCust['row'];

        } else {

            $dF->row = $dForm['row'];
        }

        if( $dFormCust['detail'] !== '' ) {

            $dF->detail = $dFormCust['detail'];
            
        } else {

            $dF->detail = $dForm['detail'];
        }

        $this->_conf['ff']->gridParam->dF = $dF;

        // remove prepend # if any
        $this->_conf[ 'ff' ]->docDetail->divContIdWinP = str_replace( '#', '', $this->_conf[ 'ff' ]->docDetail->divContIdWinP );

        // if any pivars for descriptor
        $this->_setDscrFromPiVars( $this->_conf['ff']->gridParam->selNodes );

        $this->_setGridVarLang();
    }

    /**
     *
     * @return void : uid of selected descriptor or false if piVars not set
     */
    protected function _setDscrFromPiVars( &$selNodeProp ) {

        if( isset( $this->piVars['descriptors'] ) ) {

            $selNodeProp = (int)$this->piVars['descriptors'];
        }
    }

    /**
     * grid var lang select
     * @return void :
     */
    protected function _setGridVarLang() {

        $lang = t3lib_div::GPvar( 'L' );
        if( isset( $lang ) ) {
            
            $this->_conf['ff']->gridParam->lang = (int)$lang;
        }
    }


    private function _setToInt( &$obj, array $excludeProp ) {

        //
        foreach( $obj as $p => $v ) {

            if( ! in_array( $p, $excludeProp ) ) {

                $obj->$p = (int)$v;
            }
        }
    }


    private function _deleteNotWantedProp( &$obj, array $excludeProp ) {

        //
        foreach( $obj as $p => $v ) {

            if( ! in_array( $p, $excludeProp ) ) {

                unset( $obj->$p );
            }
        }
    }
}


// avoid notice
if( defined( 'TYPO3_MODE' ) && isset( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1.php'] ) ) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1.php']);
}

}

