/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

jQuery(document).ready(function($){
	$('#add_extra_top').click(function(){
		$nextNumber = parseInt($(this).closest('.form-wrapper').children('.form-group').length) - parseInt(1);
		$html = '<div class="form-group">'
				+'<label class="control-label col-lg-3">'
				+'New Slider'
				+'</label>'
				+'<div class="col-lg-9">'
				+'<input type="text" id="SLIDER_ITEM_'+$nextNumber+'" name="SLIDER_ITEM[]" class="" value="" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">'
				+'<p class="help-block">'
				+'Write your slider text. If you need to write html you write here.'
				+'</p>'
				+'</div>'
				+'</div>';
			$($html).insertBefore($(this).closest('.form-group'));
	});
});