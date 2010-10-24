<?php
/**
 * BraintreeRemoteAdress Model File
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
 * BraintreeRemoteAddress Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeRemoteAddress extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeRemoteAddress';
	
/**
 * Schema
 *
 * @var array
 */
	public $_schema = array(
		'id' => array('type' => 'string', 'length' => '36'),
		'customer_id' => array('type' => 'string', 'length' => '36'),
		'first_name' => array('type' => 'string', 'length' => '255'),
		'last_name' => array('type' => 'string', 'length' => '255'),
		'company' => array('type' => 'string', 'length' => '255'),
		'street_address' => array('type' => 'string', 'length' => '255'),
		'extended_address' => array('type' => 'string', 'length' => '255'),
		'locality' => array('type' => 'string', 'length' => '255'),
		'region' => array('type' => 'string', 'length' => '255'),
		'postal_code' => array('type' => 'string', 'length' => '255'),
		'country_code_alpha_2' => array('type' => 'string', 'length' => '2'),
		'country_code_alpha_3' => array('type' => 'string', 'length' => '3'),
		'country_code_numeric' => array('type' => 'integer', 'length' => '5'),
		'country_name' => array('type' => 'string', 'length' => '255')
	);

/**
 * useTable
 *
 * @var string
 */
	var $useTable = false;
	
/**
 * Name of datasource config to use
 *
 * @var string
 */
	var $useDbConfig = 'braintree';
	
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