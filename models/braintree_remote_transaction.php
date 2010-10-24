<?php
/**
 * BraintreeRemoteTransaction Model File
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
 * BraintreeRemoteTransaction Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeRemoteTransaction extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeRemoteTransaction';
	
/**
 * Schema
 *
 * @var array
 */
	public $_schema = array(
		'id' => array('type' => 'string', 'length' => '36'),
		'customer_id' => array('type' => 'string', 'length' => '36'),
		'payment_method_token' => array('type' => 'string', 'length' => '36'),
		'braintree_transaction_id' => array('type' => 'string', 'length' => '36'),
		'type' => array('type' => 'string', 'length' => '16'),
		'amount' => array('type' => 'float', 'length' => '10'),
		'status' => array('type' => 'string', 'length' => '32')
	);

/**
 * useTable
 *
 * @var string
 */
	public $useTable = false;
	
/**
 * Name of datasource config to use
 *
 * @var string
 */
	public $useDbConfig = 'braintree';
	
/**
 * Construct
 *
 * @return	void
 */
	public function __construct () {
		
		return parent::__construct();
		
	}
	
}
?>