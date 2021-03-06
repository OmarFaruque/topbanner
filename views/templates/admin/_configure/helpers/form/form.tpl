{*
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

*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
	{if $input.type == 'file_lang'}
		<div class="col-lg-9">
			{foreach from=$languages item=language}
				{if $languages|count > 1}
					<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
				{/if}
				<div class="form-group">
					<div class="col-lg-6">
						<input id="{$input.name}_{$language.id_lang}" type="file" name="{$input.name}_{$language.id_lang}" class="hide" />
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="{$input.name}_{$language.id_lang}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="{$input.name}_{$language.id_lang}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='topbanner'}
								</button>
							</span>
						</div>
					</div>
					{if $languages|count > 1}
						<div class="col-lg-2">
							<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
								{$language.iso_code}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=lang}
								<li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
								{/foreach}
							</ul>
						</div>
					{/if}
				</div>
				<div class="form-group">
					{if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang] != ''}
					<div id="{$input.name}-{$language.id_lang}-images-thumbnails" class="col-lg-12">
						<img src="{$uri}views/img/{$fields_value[$input.name][$language.id_lang]}" class="img-thumbnail"/>
					</div>
					{/if}
				</div>
				{if $languages|count > 1}
					</div>
				{/if}
				<script>
				$(document).ready(function(){
					$('#{$input.name}_{$language.id_lang}-selectbutton').click(function(e){
						$('#{$input.name}_{$language.id_lang}').trigger('click');
					});
					$('#{$input.name}_{$language.id_lang}').change(function(e){
						var val = $(this).val();
						var file = val.split(/[\\/]/);
						$('#{$input.name}_{$language.id_lang}-name').val(file[file.length-1]);
					});
				});
			</script>
			{/foreach}
			{if isset($input.desc) && !empty($input.desc)}
				<p class="help-block help-1">
					{$input.desc}
				</p>
			{/if}
		</div>
	{else if $input.type == 'add_extra'}
	<div class="col-lg-9 pull-right">
		<div class="pull-left">
			<a id="add_extra_top" href="javascript:void(0)" class="btn btn-default pull-left">Add</a>
		</div>
	</div>
	<script type="text/javascript" src="../modules/topbanner/views/js/admin/topbanner.js"></script>
	{else}
			{if $input.type=='text'}
			{if is_array($fields_value[$input.name][1]) && $fields_value[$input.name][1]|@count > 0}
				{foreach $fields_value[$input.name][1] as $k => $singT}
					{if $singT != ''}
						{if $k!=0} 
							<div class="form-group if">
							<label class="control-label col-lg-3">Slider {$k+1}</label>
						{/if}
						<div class="col-lg-9">
							<input type="text" id="{$input.name}_{$k}" name="{$input.name}[]" class="" value="{$singT|htmlspecialchars}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
							<p class="help-block help-2">{l s="Write your slider text. If you need to write html you  can write here." d="topbanner"}</p>
						</div>
						{($singT==$fields_value[$input.name][1]|end)?'':'</div>'}
						{/if}
					{/foreach}
			{elseif $fields_value[$input.name][1]|json_decode:true|@count > 0 && $fields_value[$input.name][1] && $input.type == 'text' }
				{assign var=getjsonVal value=$fields_value[$input.name][1]|json_decode:true}
				{foreach $getjsonVal as $k => $singT}
					{if $singT != ''}
						{if $k!=0} 
							<div class="form-group elseif">
							<label class="control-label col-lg-3">Slider {$k+1}</label>
						{/if}
						<div class="col-lg-9">
							<input type="text" id="{$input.name}_{$k}" name="{$input.name}[]" class="" value="{$singT|htmlspecialchars}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
							<p class="help-block help-3">{l s="Write your slider text. If you need to write html you  can write here." d="topbanner"}</p>
						</div>
						{($singT==$getjsonVal|end)?'':'</div>'}
					{/if}
				{/foreach}
			{else}
			<div class="col-lg-9">
				<input type="{$input.type}" id="{$input.name}_1" name="{$input.name}[]" class="" value="{$fields_value[$input.name][1]}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
				<p class="help-block help-4">{l s="{$input.desc}" d="topbanner"}</p>
			</div>
			{/if}
			{else}
			<div class="col-lg-9">
				<input type="{$input.type}" id="{$input.name}_1" name="{$input.name}" class="form-control" value="{$fields_value[$input.name][1]}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
				<p class="help-block help-4">{l s="{$input.desc}" d="topbanner"}</p>
			</div>
			
			{/if}
		<!--{$smarty.block.parent}-->
	{/if}
{/block}
