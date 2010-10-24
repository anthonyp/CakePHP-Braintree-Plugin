<?php
/**
 * BraintreeCreditCardRelation Model File
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
 * BraintreeCreditCardRelation Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeCreditCardRelation extends BraintreeAppModel {

/**
 * Name of model
 *
 * @var string
 */
	public $name = 'BraintreeCreditCardRelation';
	
/**
 * belongsTo associations
 *
 * @var array
 */
	var $belongsTo = array(
		'BraintreeCreditCard' => array(
			'className' => 'Braintree.BraintreeCreditCard',
			'foreignKey' => 'braintree_credit_card_id'
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