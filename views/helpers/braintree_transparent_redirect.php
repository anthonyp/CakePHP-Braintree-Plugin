<?php
/**
 * BraintreeTransparentRedirect Helper File
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
 * @subpackage braintree.views.helpers
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */

/**
 * BraintreeTransparentRedirect Helper Class
 *
 * @package    braintree
 * @subpackage braintree.views.helpers
 */
App::import('Vendor', 'Braintree.Braintree');
class BraintreeTransparentRedirectHelper extends Braintree_TransparentRedirect {
	
/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array();
	
/**
 * Construct
 *
 * @return	void
 */
	public function __construct () {
	}
	
}
?>