<?php
/**
 * Leo Advance Module
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
//if (!defined('_PS_VERSION_'))
	//exit;
	/*
	$module->context->controller->addJS(_MODULE_DIR_.$module->name.'/assets/admin/jquery-ui-1.10.3.custom.min.js');
	$module->context->controller->addCSS($module->_path.'assets/admin/style.css');
	$module->context->controller->addJS($module->_path.'assets/admin/admin.js');
	*/
	$contents = array();
	$langs = Language::getLanguages(false);
	foreach($langs as $lang){
		$contents[$lang['id_lang']] = $obj->getConfig( 'content_'.$lang['id_lang'], '', $obj->id, '', $id_shop );
	}
	$html = '
	<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<div class="left" style="width:850px;float:left;padding-right:20px;">
		<fieldset>
			<legend><img src="'._MODULE_DIR_.$module->name.'/logo.gif" alt="" title="" />'.$module->l('Custom Html Configuration').'</legend>
			
			<div class="row-form">
				'.$module->params->inputTagLang( $module->l('Title:'), 'title', $obj->title, 'title¤content','','' ).'
			</div>
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Display Tittle:'), 'display_title', $obj->getConfig( 'display_title', '', $obj->id, '', $id_shop ), 'active' ).'
			</div>
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Active:'), 'active_mod', $obj->active, 'active' ).'
			</div>
			<div class="row-form">
				'.$module->params->textAreaTag( $module->l("Content:"), 'content',  $contents, true, true, 'title¤content' ).'
			</div>
			
		</fieldset>
		</div>
		
		';
		
		$exception_html = $obj->displayModuleExceptionList($obj->file_names);
		
		$html .= '
		<div class="left" style="width:300px;float:left;">
		<fieldset>
			<legend><img src="'._MODULE_DIR_.$module->name.'/logo.gif" alt="" title="" />'.$module->l('Exceptions Page').'</legend>
			'.$exception_html.'
		</fieldset>	
		</div>
		<div class="clear space"></div>
		<br/>
		<input type="hidden" value="'.($obj->task ? $obj->task : Tools::getValue('task')).'" name="task"/>
		<input type="hidden" value="'.($obj->hook ? $obj->hook : Tools::getValue('hook')).'" name="hook"/>
		<input type="hidden" value="'.$obj->id.'" name="id_leomanagewidgets"/>
		<input type="hidden" value="'.$id_shop.'" name="id_shop"/>
		<center><input type="submit" name="submitSave" value="'.$module->l('Save').'" class="button" /></center>
	</form>';