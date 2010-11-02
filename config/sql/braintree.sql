CREATE TABLE IF NOT EXISTS `braintree_addresses` (
  `id` varchar(73) NOT NULL,
  `braintree_merchant_id` varchar(36) NOT NULL,
  `braintree_customer_id` char(36) NOT NULL,
  `unique_address_identifier` char(32) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `extended_address` varchar(255) NOT NULL,
  `locality` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country_code_alpha_2` char(2) NOT NULL,
  `country_code_alpha_3` char(3) NOT NULL,
  `country_code_numeric` int(5) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `braintree_local_customer_id` (`braintree_customer_id`),
  KEY `unique_address_identifier` (`unique_address_identifier`),
  KEY `created` (`created`),
  KEY `braintree_merchant_id` (`braintree_merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `braintree_credit_cards` (
  `token` char(36) NOT NULL,
  `braintree_merchant_id` varchar(36) NOT NULL,
  `braintree_customer_id` char(36) NOT NULL,
  `braintree_address_id` varchar(73) NOT NULL,
  `unique_card_identifier` char(32) NOT NULL,
  `cardholder_name` varchar(255) NOT NULL,
  `card_type` varchar(255) NOT NULL,
  `masked_number` varchar(19) NOT NULL,
  `expiration_date` date NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`token`),
  KEY `customer_id` (`braintree_customer_id`),
  KEY `unique_card_identifier` (`unique_card_identifier`),
  KEY `created` (`created`),
  KEY `braintree_merchant_id` (`braintree_merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `braintree_credit_card_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `braintree_credit_card_id` char(36) NOT NULL,
  `model` varchar(32) NOT NULL,
  `foreign_id` varchar(36) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_idx` (`model`,`foreign_id`),
  KEY `braintree_local_credit_card_idx` (`braintree_credit_card_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `braintree_customers` (
  `id` char(36) NOT NULL,
  `braintree_merchant_id` varchar(36) NOT NULL,
  `model` varchar(32) DEFAULT NULL,
  `foreign_id` varchar(36) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_idx` (`model`,`foreign_id`),
  KEY `braintree_merchant_id` (`braintree_merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `braintree_merchants` (
  `id` varchar(36) NOT NULL,
  `braintree_public_key` varchar(36) NOT NULL,
  `braintree_private_key` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `braintree_transactions` (
  `id` varchar(73) NOT NULL,
  `braintree_merchant_id` varchar(36) NOT NULL,
  `braintree_customer_id` char(36) NOT NULL,
  `braintree_credit_card_id` char(36) NOT NULL,
  `braintree_transaction_id` varchar(73) DEFAULT NULL,
  `model` varchar(32) DEFAULT NULL,
  `foreign_id` varchar(36) DEFAULT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'sale',
  `amount` float(9,2) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'submitted_for_settlement',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `braintree_local_credit_card_id` (`braintree_credit_card_id`),
  KEY `braintree_local_customer_id` (`braintree_customer_id`),
  KEY `type` (`type`),
  KEY `braintree_transaction_id` (`braintree_transaction_id`),
  KEY `braintree_merchant_id` (`braintree_merchant_id`),
  KEY `model_idx` (`model`,`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;