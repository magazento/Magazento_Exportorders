<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/


$installer = $this;
$installer->startSetup();
$installer->run("

--
-- Table structure for table `magazento_orderexport2_item`
--

CREATE TABLE IF NOT EXISTS {$this->getTable('magazento_orderexport2_item')} (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT ' Id',
  `filename` varchar(32) DEFAULT NULL COMMENT 'Filename',
  `title` varchar(255) DEFAULT NULL COMMENT 'Title',
  `path` varchar(255) DEFAULT NULL COMMENT 'Path',
  `order_status` varchar(255) DEFAULT NULL COMMENT 'Order Status',
  `time_from` timestamp NULL DEFAULT NULL COMMENT 'Time From',
  `time_to` timestamp NULL DEFAULT NULL COMMENT 'Time To',
  `export_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `export_creditmemo` tinyint(1) NOT NULL DEFAULT '1',
  `export_shipment` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--

CREATE TABLE IF NOT EXISTS `{$this->getTable('magazento_orderexport2_item_related')}` (
  `item_id` smallint(6) unsigned DEFAULT NULL,
  `related_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{$this->getTable('magazento_orderexport2_item_store')}` (
  `item_id` smallint(6) unsigned DEFAULT NULL,
  `store_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




");




$installer->endSetup();
?>