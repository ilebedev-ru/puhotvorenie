<?php
/**
 * Leo bootstrap menu Module
 * 
 * @version		$Id: file.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) September 2012 LeoTheme.Com <@emai:leotheme@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
 

if (!defined('_PS_VERSION_'))
	exit;
		$divLang = 'title¤cdescription¤ctype_html¤csubmenu_content_text';
		$yesNo = array(
			'0' => $this->l('No'),
			'1' => $this->l('Yes'),
		);
		$target = array(
                        "_blank" => $this->l("Blank"),
                        "_self" => $this->l("Self"),
                        "_parent" => $this->l("Parent"),
                        "_top" => $this->l("Top")
                );
		$menuType= array(
			'url' => $this->l('Url'),
			'category' => $this->l('Category'),
			'product' => $this->l('Product'),
			'manufacturer' => $this->l('Manufacturer'),
			'supplier' => $this->l('Supplier'),
			'cms' => $this->l('CMS'),
			'html' => $this->l('Html'),
		);
		$type_submenus = array(
			'menu' => $this->l('Menu'),
			'html' => $this->l('Html'),
                        'category' => $this->l('Category')
		);
                $this->_html .='
                <a target="_blank" href="http://www.leotheme.com/guides/prestashop/megamenu-module/" title="'.$this->l('Click here to read guide').'">'.$this->l('Click here to read guide').'</a>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Cache Setting').'</legend>
                                <label>'.$this->l('Use cache').'</label>
                                <div class="margin-form">
                                    <input type="radio" name="btmenu_iscache" id="btmenu_iscache_on" value="1" '.(Tools::getValue('btmenu_iscache', Configuration::get('btmenu_iscache')) ? 'checked="checked" ' : '').'/>
                                    <label class="t" for="btmenu_iscache_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled Cache').'" title="'.$this->l('Enabled').'" /></label>
                                    <input type="radio" name="btmenu_iscache" id="btmenu_iscache_off" value="0" '.(!Tools::getValue('btmenu_iscache', Configuration::get('btmenu_iscache')) ? 'checked="checked" ' : '').'/>
                                    <label class="t" for="btmenu_iscache_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled Cache').'" title="'.$this->l('Disabled').'" /></label>
                                    <p class="clear">'.$this->l('Please create folder "cache" put it in modules/leobootstrapmenu and set permision of it is 755').'</p>    
				</div>
                                <p></p>
                                <label for="btmenu_cachetime">'.$this->l('Cache Time').'</label>
                                <div class="margin-form">
                                    <input type="text" name="btmenu_cachetime" value="'.Configuration::get('btmenu_cachetime').'" />
                                </div>
				<center><input type="submit" name="savecache" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>
                <br/><br/>';
                
		$this->_html .= '<form action="'.$this->base_config_url.'" method="post" enctype="multipart/form-data">
			<input type="hidden" value="'.$obj->id.'" name="id_btmegamenu"/>
			';
		/* Save */
		$this->_html .= '
		<script type="text/javascript">
			var ajaxUrlBmenu = "'._MODULE_DIR_.$this->name.'/ajax.php";
			var base_url_bmenu = "'.$this->base_config_url.'";
			var confirm_text = "'.$this->l('Do you want delete?').'";
			jQuery(document).ready(function(){
				$(".group").each(function(){
					var id = $(this).attr("id");
					var val = $(this).val();
					$(".group-"+id).css("display","none");
					$(".group-"+id+"-"+val).show(500);
				});
				$(".group").change(function(){
					var id = $(this).attr("id");
					var val = $(this).val();
					$(".group-"+id).hide(500);
					$(".group-"+id+"-"+val).show(500);
				});
			});
		</script>';
                
                
                
                $this->_html .='
		<div class="megamenu">
			<div class="tree-megamenu">
				<h3>'.$this->l('Tree Megamenu Management').'</h3>
				'.$tree.'
				<input type="button" name="serialize" id="serialize" value="Update" /> <span class="leo_load"></span>
				<p class="note"><i>'.$this->l('To sort orders or update parent-child, you drap and drop expected menu, then click to Update button to Save').'</i></p>
			</div>
                        
			<div class="megamenu-form">';
		
		$this->_html .= '<input type="submit" name="saveMenu" value="'.$this->l('Save and New').'" class="button"> ';
		$this->_html .= '<input type="submit" name="saveMenuAndEdit" value="'.$this->l('Save And Edit').'" class="button"> ';
		$this->_html .= '<a href="'.$this->base_config_url.'"  class="button">'.$this->l('Cancel').'</a> ';
		
		$this->_html .= '
				<div class="clear space"></div>
				<label>'.$this->l('Title').'</label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="40" type="text" name="title_'.$language['id_lang'].'" value="'.htmlentities(LeoBtmegamenuHelper::getFieldValue($obj, 'title', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" style="width: 150px;" /><sup> *</sup>
					</div>';							
		$this->_html .= $this->displayFlags($this->_languages, $this->_defaultFormLanguage, $divLang, 'title', true);
		$this->_html .= '			
				</div>
				<div class="clear space"></div>
				<label>'.$this->l('Description').'</label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					$this->_html .= '
					<div id="cdescription_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea id="description_'.$language['id_lang'].'" name="description_'.$language['id_lang'].'" cols="23" row="3">'.htmlentities(LeoBtmegamenuHelper::getFieldValue($obj, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$this->_html .= $this->displayFlags($this->_languages, $this->_defaultFormLanguage, $divLang, 'cdescription', true);
		$this->_html .= '		
				</div>
				<div class="clear space"></div>
				<h3>'.$this->l('Menu Type').'</h3>
				<label>'.$this->l('Is Published').'</label>
				<div class="margin-form">
					<select name="menu_active">';
					foreach($yesNo as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->active ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
				</div>
				<label>'.$this->l('Menu link').'</label>
				<div class="margin-form">
					<select name="type" class="group" id="type">';
					foreach($menuType as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->type ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
				</div>
				<div class="group-type group-type-url">
					<label>'.$this->l('URL').'</label>
					<div class="margin-form">
						<input type="text" name="url" value="'.$obj->url.'" size="40"/>
					</div>
				</div>
				<div class="group-type group-type-category">
					<label>'.$this->l('Category').'</label>
					<div class="margin-form">
						<select name="type_category">';
						foreach($categories as $cate){
							$this->_html .= '<option value="'.$cate['id_category'].'"'.($obj->type == 'category' && $cate['id_category'] == $obj->item ? ' selected="selected"' : '').'>'.$cate['name'].'</option>';
						}
			$this->_html .= '
						</select>
					</div>
				</div>
				<div class="group-type group-type-product">
					<label>'.$this->l('Product').'</label>
					<div class="margin-form">
						<input type="text" name="type_product" value="'.$obj->item.'"/>
					</div>
				</div>
				<div class="group-type group-type-manufacturer">
					<label>'.$this->l('Manufacturers').'</label>
					<div class="margin-form">
						<select name="type_manufacturer">';
						foreach($manufacturers as $manu){
							$this->_html .= '<option value="'.$manu['id_manufacturer'].'"'.($obj->type == 'manufacturer' && $manu['id_manufacturer'] == $obj->item ? ' selected="selected"' : '').'>'.$manu['name'].'</option>';
						}
			$this->_html .= '
						</select>
					</div>
				</div>
				<div class="group-type group-type-supplier">
					<label>'.$this->l('Suppliers').'</label>
					<div class="margin-form">
						<select name="type_supplier">';
						foreach($suppliers as $sup){
							$this->_html .= '<option value="'.$sup['id_supplier'].'"'.($obj->type == 'supplier' && $sup['id_supplier'] == $obj->item ? ' selected="selected"' : '').'>'.$sup['name'].'</option>';
						}
			$this->_html .= '
						</select>
					</div>
				</div>
				<div class="group-type group-type-cms">
					<label>'.$this->l('CMS').'</label>
					<div class="margin-form">
						<select name="type_cms">';
						foreach($cmss as $cms){
							$this->_html .= '<option value="'.$cms['id_cms'].'"'.($obj->type == 'cms' && $cms['id_cms'] == $obj->item ? ' selected="selected"' : '').'>'.$cms['meta_title'].'</option>';
						}
			$this->_html .= '
						</select>
					</div>
				</div>
				<div class="group-type group-type-html">
					<label>'.$this->l('Html').'</label>
					<div class="margin-form">';
						foreach ($this->_languages as $language)
					$this->_html .= '
					<div id="ctype_html_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" id="content_text_'.$language['id_lang'].'" name="content_text_'.$language['id_lang'].'" cols="50" row="5">'.htmlentities(LeoBtmegamenuHelper::getFieldValue($obj, 'content_text', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($this->_languages, $this->_defaultFormLanguage, $divLang, 'ctype_html', true);
			$this->_html .= '
					</div>
				</div>
				<div class="clear space"></div>
				<h3>'.$this->l('Menu Params').'</h3>
				<label>'.$this->l('Parent ID').'</label>
				<div class="margin-form">
					'.$menus.'
				</div>
                                <label>'.$this->l('Target').'</label>
				<div class="margin-form">
                                <select name="target">';
                                    if($obj->target=="") $obj->target = "_self";
                                    foreach($target as $val=>$text){
                                            $this->_html .= '<option value="'.$val.'"'.($val == $obj->target ? ' selected="selected"' : '').'>'.$text.'</option>';
                                    }
			$this->_html .= '
                                </select>
                                </div>
				<label>'.$this->l('Image').'</label>
				<div class="margin-form">
					<input type="file" name="fileicon"/>
					'.($obj->image ? '<br/><img src="'._MODULE_DIR_.$this->name.'/icons/'.$obj->image.'" alt="'.$this->l('icon').'"> <input type="checkbox" name="deleteIcon" value="1"> '.$this->l('Delete') : '').'
				</div>
				<label>'.$this->l('Menu Class').'</label>
				<div class="margin-form">
					<input type="text" name="menu_class" value="'.$obj->menu_class.'"/>
				</div>
				<label>'.$this->l('Show Title').'</label>
				<div class="margin-form">
					<select name="show_title">';
					foreach($yesNo as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->show_title ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
				</div>
				<label>'.$this->l('Is Group').'</label>
				<div class="margin-form">
					<select name="is_group">';
					foreach($yesNo as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->is_group ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
				</div>
				<label>'.$this->l('Is Content').'</label>
				<div class="margin-form">
					<select name="is_content">';
					foreach($yesNo as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->is_content ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
				</div>
				<label>'.$this->l('Columns').'</label>
				<div class="margin-form">
					<input type="text" name="colums" value="'.$obj->colums.'"/>
				</div>
				<label>'.$this->l('Detail Columns Width').'</label>
				<div class="margin-form">
					<textarea name="submenu_colum_width" cols="50" rows="2">'.$obj->submenu_colum_width.'</textarea>
					<p>'.$this->l('Enter detail width of each subcols in values 1->12. Example: col1=3 col3=5').'</p>
				</div>
				<br/>
				<label>'.$this->l('Sub Menu Type').'</label>
				<div class="margin-form">
					<select name="type_submenu" id="type_submenu" class="group">';
					foreach($type_submenus as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->type_submenu ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		$this->_html .= '
					</select>
					<p>'.$this->l('If the type is Menu, so submenus of this will be showed').'</p>
				</div>
				<div class="group-type_submenu group-type_submenu-html">
					<label>'.$this->l('Html').'</label>
					<div class="margin-form">';
						foreach ($this->_languages as $language)
					$this->_html .= '
					<div id="csubmenu_content_text_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" id="submenu_content_text_'.$language['id_lang'].'" name="submenu_content_text_'.$language['id_lang'].'" cols="50" row="5">'.htmlentities(LeoBtmegamenuHelper::getFieldValue($obj, 'submenu_content_text', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($this->_languages, $this->_defaultFormLanguage, $divLang, 'csubmenu_content_text', true);
			$this->_html .= '
					</div>
				</div>';
                 //display list category
                $this->_html .= '<div class="group-type_submenu group-type_submenu-category">
                                <label>'.$this->l('Category').'</label>
                                    <div class="margin-form">';
               $helper = new Helper();
               $selected_cat = explode(",", $obj->submenu_catids);
               $this->_html .= $helper->renderCategoryTree(null,$selected_cat,"categoryBox",false,true);
                
               $this->_html .= '
                                    </div>
                                    <label>'.$this->l('Show category like tree').'</label>
                                    <div class="margin-form">
                                        <select name="is_cattree">';
               
					foreach($yesNo as $val=>$text){
						$this->_html .= '<option value="'.$val.'"'.($val == $obj->is_cattree ? ' selected="selected"' : '').'>'.$text.'</option>';
					}
		
               $this->_html .= '        </select>
                                    </div>
                                </div>';        
			
               
			
		$this->_html .= '<br /><br />';
		$this->_html .= '<input type="submit" name="saveMenu" value="'.$this->l('Save and New').'" class="button"> ';
		$this->_html .= '<input type="submit" name="saveMenuAndEdit" value="'.$this->l('Save And Edit').'" class="button"> ';
		$this->_html .= '<a href="'.$this->base_config_url.'"  class="button">'.$this->l('Cancel').'</a> ';
		
		$this->_html .= '	
			</div>
		</div>';
		
$this->_html .= '</form>';
$this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->name.'/css/admin.css');
$this->context->controller->addJS(array(
	__PS_BASE_URI__."js/tiny_mce/tiny_mce.js",
	_PS_JS_DIR_.'tinymce.inc.js'
));
// TinyMCE
global $currentIndex;
$cookie = $this->context->cookie;
	$iso = Language::getIsoById((int)($cookie->id_lang));
	$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
	$ad = dirname($_SERVER["PHP_SELF"]);
	$this->_html .= '
		<script type="text/javascript">	
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		tinySetup();
		</script>';