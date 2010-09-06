<?php

$extensionPath = t3lib_extMgm::extPath('doc_db');
return array(
	'tx_docdb_extjsdirect_controller' => $extensionPath . 'classes/controller/class.tx_docdb_extjsdirect_controller.php',
	'tx_docdb_xml_controller' => $extensionPath . 'classes/controller/class.tx_docdb_xml_controller.php',
	'tx_docdb_model_descriptor' => $extensionPath . 'classes/model/class.tx_docdb_model_descriptor.php',
    'tx_docdb_model_document' => $extensionPath . 'classes/model/class.tx_docdb_model_document.php',
    'tx_docdb_model_owner' => $extensionPath . 'classes/model/class.tx_docdb_model_owner.php',
    'tx_docdb_model_status' => $extensionPath . 'classes/model/class.tx_docdb_model_status.php',
    'tx_docdb_model_type' => $extensionPath . 'classes/model/class.tx_docdb_model_type.php',
    'tx_docdb_xml_view' => $extensionPath . 'classes/view/class.tx_docdb_xml_view.php',
    'tx_docdb_div' => $extensionPath . 'classes/class.tx_docdb_div.php',
    'tx_docdb_xmlcache' => $extensionPath . 'classes/class.tx_docdb_xmlcache.php',
);
unset($extensionPath);
?>