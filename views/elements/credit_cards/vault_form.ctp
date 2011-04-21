<?php
/**
 * Vault Form Element File
 *
 * This element requires the BraintreeTransparentRedirect helper to be loaded
 *
 * Possible options:
 * 	- 'braintree_merchant_id' String		required	The Braintree Merchant ID
 *  - 'braintree_customer_id' String		required	The Customer ID the vaulted credit card should be associated with
 *  - 'billing_address' Array				required	Only requried if 'billing_postal_code_auth' is disabled.
 *  													An array with the following billing address keys:
 *  														'first_name'
 *															'last_name'
 *															'company'
 *															'address1'
 *															'address2'
 *															'city'
 *															'state'
 *															'postal_code'
 *															'country_code_alpha_2'
 *	- 'billing_postal_code_auth' Boolean	optional	If set to true, a postal code field will be displayed, and will be used in lieu of passed billing address information
 *  - 'verify_credit_card' Boolean			optional	Specifies whether the credit card is verified at the time of vault entry (vs. at the time of transaction). If false, CVV will not be asked for, as it cannot be stored for future verification. Defaults to true.
 *  - 'form_options' Array					optional	Options to pass to Form::create()
 *  - 'callback_url' Mixed					optional	Array- or String- based URL to redirect Braintree callback to. Defaults to current action.
 *  - 'foreign_model' String				optional	The foreign model or comma-separated list of foreign models the vaulted credit card should be associated with
 *  - 'foreign_id' String					optional	The foreign ID or comma-separated list of foreign IDs the vaulted credit card should be associated with
 *  - 'before_copy' String					optional	Copy to display before the credit card form
 *  - 'after_copy' String					optional	Copy to display after the credit card form
 *  - 'submit_label' String					optional	Label to use for submit button. Default to 'Submit'
 *  - 'stylesheet' String					optional	Path to custom stylesheet for form
 *  - 'braintree_badge' String				optional	The Braintree badge number to display. Defaults to '08'. Set to false to not display a badge
 *  - 'field_*_options' Array	optional	* is a wildcard for any form field name. Use this option to set/override options for individual form fields
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
 * @subpackage braintree.views.elements
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */
?>

<?=$this->Html->css(!empty($stylesheet) ? $stylesheet : '/braintree/css/credit_card_form'); ?>
<?=$this->Html->script('/braintree/js/strtotime'); ?>

<?=$this->Form->create(
	null,
	array_merge(
		array(
			'type' => false,
			'url' => $this->BraintreeTransparentRedirect->url(),
			'id' => 'credit-card-form',
			'onsubmit' => 'return braintree_check_cc_form();'
		),
		!empty($form_options) ? $form_options : array()
	)
); ?>

	<input type="hidden" name="tr_data" value="<?
	
	if (empty($callback_url)) {
		$callback_url = array(
			'controller' => $this->params['controller'],
			'action' => $this->params['action'],
			'plugin' => $this->params['plugin']
		);
		if (!empty($this->params['pass'])) {
			$callback_url = array_merge($callback_url, $this->params['pass']);
		}
		if (!empty($this->params['named'])) {
			$callback_url = array_merge($callback_url, $this->params['named']);
		}
	}
	if (!empty($foreign_model)) {
		$callback_url['foreign_model'] = $foreign_model;
	}
	if (!empty($foreign_id)) {
		$callback_url['foreign_id'] = $foreign_id;
	}
	
	echo htmlentities($this->BraintreeTransparentRedirect->createCreditCardData(array(
		'redirectUrl' => Router::url(
			$callback_url,
			true
		),
		'creditCard' => array_merge(
			array(
				'customerId' => $braintree_customer_id,
				'token' => String::uuid(),
				'options' => array(
					'verifyCard' => (!isset($verify_credit_card) || $verify_credit_card==true) ? true : false,
					'makeDefault' => true
				)
			),
			empty($billing_postal_code_auth) ? array(
				'billingAddress' => array(
					'firstName' => $billing_address['first_name'],
					'lastName' => $billing_address['last_name'],
					'company' => $billing_address['company'],
					'streetAddress' => $billing_address['address1'],
					'extendedAddress' => $billing_address['address2'],
					'locality' => $billing_address['city'],
					'region' => $billing_address['state'],
					'postalCode' => $billing_address['postal_code'],
					'countryCodeAlpha2' => $billing_address['country_code_alpha_2']
				)
			) : array()
		)
	))); ?>" />

	<?
	if (!empty($before_copy)) {
		echo $before_copy;
	}
	?>
	
	<fieldset>
	
		<?
		if (!isset($braintree_badge)) {
			$braintree_badge = '10';
		}
		if ($braintree_badge !== false) {
		?>
			<a href="https://www.braintreegateway.com/merchants/<?=$braintree_merchant_id; ?>/verified" target="_blank"><img src="https://braintree-badges.s3.amazonaws.com/<?=$braintree_badge; ?>.png" border="0" align="right" /></a>
		<? } ?>
	
		<?=$this->Form->input(
			'cardholder_name',
			array_merge(
				array(
					'label' => __('Cardholder Name', true),
					'name' => 'credit_card[cardholder_name]',
					'id' => 'credit-card-form-cardholder-name',
					'maxLength' => 64
				),
				!empty($field_cardholder_name_options) ? $field_cardholder_name_options : array()
			)
		); ?>
		
		<?=$this->Html->scriptBlock(
			'
			function braintree_check_cc_number(cc_num) {
				cc_num.value = cc_num.value.replace(/\D+/g, "");
			}
			'
		); ?>
		
		<?=$this->Form->input(
			'number',
			array_merge(
				array(
					'label' => __('Card Number', true),
					'name' => 'credit_card[number]',
					'id' => 'credit-card-form-number',
					'maxLength' => 16,
					'onkeyup' => "braintree_check_cc_number(this)"
				),
				!empty($field_number_options) ? $field_number_options : array()
			)
		); ?>
		
		<?
		if (isset($verify_credit_card) && $verify_credit_card==true) {
			echo $this->Form->input(
				'cvv',
				array_merge(
					array(
						'label' => __('CVV/CID', true) . ' <span id="credit-card-form-cvv-helper">[' . $this->Html->link(
							__('What is this?', true),
							array(
								'plugin' => 'braintree',
								'controller' => 'pages',
								'action' => 'cvv_helper'
							),
							array(
								'onclick' => "window.open('" . Router::url(array(
									'plugin' => 'braintree',
									'controller' => 'pages',
									'action' => 'cvv_helper'
								)) . "', 'PopUp', 'width=400, height=500, resizable=yes, scrollbars=yes, menubar=no, toolbar=no, left=150, top=175, screenX=150, screenY=175'); return false;"
							)
						) . ']</span>',
						'name' => 'credit_card[cvv]',
						'id' => 'credit-card-form-cvv',
						'class' => 'credit-card-form-small-field',
						'maxLength' => 4
					),
					!empty($field_cvv_options) ? $field_cvv_options : array()
				)
			);
		}
		?>
		
		<div class="select">
		
			<label><?=__('Expiration Date', true); ?></label>
			
			<?
			$field_expiration_month_options = array_merge(
				array(
					'name' => 'credit_card[expiration_month]', 
					'id' => 'credit-card-form-expiration-month',
					'empty' => false, 
					'order' => 'asc',
					'default' => date('m', time())
				),
				!empty($field_expiration_month_options) ? $field_expiration_month_options : array()
			);
			echo $this->Form->month(
				'expiration_month',
				$field_expiration_month_options['default'],
				$field_expiration_month_options
			);
			?>
			&nbsp;
			<?
			$field_expiration_year_options = array_merge(
				array(
					'name' => 'credit_card[expiration_year]', 
					'id' => 'credit-card-form-expiration-year',
					'empty' => false, 
					'orderYear' => 'asc',
					'default' => date('Y', time()),
					'minYear' => date('Y', time()),
					'maxYear' => (date('Y', time())+10)
				),
				!empty($field_expiration_year_options) ? $field_expiration_year_options : array()
			);
			echo $this->Form->year(
				'expiration_year', 
				$field_expiration_year_options['minYear'],
				$field_expiration_year_options['maxYear'],
				$field_expiration_year_options['default'],
				$field_expiration_year_options
			);
			?>
		
		</div>
		
		<?
		if (!empty($billing_postal_code_auth)) {
			echo $this->Form->input(
				'postal_code',
				array_merge(
					array(
						'label' => __('Billing Postal Code', true),
						'name' => 'credit_card[billing_address][postal_code]',
						'id' => 'credit-card-form-postal-code',
						'class' => 'credit-card-form-small-field',
						'maxLength' => 9,
						'onkeyup' => "braintree_check_cc_number(this)"
					),
					!empty($field_postal_code_options) ? $field_postal_code_options : array()
				)
			);
		}
		?>
	
	</fieldset>
	
	<?=$this->Form->submit(
		!empty($submit_label) ? $submit_label : 'Submit', 
		array_merge(
			array(
				'id' => 'credit-card-form-submit'
			),
			!empty($field_submit_options) ? $field_submit_options : array()
		)
	); ?>

<?=$this->Form->end(); ?>

<?=$this->Html->scriptBlock(
	'
	function braintree_check_cc_form () {
	
		cc_month = document.getElementById("' . $field_expiration_month_options['id'] . '").options[document.getElementById("' . $field_expiration_month_options['id'] . '").options.selectedIndex].value;
		cc_year = document.getElementById("' . $field_expiration_year_options['id'] . '").options[document.getElementById("' . $field_expiration_year_options['id'] . '").options.selectedIndex].value;
		
		var current_timestamp = strtotime("' . date('n/1/Y') . '");
		var cc_expiration_timestamp = strtotime(cc_month + "/1/" + cc_year);
		
		if (cc_expiration_timestamp < current_timestamp) {
			alert("' . __('Expiration date must not be in the past.', true) . '");
			return false;
		} else {
			return true;
		}
		
	}
	'
); ?>