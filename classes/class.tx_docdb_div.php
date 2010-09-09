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
 * Class/Function with div util static methods 
 * 
 * $Id: class.tx_docdb_div.php 199 2010-01-18 17:23:39Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-01-18 18:23:39 +0100 (lun 18 jan 2010) $
 * 
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */

class tx_docdb_div
{

    protected static $_siteUrl    = NULL;
    protected static $_siteRelPath = NULL;

    /**
	 * Return related Descriptor in CSS tooltip (not usefull)
	 * 
	 * @param int $id: related descriptor uid list
	 * @return String: html tooltip
	 */
	public static function getCssTooltip($rows, $backPath='') {
		
		$_backPath = isset($backPath) ? t3lib_div::resolveBackPath($backPath) : '';
		// store
		$_tt = array();
		
		$_tt[] = '<div style="display:inline;position:relative;">';
		$_tt[] = '<div style="position:absolute;top:0;left:0;">';
		$_tt[] = '<a href="#" class="typo3-csh-link">';
		$_tt[] = '<img width="16" height="16" border="0" alt="" style="cursor:context-menu;" src="' . $_backPath . 'gfx/icon_note.gif"/>';
		$_tt[] = '<span class="typo3-csh-inline">';
		$_tt[] = '<span class="header">See related</span><br/>';
		$_tt[] = '<span class="paragraph">';
		
		foreach($rows as $k => $row) {
			
			$_hr = ($cpt++ > 0) ? '<hr style="border:none" />' : '';
			$_tt[] = $_hr . 'Id[' . $row["uid"] . '] :<br /><i>(' . $row['title'] . ')</i>';
			
		}
		
		$_tt[] = '</span></span>';
		$_tt[] = '</a></div></div>';
		
		return implode("\n", $_tt);
	}
	
	
	
	/**
	 * _sqlGetRows, from one $table, and return them in an array.
	 *
	 * @param	string		The name of the table to select from.
	 * @param	string		Fields list to select default *: all
	 * @param	string		Optional additional WHERE clauses put in the end of the query. DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	array		The array with the category records in.
	 */
	public static function sqlGetRows($table, $fields='*', $whereClause='', $groupBy='', $orderBy='', $limit='') {
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			$fields,
			$table,
			$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);
		
		return $rows;
	}
	
	/**
	* Counts the number of rows in a table.
	*
	* @param   string      $field: Name of the field to use in the COUNT() expression (e.g. '*')
	* @param   string      $table: Name of the table to count rows for
	* @param   string      $where: (optional) WHERE statement of the query
	* @return  mixed       Number of rows counter (integer) or FALSE if something went wrong (boolean)
	*/
	public static function exec_SELECTcountRows($field, $table, $where = '') {
		$count = FALSE;
		
		$resultSet = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(' . $field . ')', $table, $where);
		
		if ($resultSet !== FALSE) {
		
			list($count) = $GLOBALS['TYPO3_DB']->sql_fetch_row($resultSet);
			
			$GLOBALS['TYPO3_DB']->sql_free_result($resultSet);
		}
		
		return $count;
	}


   /**
	* Counts the number of rows in a table.
	*
	* @param   string      $field: Name of the field to use in the COUNT() expression (e.g. '*')
	* @param   string      $table: Name of the table to count rows for
	* @param   string      $where: (optional) WHERE statement of the query
	* @return  mixed       2 dimensional array with keys ['count'] and ['rows']
	*/
	public static function exec_SELECTcountAndGetRows($fieldUid, $table, $where = '') {
		
        $store = array();
        $i = 0;

		$resultSet = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fieldUid, $table, $where, $fieldUid);

		if ($resultSet !== FALSE) {

			while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($resultSet))) {
                $store[] = $row[0];
                $i++;
            }

			$GLOBALS['TYPO3_DB']->sql_free_result($resultSet);
		}

		return array('count' => $i, 'rows' => $store);
	}


    public static function subExtPrefixPath(&$path) {

        if(strcmp(substr($path, 0, 4), 'EXT:') === 0) {

			list($extKey, $script) = explode('/', substr($path, 4), 2);

			if($extKey && t3lib_extMgm::isLoaded($extKey)) {
				$extPath = t3lib_extMgm::siteRelPath($extKey);
				$path = $extPath . $script;
			}
		}
    }

    public static function checkFileExist($filePath) {

        $filePath = t3lib_div::getFileAbsFileName($filePath, FALSE);

        if(file_exists($filePath)) {
            return TRUE;
        }

        return FALSE;
    }


	/**
	 * Forces a given URL to be absolute.
	 *
	 * @param string $url The URL to be forced to be absolute
	 * @param array $configuration TypoScript configuration of typolink
	 * @return string The absolute URL
	 */
	public static function forceAbsoluteUrl($url, array $config) {
        
		if (!empty($url) && isset($config['forceAbsoluteUrl']) && $config['forceAbsoluteUrl']) {

            $urlParts = parse_url($url);
            $urlParts['delimiter'] = '://';

//                t3lib_div::devLog('$urlParts', 'doc_db', 0, array( $urlParts));
            /**
             *  @todo make that better. more flexible scheme (http-s)
             */
            // Set scheme and host if not yet part of the URL:
            if (empty($urlParts['host'])) {

                $urlParts1 = parse_url(self::siteUrl());
                $urlParts['scheme'] = $urlParts1['scheme'];
                $urlParts['host'] = $urlParts1['host'];
                $isUrlModified = TRUE;
            }

            // Override scheme:
            $forceAbsoluteUrl =& $config['forceAbsoluteUrl.']['scheme'];
            if (!empty($forceAbsoluteUrl) && $urlParts['scheme'] !== $forceAbsoluteUrl) {
                $urlParts['scheme'] = $forceAbsoluteUrl;
                $isUrlModified = TRUE;
            }

            // Recreate the absolute URL:
            if ($isUrlModified) {
                $url = $urlParts['scheme'] . $urlParts['delimiter'] . $urlParts['host'] . '/' . $urlParts['path'];
            }
        }

		return $url;
	}


    public static function sortListOfInt(&$list) {

        $listArray = explode(',', $list);
        // clean int list order
        sort($listArray, SORT_NUMERIC);
        $list = implode(',', $listArray);
    }

    /**
     * Get the extRelPath
     * 
     * @param <boolean> $abs
     * @return <string> Relative or absolute URL path to the root extension directory
     */
    public static function extSiteUrl($abs=FALSE) {

        if(self::$_siteRelPath === NULL) {

            self::$_siteRelPath = t3lib_extMgm::siteRelPath('doc_db');
        }
        
        if($abs) {

            return self::siteUrl() . self::$_siteRelPath;
        }

        return self::$_siteRelPath;
    }

    /**
     * Get the TYPO3_SITE_URL
     * 
     * @return <string>
     */
    public static function siteUrl() {

        if(self::$_siteUrl === NULL) {

            self::$_siteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
        }

        return self::$_siteUrl;
    }

    /**
     * Check if is TYPO3 version 4.3 or above
     */
    public static function isTypo3V43min() {

        return version_compare(TYPO3_branch, '4.3', '>=');
    }

    /**
     * Flush all buffer if any content
     */
    public static function flush() {

        while (@ob_end_flush());
        @ob_flush();
        @flush();
    }
}



// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_div.php']);
}

}

