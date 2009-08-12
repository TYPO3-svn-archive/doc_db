<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Nicolas Wezel ()
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
 * Plugin 'Document DB' for the 'doc_db' extension.
 *
 * @author	Nicolas Wezel <>
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_div.php');
require_once('class.tree_descriptor.php');

class tx_docdb_pi1 extends tslib_pibase {
	var $prefixId = 'tx_docdb_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_docdb_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'doc_db';	// The extension key.
	var $template = ''; // HTML Template code
	var $mode = 0; 	// 0 for user defined query (display the search engine)
					// 1 for admin defined query (query defined in plugin flexform)
	
	/**
	 * [Put your description here]
	 */
	function getOptionList($table, $uid, $field) {
		//$rows['0'] = array('uid'=>0, $field=>'ALL');
		$rows = $this->pi_getCategories($table,'','',$field);

		$this->internal['category'][$table] = $rows;
		$out = '<option value="0">ALL</option>';
		if (count($rows)>1) {
			foreach($rows as $key=>$value) {
				$out .= '<option value="'.$value['uid'].'" '.(($uid==$value['uid'])?'selected':'').'>'.$value[$field].'</option>';
			}
		}
		return $out;
	}
	
	/**
	 * Makes a standard query for listing of records based on standard input vars from the 'browser' ($this->internal['results_at_a_time'] and $this->piVars['pointer']) and 'searchbox' ($this->piVars['sword'] and $this->internal['searchFieldList'])
	 * Set $count to 1 if you wish to get a count(*) query for selecting the number of results.
	 * Notice that the query will use $this->conf['pidList'] and $this->conf['recursive'] to generate a PID list within which to search for records.
	 *
	 * @param	string		See pi_exec_query()
	 * @param	boolean		See pi_exec_query()
	 * @param	string		See pi_exec_query()
	 * @param	mixed		See pi_exec_query()
	 * @param	string		See pi_exec_query()
	 * @param	string		See pi_exec_query()
	 * @param	string		See pi_exec_query()
	 * @param	boolean		If set, the function will return the query not as a string but array with the various parts.
	 * @return	mixed		The query build.
	 * @access private
	 * @depreciated		Use pi_exec_query() instead!
	 */
	function pi_list_query($table,$count=0,$addWhere='',$mm_cat='',$groupBy='',$orderBy='',$query='',$returnQueryArray=FALSE)	{

			// TABLENAME
		$TABLENAMES = 'pages';
		$TABLENAMES .= ' LEFT JOIN pages_tx_docdb_doc_descriptor_mm ON pages.uid = pages_tx_docdb_doc_descriptor_mm.uid_local';
		$TABLENAMES .= ' LEFT JOIN tx_docdb_descriptor ON tx_docdb_descriptor.uid = pages_tx_docdb_doc_descriptor_mm.uid_foreign';
		$TABLENAMES .= ' LEFT JOIN tx_docdb_owner ON pages.tx_docdb_doc_owner = tx_docdb_owner.uid';
		$TABLENAMES .= ' LEFT JOIN tx_docdb_type ON pages.tx_docdb_doc_type = tx_docdb_type.uid';
		// **OS 10.4.2006
		$TABLENAMES .= ' LEFT JOIN tx_docdb_status ON pages.tx_docdb_doc_status = tx_docdb_status.uid';
		
			// WHERE:
		$WHERE .= ' pages.doktype = 198 '.$this->cObj->enableFields($table).chr(10); 
		
		if (is_array($mm_cat['catUidList'])) {	
				$clause ='';
				foreach ($mm_cat['catUidList'] as $key => $catList) {
					$WHERE .=' AND '.$mm_cat['table'].'.uid IN ('.implode(',',$catList).') '; 
				}
		}

			// Add '$addWhere'
		if ($addWhere)	{$WHERE.=' '.$addWhere.chr(10);}

			// Search word:
		if ($this->piVars['sword'] && $this->internal['searchFieldList'])	{
			$WHERE.=$this->cObj->searchWhere($this->piVars['sword'],$this->internal['searchFieldList'],$table).chr(10);
		}

		if ($count) {
			$queryParts = array(
				'SELECT' => ' COUNT(DISTINCT pages.uid)',
				'FROM' => $TABLENAMES,
				'WHERE' => $WHERE,
				'GROUPBY' => '',
				'ORDERBY' => '',
				'LIMIT' => ''
			);
		} else {
				// Order by data:
			if (!$orderBy && $this->internal['orderBy'])	{
				if (t3lib_div::inList($this->internal['orderByList'],$this->internal['orderBy']))	{
					$orderBy = 'ORDER BY '.$this->internal['orderBy'].(($this->internal['descFlag']==1)?' DESC':'');
				}
			}

				// Limit data:
			$pointer = $this->piVars['pointer'];
			$pointer = intval($pointer);
			$results_at_a_time = t3lib_div::intInRange($this->internal['results_at_a_time'],1,1000);
			$LIMIT = ($pointer*$results_at_a_time).','.$results_at_a_time;

				// Add 'SELECT'
			$queryParts = array( 
				'SELECT' => ' DISTINCT pages.* , tx_docdb_owner.uid AS \'tx_docdb_owner.uid\', tx_docdb_owner.owner AS \'owner\', tx_docdb_type.uid AS \'tx_docdb_type.uid\', tx_docdb_type.type AS \'type\'',
				'FROM' => $TABLENAMES,
				'WHERE' => $WHERE,
				'GROUPBY' => $GLOBALS['TYPO3_DB']->stripGroupBy($groupBy),
				'ORDERBY' => $GLOBALS['TYPO3_DB']->stripOrderBy($orderBy),
				'LIMIT' => $LIMIT
			);
	
		}
//t3lib_div::debug('WHERE:'.$WHERE);
		$query = $GLOBALS['TYPO3_DB']->SELECTquery (
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE'],
					$queryParts['GROUPBY'],
					$queryParts['ORDERBY'],
					$queryParts['LIMIT']
				);

		return $returnQueryArray ? $queryParts : $query;
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_list_searchBox() {

		$tree = $this->internal['tree'];
		
		// this should be replace in next release by value from configuration
		// $tPath = t3lib_extMgm::extPath($this->extKey).dirname($this->scriptRelPath).'/frontend_js.html';
		// **OS 10.1.07 $this->templateCode = t3lib_div::getUrl($tPath);
		$template = $GLOBALS['TSFE']->cObj->getSubpart($this->template, 'TEMPLATE_SEARCH');

		// include Owner
		//function pi_getCategoryTableContents($table,$pid,$whereClause='',$groupBy='',$orderBy='',$limit='')	
		$out = $this->getOptionList('tx_docdb_owner', intval($this->piVars['owner']), 'owner');
		$template = $this->cObj->substituteSubpart($template,'OWNER_LIST',$out);
		
		// include Document Type
		$out = $this->getOptionList('tx_docdb_type', intval($this->piVars['docType']), 'type');
		$template = $this->cObj->substituteSubpart($template,'DOCTYPE_LIST',$out);

		// **OS 10.4.2005 include Document Status
		$out = $this->getOptionList('tx_docdb_status', intval($this->piVars['docStatus']), 'statuts');
		$template = $this->cObj->substituteSubpart($template,'STATUS_LIST',$out);
		
		// include Descriptors tree
		$out = $tree->getBrowsableTree();		
		$template = $this->cObj->substituteSubpart($template,'TREE',$out);
		
		// include Descriptor List	
		$tree->setDataFromArray($tree->tree,true); //set dataLookup array in tree, must be for getRecord(uid)
		$descrList = explode(',',$this->piVars['descriptors']);
		$out = '';
		foreach ($descrList as $uid) {
			if ($uid) {
				$row = $tree->getRecord($uid);
				$out .= '<option value="'.$uid.'">'.$row['title'].'</option>';
			}
		}
		$template = $this->cObj->substituteSubpart($template,'SELECTED_LIST',$out);
		
		// **OS 16.3.2006 Corrected for compatiblity with RealURL
            $markArray['###PAGE_ID###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id);
		$markArray['###AND_SELECTED###']='';
		$markArray['###OR_SELECTED###']='';
		($this->piVars['combination']==0)?$markArray['###AND_SELECTED###']='checked="checked"':$markArray['###OR_SELECTED###']='checked="checked"';
		$markArray['###TREE_STATUS###'] = $tree->getTreeStatus();
		$markArray['###SWORD###'] = t3lib_div::_GP('sword');
		$markArray['###DESCRIPTORS###'] = $this->piVars['descriptors'];
		$template = $this->cObj->substituteMarkerArray($template,$markArray);

		return $template;
	}
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		
		$this->pi_initPIflexForm();
		$this->mode = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'mode');
		
		// if query defined in plugin flexform, surcharge the piVars with values from flexform
		if ($this->mode) {
			$this->piVars['owner'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'owner');
			$this->piVars['docType'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'docType');
			// **OS 10.4.2006
			$this->piVars['docStatus'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'docStatus');
			$this->piVars['combination'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'descr_combination');
			$this->piVars['descriptors'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'descriptors/el');
			$this->piVars['CMD'] = 'list';
			$this->piVars['addWhere'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'addWhere');
		}
		// **OS 10.1.2007  Get the template file
		$tPath = t3lib_extMgm::siteRelPath($this->extKey).dirname($this->scriptRelPath).'/frontend_js.html';
            $tFlex = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'template_file');
            $this->piVars['templateFile'] = $tFlex?$tFlex:$tPath;
 //debug($this->piVars);

            $this->template = $this->cObj->fileResource($this->piVars['templateFile']);

		switch((string)$conf['CMD'])	{
			case 'singleView':
				list($t) = explode(':',$this->cObj->currentRecord);
				$this->internal['currentTable']=$t;
				$this->internal['currentRow']=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content,$conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,'tt_content'))	{
					$conf['pidList'] = $this->cObj->data['pages'];
					$conf['recursive'] = $this->cObj->data['recursive'];
				}
				//t3lib_div::debug($this->piVars['descriptors']);
				return $this->pi_wrapInBaseClass($this->listView($content,$conf));
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_getDescrList($type)	{
		if($this->internal['descrList']) return $this->internal['descrList'];
		
		$descr = explode(',',$this->piVars['descriptors']);
		$tree = $this->internal['tree'];
		
		foreach ($descr as $key => $uid) {
		($type)?$index=0:$index=$key;
			if ($uid) {
					//initiate array if necessary
				if (!is_array($list[$index])) $list[$index]=array();
				
				$node = $tree->getRecord($uid);
				$list[$index] = $tree->getListNodes($node,$list[$index]);
				  
				// include here the nodes for which are synonym of the current node
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_docdb_descriptor', ' tx_docdb_descriptor.dscr_related='.$uid);
				while ($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){	
					$list[$index] = $tree->getListNodes($row,$list[$index]);
				} 
				$list[$index] = array_unique($list[$index]);
			}
		}
		
		return $list;
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_descr_query($isCount)	{
		
		if($this->mode) $addWhere = $this->piVars['addWhere'];
	
		$table = $this->internal['currentTable'];
		$fields = $this->internal['searchFieldList'];
	 
		if ($this->piVars['owner']) $addWhere .=' AND pages.tx_docdb_doc_owner='.intval($this->piVars['owner']);
		if ($this->piVars['docType']) $addWhere .=' AND pages.tx_docdb_doc_type='.intval($this->piVars['docType']);
		// **OS 10.4.2006
		if ($this->piVars['docStatus']) $addWhere .=' AND pages.tx_docdb_doc_status='.intval($this->piVars['docStatus']);
		
		if ($this->piVars['descriptors']) {
			$mm_cat['table']='tx_docdb_descriptor';
			$mm_cat['mmtable']='pages_tx_docdb_doc_descriptor_mm';		
			$mm_cat['catUidList'] = $this->pi_getDescrList($this->piVars['combination']);
		}
		
		$queryParts = $this->pi_list_query($table,$isCount,$addWhere,$mm_cat,$groupBy,$orderBy,'',TRUE);
		return $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
	}
	
	/**
	 * [Put your description here]
	 */
	function listView($content,$conf)	{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$lConf = $this->conf['listView.'];	// Local settings for the listView function
		
		// initiate the tree descriptor object
		$tree = t3lib_div::makeInstance('tree_descriptor');	
		$tree->init('','title');
		$tree->table='tx_docdb_descriptor';
		$tree->title='Descriptors';
		$tree->treeName='descriptor';
		$tree->defaultList='uid,pid,dscr_pid,dscr_related';
		$tree->parentField ='dscr_pid';
		$tree->addField($tree->parentField);
		$tree->expandFirst=1;
		$tree->expandAll=0;
		$tree->backPath = 'typo3/';
		$tree->synonymField = 'dscr_related';
		$tree->addField($tree->synonymField);
		$tree->stored=unserialize($this->piVars['tree']);
		
		$this->internal['tree'] = $tree;
	
		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		if (!isset($this->piVars['mode']))	$this->piVars['mode']=1;

		// Initializing the query parameters:
		list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
		$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,20);		// Number of results to show in a listing.
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,3);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
		$this->internal['searchFieldList']='type';
		$this->internal['orderByList']='uid,type,title,owner,type,lastUpdated,tx_docdb_doc_key';
		// orderby field
		if ($this->piVars['sort']) {
			$this->internal['orderBy']=substr($this->piVars['sort'], 0, strlen($this->piVars['sort'])-2);
			(intval(substr($this->piVars['sort'], -1, 1))==1)?$this->internal['descFlag']=1:$this->internal['descFlag']=0;
		} else {
			// **OS 11.1.2007 Sort order defaults
                  $tmpSort = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'sorting');
                  $tmpSortOrder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'sorting_order'); 
                  $this->internal['orderBy'] = $tmpSort?$tmpSort:'tx_docdb_doc_key';
                  $this->internal['descFlag'] = ($tmpSortOrder=='DESC')?1:0;
		}
		$this->internal['currentTable'] = 'pages';
		
			// Get number of records:
		$res = $this->pi_descr_query(true);
		list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

			// Make listing query, pass query to SQL database:
		$res = $this->pi_descr_query(false);
//debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		
			// Put the whole list together:
		$fullTable='';	// Clear var;
	#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
			
			// Adds the search box:
		if (!$this->mode) {
			$fullTable.=$this->pi_list_searchBox();
		}

		if ($this->piVars['CMD']=='list') { 
				// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);
				// Adds the result browser:
			$fullTable.=$this->pi_list_browseresults();
		}
			// Returns the content from the plugin.
		return $fullTable;
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel='<TD>'.$editPanel.'</TD>';
		
		$datarowcontent = $this->cObj->getSubpart($this->template, '###ROW###');
	      
	      $substArray=array();
	      $substArray['###TITLE###'] = $this->getFieldContent('title');
	      $substArray['###LASTUPDATED###'] = $this->getFieldContent('lastUpdated');
	      $substArray['###OWNER###'] = $this->getFieldContent('owner');
	      $substArray['###DOC_KEY###'] = $this->getFieldContent('tx_docdb_doc_key');
	      $substArray['###TYPE###'] = $this->getFieldContent('type');
	      
	      $row = '<tr'.($c%2 ? $this->pi_classParam('listrow-odd') : '').'>';
            $row .= $this->cObj->substituteMarkerArrayCached($datarowcontent,$substArray).'</tr>';
	      
		return $row;
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_list_header()	{
	      $datarowcontent = $this->cObj->getSubpart($this->template, '###ROW###');
	      
	      $substArray=array();
	      $substArray['###TITLE###'] = $this->getFieldHeader_sortLink('title');
	      $substArray['###LASTUPDATED###'] = $this->getFieldHeader_sortLink('lastUpdated');
	      $substArray['###OWNER###'] = $this->getFieldHeader_sortLink('owner');
	      $substArray['###DOC_KEY###'] = $this->getFieldHeader_sortLink('tx_docdb_doc_key');
	      $substArray['###TYPE###'] = $this->getFieldHeader_sortLink('type');
	      
	      $row = '<tr'.$this->pi_classParam('listrow-header').'>';
            $row .= $this->cObj->substituteMarkerArrayCached($datarowcontent,$substArray).'</tr>';
	      
		return $row;
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case 'title':
				//return '<a href=index.php?id='.$this->internal['currentRow']['uid'].'>'.$this->internal['currentRow'][$fN].'</a>';
				// **OS 10.4.2006
                        return '<a href="'.$this->pi_getPageLink($this->internal['currentRow']['uid']).'">'.$this->internal['currentRow'][$fN].'</a>';
			break;
					
			case 'lastUpdated':
				if ($this->internal['currentRow'][$fN]) {
					return date('d/m/Y',$this->internal['currentRow'][$fN]);
				} else {
					 return ''; 
				}
			break;
			
			default:
				return $this->internal['currentRow'][$fN];
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			
			default:
				return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
		
		if (!strcmp($fN,$this->internal['orderBy'])) {
			($this->internal['descFlag']==0)?$desc=1:$desc=0;
		} else {
			$desc=0;
		}
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array('sort'=>$fN.':'.$desc));
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
	function pi_getCategories($table,$whereClause='',$groupBy='',$orderBy='',$limit='')	{
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
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1.php']);
}

?>