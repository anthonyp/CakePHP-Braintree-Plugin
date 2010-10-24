<?php
/**
 * BraintreeMerchant Model File
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
 * BraintreeMerchant Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeMerchant extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeMerchant';
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $hasMany = array(
		'BraintreeAddress' => array(
			'className'=> 'Braintree.BraintreeAddress',
			'foreignKey' => 'braintree_merchant_id',
			'dependent' => false
		),
		'BraintreeCreditCard' => array(
			'className' => 'Braintree.BraintreeCreditCard',
			'foreignKey' => 'braintree_merchant_id',
			'dependent' => false
		),
		'BraintreeCustomer' => array(
			'className' => 'Braintree.BraintreeCustomer',
			'foreignKey' => 'braintree_merchant_id',
			'dependent' => false
		),
		'BraintreeTransaction' => array(
			'className' => 'Braintree.BraintreeTransaction',
			'foreignKey' => 'braintree_merchant_id',
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
	
}
?>