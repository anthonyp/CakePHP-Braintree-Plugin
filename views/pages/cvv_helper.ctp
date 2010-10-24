<?php
/**
 * CVV Helper View File
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
 * @subpackage braintree.views
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */
?>
<p style="margin: 0 0 5px 0"><?=__('For VISA, MasterCard and Discover, the CVV number is the last 3 digits on the Signature Panel on the back of the card.', true); ?></p> 
<?=$this->Html->image(
	'/braintree/img/cvv_helper/csn-small.gif',
	array(
		'style' => 'border: 0px; margin: 0 0 10px 0'
	)
); ?>
<p style="margin: 0 0 5px 0"><?=__('For American Express, the CID number is the four digits on the front of the card above the 15-digit account number.', true); ?></p> 
<?=$this->Html->image(
	'/braintree/img/cvv_helper/amex-cid2.gif',
	array(
		'style' => 'border: 0px; margin: 0 0 10px 0'
	)
); ?>