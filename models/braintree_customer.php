<?php
/**
 * BraintreeCustomer Model File
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
 * BraintreeCustomer Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
App::import('Braintree.BraintreeLocalAppModel');
class BraintreeCustomer extends BraintreeLocalAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeCustomer';
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'first_name' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'First name must be no longer than 255 characters.'
			)
		),
		'last_name' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Last name must be no longer than 255 characters.'
			)
		),
		'company' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Company must be no longer than 255 characters.'
			)
		),
		'email_address' => array (
			'isEmail' => array (
				'rule' => array('email', false),
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Please enter a valid email address.'
			),
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Email address must be no longer than 255 characters.'
			)
		),
		'phone' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Phone must be no longer than 255 characters.'
			)
		),
		'fax' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Fax must be no longer than 255 characters.'
			)
		),
		'website' => array (
			'maxLength' => array (
				'rule' => array('maxLength', 255),
				'last' => true,
				'message' => 'Website must be no longer than 255 characters.'
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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'BraintreeCreditCard' => array(
			'className' => 'Braintree.BraintreeCreditCard',
			'foreignKey' => 'braintree_customer_id',
			'order' => array(
				'BraintreeCreditCard.created' => 'asc'
			),
			'dependent' => true
		),
		'BraintreeAddress' => array(
			'className' => 'Braintree.BraintreeAddress',
			'foreignKey' => 'braintree_customer_id',
			'order' => array(
				'BraintreeAddress.created' => 'asc'
			),
			'dependent' => true
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
 * Gets or Creates the Braintree Customer ID for an existing foreign Customer
 *
 * @param	string	$model		The foreign model name
 * @param	string	$foreign_id	The foreign ID
 * @param	array	$data		Data to be saved with the Braintree Customer. See BraintreeRemoteCustomer::$_schema for possible keys
 * @return	string
 */
	public function getOrCreateCustomerId ($model, $foreign_id, $data=array()) {
		
		$braintree_customer_id = false;
		
	    $braintree_customer = $this->find('first', array(
    		'conditions' => array(
    			$this->alias . '.model' => $model,
    			$this->alias . '.foreign_id' => $foreign_id
    		),
    		'contain' => false
    	));
    	
    	if ($braintree_customer) {
    		
    		$braintree_customer_id = $braintree_customer[$this->alias]['id'];
    		
    	} else {
    		
    		$default_remote_sync_setting = $this->remote_sync;
    		$this->remote_sync = true;
    		$braintree_customer_saved = $this->save(array(
    			$this->alias => array_merge(
    				array(
	    				'model' => $model,
	    				'foreign_id' => $foreign_id
    				),
    				$data
    			)
    		));
    		$this->remote_sync = $default_remote_sync_setting;
    		
    		if ($braintree_customer_saved) {
    			$braintree_customer_id = $this->id;
    		}
    		
    	}
    	
    	return $braintree_customer_id;
		
	}
	
}
?>