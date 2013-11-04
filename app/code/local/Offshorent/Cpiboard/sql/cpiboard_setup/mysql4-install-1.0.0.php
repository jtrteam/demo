<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('offshorent_cpi')};
CREATE TABLE {$this->getTable('offshorent_cpi')} (
  `cpi_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY (`cpi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('offshorent_cpi')} (
`cpi_id` ,
`title`
)
VALUES (
NULL , 'Number of quotes'
), (
NULL , 'Number of quotes converted to orders'
), (
NULL , 'Number of PO’s'
), (
NULL , 'Orderintake'
), (
NULL , 'Number of complaints'
), (
NULL , 'Number of visitors'
);


-- DROP TABLE IF EXISTS {$this->getTable('offshorent_cpi_definition')};
CREATE TABLE {$this->getTable('offshorent_cpi_definition')} (
  `def_id` int(11) unsigned NOT NULL auto_increment,
  `cpi_id` int(11) unsigned NOT NULL ,
  `year` int(11) unsigned NOT NULL,
  `jan` int(11) unsigned NOT NULL,
  `feb` int(11) unsigned NOT NULL,
  `mar` int(11) unsigned NOT NULL,
  `apr` int(11) unsigned NOT NULL,
  `may` int(11) unsigned NOT NULL,
  `jun` int(11) unsigned NOT NULL,
  `jul` int(11) unsigned NOT NULL,
  `aug` int(11) unsigned NOT NULL,
  `sep` int(11) unsigned NOT NULL,
  `oct` int(11) unsigned NOT NULL,
  `nov` int(11) unsigned NOT NULL,
  `dec` int(11) unsigned NOT NULL,
  PRIMARY KEY (`def_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 