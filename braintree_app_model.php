<?php
/**
 * Braintree App Model File
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
 * Braintree App Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
App::import('Vendor', 'Braintree.Braintree');
class BraintreeAppModel extends AppModel {
	
/**
 * Adds the datasource to the connection manager if it's not already there,
 * which it won't be if you've not added it to your app/config/database.php
 * file.
 *
 * @param 	$id
 * @param 	$table
 * @param 	$ds
 * @return	void
 */
	public function __construct ($id = false, $table = null, $ds = null) {

		$sources = ConnectionManager::sourceList();
		
		if (!in_array('braintree', $sources)) {
			ConnectionManager::create('braintree', array('datasource' => 'Braintree.BraintreeSource'));
		}
		
		parent::__construct($id, $table, $ds);

  }
	
/**
 * beforeSave
 *
 * @return	bool
 */
	public function beforeSave () {
		
		if (!parent::beforeSave()) {
			return false;
		}
		
		return true;
		
	}
	
/**
 * beforeDelete
 *
 * @param	bool	$cascade
 * @return	bool
 */
	public function beforeDelete ($cascade = true) {
		
		if (!parent::beforeDelete($cascade)) {
			return false;
		}
		
		return true;
		
	}
	
/**
 * afterDelete
 *
 * @return	bool
 */
	public function afterDelete () {
		
		if (!parent::afterDelete()) {
			return false;
		}
		
		return true;
		
	}
	
/**
 * Trims and uppercases a string
 *
 * @param	string	$string
 * @return	string
 */
	public function standardizeString ($string) {
		
		return strtoupper(trim($string));
		
	}
	
/**
 * Get options array for a field
 *
 * @param	string	$string
 * @return	array
 */
	public function getOptions ($field) {
		
		$name = '_' . $field . '_options';
		
		if (isset($this->{$name})) {
			return $this->{$name};
		} else {
			return array();
		}
		
	}
	
}
?>