<?php
/**
 * BraintreeRemoteCustomer Model File
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
 * BraintreeRemoteCustomer Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeRemoteCustomer extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeRemoteCustomer';
	
/**
 * Schema
 *
 * @var array
 */
	public $_schema = array(
		'id' => array('type' => 'string', 'length' => '36'),
		'first_name' => array('type' => 'string', 'length' => '255'),
		'last_name' => array('type' => 'string', 'length' => '255'),
		'company' => array('type' => 'string', 'length' => '255'),
		'email' => array('type' => 'string', 'length' => '255'),
		'phone' => array('type' => 'string', 'length' => '255'),
		'fax' => array('type' => 'string', 'length' => '255'),
		'website' => array('type' => 'string', 'length' => '255')
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