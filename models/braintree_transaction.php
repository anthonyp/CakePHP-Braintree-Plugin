<?php
/**
 * BraintreeTransaction Model File
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
 * BraintreeTransaction Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
App::import('Braintree.BraintreeLocalAppModel');
class BraintreeTransaction extends BraintreeLocalAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeTransaction';
	
/**
 * Should the primary key be automatically generated?
 *
 * @var boolean
 */
	public $autoPK = false;
	
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
		'braintree_credit_card_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Braintree Credit Card ID cannot be left blank.'
			),
			'between' => array(
				'rule' => array('between', 36, 36),
				'last' => true,
				'message' => 'Braintree Credit Card ID must be 36 characters.'
			)
		),
		'payment_method_token' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Payment Method Token cannot be left blank.'
			),
			'between' => array(
				'rule' => array('between', 36, 36),
				'last' => true,
				'message' => 'Payment Method Token must be 36 characters.'
			)
		),
		'type' => array(
			'inList' => array(
				'rule' => array('inList', array('sale', 'credit')),
				'message' => 'Type must be valid.'
			)
		),
		'amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true,
				'message' => 'Amount cannot be left blank.'
			),
			'numeric' => array (
				'rule' => 'numeric',
				'last' => true,
				'message' => 'Amount must be numeric.'
			)
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array('authorized', 'submitted_for_settlement', 'settled', 'voided', 'processor_declined', 'gateway_rejected', 'failed')),
				'message' => 'Status must be valid.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BraintreeCreditCard' => array(
			'className' => 'Braintree.BraintreeCreditCard',
			'foreignKey' => 'braintree_credit_card_id'
		),
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
 * beforeSave
 * If self::$remote_sync is true, this accomplishes the following:
 * - Throws the data to save into a temporary variable
 * - Converts the braintree_customer_id key to customer_id
 * - Convers the braintree_credit_card_id key to payment_method_token
 * - Calls the parent (and sends the data to the API)
 * - Puts the temporary data back into the main data array, adding the 'id' (remote Transaction ID) and braintree_merchant_id keys
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
			if (!empty($this->data[$this->alias]['braintree_credit_card_id'])) {
				$this->data[$this->alias]['payment_method_token'] = $this->data[$this->alias]['braintree_credit_card_id'];
				unset($this->data[$this->alias]['braintree_credit_card_id']);
			}
		}
		
		if (!parent::beforeSave()) {
			return false;
		}
		
		if ($this->remote_sync) {
			$this->data[$this->alias] = array_merge(
				array(
					'id' => $this->id,
					'braintree_merchant_id' => $this->data[$this->alias]['braintree_merchant_id']
				),
				$temp_data[$this->alias]
			);
		}
		
		return true;
		
	}
	
}
?>