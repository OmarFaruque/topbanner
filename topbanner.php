<?php
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
*  Product Key: 9faac2c3dd9332156636017f33a4c9c6
*/

if(!defined('_PS_VERSION_')){exit;}

class TopBanner extends Module
{
	public function __construct()
	{
		$this->name = 'topbanner';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'LaraSoft';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Top Banner');
		$this->description = $this->l('Displays a banner at the top of the shop with slider.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->module_key = '9faac2c3dd9332156636017f33a4c9c6';
	}

	public function install()
	{
		return
			parent::install() &&
			$this->registerHook('displayBanner') &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('actionObjectLanguageAddAfter') &&
			$this->installFixtures() &&
			$this->disableDevice(Context::DEVICE_MOBILE);
	}

	public function hookActionObjectLanguageAddAfter($params)
	{
		return $this->installFixture((int)$params['object']->id, Configuration::get('TOPBANNER_IMG', (int)Configuration::get('PS_LANG_DEFAULT')));
	}

	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
			$this->installFixture((int)$lang['id_lang'], 'orange.png');

		return true;
	}

	protected function installFixture($id_lang, $image = null)
	{
		$values = array();
		$values['TOPBANNER_IMG'][(int)$id_lang] = $image;
		$values['SLIDER_ITEM'][(int)$id_lang] = '';
		Configuration::updateValue('TOPBANNER_IMG', $values['TOPBANNER_IMG']);
		Configuration::updateValue('SLIDER_ITEM', $values['SLIDER_ITEM']);
	}

	public function uninstall()
	{
		Configuration::deleteByName('TOPBANNER_IMG');
		Configuration::deleteByName('SLIDER_ITEM');
		return parent::uninstall();
	}

	public function hookDisplayTop($params)
	{
		if (!$this->isCached('views/templates/front/topbanner.tpl', $this->getCacheId()))
		{
			$imgname = Configuration::get('TOPBANNER_IMG', $this->context->language->id);

			if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views/img'.DIRECTORY_SEPARATOR.$imgname))
				$this->smarty->assign('banner_img', $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'views/img/'.$imgname);

			$this->smarty->assign(array(
				'sitems' => json_decode( Configuration::get('SLIDER_ITEM', $this->context->language->id) )
			));
		}

		return $this->display(__FILE__, 'views/templates/front/topbanner.tpl', $this->getCacheId());
	}

	public function hookDisplayBanner($params)
	{
		return $this->hookDisplayTop($params);
	}

	public function hookDisplayFooter($params)
	{
		return $this->hookDisplayTop($params);
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/topbanner.css', 'all');
		$this->context->controller->addCSS($this->_path.'views/css/slick.css', 'all');
		
		$this->context->controller->addJS($this->_path.'views/js/front/slick.js', 'all');
		$this->context->controller->addJS($this->_path.'views/js/front/topbanner_front.js', 'all');
		
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;

			foreach ($languages as $lang)
			{
				if (isset($_FILES['TOPBANNER_IMG_'.$lang['id_lang']])
					&& isset($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['TOPBANNER_IMG_'.$lang['id_lang']], 4000000))
						return $error;
					else
					{
						$ext = Tools::substr($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['name'], strrpos($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['name'], '.') + 1);
						$file_name = md5($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['TOPBANNER_IMG_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext('TOPBANNER_IMG', $lang['id_lang'], Shop::getContext())
								&& Configuration::get('TOPBANNER_IMG', $lang['id_lang']) != $file_name)
								@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.Configuration::get('TOPBANNER_IMG', $lang['id_lang']));

							$values['TOPBANNER_IMG'][$lang['id_lang']] = $file_name;
						}
					}

					$update_images_values = true;
				}

				$values['SLIDER_ITEM'][$lang['id_lang']] = json_encode( Tools::getValue('SLIDER_ITEM') );
			}

			if ($update_images_values){
				Configuration::updateValue('TOPBANNER_IMG', $values['TOPBANNER_IMG']);
			}
				Configuration::updateValue('SLIDER_ITEM', $values['SLIDER_ITEM']); //$values['SLIDER_ITEM']
			
			$this->_clearCache('topbanner.tpl');
			return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		return '';
	}

	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Top banner image'),
						'name' => 'TOPBANNER_IMG',
						'desc' => $this->l('Upload an image for your top banner background. The recommended dimensions are 1170 x 35px if you are using the default theme.'),
						'lang' => true,
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Slider 1'),
						'name' => 'SLIDER_ITEM',
						'desc' => $this->l('Write your slider text. If you need to write html you  can write here.')
					), 
					array(
						'type' => 'add_extra',
						'name' => ''
						)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitStoreConf';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();

		foreach ($languages as $lang)
		{
			$fields['TOPBANNER_IMG'][$lang['id_lang']] = Tools::getValue('TOPBANNER_IMG_'.$lang['id_lang'], Configuration::get('TOPBANNER_IMG', $lang['id_lang']));
			$fields['SLIDER_ITEM'][$lang['id_lang']] = Tools::getValue('SLIDER_ITEM', Configuration::get('SLIDER_ITEM', $lang['id_lang']));

		}

		return $fields;
	}
}
