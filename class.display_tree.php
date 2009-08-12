<?php
/*
 * Created on 23 nov. 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * Used in the Backend tree
 */
 
require_once (PATH_t3lib.'class.t3lib_treeview.php');

class display_tree extends t3lib_treeView	{
	var $fieldArray = Array('uid','dscr_pid','pid','dscr_related','title');
	var $defaultList = 'uid,pid,tstamp,crdate,title,cruser_id,dscr_pid';
	var $setRecs = 0;
	var $sourceRecord = array(); // uid of the record source for the category table
	
	/**
	 * Init function
	 * REMEMBER to feed a $clause which will filter out non-readable pages!
	 *
	 * @param	string		Part of where query which will filter out non-readable pages.
	 * @return	void
	 */
	function init($clause='',$order)	{
		parent::init(' '.$clause,$order);

		/*if (t3lib_extMgm::isLoaded('cms'))	{
			$this->fieldArray=array_merge($this->fieldArray,array('hidden'));
		}*/
		$this->table='tx_docdb_descriptor';
		$this->treeName='descriptor';
		$this->parentField='dscr_pid';
		$this->expandFirst=0;
		$this->expandAll=0;			
	}
	/**
	 * ????
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function isLinkActive($v, $sourceDepth)	{	
		if ($v['row']['uid']==0) return FALSE;
		// prevent first level from being clickable **OS 1.5.06
		if ($v['row']['uid']< 100) return FALSE;
		// prevent titles with one letter from being clickable (Alphabetic letters) **OS 1.5.06
		if (strlen($v['row']['title'])< 2) return FALSE;
		if (isset($this->sourceRecord['field'])) {
			if ($v['row']['uid']==$this->sourceRecord['uid']) return FALSE;
			if (!strcmp($this->sourceRecord['field'],$this->parentField) && $v['invertedDepth'] < $sourceDepth) return FALSE;
		}
		return TRUE;
	}
		
	/**
	 * Compiles the HTML code for displaying the structure found inside the ->tree array
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function printTree($treeArr='')	{
		$titleLen=intval($this->BE_USER->uc['titleLen']);
		if (!is_array($treeArr))	$treeArr=$this->tree;
		
		// getting depth from source uid
		while(list($key,$tempArray) = each($treeArr)) {			
			if ($tempArray['row']['uid'] == $this->sourceRecord['uid']) {
				$sourceDepth = $tempArray['invertedDepth'];
				break;
			} 
		}
	
		$out='';

			// put a table around it with IDs to access the rows from JS
			// not a problem if you don't need it
			// In XHTML there is no "name" attribute of <td> elements - but Mozilla will not be able to highlight rows if the name attribute is NOT there.
		$out .= '

			<!--
			  TYPO3 tree structure.
			-->
			<table cellpadding="0" cellspacing="0" border="0" id="typo3-tree">';

		foreach($treeArr as $k => $v)	{
			$idAttr = htmlspecialchars($this->domIdPrefix.$this->getId($v['row']).'_'.$v['bank']);
			$out.='
				<tr>
					<td id="'.$idAttr.'">'.
						$v['HTML'].
						$this->wrapTitle($this->getTitleStr($v['row'],$titleLen),$v['row'],$v['bank'],$this->isLinkactive($v,$sourceDepth)). // active added by nwe
					'</td>
				</tr>
			';
		}
		$out .= '
			</table>';
		return $out;
	}
	
	
	/**
	 * wraps the record titles 
	 *
	 * @param	string		$title: the title
	 * @param	array		$v: an array with uid and title of the current item.
	 * @return	string		the wrapped title
	 */
	function wrapTitle($title,$v,$bank='',$isLinkactive=0)	{
		global $TCA;
		
		// ** OS 28.4.2006 Add related info
            $related = '';
		if ($v['dscr_related'] > 0) {
                	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_docdb_descriptor',' tx_docdb_descriptor.uid='.$v['dscr_related'],'','', '');
		      $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);   
		      $related = ($row['title']=='')?'':' (See: '.$row['title'].')';
		      $GLOBALS['TYPO3_DB']->sql_free_result($res);  
            }
		
		if (!$title) {
			$code = '<i>['.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.no_title',1).']</i>';
		} else {
		      // **OS 5.2006 Display related
			$code = htmlspecialchars(t3lib_div::fixed_lgd_cs($title,$this->fixedL).' '.$related);
		}
		
		$ficon = t3lib_iconWorks::getIcon($this->table,$v); 
		$ATag_alt = '';
		$ATag_e = '';
		$img = '';
		$aOnClick = '';
		
		// activate the link only if the record is not excluded from the list (excludeUids)
		if ($isLinkactive) {
		      if ($v['dscr_related'] > 0) {
		          // If the keyword is a synonym, use the original instead
		          $aOnClick = "return insertElement('".$this->table."', '".$v['dscr_related']."', 'db', unescape('".rawurlencode($row['title'])."'), '', '', '".$ficon."');";
		      } else {
			    $aOnClick = "return insertElement('".$this->table."', '".$v['uid']."', 'db', unescape('".rawurlencode($title)."'), '', '', '".$ficon."');";
			}
                  $ATag = '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">';
			$ATag_alt = substr($ATag,0,-4).',\'\',1);">';
			$ATag_e = '</a>';
			$img = 	$ATag.
					'<img'.t3lib_iconWorks::skinImg('','gfx/plusbullet2.gif','width="18" height="16"').' title="'.$GLOBALS['LANG']->getLL('addToList',1).'" alt="" />'.
					$ATag_e;
                  }	
		
		return 
				$ATag_alt.
				$code.
				$ATag_e.
				$img;	
	
	}

	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		//$aUrl = $this->thisScript.'?PM='.$cmd.$anchor;
		// **OS 15.05.2006
            //$aUrl = $this->script.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&expandFolder='.rawurlencode($v['path']).'&bparams='.$GLOBALS['SOBE']->bparams.'&PM='.$cmd.$anchor;
		//return '<a href="'.htmlspecialchars($aUrl).'"'.$name.'>'.$icon.'</a>';
		
            $aOnClick = "return jumpToUrl('".$this->thisScript.'?PM='.$cmd."','".$anchor."');";
		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}
	
	
	/**
	 * return the uid of the source record in the main window
	 *
	 * @param	string		table name
	 *
	 * @return	int			uid of the source record in the main window.
	 */
	function setSourceRecord($table,$bparams) {	
		$pArr = explode('|',$bparams);		
				
		// get the uid and the field name of the source record
		$pattern = '/data\['.$table.'\]\[(\d*)\]\[([A-Za-z_][A-Za-z_0-9]*)\]/';
		preg_match($pattern,$pArr[0],$matches);
		$this->sourceRecord['uid'] = $matches[1];
		$this->sourceRecord['field'] = $matches[2];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_pagetree.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_pagetree.php']);
}


?>