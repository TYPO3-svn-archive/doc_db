<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Laurent Cherpit <laurent.cherpit@gmail.com>
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
 * Class to handle cache for XML export
 * Code example from pmkshadowbox tx Stefan
 *
 * $Id: class.tx_docdb_xmlcache.php 209 2010-05-04 10:49:23Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-05-04 12:49:23 +0200 (mar 04 mai 2010) $
 *
 * @author	Laurent Cherpit <laurent.cherpit@gmail.com>
 * @package	TYPO3
 * @subpackage doc_db
 */
class tx_docdb_xmlcache {
    
	/**
	 * Path to the Cache Directory
	 *  
	 * @var string
	 */
	protected $_cacheDirectory = '';


	/**
	 * Constructor
	 * 
	 * Note: The cache directory is created if it does not already exists!
	 * 
	 * @param string $cacheDirectory 
	 * @return void
	 */
	public function __construct($cacheDirectory = 'typo3temp/docdb/') {

        $this->_cacheDirectory = $cacheDirectory;
		if (!is_dir(PATH_site . $this->_cacheDirectory)) {
			if (!t3lib_div::mkdir(PATH_site . $this->_cacheDirectory)) {
                
				$message = 'Cache directory "' . PATH_site . $this->_cacheDirectory .
					'" couldn\'t be created!';
				t3lib_div::sysLog($message, 'doc_db', t3lib_div::SYSLOG_SEVERITY_ERROR);
				throw new Exception($message);
			}
		}
	}

	/**
	 * Returns the cache directory
	 *
	 * @return string cache directory
	 */
	public function getCacheDirectory() {

        return $this->_cacheDirectory;
	}

	/**
	 * Removes all files inside the cache directory
	 *
	 * @throws Exception if a file or directory couldn't be deleted
	 * @return void
	 */
	public function clear() {

		$cacheDirectoryIterator = new DirectoryIterator(PATH_site . $this->_cacheDirectory);

		foreach($cacheDirectoryIterator as $fileInfo) {

			if($fileInfo->isDot() || !$fileInfo->isFile()) {
				continue;
			}
            
            if (unlink($fileInfo->getPathname()) === FALSE) {
                $message = 'cache->clear(): File "' .
                    $fileInfo->getPathname() . '" couldn\'t be removed!';
                t3lib_div::sysLog($message, 'doc_db', t3lib_div::SYSLOG_SEVERITY_ERROR);
                throw new Exception($message);
            }
		}
	}

	/**
	 * Writes the cache file "docdb md5.xml" into the given temp cache directory
	 *
	 * @param string $hashFileName
	 * @param string $content
	 * @throws Exception if the file could not be written
	 * @return void
	 */
	public function writeCacheFile($hashFileName, $content) {

        $file = PATH_site . $this->_cacheDirectory . 'docdb' . $hashFileName .'.xml';
		if(t3lib_div::writeFile($file, $content) === FALSE) {
            
			$message = 'cache->writeCacheFile: Could not write the xml file: ' . $file;
			t3lib_div::sysLog($message, 'doc_db', t3lib_div::SYSLOG_SEVERITY_ERROR);
			throw new Exception($message);
		}
	}

    /**
     * Check if cache file exists and is readable
     *
     * @param <string>      $hashFileName
     * @return <boolean>
     */
    public function isFileInCache($hashFileName) {
        
        $path = PATH_site . $this->_cacheDirectory . 'docdb' . $hashFileName .'.xml';
        return (file_exists($path) && is_readable($path));
    }


    public function getCacheFileContents($hashFileName) {

        return file_get_contents(PATH_site . $this->_cacheDirectory . 'docdb' . $hashFileName .'.xml');
    }

    /**
	 * Clear cache post processing hook
	 *
	 * @param array $parameters
	 * @param object $parent
	 * @return void
	 */
	public function clearCachePostProc($parameters, $parent) {

        if ($parameters['cacheCmd'] === 'all') {
			$this->clear();
		}
	}
}

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_xmlcache.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_xmlcache.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.tx_docdb_xmlcache.php']);
}

}

?>