<?php
/**
 * BraintreeCreditCard Model File
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
 * BraintreeCreditCard Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
App::import('Braintree.BraintreeLocalAppModel');
class BraintreeCreditCard extends BraintreeLocalAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeCreditCard';
	
/**
 * Primary Key
 *
 * @var string
 */
	public $primaryKey = 'token';
	
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
		'cardholder_name' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Cardholder name cannot be left blank.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Cardholder name must be no longer than 255 characters.'
			)
		),
		'card_type' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Card type must be no longer than 255 characters.'
			)
		),
		'number' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Card number cannot be left blank.'
			),
			'isCC' => array (
				'rule' => array('cc', 'fast', true, null),
				'last' => true,
				'message' => 'Credit card number must be vali.'
			)
		),
		'cvv' => array (
			'numeric' => array (
				'rule' => 'numeric',
				'allowEmpty' => true,
				'last' => true,
				'message' => 'CVV must be numeric.'
			),
			'between' => array(
				'rule' => array('between', 3, 4),
				'allowEmpty' => true,
				'last' => true,
				'message' => 'CVV must be either 3 or 4 digits.'
			)
		),
		'expiration_date' => array (
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Expiration date cannot be left blank.'
			),
			'date' => array (
				'rule' => array('date', 'ymd'),
				'last' => true,
				'message' => 'Expiration date must be a valid date.'
			),
			'inFuture' => array(
				'rule' => array('validateCCExpiration', 'expiration_date'),
				'last' => true,
				'message' => 'Expiration date must not be in the past.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BraintreeAddress' => array(
			'className' => 'Braintree.BraintreeAddress',
			'foreignKey' => 'braintree_address_id'
		),
		'BraintreeMerchant' => array(
			'className' => 'Braintree.BraintreeMerchant',
			'foreignKey' => 'braintree_merchant_id'
		)
	);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'BraintreeTransaction' => array(
			'className' => 'Braintree.BraintreeTransaction',
			'foreignKey' => 'braintree_credit_card_id',
			'dependent' => false
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
 * beforeSave
 * If self::$remote_sync is true, this accomplishes the following:
 * - Throws the data to save into a temporary variable
 * - Converts the braintree_customer_id key to customer_id
 * - Calls the parent (and sends the data to the API)
 * - Puts the temporary data back into the main data array
 * - Unsets the CVV number
 * - Converts the credit card number to a masked number, and unsets the full number
 * 
 * @return	bool
 */
	public function beforeSave () {
		
		if ($this->remote_sync) {
			$temp_data = $this->data;
			if (!empty($this->data[$this->alias]['braintree_customer_id'])) {
				$this->data[$this->alias]['customer_id'] = $this->data[$this->alias]['braintree_customer_id'];
				unset($this->data[$this->alias]['braintree_customer_id']);
			}
		}
		
		if (!parent::beforeSave()) {
			return false;
		}
		
		if ($this->remote_sync) {
			$this->data = $temp_data;
		}
		
		unset($this->data[$this->alias]['cvv']);
		
		if (!empty($this->data[$this->alias]['number'])) {
			$first_six = substr($this->data[$this->alias]['number'], 0, 6);
			$last_four = substr($this->data[$this->alias]['number'], -4);
			$strlen = strlen($this->data[$this->alias]['number']);
			$difference = ($strlen - 10);
			$masked_number = $first_six;
			for ($count=1; $count<=$difference; $count++) {
				$masked_number .= '*';
			}
			$masked_number .= $last_four;
			$this->data[$this->alias]['masked_number'] = $masked_number;
			unset($this->data[$this->alias]['number']);
		}
		
		return true;
		
	}
	
/**
 * Validates a credit card expiration date
 *
 * @param	array	$data		Data to save
 * @param	string	$fieldName	Field name being validated
 * @return	bool
 */
	public function validateCCExpiration ($data, $fieldName) {
		
		if (
			is_array($data[$fieldName]) && 
			!empty($data[$fieldName]['year']) && 
			!empty($data[$fieldName]['month'])
		) {
			$entered = strtotime($data[$fieldName]['year'] . '-' . $data[$fieldName]['month'] . '-01');
		} elseif (is_string($data[$fieldName])) {
			$entered = strtotime($data[$fieldName]);
		} else {
			return false;
		}
		
		$validStarting = strtotime(date('Y', time()) . '-' . date('m', time()) . '-01');
		
		if ($entered < $validStarting) {
			return false;
		}
		
		return true;
		
	}
	
/**
 * Generates an md5 hash that represents a unique credit card in the database
 *
 * @param	object	$result		The CreditCard result object that comes back directly from Braintree
 * @return	string
 */
	public function generateUniqueCardIdentifier ($result) {
		
		$merchantId = Configure::read('Braintree.merchantId');
		
		if (empty($merchantId) || empty($result)) {
			return false;
		}
		
		return md5(
			Configure::read('Braintree.merchantId') . ':' . 
			$result->creditCard->customerId . ':' . 
			$this->standardizeString($result->creditCard->cardholderName) . ':' . 
			$this->standardizeString($result->creditCard->cardType) . ':' . 
			$result->creditCard->maskedNumber . ':' . 
			$result->creditCard->expirationYear . $result->creditCard->expirationMonth . '-01'
		);
		
	}
	
}
?>