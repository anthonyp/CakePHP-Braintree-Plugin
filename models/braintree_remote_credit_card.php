<?php
/**
 * BraintreeRemoteCreditCard Model File
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
 * BraintreeRemoteCreditCard Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeRemoteCreditCard extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeRemoteCreditCard';
	
/**
 * Schema
 *
 * @var array
 */
	public $_schema = array(
		'token' => array('type' => 'string', 'length' => '36'),
		'customer_id' => array('type' => 'string', 'length' => '36'),
		'cardholder_name' => array('type' => 'string', 'length' => '255'),
		'number' => array('type' => 'integer', 'length' => '19'),
		'cvv' => array('type' => 'integer', 'length' => '4'),
		'expiration_date' => array('type' => 'string', 'length' => '255')
	);
	
/**
 * Primary Key
 *
 * @var string
 */
	public $primaryKey = 'token';

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
	
/**
 * beforeSave
 * Accomplishes the following:
 * - Converts a month & year expiration date to 1 field to send to the API
 * 
 * @return	bool
 */
	public function beforeSave () {
		
		if (!parent::beforeSave()) {
			return false;
		}
		
		if (!empty($this->data[$this->alias]['expiration_date'])) {
			$expiration_date = $this->data[$this->alias]['expiration_date'];
			if (is_array($expiration_date)) {
				$expiration_date = $expiration_date['year'] . '-' . $expiration_date['month'] . '-01';
			}
			$this->data[$this->alias]['expiration_date'] = date('m/Y', strtotime($expiration_date));
		}
		
		return true;
		
	}
	
}
?>