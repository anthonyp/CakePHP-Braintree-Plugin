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
 *		)
 *	)
 * )
 * ... In this example, 'payment' is the action being watched for callbacks. Upon successful processing, the user is redirected 
 * to the 'review' action. In order to log one or more foreign model(s) and foreign ID(s), named parameters 'foreign_model' and 
 * 'foreign_id' can be passed with the Braintree request containing either 1 model and 1 ID, or a comma-separated list of models 
 * and IDs for the purpose of creating multiple relationships.
 *
 * @var array
 */
	public $_callback_actions = array();
	
/**
 * List of processor response codes
 *
 * @var array
 */
	public $_processor_response_codes = array(
		// Success
		1000 => 'Approved',
		1001 => 'Approved, check customer ID',
		1002 => 'Processed - This code will be assigned to all credits and voice authorizations. These types of transactions do not need to be authorized they are immediately submitted for settlement.',
		// Failure
		2000 => 'Do Not Honor',
		2001 => 'Insufficient Funds',
		2002 => 'Limit Exceeded',
		2003 => 'Cardholder\'s Activity Limit Exceeded',
		2004 => 'Expired Card',
		2005 => 'Invalid Credit Card Number',
		2006 => 'Invalid Date',
		2007 => 'No Account',
		2008 => 'Card Account Length Error',
		2009 => 'No Such Issuer',
		2010 => 'Card Issuer Declined CVV',
		2011 => 'Voice Authorization Required',
		2012 => 'Voice Authorization Required. Possible Lost Card',
		2013 => 'Voice Authorization Required. Possible stolen card',
		2014 => 'Voice Authorization Required. Fraud Suspected.',
		2015 => 'Transaction Not Allowed',
		2016 => 'Duplicate Transaction',
		2017 => 'Cardholder Stopped Billing',
		2018 => 'Cardholder Stopped All Billing',
		2019 => 'Declined by Issuer- Invalid Transaction',
		2020 => 'Violation',
		2021 => 'Security Violation',
		2022 => 'Declined- Updated cardholder available',
		2023 => 'Processor does not support this feature',
		2024 => 'Card Type not enabled',
		2025 => 'Set up error- Merchant',
		2026 => 'Invalid Merchant ID',
		2027 => 'Set up error - Amount',
		2028 => 'Set Up Error - Hierarchy',
		2029 => 'Set up error- Card',
		2030 => 'Set up error- Terminal',
		2031 => 'Encryption Error',
		2032 => 'Surcharge Not Permitted',
		2033 => 'Inconsistent Data',
		2034 => 'No Action Taken',
		2035 => 'Partial Approval for amount in Group III version',
		2036 => 'Unsolicited Reversal',
		2037 => 'Already Reversed',
		2038 => 'Processor Declined',
		2039 => 'Invalid Authorization Code',
		2040 => 'Invalid Store',
		2041 => 'Declined Call for Approval',
		2043 => 'Error. Do not retry, call issuer',
		2044 => 'Declined. Call issuer',
		2045 => 'Invalid Merchant Number',
		2046 => 'Declined',
		2047 => 'Call Issuer. Pick Up Card',
		// Failure
		3000 => 'Processor network unavailable. Try Again'
	);
	
/**
 * Parse out foreign model(s) and foreign id(s) from return request into an array like: array('ModelName' => '[the_foreign_id]')
 * 
 * @return	array
 */
	public function parseForeignRelationshipsFromRequest () {
		
		if (
			!empty($this->controller->params['named']['foreign_model']) && 
			!empty($this->controller->params['named']['foreign_id'])
		) {
			$foreign_models = explode(',', $this->controller->params['named']['foreign_model']);
			$foreign_ids = explode(',', $this->controller->params['named']['foreign_id']);
		}
		
		if (empty($foreign_models) || empty($foreign_ids)) {
			return array();
		}
		
		if (count($foreign_models) < count($foreign_ids)) {
			$model_count = count($foreign_models);
			foreach ($foreign_ids as $key => $foreign_id) {
				if ($key >= $model_count) {
					unset($foreign_ids[$key]);
				}
			}
		}
		
		if (count($foreign_ids) < count($foreign_models)) {
			$foreign_id_count = count($foreign_ids);
			foreach ($foreign_models as $key => $model) {
				if ($key >= $foreign_id_count) {
					unset($foreign_models[$key]);
				}
			}
		}
		
		$return_array = array();
		foreach ($foreign_models as $key => $model) {
			$return_array[] = array(
				'foreign_model' => $foreign_models[$key],
				'foreign_id' => $foreign_ids[$key]
			);
		}
		
		return $return_array;
		
	}

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
		
		$plain_english_error = '';
		
		if (!empty($result->verification['status'])) {
			
			if ($result->verification['status'] == 'gateway_rejected') {
				
				if (!empty($result->verification['gatewayRejectionReason'])) {
					switch ($result->verification['gatewayRejectionReason']) {
						case 'avs_and_cvv':
							$plain_english_error = __('Both the billing address and CVV entered do not match those on file. Please enter the correct information before proceeding again.', true);
							break;
						case 'avs':
							$plain_english_error = __('The billing address entered does not match the one on file. Please enter the correct address information before proceeding again.', true);
							break;
						case 'cvv':
							$plain_english_error = __('The CVV entered does not match the one on file. Please enter the correct CVV before proceeding again.', true);
							break;
						default:
							break;
					}
				}
				
			} elseif (
				!empty($result->verification['processorResponseCode']) && 
				$result->verification['processorResponseCode'] >= 2000 && 
				!empty($this->_processor_response_codes[$result->verification['processorResponseCode']])
			) {
				
				$plain_english_error = $this->_processor_response_codes[$result->verification['processorResponseCode']];
				
			}
			
		}
		
		if (empty($plain_english_error)) {
			if (!empty($result)) {
				$plain_english_error = $result->message;
			} else {
				$plain_english_error = __('No response. Please try again.', true);
			}
		}
		
		$this->braintree_error = $plain_english_error;
		
		$error = 'BRAINTREE ERROR: ';
		$foreign_relationships = $this->parseForeignRelationshipsFromRequest();
		if (!empty($foreign_relationships)) {
			$count = 0;
			foreach ($foreign_relationships as $relationship) {
				if ($count > 0) {
					$error .= ', ';
				}
				$error .= $relationship['foreign_model'] . '.' . $relationship['foreign_id'];
				$count++;
			}
			$error .= ': ';
		}
		$error .= $plain_english_error;
		
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
		
		$full_address_blank = true;
		foreach (array(
			'firstName',
			'lastName',
			'company',
			'streetAddress',
			'extendedAddress',
			'locality',
			'countryCodeAlpha2',
			'countryCodeAlpha3',
			'countryCodeNumeric',
			'countryName'
		) as $key) {
			if (!empty($result->creditCard->billingAddress->{$key})) {
				$full_address_blank = false;
				break;
			}
		}
		$braintree_address = array();
		
		$this->BraintreeCreditCard->begin();
		$default_remote_sync = $this->BraintreeAddress->remote_sync;
		$this->BraintreeAddress->remote_sync = false;
		$address_saved = $this->BraintreeAddress->save(array(
			'BraintreeAddress' => array_merge(
				array(
					'id' => $result->creditCard->billingAddress->customerId . '|' . $result->creditCard->billingAddress->id,
					'braintree_customer_id' => $result->creditCard->billingAddress->customerId,
					'unique_address_identifier' => $this->BraintreeAddress->generateUniqueAddressIdentifier($result)
				),
				!$full_address_blank ? array(
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
				) : array(
					'postal_code' => $result->creditCard->billingAddress->postalCode
				)
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
		
		$foreign_relationships = $this->parseForeignRelationshipsFromRequest();
		
		if (!empty($foreign_relationships)) {
				
			foreach ($foreign_relationships as $relationship) {
				
				$this->BraintreeCreditCardRelation->create(false);
				$credit_card_relation_saved = $this->BraintreeCreditCardRelation->save(array(
					'BraintreeCreditCardRelation' => array(
						'braintree_credit_card_id' => $result->creditCard->token,
						'model' => $relationship['foreign_model'],
						'foreign_id' => $relationship['foreign_id']
					)
				));
				
				if (!$credit_card_relation_saved) {
					$this->BraintreeCreditCard->rollback();
					$this->braintree_error = $system_error;
					return false;
				}
				
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