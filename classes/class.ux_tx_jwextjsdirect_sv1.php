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
 * Class/Function override extjs direct service to modify the tech. used to load js and css files
 * And also change static conf key to statvar, because static is a reserved keyword in javascript
 *
 * $Id: class.ux_tx_jwextjsdirect_sv1.php 199 2010-01-18 17:23:39Z lcherpit $
 * $Author: lcherpit $
 * $Date: 2010-01-18 18:23:39 +0100 (lun 18 jan 2010) $
 *
 * @author  laurent cherpit <laurent@eosgarden.com>
 * @version     1.0
 * @package TYPO3
 * @subpackage  doc_db
 */



class ux_tx_jwextjsdirect_sv1 extends tx_jwextjsdirect_sv1
{


    /**
     * performs the service processing
     *
     * @param	string		Content which should be processed.
     * @param	string		Content type
     * @param	array		Configuration array
     * @return	boolean
     */
	public function process($conf, $typo3conf) {

        require_once(t3lib_extMgm::extPath('doc_db') . 'classes/class.tx_docdb_div.php');
		require_once(PATH_t3lib . 'class.t3lib_tsparser.php');

		$parser = t3lib_div::makeInstance('t3lib_TSparser');
		$ts     = @file_get_contents(t3lib_extMgm::extPath('jw_extjsdirect') . '/configuration/typoscript/setup.txt');
		$ts = $parser->parse($ts);

		$_conf = $parser->setup['plugin.']['tx_jwextjsdirect_sv1.']['extJS.'];

        if(is_array($_conf)) {
			$conf = t3lib_div::array_merge_recursive_overrule($_conf, $conf);
        }

		if(is_array($typo3conf)) {
			$conf = t3lib_div::array_merge_recursive_overrule($conf,$typo3conf);
        }

        tx_docdb_div::subExtPrefixPath($conf['path']);
        tx_docdb_div::subExtPrefixPath($conf['resourcesPath']);
        
		$this->setHeaderIncludes($conf);
		return false;
	}


	/**
	 * get conf from pi1 and build the tags of the page header
     * if [production] is true, css and js files are concat version processed in pi1
     * if [asyncLoading] is true, use defer async tag technique to defer loading without blocking the page
	 *
	 * @param	array		$conf
	 * @return	void
	 */
	protected function setHeaderIncludes($conf) {
        
        $deferTag = '';
        $minified = '';
        
        if($conf['asyncLoading']) {

            //$deferTag = ' defer="defer" async="async"';
            $deferTag = '';
        }

        if($conf['production']) {

            $minified = '-min';
        }
        
        $includesJsLL = '';

        if(! $conf['doNotLoadExtAllCSS']) {

            $includes =  "\t" . '<style type="text/css">@import url("' . $conf['resourcesPath'] . 'css/ext-all' . $minified . '.css");</style>' . "\n";
        }

        if(is_array($conf['themes.'])) {

			foreach($conf['themes.']  as $idx =>  $theme) {
                
                $themeFile = $conf['resourcesPath'] . 'css/xtheme-' . $theme . $minified .'.css';
                if(! tx_docdb_div::checkFileExist($themeFile)) {

                    $themeFile = $conf['resourcesPath'] . 'css/xtheme-' . $theme .'.css';
                }
                
				$conf['themes.'][$idx] = "\t" .
                '<style type="text/css">@import url("' . $themeFile .'");</style>';
			}
			$includes .= implode("\n", $conf['themes.']) . "\n";

		} else {
            
			$includes .= "\t" . '<style type="text/css">@import url("' . $conf['resourcesPath'] . 'css/xtheme-wcc.css");</style>' . "\n";
        }

		if(is_array($conf['css.'])) {

			foreach($conf['css.'] as $idx => $scr) {

				$conf['css.'][$idx] = "\t" . '<style type="text/css">@import url("' . $scr . '");</style>';
			}
            
			$includes .=  implode("\n", $conf['css.']) . "\n";

		} else {
            
			$includes .=  '';
		}

        if(! $conf['doNotLoadExtAllJS']) {
            
            if($conf['production']) {

                // ext base and ext-all in on file
                $includes .= "\t" . '<script type="text/javascript"' . $deferTag . ' src="' . $conf['path'] . 'ext-base-and-all.js"></script>'."\n";

            } else {

                // TODO adapter .. but here not use other than default ext-base
                $includes .= "\t" . '<script type="text/javascript"' . $deferTag . ' src="' . $conf['path'] .
                'adapter/' . ($conf['adapter'] ? $conf['adapter'] : 'ext/ext-base.js').'"></script>' . "\n";

                $includes .= "\t" . '<script type="text/javascript"' . $deferTag . ' src="' . $conf['path'] . 'ext-all.js"></script>'."\n";
                
            }
        } // eo $conf['doNotLoadExtAllJS']
        
        // put locale js in $includesJsLL to put after other js to override label
		if(file_exists(PATH_site . $conf['path'] . 'src/locale/ext-lang-' . $GLOBALS['TSFE']->config['config']['language'] . $minified . '.js')) {
//' . $deferTag . '
			$includesJsLL .= "\t" .'<script type="text/javascript" src="' . $conf['path'] .
            'src/locale/ext-lang-' . $GLOBALS['TSFE']->config['config']['language'] . $minified . '.js"></script>' . "\n";

        } else {

			$includesJsLL .= "\t" . '<script type="text/javascript" src="' . $conf['path'] . 'src/locale/ext-lang-en.js"></script>' . "\n";
        }
        
		$blankImg = "\t" . 'Ext.BLANK_IMAGE_URL = "' . $conf['resourcesPath'] . 'images/default/s.gif";' . "\n";

		if(is_array($conf['js.'])) {

			foreach($conf['js.'] as $idx => $scr) {

				$conf['js.'][$idx] = "\t" . '<script type="text/javascript"' . $deferTag . ' src="' . $scr . '"></script>';
			}

			$conf['js.'] = implode("\n", $conf['js.']);

		} else {
            
			$conf['js.'] = '';
		}

        

		$scriptLL='';
		if(is_array($conf['statvar.'])) {

			if(isset($conf['statvar.']['default.'])) {
				
                $default = $conf['statvar.']['default.'];
				unset($conf['statvar.']['default.']);

			} else {

				$default = '';
            }

			foreach($conf['statvar.'] as $idx => $scr) {

				if(is_array($default)) {

					$scr = array_merge($default, $scr);
                }

				$conf['statvar.'][$idx] = $this->getStaticJavaScript($scr, '', $blankImg);
			}

			$scriptLL .= '<script type="text/javascript">' .
                        "\n\t" . implode("\n\t", $conf['statvar.']) .
                        '</script>' . "\n";
		}


		$conf['url'] = $conf['url'] ? $conf['url'] : 'index.php?id=' . $GLOBALS['TSFE']->id . '&amp;L=' .
        ($GLOBALS['TSFE']->config['config']['sys_language_uid'] ? $GLOBALS['TSFE']->config['config']['sys_language_uid'] : 0) .
        '&amp;ext=' . ($conf['extensionId'] ? $conf['extensionId'] : '0') . '&amp;eID=' . $conf['eID'] . '&amp;api=1';


        $useradd_JsInc =    $includes . "\n" .
                            $conf['js.'] . "\n" .
                            "\t" .'<script type="text/javascript"' . $deferTag . ' src="' . $conf['url'] . '"></script>' . "\n" .
                //            $conf['js.'] . "\n" .
                            "\t" . $scriptLL . "\n" .
                            $includesJsLL . "\n";

        // finally put in footer
        if (t3lib_div::int_from_ver(TYPO3_version) >= 4003000) {

            $GLOBALS['TSFE']->additionalFooterData[($extKey ? $extKey : $this->extKey) . 'useradd_inc'] = $useradd_JsInc;
        // or in header
        } else {

            $GLOBALS['TSFE']->additionalHeaderData[($extKey ? $extKey : $this->extKey) . 'useradd_inc'] = $useradd_JsInc;
        }

        unset($useradd_JsInc);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$params: ...
	 * @param	[type]		$filename: ...
	 * @param	[type]		$applyTo: ...
	 * @param	[type]		$jsvar: ...
	 * @param	[type]		$selectionPrefix: ...
	 * @param	[type]		$stripFromSelectionName: ...
	 * @return	[type]		...
	 */

	protected function getStaticJavaScript($params, $stripFromSelectionName = '', $blankImg = '') {

        $filename = $params['lang.']['LLfile'];
        $selectionPrefix = $params['lang.']['prefix'] ? $params['lang.']['prefix'] : '';
        $type = $params['type'];
        $assign = $params['assign'];
        //$filename = t3lib_div::getFileAbsFileName($filename);
        if(!$this->mLang[$filename]) {

            $this->lang = $lang = $GLOBALS['TSFE']->config['config']['language'];
            $this->language_uid = $GLOBALS['TSFE']->config['config']['sys_language_uid'];
            $this->mLang[$filename] = t3lib_div::readLLfile($filename, $lang);

            if(!array_key_exists($lang, $this->mLang[$filename])) {

                $this->mLang[$filename] = $this->mLang['default'];

            } else {

                $this->mLang[$filename] = t3lib_div::array_merge_recursive_overrule($this->mLang[$filename]['default'], $this->mLang[$filename][$lang]);
            }
        }

        if($selectionPrefix) {

            $labelPattern = '#^' . preg_quote($selectionPrefix, '#') . '(' . preg_quote($stripFromSelectionName, '#') . ')?#';
            $extraction = array();
            foreach($this->mLang[$filename] as $label => $value) {

                if(strpos($label, $selectionPrefix) === 0) {

                    $key = preg_replace($labelPattern, '', $label);
                    $extraction[$key] = $value;
                }
            }

        } else {

            $extraction = $this->mLang[$filename];
        }

        $extr = array();
        if($extraction) {

            foreach($extraction as $label => $value) {

                if(strpos($label, '.') !== FALSE) {

                    $labelParts = t3lib_div::trimExplode('.', $label);
                    $extr = t3lib_div::array_merge_recursive_overrule($extr, $this->buildLLArray($labelParts, $value));
                    unset($extraction[$label]);
                }
            }

            $extraction = t3lib_div::array_merge_recursive_overrule($extraction, $extr);
        }

        $script='';

        $res = array();
        if(count($extraction)) {

            $res['lang'] = $extraction;
        }

        if($params['statvar.']) {

            $res['statvar'] = $this->removeTypo3Points($params['statvar.']);
        }

        if($params['template.']) {

            $res['tmpl'] = $this->removeTypo3Points($params['template.']);
        }

        if(count($res)) {

            switch($type) {

                case 'VAR':
                    $script =  ($params['namespace'] ? 'Ext.ns("' . $params['namespace'] . '");' . $params['namespace'] . '.' : 'var ') . $assign . ' =' . json_encode($res) . ';';
                break;

                case 'APPLY_TO':
                    $script = 'if(' . $assign . '){Ext.apply(' . $assign . '.prototype, ' . json_encode($res) . ');}';
                break;

                case 'ASSIGN':
                    $script =  $params['namespace'] ? 'Ext.ns("' . $params['namespace'] . '");' : '';

                    if($res['lang']) {

                        $script .= $assign . '.lang =' . json_encode($res['lang']) . ';';
                    }

                    if($res['statvar']) {

                        $script .= $assign . '.statvar =' . json_encode($res['statvar']) . ';';
                    }
                break;
            }
        }

        $ExtOnReady  = 'Ext.onReady(function() {' . "\n\t";
        $ExtOnReady .= $blankImg;
        $ExtOnReady .= $script . "\n\t";
        $ExtOnReady .= '(function() {DocDb.initMain();}.defer(50))' . "\n\t";
        $ExtOnReady .= '});';

        unset($script);
        
        return 	$ExtOnReady;
	}
}


// avoid notice
if(defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.ux_tx_jwextjsdirect_sv1.php'])) {

// XCLASS inclusion, please do not modify the 3 lines below, otherwise the extmanager will not be happy
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.ux_tx_jwextjsdirect_sv1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/doc_db/classes/class.ux_tx_jwextjsdirect_sv1.php']);
}

}