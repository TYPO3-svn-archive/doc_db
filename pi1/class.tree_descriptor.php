<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Contains base class for creating a browsable array/page/folder tree in HTML
 *
 * $Id: class.t3lib_treeview.php,v 1.20 2005/04/01 14:37:07 typo3 Exp $
 * Revised for TYPO3 3.6 November/2003 by Kasper Skaarhoj
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @coauthor	René Fritz <r.fritz@colorcube.de>
 * 
 * modified by Nicolas Wezel
 * for use in the frontend
 * 
 */
 
 require_once (PATH_t3lib.'class.t3lib_treeview.php');
  
 
/**
 * Extended class for displaying a browsable tree in HTML in TYPO3 frontend
 *
 * @author	Nicolas Wezel
 *
 * @subpackage t3lib
 * @see t3lib_treeView
 */
class tree_descriptor extends t3lib_treeView {
	
	/**
	 *
	 */
	function getListNodes($node,$list=array())	{

		if (is_array($node)){
			$uid = $node['uid'];
					
					// break recursivity for synonyms
			if (in_array($uid,$list)) {
				return $list;
			}
			
			$res = $this->getDataInit($uid);
			
					// treat childs
			while ($child = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { 
					$list = $this->getListNodes($child,$list);	
			}
				
			$this->getDataFree($res);
			$list[] = $uid;					
					// treat synonyms
			if ($this->synonymField && $node[$this->synonymField]) {
				$nodeSyn = $this->getRecord($node[$this->synonymField]);
				$list = $this->getListNodes($nodeSyn,$list);
			}
		}
		
		return $list;
	}
	
	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 * @access private
	 */
	function wrapTitle($title,$row,$bank=0)	{
	      $clickable = true;
	      // prevent first level from being clickable **OS 1.5.06
		if (($row['uid']< 100) or (strlen($row['title'])< 2)) $clickable = FALSE;
		
		// ** OS 28.4.2006 Display information about synonyms
            $related = '';
		if ($row['dscr_related'] > 0) {
                	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					'tx_docdb_descriptor',
					' tx_docdb_descriptor.uid='.$row['dscr_related'],	// whereClauseMightContainGroupOrderBy
					'',
					'',
					''
				);
		      $row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);   
		      $related = ($row2['title']=='')?'':' (See: '.$row2['title'].')';
		      $GLOBALS['TYPO3_DB']->sql_free_result($res);  
            } 
		if ($clickable) {
		      if ($related <> '') { // If it is a synonym, use the original instead
		            $aOnClick='setFormValueFromBrowseWin(\'tx_docdb_pi1[descriptors]\','.$row2['uid'].',\''.$row2['title'].'\')';
		            $link = '<a href="'.$_SERVER['REQUEST_URI'].'#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.' '.$related.'</a>';
		      } else {
		            $aOnClick='setFormValueFromBrowseWin(\'tx_docdb_pi1[descriptors]\','.$row['uid'].',\''.$title.'\')';
                        $link = '<a href="'.$_SERVER['REQUEST_URI'].'#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
                  }
            } else {
                  $link = $title.' '.$related;
            }
            return $link;
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
				$anchor = $_SERVER['REQUEST_URI'].'#'.$bMark;
				$name=' name="'.$bMark.'"';
		}
		//$aUrl = $this->thisScript.'?PM='.$cmd.$anchor;
		$onClick = ' onclick="submitForm(\''.$cmd.'\',\''.$anchor.'\')" ';
		return '<a href="'.$anchor.'"'.$name.$onClick.'>'.$icon.'</a>';
	}
	
	/**
	 *
	 */
	function getTreeStatus(){
		return htmlspecialchars(serialize($this->stored));
	}
	
	/**
	 * Get stored tree structure AND updating it if needed according to incoming PM GET var.
	 *
	 * @return	void
	 * @access private
	 */
	function initializePositionSaving()	{
			// Get stored tree structure:
		//$this->stored=unserialize($this->BE_USER->uc['browseTrees'][$this->treeName]);
		
		
//debug($this->stored);

			// PM action
			// (If an plus/minus icon has been clicked, the PM GET var is sent and we must update the stored positions in the tree):
		$PM = explode('_',t3lib_div::_GP('PM'));	// 0: mount key, 1: set/clear boolean, 2: item ID (cannot contain "_"), 3: treeName
		if (count($PM)==4 && $PM[3]==$this->treeName)	{
			if (isset($this->MOUNTS[$PM[0]]))	{
				if ($PM[1])	{	// set
					$this->stored[$PM[0]][$PM[2]]=1;
					$this->savePosition();
				} else {	// clear
					unset($this->stored[$PM[0]][$PM[2]]);
					$this->savePosition();
				}
			}
		}
	}
	
	
	
	/**
	 * Saves the content of ->stored (keeps track of expanded positions in the tree)
	 * $this->treeName will be used as key for BE_USER->uc[] to store it in
	 *
	 * @return	void
	 * @access private
	 */
	function savePosition()	{
		//$this->BE_USER->uc['browseTrees'][$this->treeName] = serialize($this->stored);
		//$this->BE_USER->writeUC();
		//debug($this->stored);
	}		

}
