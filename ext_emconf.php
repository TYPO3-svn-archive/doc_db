<?php

########################################################################
# Extension Manager/Repository config file for ext: "doc_db"
#
# Auto generated 03-01-2010 13:38
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Document DB',
	'description' => 'This extension allows Typo3 to act as a document database. It extends the logic of pages, adding various sorts of descriptors (type of doc, institutional owner, status), including a fully flexible tree of categories. A search plugin is also featured, that can be used in the frontend for user searches, put also in the backend, to create preset dynamic thematic lists of documents.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.2.9',
	'dependencies' => 'ta_xajaxwrapper,jw_extjsdirect',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Laurent Cherpit, Olivier Schopfer,Nicolas Wezel',
	'author_email' => 'laurent@eosgarden.com,ops@wcc-coe.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
			'ta_xajaxwrapper' => '0.4.0-0.0.0',
			'jw_extjsdirect' => '0.0.7-0.0.7',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:313:{s:9:"ChangeLog";s:4:"0fce";s:10:"README.txt";s:4:"5f72";s:20:"class.ext_update.php";s:4:"6c53";s:21:"ext_conf_template.txt";s:4:"e1be";s:12:"ext_icon.gif";s:4:"6da5";s:17:"ext_localconf.php";s:4:"5487";s:14:"ext_tables.php";s:4:"dfec";s:14:"ext_tables.sql";s:4:"1c67";s:13:"icon_page.gif";s:4:"742b";s:28:"icon_tx_docdb_descriptor.gif";s:4:"3a03";s:23:"icon_tx_docdb_owner.gif";s:4:"ffc3";s:24:"icon_tx_docdb_status.gif";s:4:"b5f6";s:22:"icon_tx_docdb_type.gif";s:4:"5e05";s:7:"tca.php";s:4:"0b66";s:33:"configuration/llang/locallang.xml";s:4:"8065";s:46:"configuration/llang/locallang_csh_tx_docdb.xml";s:4:"3b72";s:36:"configuration/llang/locallang_db.xml";s:4:"2160";s:36:"configuration/llang/locallang_ff.xml";s:4:"a400";s:38:"configuration/typoscript/constants.txt";s:4:"1cd2";s:34:"configuration/typoscript/setup.txt";s:4:"17fc";s:43:"configuration/flexform/docDBflexform_ds.xml";s:4:"4628";s:48:"wizard/class.tx_docdb_wizardSearchDescriptor.php";s:4:"f613";s:30:"classes/class.tx_docdb_div.php";s:4:"1a76";s:40:"classes/class.tx_docdb_itemsProcFunc.php";s:4:"593a";s:49:"classes/class.tx_docdb_tceFunc_selectTreeView.php";s:4:"e923";s:35:"classes/class.tx_docdb_tceforms.php";s:4:"1e65";s:34:"classes/class.tx_docdb_tcemain.php";s:4:"ab44";s:35:"classes/class.tx_docdb_treeview.php";s:4:"5141";s:38:"classes/class.tx_docdb_tsparserext.php";s:4:"fb2e";s:41:"classes/class.ux_tx_jwextjsdirect_sv1.php";s:4:"d089";s:60:"classes/controller/class.tx_docdb_extjsdirect_controller.php";s:4:"03e8";s:49:"classes/model/class.tx_docdb_model_descriptor.php";s:4:"96a2";s:47:"classes/model/class.tx_docdb_model_document.php";s:4:"1243";s:44:"classes/model/class.tx_docdb_model_owner.php";s:4:"964d";s:45:"classes/model/class.tx_docdb_model_status.php";s:4:"52f9";s:43:"classes/model/class.tx_docdb_model_type.php";s:4:"24ad";s:28:"res/compat/flashmessages.css";s:4:"fdbc";s:24:"res/compat/gfx/error.png";s:4:"e4dd";s:30:"res/compat/gfx/information.png";s:4:"3750";s:25:"res/compat/gfx/notice.png";s:4:"a882";s:21:"res/compat/gfx/ok.png";s:4:"8bfe";s:26:"res/compat/gfx/warning.png";s:4:"c847";s:35:"res/csh_img/search-a-descriptor.png";s:4:"b445";s:14:"doc/manual.sxw";s:4:"64cc";s:35:"resources/shared/icons/fam/SILK.txt";s:4:"67b1";s:45:"resources/shared/icons/fam/application_go.gif";s:4:"bdab";s:38:"resources/ExtJS/3.0.3/ext-all-debug.js";s:4:"d028";s:32:"resources/ExtJS/3.0.3/ext-all.js";s:4:"87c2";s:41:"resources/ExtJS/3.0.3/ext-base-and-all.js";s:4:"5b06";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-af.js";s:4:"e877";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-bg.js";s:4:"f87f";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-ca.js";s:4:"dbb4";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-cs.js";s:4:"40be";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-da.js";s:4:"a865";s:51:"resources/ExtJS/3.0.3/src/locale/ext-lang-de-min.js";s:4:"771f";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-de.js";s:4:"b6f6";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-el_GR.js";s:4:"d35b";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-en.js";s:4:"9caf";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-en_GB.js";s:4:"e45d";s:51:"resources/ExtJS/3.0.3/src/locale/ext-lang-es-min.js";s:4:"4eaf";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-es.js";s:4:"05ab";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-fa.js";s:4:"2b9b";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-fi.js";s:4:"585b";s:51:"resources/ExtJS/3.0.3/src/locale/ext-lang-fr-min.js";s:4:"37eb";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-fr.js";s:4:"8094";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-fr_CA.js";s:4:"d505";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-gr.js";s:4:"7839";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-he.js";s:4:"42e3";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-hr.js";s:4:"f8f7";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-hu.js";s:4:"dea7";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-id.js";s:4:"3c60";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-it.js";s:4:"e66c";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-ja.js";s:4:"7a46";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-ko.js";s:4:"7ba3";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-lt.js";s:4:"8bc9";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-lv.js";s:4:"fe29";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-mk.js";s:4:"6105";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-nl.js";s:4:"7a61";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-no_NB.js";s:4:"0e04";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-no_NN.js";s:4:"212c";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-pl.js";s:4:"25cd";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-pt.js";s:4:"64c9";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-pt_BR.js";s:4:"741e";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-pt_PT.js";s:4:"892e";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-ro.js";s:4:"7fc1";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-ru.js";s:4:"16fd";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-sk.js";s:4:"75ad";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-sl.js";s:4:"95cd";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-sr.js";s:4:"d91e";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-sr_RS.js";s:4:"7026";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-sv_SE.js";s:4:"5ce1";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-th.js";s:4:"1dc6";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-tr.js";s:4:"ad5c";s:48:"resources/ExtJS/3.0.3/src/locale/ext-lang-ukr.js";s:4:"e1b2";s:47:"resources/ExtJS/3.0.3/src/locale/ext-lang-vn.js";s:4:"b844";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-zh_CN.js";s:4:"f974";s:50:"resources/ExtJS/3.0.3/src/locale/ext-lang-zh_TW.js";s:4:"54f6";s:51:"resources/ExtJS/3.0.3/adapter/ext/ext-base-debug.js";s:4:"381f";s:45:"resources/ExtJS/3.0.3/adapter/ext/ext-base.js";s:4:"976c";s:51:"resources/ExtJS/3.0.3/resources/css/ext-all-min.css";s:4:"a325";s:47:"resources/ExtJS/3.0.3/resources/css/ext-all.css";s:4:"316c";s:54:"resources/ExtJS/3.0.3/resources/css/xtheme-wcc-min.css";s:4:"e322";s:50:"resources/ExtJS/3.0.3/resources/css/xtheme-wcc.css";s:4:"d424";s:52:"resources/ExtJS/3.0.3/resources/images/default/s.gif";s:4:"fc94";s:59:"resources/ExtJS/3.0.3/resources/images/default/shadow-c.png";s:4:"7ab6";s:60:"resources/ExtJS/3.0.3/resources/images/default/shadow-lr.png";s:4:"9862";s:57:"resources/ExtJS/3.0.3/resources/images/default/shadow.png";s:4:"860b";s:63:"resources/ExtJS/3.0.3/resources/images/default/grid/loading.gif";s:4:"00ef";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/ajax-loader.gif";s:4:"1790";s:51:"resources/ExtJS/3.0.3/resources/images/wcc/find.png";s:4:"9f1c";s:55:"resources/ExtJS/3.0.3/resources/images/wcc/shadow-c.png";s:4:"32f9";s:56:"resources/ExtJS/3.0.3/resources/images/wcc/shadow-lr.png";s:4:"f842";s:53:"resources/ExtJS/3.0.3/resources/images/wcc/shadow.png";s:4:"55eb";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/toolbar/bg.gif";s:4:"c4e7";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/toolbar/btn-arrow-light.gif";s:4:"c432";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/toolbar/more.gif";s:4:"f504";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/e-handle.gif";s:4:"562b";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/ne-handle.gif";s:4:"fa8a";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/nw-handle.gif";s:4:"86e4";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/s-handle.gif";s:4:"ad42";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/se-handle.gif";s:4:"c38a";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/sizer/sw-handle.gif";s:4:"3680";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/window/icon-error.png";s:4:"8ce5";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/window/icon-info.png";s:4:"aaff";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/window/icon-question.png";s:4:"6b51";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/window/icon-warning.png";s:4:"c25b";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/window/left-corners.gif";s:4:"573e";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/window/left-right.gif";s:4:"8cd8";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/window/left-right.png";s:4:"4ccf";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/window/right-corners.gif";s:4:"ec6b";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/window/top-bottom.gif";s:4:"8695";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/window/top-bottom.png";s:4:"15d1";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/button/arrow.gif";s:4:"e180";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/button/btn.gif";s:4:"0d51";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/button/group-cs.gif";s:4:"0698";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/button/group-lr.gif";s:4:"ac4c";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/button/group-tb.gif";s:4:"4653";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow-b-noline.gif";s:4:"f557";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow-b.gif";s:4:"ffc7";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow-bo.gif";s:4:"6248";s:68:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow-noline.gif";s:4:"7329";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow-o.gif";s:4:"dbb7";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/button/s-arrow.gif";s:4:"49b4";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/tree/arrows.gif";s:4:"b35f";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/tree/collapse-all.gif";s:4:"063c";s:60:"resources/ExtJS/3.0.3/resources/images/wcc/tree/drop-add.gif";s:4:"b3b0";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/tree/drop-between.gif";s:4:"25fe";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/tree/drop-over.gif";s:4:"f22e";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/tree/drop-under.gif";s:4:"b171";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-end-minus-nl.gif";s:4:"f831";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-end-minus.gif";s:4:"3129";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-end-plus-nl.gif";s:4:"e545";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-end-plus.gif";s:4:"3a33";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-end.gif";s:4:"74c1";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-line.gif";s:4:"334e";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-minus-nl.gif";s:4:"f831";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-minus.gif";s:4:"1818";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-plus-nl.gif";s:4:"e545";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow-plus.gif";s:4:"993a";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/tree/elbow.gif";s:4:"a765";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/tree/folder-open.gif";s:4:"efd3";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/tree/folder.gif";s:4:"26f6";s:56:"resources/ExtJS/3.0.3/resources/images/wcc/tree/leaf.gif";s:4:"bd09";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/tree/loading.gif";s:4:"c744";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/panel/corners-sprite.gif";s:4:"447a";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/panel/left-right.gif";s:4:"7d15";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/panel/light-hd.gif";s:4:"55a9";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/panel/tool-sprites.gif";s:4:"b94e";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/panel/top-bottom.gif";s:4:"8d1a";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/panel/white-top-bottom.gif";s:4:"a346";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/shared/glass-bg.gif";s:4:"6ee8";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/shared/hd-sprite.gif";s:4:"d258";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/shared/left-btn.gif";s:4:"fa66";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/shared/loading-balls.gif";s:4:"710c";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/shared/right-btn.gif";s:4:"38a1";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/shared/warning.gif";s:4:"6243";s:54:"resources/ExtJS/3.0.3/resources/images/wcc/qtip/bg.gif";s:4:"ebd0";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/qtip/close.png";s:4:"888a";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/qtip/tip-anchor-sprite.gif";s:4:"e5b2";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/qtip/tip-sprite.gif";s:4:"6ffc";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/editor/tb-sprite.gif";s:4:"1eca";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/scroll-left.gif";s:4:"1847";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/scroll-right.gif";s:4:"c48f";s:76:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-btm-inactive-left-bg.gif";s:4:"a81f";s:77:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-btm-inactive-right-bg.gif";s:4:"44a0";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-btm-left-bg.gif";s:4:"32cf";s:68:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-btm-right-bg.gif";s:4:"ec3c";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-close.gif";s:4:"d687";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tab-scroller-menu.gif";s:4:"24ab";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/tabs/tabs-sprite.gif";s:4:"1e50";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/menu/checked.gif";s:4:"f988";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/menu/group-checked.gif";s:4:"7402";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/menu/item-over.gif";s:4:"5c39";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/menu/menu-parent.gif";s:4:"5347";s:56:"resources/ExtJS/3.0.3/resources/images/wcc/menu/menu.gif";s:4:"1055";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/menu/unchecked.gif";s:4:"420f";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/progress/progress-bg.gif";s:4:"ee82";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/layout/mini-bottom.gif";s:4:"cf12";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/layout/mini-left.gif";s:4:"0829";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/layout/mini-right.gif";s:4:"a444";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/layout/mini-top.gif";s:4:"5aa5";s:74:"resources/ExtJS/3.0.3/resources/images/wcc/layout/panel-title-light-bg.gif";s:4:"155b";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/slider/slider-bg.png";s:4:"1015";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/slider/slider-thumb.png";s:4:"5b5e";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/slider/slider-v-bg.png";s:4:"461b";s:68:"resources/ExtJS/3.0.3/resources/images/wcc/slider/slider-v-thumb.png";s:4:"b297";s:68:"resources/ExtJS/3.0.3/resources/images/wcc/grid/arrow-left-white.gif";s:4:"b04e";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/grid/arrow-right-white.gif";s:4:"714e";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/grid/col-move-bottom.gif";s:4:"a83c";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/col-move-top.gif";s:4:"8e38";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/columns.gif";s:4:"8dfa";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/grid/dirty.gif";s:4:"decc";s:56:"resources/ExtJS/3.0.3/resources/images/wcc/grid/done.gif";s:4:"3652";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/drop-no.gif";s:4:"b53c";s:60:"resources/ExtJS/3.0.3/resources/images/wcc/grid/drop-yes.gif";s:4:"af96";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/footer-bg.gif";s:4:"65ed";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-blue-hd.gif";s:4:"dd35";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-blue-split.gif";s:4:"d993";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-hrow.gif";s:4:"a322";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-loading.gif";s:4:"9ac6";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-split.gif";s:4:"3ef4";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid-vista-hd.gif";s:4:"675f";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid3-hd-btn.gif";s:4:"fd8f";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid3-hrow-over.gif";s:4:"c500";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid3-hrow.gif";s:4:"3e44";s:72:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid3-special-col-bg.gif";s:4:"c9df";s:76:"resources/ExtJS/3.0.3/resources/images/wcc/grid/grid3-special-col-sel-bg.gif";s:4:"b521";s:60:"resources/ExtJS/3.0.3/resources/images/wcc/grid/group-by.gif";s:4:"f6bf";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/grid/group-collapse.gif";s:4:"6437";s:71:"resources/ExtJS/3.0.3/resources/images/wcc/grid/group-expand-sprite.gif";s:4:"cb31";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/group-expand.gif";s:4:"e2bc";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hd-pop.gif";s:4:"e5f2";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hmenu-asc.gif";s:4:"048e";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hmenu-desc.gif";s:4:"f0a9";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hmenu-lock.gif";s:4:"bcef";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hmenu-lock.png";s:4:"2a3b";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/hmenu-unlock.gif";s:4:"8cc8";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/grid/invalid_line.gif";s:4:"04a8";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/loading.gif";s:4:"00ef";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/grid/mso-hd.gif";s:4:"37fb";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/grid/nowait.gif";s:4:"23c9";s:71:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-first-disabled.gif";s:4:"8d31";s:62:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-first.gif";s:4:"4136";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-last-disabled.gif";s:4:"1d12";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-last.gif";s:4:"f082";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-next-disabled.gif";s:4:"0f4b";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-next.gif";s:4:"2e3b";s:70:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-prev-disabled.gif";s:4:"eefc";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/page-prev.gif";s:4:"dee4";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/grid/pick-button.gif";s:4:"6cdc";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/refresh.gif";s:4:"7e36";s:68:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-check-sprite.gif";s:4:"2d0a";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-editor-bg.gif";s:4:"8454";s:67:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-editor-btns.gif";s:4:"574d";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-expand-sprite.gif";s:4:"29e7";s:60:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-over.gif";s:4:"f639";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/row-sel.gif";s:4:"ca87";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/grid/sort-hd.gif";s:4:"2640";s:60:"resources/ExtJS/3.0.3/resources/images/wcc/grid/sort_asc.gif";s:4:"49d8";s:61:"resources/ExtJS/3.0.3/resources/images/wcc/grid/sort_desc.gif";s:4:"71c2";s:56:"resources/ExtJS/3.0.3/resources/images/wcc/grid/wait.gif";s:4:"b0cd";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/box/corners-blue.gif";s:4:"4677";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/box/corners.gif";s:4:"66df";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/box/l-blue.gif";s:4:"bb5c";s:52:"resources/ExtJS/3.0.3/resources/images/wcc/box/l.gif";s:4:"037f";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/box/r-blue.gif";s:4:"b861";s:52:"resources/ExtJS/3.0.3/resources/images/wcc/box/r.gif";s:4:"9dff";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/box/tb-blue.gif";s:4:"8911";s:53:"resources/ExtJS/3.0.3/resources/images/wcc/box/tb.gif";s:4:"c2c7";s:65:"resources/ExtJS/3.0.3/resources/images/wcc/form/clear-trigger.gif";s:4:"ce94";s:64:"resources/ExtJS/3.0.3/resources/images/wcc/form/date-trigger.gif";s:4:"694a";s:69:"resources/ExtJS/3.0.3/resources/images/wcc/form/error-tip-corners.gif";s:4:"fd14";s:63:"resources/ExtJS/3.0.3/resources/images/wcc/form/exclamation.gif";s:4:"b6e9";s:66:"resources/ExtJS/3.0.3/resources/images/wcc/form/search-trigger.gif";s:4:"df68";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/form/text-bg.gif";s:4:"73d0";s:59:"resources/ExtJS/3.0.3/resources/images/wcc/form/trigger.gif";s:4:"e135";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/dd/drop-add.gif";s:4:"b3b0";s:57:"resources/ExtJS/3.0.3/resources/images/wcc/dd/drop-no.gif";s:4:"ae53";s:58:"resources/ExtJS/3.0.3/resources/images/wcc/dd/drop-yes.gif";s:4:"f321";s:61:"resources/css/all-styles-except-default-are-in-xtheme-wcc.css";s:4:"d41d";s:29:"resources/css/default-min.css";s:4:"fe03";s:25:"resources/css/default.css";s:4:"e477";s:55:"resources/lang/locallang_extjs.docdb.gridStandalone.xml";s:4:"a9fa";s:40:"resources/lang/locallang_extjs.docdb.xml";s:4:"d392";s:29:"resources/js/DocDb-lib-min.js";s:4:"0dbd";s:33:"resources/js/DocDb.Descriptors.js";s:4:"5bf3";s:42:"resources/js/DocDb.DocGridResultDetails.js";s:4:"37f1";s:37:"resources/js/DocDb.DocumentDetails.js";s:4:"c460";s:33:"resources/js/DocDb.GridResults.js";s:4:"6c5c";s:32:"resources/js/DocDb.OwnersList.js";s:4:"0f6e";s:32:"resources/js/DocDb.SearchForm.js";s:4:"efaa";s:32:"resources/js/DocDb.StatusList.js";s:4:"9f55";s:31:"resources/js/DocDb.TypesList.js";s:4:"392a";s:42:"resources/js/DocDb.gridMainApp-prod-min.js";s:4:"5205";s:33:"resources/js/DocDb.gridMainApp.js";s:4:"2ca8";s:44:"resources/js/DocDb.searchMainApp-prod-min.js";s:4:"b5a6";s:35:"resources/js/DocDb.searchMainApp.js";s:4:"8f9d";s:32:"resources/js/ux/Ext.ux.MsgBus.js";s:4:"9624";s:40:"resources/js/ux/Ext.ux.PageSizePlugin.js";s:4:"e59e";s:42:"resources/js/ux/Ext.ux.form.MultiSelect.js";s:4:"4fdb";s:42:"resources/js/ux/Ext.ux.grid.GridFilters.js";s:4:"7a81";s:42:"resources/js/ux/Ext.ux.grid.RowExpander.js";s:4:"3925";s:40:"resources/js/ux/Ext.ux.menu.RangeMenu.js";s:4:"e6c5";s:47:"resources/js/ux/Ext.ux.plugins.HeaderButtons.js";s:4:"faeb";s:37:"resources/js/ux/filters/DateFilter.js";s:4:"70f4";s:33:"resources/js/ux/filters/Filter.js";s:4:"d790";s:37:"resources/js/ux/filters/ListFilter.js";s:4:"ba71";s:39:"resources/js/ux/filters/StringFilter.js";s:4:"dc58";s:14:"pi1/ce_wiz.gif";s:4:"f50f";s:26:"pi1/class.tx_docdb_pi1.php";s:4:"2f65";s:34:"pi1/class.tx_docdb_pi1_wizicon.php";s:4:"ca95";s:17:"pi1/locallang.xml";s:4:"ae4c";s:21:"pi1/locallang_wiz.xml";s:4:"9809";}',
	'suggests' => array(
	),
);

?>