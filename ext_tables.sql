#
# Table structure for table 'tx_docdb_type'
#
CREATE TABLE tx_docdb_type (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	type tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_docdb_status'
#
CREATE TABLE tx_docdb_status (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	statuts tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_docdb_owner'
#
CREATE TABLE tx_docdb_owner (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	owner tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_docdb_descriptor'
#
CREATE TABLE tx_docdb_descriptor (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	dscr_pid int(11) DEFAULT '0' NOT NULL,
	dscr_related int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY dscr_pid (dscr_pid)
);




#
# Table structure for table 'pages_tx_docdb_doc_descriptor_mm'
# 
#
CREATE TABLE pages_tx_docdb_doc_descriptor_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_docdb_doc_type int(11) DEFAULT '0' NOT NULL,
	tx_docdb_doc_status int(11) DEFAULT '0' NOT NULL,
	tx_docdb_doc_owner int(11) DEFAULT '0' NOT NULL,
	tx_docdb_doc_key tinytext NOT NULL,
	tx_docdb_doc_descriptor int(11) DEFAULT '0' NOT NULL,
	tx_docdb_doc_related_pages int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'pages_tx_docdb_doc_related_pages_mm'
# 
#
CREATE TABLE pages_tx_docdb_doc_related_pages_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
