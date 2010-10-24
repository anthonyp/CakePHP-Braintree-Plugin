<?php
require_once('api' . DS . 'lib' . DS . 'Braintree.php');

class BraintreeConfig {
	
	public function set ($configs=array()) {
		
		extract($configs);
		
		if (!empty($environment)) {
			Braintree_Configuration::environment($environment);
		}
		if (!empty($merchantId)) {
			Braintree_Configuration::merchantId($merchantId);
		}
		if (!empty($publicKey)) {
			Braintree_Configuration::publicKey($publicKey);
		}
		if (!empty($privateKey)) {
			Braintree_Configuration::privateKey($privateKey);
		}
		
		return true;
		
	}
	
	public function get ($config) {
		
		switch ($config) {
			case 'environment':
				return Braintree_Configuration::environment();
				break;
			case 'merchantId':
				return Braintree_Configuration::merchantId();
				break;
			case 'publicKey':
				return Braintree_Configuration::publicKey();
				break;
			case 'privateKey':
				return Braintree_Configuration::privateKey();
				break;
			default:
				return false;
				break;
				
		}
		
	}
	
}

BraintreeConfig::set(array(
	'environment' => Configure::read('Braintree.environment'),
	'merchantId' => Configure::read('Braintree.merchantId'),
	'publicKey' => Configure::read('Braintree.publicKey'),
	'privateKey' => Configure::read('Braintree.privateKey')
));

?>