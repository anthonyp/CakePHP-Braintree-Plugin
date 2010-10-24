<?php
/**
 * Pages Controller File
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
 * @subpackage braintree.controllers
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */

/**
 * Pages Controller Class
 *
 * @package    braintree
 * @subpackage braintree.controllers
 */
App::import('Vendor', 'Braintree.Braintree');
class PagesController extends BraintreeAppController {

/**
 * Name
 *
 * @var string
 */
	public $name = 'Pages';
	
/**
 * Uses
 *
 * @var array
 */
	public $uses = array();
	
/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array();
	
/**
 * Components
 *
 * @var array
 */
	public $components = array();

/**
 * beforeFilter
 *
 * @return	void
 */
	public function beforeFilter () {
		
        parent::beforeFilter();
        
    }

/**
 * Popup helper for identifying credit card security codes
 *
 * @return	void
 */
    public function cvv_helper () {
    	
    	$this->set('title_for_layout', __('CVV/CID', true));
    	
    	$this->layout = 'braintree_popup';
    	
    }

}
?>