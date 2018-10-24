<?php
/**
 * Leo Advance Module Module
 * 
 * @version		$Id: file.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) September 2012 LeoTheme.Com <@emai:leotheme@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
 
/**
 * @since 1.5.0
 * @version 1.2 (2012-03-14)
 */
if (!defined('_PS_VERSION_'))
	exit;
	/*
	$module->context->controller->addJS(_MODULE_DIR_.$module->name.'/assets/admin/jquery-ui-1.10.3.custom.min.js');
	$module->context->controller->addCSS($module->_path.'assets/admin/style.css');
	$module->context->controller->addJS($module->_path.'assets/admin/admin.js');
	*/
	$contents = array();
	$langs = Language::getLanguages(false);
	foreach($langs as $lang){
		$contents[$lang['id_lang']] = $obj->getConfig( 'description_'.$lang['id_lang'], '', $obj->id, '', $id_shop );
	}
	$orders = array('date_add' => $module->l('Date Add'),
					'date_upd' => $module->l('Date update'),
					'name' => $module->l('Name'),
					'id_product' => $module->l('Product Id'),
					'price' => $module->l('Price')
				);
	$order_ways = array('ASC' => $module->l('Ascending'),
					'DESC' => $module->l('Descending')
				);
	$imgPath = __PS_BASE_URI__."modules/".$module->name."/img/icons/";
	
	$html = '
	<script type="text/javascript">
		jQuery(document).ready(function(){
			$("#type").change(function(){
				if($(this).val() == "category")
					$(".catids").css("display","block");
				else
					$(".catids").css("display","none");
			});
			
			if($("#type").val() == "category")
				$(".catids").css("display","block");
			else
				$(".catids").css("display","none");
			
			$(".chooseBackground").each(function(){
				var img = $(this).val();
				if(img != 0)
					$(this).parent().parent().find(".image").html(\'<img src="'.$imgPath.'\' + img + \'" alt="\'+img+\'"/>\');
			});		
			$(".chooseBackground").change(function(){
				var img = $(this).val();
				if(img != 0)
					$(this).parent().parent().find(".image").html(\'<img src="'.$imgPath.'\' + img + \'" alt="\'+img+\'"/>\');
			});
			
		});
	</script> 
	<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<center><input type="submit" name="submitSave" value="'.$module->l('Save').'" class="button" /></center>
		<br/>
		<div class="left" style="width:850px;float:left;padding-right:20px;">
		<fieldset>
			<legend><img src="'._MODULE_DIR_.$module->name.'/logo.gif" alt="" title="" />'.$module->l('Carousel Settings').'</legend>
		';
		$html .= '
			<div class="row-form">
				'.$module->params->inputTagLang( $module->l('Title:'), 'title', $obj->title, 'title¤description','<sup>*</sup>','' ).'
			</div>';
		$html .= '
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Display Tittle:'), 'display_title', $obj->getConfig( 'display_title', '', $obj->id, '', $id_shop ), 'display_title' ).'
			</div>';
		$html .= '
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Active:'), 'active_mod', $obj->active, 'active' ).'
			</div>';
			
		$types = array('featured'=>$module->l('Featured Products'),'special'=>$module->l('Special Products'),'new'=>$module->l('New Products'),'bestseller'=>$module->l('Bestseller Products'),'category'=>$module->l('Category Products') );
		$html .= '
			<div class="row-form">
				'.$module->params->selectTag($types, $module->l('Type'), 'type', $obj->getConfig('type', '', $obj->id, '', $id_shop)).'
			</div>';
		
		$html .= '
			<div class="row-form catids">
				'.$module->params->categoryTag('catids', $obj->getConfig('catids', '', $obj->id, '', $id_shop), $module->l('Categories'), ' size="10" multiple="multiple"').'
			</div>
			
			<div class="row-form">
				'.$module->params->selectTag( $orders, $module->l("Order By"), 'porder',  $obj->getConfig('porder', '', $obj->id, '', $id_shop),'<p class="clear">'.$module->l('The order in which products are displayed in the product list.').'</p>' ).'
			</div>
			<div class="row-form">
				'.$module->params->selectTag( $order_ways, $module->l("Order Method"), 'way',  $obj->getConfig('way', '', $obj->id, '', $id_shop),'<p class="clear">'.$module->l('Default order method for product list.').'</p>' ).'
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Items Per Page'), 'itemspage', $obj->getConfig('itemspage', '', $obj->id, 3, $id_shop),'<sup>*</sup><p class="clear">'.$module->l('The maximum number of products in each page tab (default: 3).').'</p>' ).'
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Colums In Tab'), 'columns', $obj->getConfig('columns', '', $obj->id, 3, $id_shop),'<sup>*</sup><p class="clear">'.$module->l('The maximum column products in each page tab (default: 3).').'</p>' ).'
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Items In Tab'), 'itemstab', $obj->getConfig('itemstab', '', $obj->id, 6, $id_shop), '<sup>*</sup><p class="clear">'.$module->l('The maximum number of products in each tab (default: 6).').'</p>' ).'
			</div>
			<div class="row-form">
				'.$module->params->textAreaTag( $module->l("Description:"), 'description',  $contents, true, true, 'title¤description' ).'
			</div>
			
			<input type="hidden" value="'.($obj->task ? $obj->task : Tools::getValue('task')).'" name="task"/>
			<input type="hidden" value="'.($obj->hook ? $obj->hook : Tools::getValue('hook')).'" name="hook"/>
			<input type="hidden" value="'.$obj->id.'" name="id_leomanagewidgets"/>
			<input type="hidden" value="'.(int)$id_shop.'" name="id_shop"/>
		</fieldset>
		<br/>
		</div>
		';
		
		$exception_html = $obj->displayModuleExceptionList($obj->file_names);
			
		$html .= '
		<br/>
		<div class="left" style="width:300px;float:left;">
		<fieldset>
			<legend><img src="'._MODULE_DIR_.$module->name.'/logo.gif" alt="" title="" />'.$module->l('Exceptions Page').'</legend>
			'.$exception_html.'
		</fieldset>	
		</div>
		<div class="clear space"></div>
		<br/>
		<center><input type="submit" name="submitSave" value="'.$module->l('Save').'" class="button" /></center>
		<br/>
		<br/>
		<br/>
	</form>';