**** COMMENT ABOUT THE ADDWHERE CLAUSE IN FLEXFORM OF THE PLUGIN ***

---The query is for the view on document pages look like:---

SELECT pages.* , tx_docdb_owner.uid AS 'tx_docdb_owner.uid', tx_docdb_owner.owner AS 'owner', tx_docdb_type.uid AS 'tx_docdb_type.uid', tx_docdb_type.type AS 'type'
FROM pages 
LEFT JOIN pages_tx_docdb_doc_descriptor_mm ON pages.uid = pages_tx_docdb_doc_descriptor_mm.uid_local 
LEFT JOIN tx_docdb_descriptor ON  = pages_tx_docdb_doc_descriptor_mm.uid_foreign 
LEFT JOIN tx_docdb_owner ON pages.tx_docdb_doc_owner = tx_docdb_owner.uid 
LEFT JOIN tx_docdb_type ON pages.tx_docdb_doc_type = tx_docdb_type.uid
WHERE
pages.doktype = 198 
AND pages.deleted=0 
AND pages.hidden=0 
AND (pages.starttime<=1135067107) 
AND (pages.endtime=0 OR pages.endtime>1135067107) 
AND pages.fe_group IN (0,-1)


---Where clause exemple: select only documents with owner uid=5---
AND tx_docdb_owner.uid=5


---Where clause exemple: select only documents with owner name = 'Owner1'---
AND owner='Owner 1'

---Where clause exemple: select only documents with owner name = 'Owner1'---
AND tx_docdb_descriptor.uid = 14

