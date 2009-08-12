<?php

########################################################################
# Extension Manager/Repository config file for ext: "doc_db"
#
# Auto generated 12-08-2009 16:04
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
	'version' => '0.2.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Nicolas Wezel, Olivier Schopfer',
	'author_email' => '',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:28:{s:9:"ChangeLog";s:4:"0d86";s:10:"README.txt";s:4:"5f72";s:22:"class.display_tree.php";s:4:"b27e";s:27:"class.tx_docdb_treeview.php";s:4:"9504";s:25:"class.ux_browse_links.php";s:4:"1b3b";s:20:"docDBflexform_ds.xml";s:4:"b093";s:21:"ext_conf_template.txt";s:4:"6533";s:12:"ext_icon.gif";s:4:"1455";s:17:"ext_localconf.php";s:4:"0063";s:14:"ext_tables.php";s:4:"b4b9";s:14:"ext_tables.sql";s:4:"6298";s:24:"ext_typoscript_setup.txt";s:4:"8a1f";s:28:"icon_tx_docdb_descriptor.gif";s:4:"475a";s:23:"icon_tx_docdb_owner.gif";s:4:"475a";s:24:"icon_tx_docdb_status.gif";s:4:"475a";s:22:"icon_tx_docdb_type.gif";s:4:"475a";s:13:"locallang.php";s:4:"66fc";s:16:"locallang_db.php";s:4:"80f0";s:7:"tca.php";s:4:"bd19";s:19:"doc/wizard_form.dat";s:4:"e95f";s:20:"doc/wizard_form.html";s:4:"dc69";s:29:"pi1/class.tree_descriptor.php";s:4:"8eb0";s:26:"pi1/class.tx_docdb_pi1.php";s:4:"d6ff";s:20:"pi1/frontend_js.html";s:4:"52ba";s:17:"pi1/locallang.php";s:4:"fef2";s:32:"pi1/wizard_search_descriptor.php";s:4:"680c";s:24:"pi1/static/editorcfg.txt";s:4:"92ea";s:20:"pi1/static/setup.txt";s:4:"19cf";}',
	'suggests' => array(
	),
);

?>