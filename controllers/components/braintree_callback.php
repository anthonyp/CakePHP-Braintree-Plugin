<?php
/**
 * BraintreeCallback Component File
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
 * @subpackage braintree.controllers.components
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */

/**
 * BraintreeCallback Component Class
 *
 * @package    braintree
 * @subpackage braintree.controllers.components
 */
App::import('Vendor', 'Braintree.Braintree');
class BraintreeCallbackComponent extends Object {

/**
 * Controller
 *
 * @var object
 */
	public $controller;
	
/**
 * Components
 *
 * @var array
 */
	public $components = array();
	
/**
 * List of $_GET parameters we expect back from Braintree
 *
 * @var array
 */
	public $_callback_parameters = array(
		'http_status',
		'id',
		'kind',
		'hash'
	);
	
/**
 * Callback Actions
 * 
 * An array of actions in the controller this component is attached to that should be "watched" for Braintree callbacks
 * Example:
 * array(
 *	'payment' => array(
 *		'redirect' => array(
 *			'action' => 'review'
 *		),
 *		'model' => 'Order'
 *	)
 * )
 * ... In this example, 'payment' is the action being watched for callbacks. Upon successful processing, the user is redirected 
 * to the 'review' action. 'Order' is the foreign model that is associated with the callback. In order to log a foreign ID, 
 * a named parameter 'foreign_id' can be passed with the Braintree request
 *
 * @var array
 */
	public $_callback_actions = array();

/**
 * Initialize
 *
 * @param	object	$controller
 * @param	array	$settings	Possible array keys:
 * 								- 'callback_actions' - see self::$_callback_actions
 * 								- 'error_messages'
 * @return	void
 */
	public function initialize (&$controller, $settings=array()) {
		
		$this->controller = $controller;
		
		foreach (array(
			'callback_actions',
			'error_messages'
		) as $key) {
			if (!empty($settings[$key])) {
				$name = '_' . $key;
				$this->{$name} = $settings[$key];
			}
		}
		
	}

/**
 * Startup
 *
 * @param	object	$controller
 * @return	void
 */
	public function startup (&$controller) {
		
		if (
			in_array($this->controller->params['action'], $this->_callback_actions) || 
			array_key_exists($this->controller->params['action'], $this->_callback_actions)
		) {
			
			$this->action_settings = array();
			if (
				!empty($this->_callback_actions[$this->controller->params['action']]) && 
				is_array($this->_callback_actions[$this->controller->params['action']])
			) {
				$this->action_settings = $this->_callback_actions[$this->controller->params['action']];
			}
			
			$continue = true;
			$parameters = array();
			foreach ($this->_callback_parameters as $parameter) {
				if (empty($this->controller->params['url'][$parameter])) {
					$continue = false;
					break;
				} else {
					$parameters[$parameter] = $this->controller->params['url'][$parameter];
				}
			}
			
			if ($continue) {

				App::import('Vendor', 'Braintree.Braintree');
				
				$query_string = http_build_query($parameters, '', '&');
				
				foreach (array(
					'BraintreeAddress', 
					'BraintreeCreditCard', 
					'BraintreeCreditCardRelation'
				) as $model_name) {
					if (!isset($this->{$model_name})) {
						$this->{$model_name} = ClassRegistry::init('Braintree.' . $model_name);
					}
				}
				
				if (!$this->beforeConfirmation()) {
					return false;
				}
				
				try {
    				$result = Braintree_TransparentRedirect::confirm($query_string);
				} catch (Exception $e) {
					$result = false;
				}
				
				if (empty($result) || !$result->success) {
					
					$this->onFailure($result);
					
					return true;
					
				} else {
					
					if (!$this->onSuccess($result)) {
						return true;
					}
					
				}
				
				if (!$this->afterProcessing()) {
					return false;
				}
				
			}
			
		}
		
	}
	
/**
 * Get any errors currently queued up in the BraintreeCallback component
 *
 * @return	array
 */
	public function getError () {
		
		if (!empty($this->braintree_error)) {
			return $this->braintree_error;
		} else {
			return array();
		}
		
	}
	
/**
 * Set an error
 *
 * @param	string	$error		The error message
 * @return	bool
 */
	public function setError ($error) {
		
		$this->braintree_error = $error;
		
		return true;
		
	}
	
/**
 * Log an error
 *
 * @param	string	$error		The error message
 * @return	bool
 */
	public function logError ($error) {
		
		$this->log(
			$error,
			'braintree_errors'
		);
		
		return true;
		
	}
	
/**
 * beforeConfirmation
 * 
 * Called immediately before Braintree processes & verifies the request
 *
 * @return	bool
 */
	public function beforeConfirmation () {
		
		return true;
		
	}
	
/**
 * afterProcessing
 * 
 * Called after both Braintree has processed & verified the request, and the onFailure/onSuccess callback is called
 *
 * @return	bool
 */
	public function afterProcessing () {
		
		if (empty($this->redirect)) {
			$this->redirect = array('action' => $this->controller->params['action']);
		}
		
		$this->controller->redirect($this->redirect);
		
		return true;
		
	}
	
/**
 * onFailure
 * 
 * Called immediately after Braintree processes & verifies an invalid request
 *
 * @return	bool
 */
	public function onFailure ($result) {
		
		$this->braintree_error = !empty($result) ? $result->message : __('No response. Please try again.', true);
		
		$error = 'BRAINTREE ERROR: ';
		if (
			!empty($this->action_settings['model']) && 
			!empty($this->controller->params['named']['foreign_id'])
		) {
			$error .= $this->action_settings['model'] . '.' . $this->controller->params['named']['foreign_id'] . ': ';
		}
		if (!empty($result)) {
			$error .= $result->message;
		} else {
			$error .= 'No response';
		}
		
		$this->logError($error);
		
		return true;
		
	}
	
/**
 * onSuccess
 * 
 * Called immediately after Braintree processes & verifies a valid request
 *
 * @return	bool
 */
	public function onSuccess ($result) {
		
		$this->BraintreeCreditCard->begin();
		
		$default_remote_sync = $this->BraintreeAddress->remote_sync;
		$this->BraintreeAddress->remote_sync = false;
		$address_saved = $this->BraintreeAddress->save(array(
			'BraintreeAddress' => array(
				'id' => $result->creditCard->billingAddress->customerId . '|' . $result->creditCard->billingAddress->id,
				'braintree_customer_id' => $result->creditCard->billingAddress->customerId,
				'unique_address_identifier' => $this->BraintreeAddress->generateUniqueAddressIdentifier($result),
				'first_name' => $result->creditCard->billingAddress->firstName,
				'last_name' => $result->creditCard->billingAddress->lastName,
				'company' => $result->creditCard->billingAddress->company,
				'street_address' => $result->creditCard->billingAddress->streetAddress,
				'extended_address' => $result->creditCard->billingAddress->extendedAddress,
				'locality' => $result->creditCard->billingAddress->locality,
				'region' => $result->creditCard->billingAddress->region,
				'postal_code' => $result->creditCard->billingAddress->postalCode,
				'country_code_alpha_2' => $result->creditCard->billingAddress->countryCodeAlpha2,
				'country_code_alpha_3' => $result->creditCard->billingAddress->countryCodeAlpha3,
				'country_code_numeric' => $result->creditCard->billingAddress->countryCodeNumeric,
				'country_name' => $result->creditCard->billingAddress->countryName
			)
		));
		$this->BraintreeAddress->remote_sync = $default_remote_sync;
		
		$system_error = !empty($this->action_settings['system_error']) ? $this->action_settings['system_error'] : __('There was an error. Please try again', true);
		
		if (!$address_saved) {
			$this->BraintreeCreditCard->rollback();
			$this->braintree_error = $system_error;
			return false;
		}
		
		$default_remote_sync = $this->BraintreeCreditCard->remote_sync;
		$this->BraintreeCreditCard->remote_sync = false;
		$credit_card_saved = $this->BraintreeCreditCard->save(array(
			'BraintreeCreditCard' => array(
				'token' => $result->creditCard->token,
				'braintree_customer_id' => $result->creditCard->customerId,
				'braintree_address_id' => $result->creditCard->customerId . '|' . $result->creditCard->billingAddress->id,
				'unique_card_identifier' => $this->BraintreeCreditCard->generateUniqueCardIdentifier($result),
				'cardholder_name' => $result->creditCard->cardholderName,
				'card_type' => $result->creditCard->cardType,
				'masked_number' => $result->creditCard->maskedNumber,
				'expiration_date' => $result->creditCard->expirationYear . '-' . $result->creditCard->expirationMonth . '-01',
				'is_default' => $result->creditCard->default
			)
		));
		$this->BraintreeCreditCard->remote_sync = $default_remote_sync;
		
		if (!$credit_card_saved) {
			$this->BraintreeCreditCard->rollback();
			$this->braintree_error = $system_error;
			return false;
		}
		
		if (
			!empty($this->action_settings['model']) && 
			!empty($this->controller->params['named']['foreign_id'])
		) {
		
			$credit_card_relation_saved = $this->BraintreeCreditCardRelation->save(array(
				'BraintreeCreditCardRelation' => array(
					'braintree_credit_card_id' => $result->creditCard->token,
					'model' => $this->action_settings['model'],
					'foreign_id' => $this->controller->params['named']['foreign_id']
				)
			));
			
			if (!$credit_card_relation_saved) {
				$this->BraintreeCreditCard->rollback();
				$this->braintree_error = $system_error;
				return false;
			}
		
		}
		
		$non_defaults_saved = $this->BraintreeCreditCard->updateAll(
			array(
				'BraintreeCreditCard.is_default' => '"0"'
			),
			array(
				'BraintreeCreditCard.braintree_customer_id' => $result->creditCard->customerId,
				'BraintreeCreditCard.token !=' => $result->creditCard->token
			)
		);
		if (!$non_defaults_saved) {
			$this->BraintreeCreditCard->rollback();
			$this->braintree_error = $system_error;
			return false;
		}
		
		$this->BraintreeCreditCard->commit();
		
		if (!empty($this->action_settings['redirect'])) {
    		$this->redirect = $this->action_settings['redirect'];
    	}
    	
    	return true;
		
	}
	
}
?>