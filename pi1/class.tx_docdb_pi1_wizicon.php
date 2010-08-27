<?php

/**
 * Description of classtx_docdb_pi1_wizicon
 *
 * @author lcherpit
 */
class tx_docdb_pi1_wizicon {

    /**
     * Processing the wizard items array
     *
     * @param	array		$wizardItems: The wizard items
     * @return	Modified array with wizard items
     */
    public function proc($wizardItems) {

        $LL = $this->includeLocalLang();

        $wizardItems['plugins_tx_docdb_pi1'] = array(

            'icon'  => t3lib_extMgm::extRelPath('doc_db') . 'pi1/ce_wiz.gif',
            'title' => $GLOBALS['LANG']->getLLL('pi1_title', $LL),
            'description' => $GLOBALS['LANG']->getLLL('pi1_plus_wiz_description', $LL),
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=doc_db_pi1'
        );

        return $wizardItems;
    }

    /**
     * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
     *
     * @return	The array with language labels
     */
    protected function includeLocalLang() {

        $llFile     = t3lib_extMgm::extPath('doc_db') . 'pi1/locallang_wiz.xml';
        return t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
    }
}

// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1_wizicon.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1_wizicon.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/pi1/class.tx_docdb_pi1_wizicon.php']);
}

}

?>