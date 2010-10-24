<?php
/**
 * Braintree Local App Model File
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
 * Braintree Local App Model Class
 *
 * @package    braintree
 * @subpackage braintree.models
 */
class BraintreeLocalAppModel extends BraintreeAppModel {
	
/**
 * Should the primary key be automatically generated?
 *
 * @var boolean
 */
	public $autoPK = true;
	
/**
 * Should C*UD calls be synced to the remote API automatically?
 *
 * @var boolean
 */
	public $remote_sync = true;
	
/**
 * Construct
 *
 * @return	void
 */
	public function __construct () {
		
		return parent::__construct();
		
	}
	
/**
 * Get the Remote model name that corresponds to this Local model name.
 * 
 * For example, BraintreeTransaction corresponds to BraintreeRemoteTransaction
 *
 * @return	string
 */
	private function _getRemoteModelName () {
		
		$entity = str_replace(array('Braintree'), '', $this->name);
		$remote_model = 'BraintreeRemote' . $entity;
		
		return $remote_model;
		
	}
	
/**
 * If remote sync has kicked in, this turns remote sync on/off for any related, dependent models
 *
 * @param	bool
 * @return	void
 */
	private function _toggleRemoteSyncOnDependentModels ($enabled=true) {
		
		$model_list = array();
		if (!empty($this->hasMany)) {
			foreach ($this->hasMany as $model_name => $info) {
				if (!is_numeric($model_name)) {
					$model_list[] = $model_name;
				} elseif (is_string($info)) {
					$model_list[] = $model_name;
				}
			}
		}
		if (!empty($this->hasOne)) {
			foreach ($this->hasOne as $model_name => $info) {
				if (!is_numeric($model_name)) {
					$model_list[] = $model_name;
				} elseif (is_string($info)) {
					$model_list[] = $model_name;
				}
			}
		}
		
		foreach ($model_list as $model_name) {
			$this->{$model_name}->remote_sync = $enabled;
		}
		
	}
	
/**
 * beforeSave
 * Accomplishes the following if remote sync is enabled:
 * - Sets the primary key to be saved + any fields passed that are in the actual API schema
 * - Saves the information to the API
 * - Sets the remote ID to the ID for this local model
 * - Sets the braintree_merchant_id key for this model to the current merchantId being used
 *
 * @return	bool
 */
	public function beforeSave () {
		
		if (empty($this->id) && $this->autoPK) {
			$uuid = String::uuid();
			$this->id = $this->data[$this->alias][$this->primaryKey] = $uuid;
		}
		
		if ($this->remote_sync) {
			
			$remote_model_name = $this->_getRemoteModelName();
			
			if (!isset($this->{$remote_model_name})) {
				$this->{$remote_model_name} = ClassRegistry::init('Braintree.' . $remote_model_name);
			}
			
			if (!empty($this->id)) {
				$this->{$remote_model_name}->id = $this->id;
			}
			$whitelisted_fields = $this->{$remote_model_name}->_schema;
			if ($this->primaryKey != 'id') {
				$whitelisted_fields = array_merge(
					array($this->primaryKey => array()),
					$whitelisted_fields
				);
			}
			foreach ($whitelisted_fields as $field => $schema) {
				if (!empty($this->data[$this->alias][$field])) {
					$remote_data[$remote_model_name][$field] = $this->data[$this->alias][$field];
				}
			}
			
			$success = $this->{$remote_model_name}->save($remote_data);
			
			if (!empty($this->{$remote_model_name}->id)) {
				$this->id = $this->{$remote_model_name}->id;
				$this->data[$this->alias][$this->primaryKey] = $this->id;
				$this->{$remote_model_name}->create(false);
				$this->{$remote_model_name}->id = null;
			}
			
			if (!$success) {
				return false;
			}
			
		}
		
		if (!empty($this->_schema['braintree_merchant_id'])) {
		
			$merchantId = BraintreeConfig::get('merchantId');
			
			if (empty($merchantId)) {
				return false;
			}
			
			$this->data[$this->alias]['braintree_merchant_id'] = $merchantId;
		
		}
		
		return true;
		
	}
	
/**
 * beforeDelete
 * Accomplishes the following if remote sync is enabled:
 * - Deletes the record from the API
 * - Turns off remote sync for related, dependent models
 *
 * @param	bool	$cascade
 * @return	bool
 */
	public function beforeDelete ($cascade = true) {
		
		if (empty($this->id)) {
			return false;
		}
		
		if ($this->remote_sync) {
			
			$remote_model_name = $this->_getRemoteModelName();
			
			if (!isset($this->{$remote_model_name})) {
				$this->{$remote_model_name} = ClassRegistry::init('Braintree.' . $remote_model_name);
			}
			
			$success = $this->{$remote_model_name}->delete($this->id);
			
			if (!$success) {
				return false;
			}
			
		}
		
		$this->_toggleRemoteSyncOnDependentModels(false);
		
		return true;
		
	}
	
/**
 * afterDelete
 * Accomplishes the following if remote sync is enabled:
 * - Turns on remote sync for related, dependent models
 *
 * @param	bool	$cascade
 * @return	bool
 */
	public function afterDelete () {
		
		$this->_toggleRemoteSyncOnDependentModels(true);
		
		return true;
		
	}
	
}
?>