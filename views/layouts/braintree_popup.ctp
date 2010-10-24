<?php
/**
 * Braintree Popup Layout File
 *
 * Used for informational popups
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
 * @subpackage braintree.views.layouts
 * @copyright  2010 Anthony Putignano <anthonyp@xonatek.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/anthonyp/braintree
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
	
	<head>
		<?=$this->Html->charset();?>
		<title><?=$title_for_layout; ?></title>
	</head>
	
	<body style="font-family: arial; font-size: 12px; color: black; margin: 0; padding: 10px;">
	
		<?=$content_for_layout; ?>
	
	</body>
	
</html>