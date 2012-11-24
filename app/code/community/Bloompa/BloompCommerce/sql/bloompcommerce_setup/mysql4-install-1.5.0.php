<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
DROP TABLE IF EXISTS `bloompa_settings`;
CREATE TABLE `bloompa_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `param` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `value` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci COMMENT='Tabela com as configurações do bloompa';
INSERT INTO bloompa_settings(id,product,param,value) VALUES(NULL,'BloompCommerce','token','novo');
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 