<?php
/**
 * BraintreeAddress Model File
 *
 * Copyright (c) 2010 Anthony Putignano
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5.2
 * CakePHP version 1.3
 *
 * @package    braintree
 * @subpackage braintree.models
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */

/**
 * BraintreeAddress Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
App::import('Braintree.BraintreeLocalAppModel');
class BraintreeAddress extends BraintreeLocalAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeAddress';
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'braintree_customer_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Braintree Customer ID cannot be left blank.'
			),
			'between' => array(
				'rule' => array('between', 36, 36),
				'last' => true,
				'message' => 'Braintree Customer ID must be 36 characters.'
			)
		),
		'first_name' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'First name cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'First name must be no longer than 255 characters.'
			)
		),
		'last_name' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Last name cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Last name must be no longer than 255 characters.'
			)
		),
		'company' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Company must be no longer than 255 characters.'
			)
		),
		'street_address' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Street address cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Street address must be no longer than 255 characters.'
			)
		),
		'extended_address' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Extended address must be no longer than 255 characters.'
			)
		),
		'locality' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Locality cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Locality must be no longer than 255 characters.'
			)
		),
		'region' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Region cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Region must be no longer than 255 characters.'
			)
		),
		'postal_code' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Postal code cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Postal code must be no longer than 255 characters.'
			)
		),
		'country_code_alpha_2' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Country code alpha 2 cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 2),
				'last' => true,
				'message' => 'Country code alpha 2 must be no longer than 2 characters.'
			)
		),
		'country_code_alpha_3' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 3),
				'allowEmpty' => true,
				'message' => 'Country code alpha 3 must be no longer than 3 characters.'
			)
		),
		'country_code_numeric' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Country code numeric cannot be left blank.'
			),
			'numeric' => array(
				'rule' => 'numeric',
				'last' => true,
				'message' => 'Country code numeric must be numeric.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 5),
				'last' => true,
				'message' => 'Country code numeric must be no longer than 5 characters.'
			)
		),
		'country_name' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Country name must be no longer than 255 characters.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BraintreeMerchant' => array(
			'className' => 'Braintree.BraintreeMerchant',
			'foreignKey' => 'braintree_merchant_id'
		)
	);
	
/**
 * Construct
 *
 * @return	void
 */
	public function __construct () {
		
		return parent::__construct();
		
	}
	
/**
 * Generates an md5 hash that represents a unique address in the database
 *
 * @param	object	$result		The CreditCard result object that comes back directly from Braintree
 * @return	string
 */
	public function generateUniqueAddressIdentifier ($result) {
		
		$merchantId = Configure::read('Braintree.merchantId');
		
		if (empty($merchantId) || empty($result)) {
			return false;
		}
		
		return md5(
			Configure::read('Braintree.merchantId') . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->firstName) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->lastName) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->company) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->streetAddress) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->extendedAddress) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->locality) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->region) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->postalCode) . ':' . 
			$this->standardizeString($result->creditCard->billingAddress->countryCodeAlpha2)
		);
		
	}
	
}
?>