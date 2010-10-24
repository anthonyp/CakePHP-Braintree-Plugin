<?php
/**
 * Braintree Shell File
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
 * @subpackage braintree.shells
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */

set_time_limit(0);
require_once(dirname(dirname(dirname(__FILE__))) . DS . 'config' . DS . 'core.php');

/**
 * Braintree Shell Class
 *
 * @package    braintree
 * @subpackage braintree.shells
 */
App::import('Vendor', 'Braintree.Braintree');
class BraintreeShell extends Shell {

/**
 * Tasks
 *
 * @var string
 */
	public $tasks = array();
	
/**
 * Log warnings and errors?
 *
 * @var boolean
 */
	public $log = true;
	
/**
 * Log file name
 *
 * @var boolean
 */
	public $logfile = 'braintree_shell';
	
/**
 * Braintree environment
 *
 * @var boolean
 */
	public $environment = 'production';

/**
 * Startup
 *
 * @return void
 */
	 public function startup () {
		
		parent::startup();
		
	}

/**
 * Welcome
 *
 * @return void
 */
	public function _welcome () {
		
		$this->hr();
		$this->out('Braintree Shell');
		$this->hr();
		
	}

/**
 * Main
 *
 * @return void
 */
	 public function main () {
	 	
	 	if (!empty($this->args[0])) {
			$this->environment = $this->args[0];
		}
	 	
		$this->out('[S] Update Transaction Statuses');
		$this->out('[H] Help');
		$this->out('[Q] Quit');

		$action = strtoupper($this->in(
			__('What would you like to do?', true),
			array(
				'S', 
				'H', 
				'Q'
			)
		));

		$this->out();

		switch ($action) {
			case 'S':
				$this->update_transaction_statuses();
				break;
			case 'H':
				$this->help();
				break;
			case 'Q':
				$this->_stop();
		}
		$this->main();
		
	}

/**
 * Displays help contents
 *
 * @return	void
 */
	public function help () {
		
		$this->out("SYNOPSIS");
		$this->out("\tcake braintree <params> <command> <args>");
		$this->out('');
		$this->out("COMMANDS");
		$this->out("\tupdate_transaction_statuses");
		$this->out("\t\tLoops through all locally stored transactions with the 'authorized' or 'submittd_for_settlement' status, checks to see if the status in the API has changed, and updates the local status accordingly.");
		$this->out('');
		$this->out("\thelp");
		$this->out("\t\tShows this help message.");
		$this->out('');
		
	}

/**
 * Update Transaction Statuses
 * 
 * Loops through all locally stored transactions with the 'authorized' or 'submittd_for_settlement' status, 
 * checks to see if the status in the API has changed, and updates the local status accordingly.
 *
 * @access public
 * @return void
 */
	public function update_transaction_statuses () {
		
		$this->info('BEGIN: Update Transaction Statuses');
		
		if (!empty($this->args[0])) {
			$this->environment = $this->args[0];
		}
		
		foreach (array(
			'BraintreeTransaction',
			'BraintreeRemoteTransaction'
		) as $model_name) {
			if (!isset($this->{$model_name})) {
				$this->{$model_name} = ClassRegistry::init('Braintree.' . $model_name);
			}
		}
		
		$this->BraintreeTransaction->remote_sync = false;
		
		$transactions = $this->BraintreeTransaction->find('all', array(
			'conditions' => array(
				'BraintreeTransaction.status' => array(
					'authorized',
					'submitted_for_settlement'
				)
			),
			'order' => array(
				'BraintreeTransaction.created' => 'asc'
			),
			'contain' => array(
				'BraintreeMerchant'
			)
		));
		
		foreach ($transactions as $transaction) {
			
			if (empty($transaction['BraintreeMerchant']['id'])) {
				continue;
			}
			
			BraintreeConfig::set(array(
				'environment' => $this->environment,
				'merchantId' => $transaction['BraintreeMerchant']['id'],
				'publicKey' => $transaction['BraintreeMerchant']['braintree_public_key'],
				'privateKey' => $transaction['BraintreeMerchant']['braintree_private_key']
			));
			
			$remote_transaction = $this->BraintreeRemoteTransaction->find('first', array(
				'conditions' => array(
					'BraintreeRemoteTransaction.id' => $transaction['BraintreeTransaction']['id']
				),
				'contain' => false
			));
			
			if (
				!empty($remote_transaction['BraintreeRemoteTransaction']['status']) && 
				$transaction['BraintreeTransaction']['status'] != $remote_transaction['BraintreeRemoteTransaction']['status']
			) {
				
				$this->BraintreeTransaction->id = $transaction['BraintreeTransaction']['id'];
				if ($this->BraintreeTransaction->saveField('status', $remote_transaction['BraintreeRemoteTransaction']['status'])) {
					$this->success('Successfully updated status for BraintreeTransaction.id ' . $transaction['BraintreeTransaction']['id'] . ' to ' . $remote_transaction['BraintreeRemoteTransaction']['status']);
				} else {
					$this->error('Could not save status for BraintreeTransaction.id ' . $transaction['BraintreeTransaction']['id'] . ' to ' . $remote_transaction['BraintreeRemoteTransaction']['status'], false);
				}
				
			}
			
		}
		
		$this->info('END: Update Transaction Statuses');
		
	}
	
/**
 * Prints a message in successful green
 *
 * @return void
 */
	public function success($message = '') {

		$this->out((empty($message) ? "\t" : '') . "\033[0;32mSUCCESS\033[0;37m" . (empty($message) ? '!' : ":\t") . $message);
		
		$this->log('success', $message);

	}

/**
 * Logs a message
 *
 * @return void
 */
	public function log($type = 'info', $message = '') {

		if (!empty($this->log)) {

			$output = date('Y-m-d H:i:s') . ' ' . ucfirst($type) . ': ' . $message . "\n";
			$log = new File(LOGS . $this->logfile . '.log', true);

			if ($log->writable()) {

				return $log->append($output);

			}

		}

	}

/**
 * Prints an informational message
 *
 * @return void
 */
	public function info ($message = '') {

		$this->out("\033[0;36mINFO\033[0;37m:\t" . $message);

		$this->log('info', $message);

	}

/**
 * Prints a message
 *
 * @return void
 */
	public function out ($message = '') {

		parent::out($message);

	}

/**
 * Prints a warning and logs it
 *
 * @return void
 */
	public function warn ($message = '') {

		$this->out("\033[0;33mWARN\033[0;37m:\t" . $message);
		$this->log($message, 'warning');

	}

/**
 * Prints an error and sends it up to the shell
 *
 * @return void
 */
	public function error ($message = '', $fatal = true) {

		$this->out("\033[1;31mERROR\033[0;37m:\t" . $message . ($fatal ? "\r\n" : ''));
		$this->log($message, 'error');

		if ($fatal) {
			die;
		}

	}
	
}
?>