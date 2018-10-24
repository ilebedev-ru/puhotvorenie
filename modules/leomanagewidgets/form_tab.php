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
	
	$ordering = $obj->getConfig('ordering', '', $obj->id, '', $id_shop );
	if(!$ordering)
		$ordering = array('featured'=>0,'special'=>1,'new'=>2,'bestseller'=>3,'category'=>4 );
	asort($ordering);
	
	$html = '
		<script type="text/javascript">
	
			var bgCatUrl = "'.__PS_BASE_URI__.'/modules'.$module->name.'/img/icons/'.'";
		function addBackGround(){
				var catName = $("#params_leocatlist option:selected").text();
			catName = catName.replace(/-/g,"");catName = catName.replace(/\|/g,"");
			
			var catID   = $("#params_leocatlist").val();
			if($("#row-"+catID).length){alert("'.$module->l("This element exists").'");$("#row-"+catID).css("background-color","yellow");return false;}
			var imgName = $("#tabBackground").val();
			var tdID    = \'<td>\'+catID+\'</td>\';
			var tdCat   = \'<td><input type="hidden" value="\'+catID+\'#@#\'+imgName+\'" name="leocblist[]"/>\'+catName+\'</td>\';
			var tdImg   = \'<td><img src="'.$imgPath.'\'+imgName+\'" stype="height:15px"/></td>\';
			var tdAct   = \'<td><a href="javascript:deleteCBG(\'+catID+\');"><img src="../img/admin/delete.gif"/></a></td>\';
			
			$("#bgCatList").append(\'<tr id="row-\'+catID+\'">\'+tdID+tdCat+tdImg+tdAct+\'</tr>\');
			$("#bgCatList tr").css("background-color","#FFF");
			$("#row-"+catID).css("background-color","#e1f2fa");
			
			return false;
		}
		function deleteCBG(catID){
			$("#row-"+catID).remove();
		}
		jQuery(document).ready(function(){
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
			<legend><img src="'._MODULE_DIR_.$module->name.'/logo.gif" alt="" title="" />'.$module->l('Tab Settings').'</legend>';
		$html .= '
			<div class="row-form">
				'.$module->params->inputTagLang( $module->l('Title:'), 'title', $obj->title, 'title¤description','<sup>*</sup>','' ).'
			</div>';
		$html .= '
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Display Tittle:'), 'display_title', $obj->getConfig( 'display_title', '', $obj->id, '', $id_shop  ), 'display_title' ).'
			</div>';
		$html .= '
			<div class="row-form">
				'.$module->params->statusTag( $module->l('Active:'), 'active_mod', $obj->active, 'active' ).'
			</div>';
			
		$html .= '	
		<ul id="sortable">';
		
		foreach($ordering as $key => $val){
			if($key == 'category'){
				$html .= '
				<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<div class="row-form">
					<input type="hidden" class="ordering" value="'.$val.'" name="ordering[category]"/>
					'.$module->params->statusTag( $module->l('Show Category'), 'show_category', $obj->getConfig('show_category', '', $obj->id, '', $id_shop ), 'show_category' ).'
					'.$module->params->categoryTag('catids', $obj->getConfig('catids', '', $obj->id, '', $id_shop ), 'Categories', ' size="10" multiple="multiple"').'
					<p class="clear">'.$module->l('The category will show in tab.').'</p>
				</div>
				</li>';
			} elseif ($key == 'featured') {
                $html .= '
				<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<div class="row-form">
					<input type="hidden" class="ordering" value="'.$val.'" name="ordering['.$key.']"/>
					'.$module->params->statusTag( $module->l( ucfirst($key).' Product'), 'show_'.$key, $obj->getConfig('show_'.$key, '', $obj->id, '', $id_shop ), 'show_'.$key ).'
					'.$module->params->getBackgroundList(_PS_MODULE_DIR_.$module->name.'/img/icons/', 'class="chooseBackground"', 'tabBackground'.ucfirst($key), false, 'img_'.$key, $obj->getConfig('img_'.$key, '', $obj->id, '', $id_shop )).'
					'.$module->params->inputTag('id_category', 'id_category', $obj->getConfig('id_category')) .'
					<div class="margin-form image"></div>
				</div>
				</li>';
            } else{
			$html .= '
				<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<div class="row-form">
					<input type="hidden" class="ordering" value="'.$val.'" name="ordering['.$key.']"/>
					'.$module->params->statusTag( $module->l( ucfirst($key).' Product'), 'show_'.$key, $obj->getConfig('show_'.$key, '', $obj->id, '', $id_shop ), 'show_'.$key ).'
					'.$module->params->getBackgroundList(_PS_MODULE_DIR_.$module->name.'/img/icons/', 'class="chooseBackground"', 'tabBackground'.ucfirst($key), false, 'img_'.$key, $obj->getConfig('img_'.$key, '', $obj->id, '', $id_shop )).'
					<div class="margin-form image"></div>
				</div>
				</li>';
			}
		}
		$html .= '
		</ul>
			<div class="row-form">
				<hr/>
			</div>
			<div class="row-form">
				
				'.$module->params->categoryTag('leocatlist', 1, 'category', 'style="display:inline"').'
				'.$module->params->getBackgroundList(_PS_MODULE_DIR_.$module->name.'/img/icons/', 'style="display:inline"').'
				<p class="clear">'.$module->l('Please do not delete default.png file in folder img/icons and put image size is 30px*30px').'</p>
			</div>
			
			<div class="row-form">	
				<table class="table order" id="bgCatList" style="width:50%;">
					<tr><th>'.$module->l("ID").'</th><th style="width:40%">'.$module->l("Category").'</th><th style="width:40%">'.$module->l("Image").'</th><th style="width:20%">'.$module->l("Action").'</th></tr>
				'.$module->params->listCategoryImage($obj->getConfig('leocblist', '', $obj->id, '', $id_shop ),__PS_BASE_URI__."modules/".$module->name."/img/icons/").'
				</table>
				<p class="clear">'.$module->l('Please select image for category tab. If you do not set image, the system will auto get default image (default.png)').'</p>
			</div>
			
			<div class="row-form">
					
				<hr style="width:100%;color:#000"/>
			</div>
			
			<div class="row-form">
				'.$module->params->selectTag( $orders, $module->l("Order By"), 'porder',  $obj->getConfig('porder', '', $obj->id, '', $id_shop ) ).'
				<p class="clear">'.$module->l('The order in which products are displayed in the product list.').'</p>
			</div>
			<div class="row-form">
				'.$module->params->selectTag( $order_ways, $module->l("Order Method"), 'way',  $obj->getConfig('way', '', $obj->id, '', $id_shop ) ).'
				<p class="clear">'.$module->l('Default order method for product list.').'</p>
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Items Per Page'), 'itemspage', $obj->getConfig('itemspage', '', $obj->id, 3, $id_shop ) ).'
				<p class="clear">'.$module->l('The maximum number of products in each page tab (default: 3).').'</p>
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Colums In Tab'), 'columns', $obj->getConfig('columns', '', $obj->id, 3, $id_shop ) ).'
				<p class="clear">'.$module->l('The maximum column products in each page tab (default: 3).').'</p>
			</div>
			<div class="row-form">
				'.$module->params->inputTag( $module->l('Items In Tab'), 'itemstab', $obj->getConfig('itemstab', '', $obj->id, 6, $id_shop ) ).'
				<p class="clear">'.$module->l('The maximum number of products in each tab (default: 6).').'</p>
			</div>
			
			<input type="hidden" value="'.($obj->task ? $obj->task : Tools::getValue('task')).'" name="task"/>
			<input type="hidden" value="'.($obj->hook ? $obj->hook : Tools::getValue('hook')).'" name="hook"/>
			<input type="hidden" value="'.$obj->id.'" name="id_leomanagewidgets"/>
			<input type="hidden" value="'.$id_shop.'" name="id_shop"/>
		</fieldset>
		<br/>
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
		<center><input type="submit" name="submitSave" value="'.$module->l('Save').'" class="button" /></center>
		<br/>
		<br/>
		<br/>
	</form>';