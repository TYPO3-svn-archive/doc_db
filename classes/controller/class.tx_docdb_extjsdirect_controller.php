<?php

require_once(t3lib_extMgm::extPath('jw_extjsdirect') . 'classes/controller/class.tx_jwextjsdirect_controller.php');

class tx_docdb_extjsdirect_controller extends tx_jwextjsdirect_controller {

	/**
	 * Prefix pi1
	 * 
	 * @var $prefixId
	 */
	public $prefixId = 'tx_docdb_pi1';
	
	/**
	 * Extension key
	 * 
	 * @var $extKey
	 */
	public $extKey = 'doc_db';
	
	/**
	 * Path to this script relative to the extension dir.
	 * 
	 * @var $scriptRelPath
	 */
	public $scriptRelPath = 'classes/controller/class.tx_docdb_extjsdirect_controller.php';

	/**
	 * 
	 * @return 
	 */
	public function main() {
		
		$this->init();
		
		$extjsConf = array(
			'eID'    => 'docdb',
			'cached' => 1,
			'debug'  => 0,
			'debug.' => array(
					'routerFile' => t3lib_extMgm::siteRelPath($this->extKey) . 'zzz_debug.html',
			),
			'classes.' => array(
				'path' => t3lib_extMgm::extPath($this->extKey).'classes/model',
                'add'  => array(
                    'xmllink'           => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
					'model_owner'       => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
					'model_type'        => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
					'model_status'      => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
					'model_descriptor'  => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
					'model_document'    => array('filePrefix'=> 'class.','prefix' => 'tx_docdb_','postfix' => ''),
				)
			)
		);
		
		parent::main($extjsConf);
	}
}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_extjsdirect_controller.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_extjsdirect_controller.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/controller/class.tx_docdb_extjsdirect_controller.php']);
}

}

$output = t3lib_div::makeInstance('tx_docdb_extjsdirect_controller');
$output->main();

