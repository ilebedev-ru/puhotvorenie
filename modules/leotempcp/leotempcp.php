<?php
/**
 * $ModDesc
 * 
 * @version		$Id: file.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) Jan 2012 leotheme.com <@emai:leotheme@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
 
if (!defined('_CAN_LOAD_FILES_'))
	exit;
class Leotempcp extends Module
{

	var $themeInfo = array();
	var $themePrefix = '';
	var $prefix = '';
	var $fonts = array();
	var $amounts = 4;
	var $base_config_url = '';
	var $overrideHooks  = array();
	function __construct( $createOnly=false )
	{
		if( !$createOnly ) {
			global $currentIndex;
			$this->name = 'leotempcp';
			$this->tab = 'Home';
			$this->version = '2.0';
			$this->author = 'leotheme';
			
			parent::__construct();
			
			$this->displayName = $this->l('Leo Theme Control Panel');
			$this->description = $this->l('change theme color');
			$this->confirmUninstall = $this->l('Are you sure you want to unistall Theme Skins?');
			
				/* merging addition configuration from current theme */
			$theme_dir = Context::getContext()->shop->getTheme();
			if(  file_exists(_PS_ALL_THEMES_DIR_.$theme_dir."/info/info.php") ){
				require( _PS_ALL_THEMES_DIR_.$theme_dir."/info/info.php" );
			}
			
			$this->themeInfo   = $this->getInfo();
			
			$this->themePrefix  = Context::getContext()->shop->getTheme();
			$this->prefix = 'leocp_';
			$this->_fonts();
			$this->amounts = 4;
			$this->base_config_url = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');	
		}
	}

	private function _installTradDone() {
		require_once( dirname(__FILE__)."/sql/sql.tables.php" );
	 	$error=true;
		if( isset($query) && !empty($query) ){
			if(  !($data=Db::getInstance()->ExecuteS( "SHOW TABLES LIKE '"._DB_PREFIX_."leohook'" )) ){
				$query = str_replace( "_DB_PREFIX_", _DB_PREFIX_, $query );
				$query = str_replace( "_MYSQL_ENGINE_", _MYSQL_ENGINE_, $query );
				$db_data_settings = preg_split("/;\s*[\r\n]+/",$query);
				foreach ($db_data_settings as $query){
					$query = trim($query);
					if (!empty($query))	{
						if (!Db::getInstance()->Execute($query)){
							 $error = false;
						}
					}
				}
			}
		} 
		return $error;
	}
	
	public function install()
	{
		if (!parent::install()
				OR !$this->registerHook('header')
				OR !$this->registerHook('actionShopDataDuplication')
				OR !$this->_installTradDone()
				OR !$this->_installHook()
				OR Configuration::updateValue('DISPLAY_THMSKINSBLACK', 1) == false
			)
			return false;
	    $this->installModuleTab('LeoTheme Position Control Panel', 'panel', 'AdminParentModules');
		$this->_installConfig();
		Configuration::updateValue('leocp_enable_font', '1', true);
		Configuration::updateValue('leorespon', '1', true);		
		return true;
	}
	 public function uninstall() {
        if (!parent::uninstall())
		  return false;
	
		 $this->uninstallModuleTab("panel"); 
		 
		 return true;
	}	
	private function _installHook(){
		$hookspos = array(
				'displayTop',
				'displayHeaderRight',
				'displaySlideshow',
				'topNavigation',
				'displayPromoteTop',
				'displayRightColumn',
				'displayLeftColumn',
				'displayHome',
				'displayFooter',
				'displayBottom',
				'displayContentBottom',
				'displayFootNav'
			); 
		foreach( $hookspos as $hook ){
			if( Hook::getIdByName($hook) ){
				
			} else {
				$new_hook = new Hook();
				$new_hook->name = pSQL($hook);
				$new_hook->title = pSQL($hook);
				$new_hook->add();
				$id_hook = $new_hook->id;
			}
		}
		return true;
	}	
	private function uninstallModuleTab($class_sfx = '') {
        $tabClass = 'Admin' . ucfirst($this->name) . ucfirst($class_sfx);
 
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }
	
	  private function installModuleTab($title, $class_sfx = '', $parent = '') {
        $class = 'Admin' . ucfirst($this->name) . ucfirst($class_sfx);
        @copy(_PS_MODULE_DIR_ . $this->name . '/logo.gif', _PS_IMG_DIR_ . 't/' . $class . '.gif');
        if ($parent == '') {
            $position = Tab::getCurrentTabId();
        } else {
            $position = Tab::getIdFromClassName($parent);
        }
        
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = intval($position);
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $title;
        }
        $id_tab1 = $tab1->add(true, false);
    }

	private function _fonts(){
		$this->fonts = array(
			'Verdana, Geneva, sans-serif' => $this->l('Verdana'),
			'Georgia, \'Times New Roman\', Times, serif' => $this->l('Georgia'),
			'Arial, Helvetica, sans-serif' => $this->l('Arial'),
			'Impact, Arial, Helvetica, sans-serif' => $this->l('Impact'),
			'Tahoma, Geneva, sans-serif' => $this->l('Tahoma'),
			'\'Trebuchet MS\', Arial, Helvetica, sans-serif' => $this->l('Trebuchet MS'),
			'\'Arial Black\', Gadget, sans-serif' => $this->l('Arial Black'),
			'Times, \'Times New Roman\', serif' => $this->l('Times'),
			'\'Palatino Linotype\', \'Book Antiqua\', Palatino, serif' => $this->l('Palatino Linotype'),
			'\'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif' => $this->l('Lucida Sans Unicode'),
			'\'MS Serif\', \'New York\', serif' => $this->l('MS Serif'),
			'\'Comic Sans MS\', cursive' => $this->l('Comic Sans MS'),
			'\'Courier New\', Courier, monospace' => $this->l('Courier New'),
			'\'Lucida Console\', Monaco, monospace' => $this->l('Lucida Console')
		);
	}
	private function _installConfig(){
		$configs = array(
			'font_type1' => 'standard',
			'standard_font1' => 'Arial, Helvetica, sans-serif',
			'google_link1' => '',
			'google_font1' => '',
			'selector1' => '',
			
			'font_type2' => 'google',
			'standard_font2' => 'Arial, Helvetica, sans-serif',
			'google_link2' => 'https://fonts.googleapis.com/css?family=Oswald:700',
			'google_font2' => 'Oswald',
			'selector2' => '.highlight-image h3,.sidebar .title_block, #lofadva-pos-1 .title_block, .slide-info h2',
			
			'font_type3' => 'standard',
			'standard_font3' => 'Arial, Helvetica, sans-serif',
			'google_link3' => '',
			'google_font3' => '',
			'selector3' => '',
			
			'font_type4' => 'standard',
			'standard_font4' => 'Arial, Helvetica, sans-serif',
			'google_link4' => '',
			'google_font4' => '',
			'selector4' => '',
		);
		
		foreach($configs as $key => $val){
			Configuration::updateValue($this->prefix.$key, $val, true);
		}
		Configuration::updateValue( 'leocopyright','Copyright 2013 Powered by PrestaShop. All Rights Reserved');
		
		return true;
	}
	
	function getContent()
	{
		$errors = array();
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$variables = array(
			'enable_font' => '',
		);
		for($i = 1; $i <= $this->amounts; $i++){
			$variables['font_type'.$i] = '';
			$variables['standard_font'.$i] = '';
			$variables['google_link'.$i] = '';
			$variables['google_font'.$i] = '';
			$variables['selector'.$i] = '';
		}
		if (Tools::isSubmit('submitUpdate')) {
			foreach($variables as $k=>$v){
				Configuration::updateValue($this->prefix.$k, Tools::getValue($k));
			}
			$leoskin = (Tools::getValue('leoskin')); 
			Configuration::updateValue('leoskin', $leoskin);
			$leopntool = (Tools::getValue('leopntool')); 
			Configuration::updateValue('leopntool', $leopntool);
			$leorespon = (Tools::getValue('leorespon')); 
			Configuration::updateValue('leorespon', $leorespon);
			$leofontsize = (Tools::getValue('leofontsize')); 
			Configuration::updateValue('leofontsize', $leofontsize);
			
			
		 	$templatewidth = (Tools::getValue('templatewidth')); 
			Configuration::updateValue('templatewidth', $templatewidth);
			$productlistcols = (int)(Tools::getValue('productlistcols')); 
			Configuration::updateValue('productlistcols', $productlistcols);
			$leolayout = (Tools::getValue('leolayout')); 
			Configuration::updateValue('leolayout', $leolayout);
			$leocopyright = (Tools::getValue('leocopyright')); 
			Configuration::updateValue('leocopyright', $leocopyright);
			
			LeoThemeInfo::onUpdateConfig();
			$forbidden = array('submitUpdate');
			
			foreach ($_POST AS $key => $value){
				if (!Validate::isCleanHtml(Tools::getValue($key))){
					$this->_html .= $this->displayError($this->l('Invalid html field, javascript is forbidden'));
					$this->_displayForm();
					return $this->_html;
				}
			}
			$this->_html .= '<div class="conf confirm">'.$this->l('Settings updated successful').'</div>';
		}elseif(Tools::isSubmit('submitExport')){
			$json_data = array();
			for($i = 1; $i <= $this->amounts; $i++){
				$json_data[$i] = array(
					'font_type' => Tools::getValue('font_type'.$i),
					'standard_font' => Tools::getValue('standard_font'.$i),
					'google_link' => Tools::getValue('google_link'.$i),
					'google_font' => Tools::getValue('google_font'.$i),
					'selector' => Tools::getValue('selector'.$i),
				);
			}
			$data = json_encode($json_data);
			if(!is_dir(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/'))
				mkdir(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/', 0777);
			
			$filePath = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/'.date('Y-m-d_H-i-s').'.txt';
			$fp = @fopen($filePath, 'w');
			@fwrite($fp, $data);
			@fclose($fp);
			$this->_html .= '<div class="conf confirm">'.$this->l('Export data sucessful').'</div>';
		}elseif(Tools::isSubmit('submitDownload')){
			if(version_compare(_PS_VERSION_, '1.5.0', '>=')){
				$filename = urldecode(Tools::getValue('font_load'));
				header('Content-type: text/plain');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				
				readfile(_PS_THEME_DIR_.'fonts/'.$filename);
				exit;
			}else{
				$filename = urldecode(Tools::getValue('font_load'));
				$file = _PS_THEME_DIR_.'fonts/'.$filename;
				header('Content-Description: File Transfer');
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			}
		}elseif(Tools::isSubmit('submitImport')){
			$file = $_FILES['font_file'];
			if($file && $file['name'] && $file['type'] == 'text/plain'){
				if(!is_dir(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/'))
					mkdir(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/', 0777);
				
				if(!move_uploaded_file($file["tmp_name"], _PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/' . $file["name"]))
					$errors[] = $this->l('Move file is error.');
				else
					$this->_html .= '<div class="conf confirm">'.$this->l('Import file successfull.').'</div>';
			}else{
				$errors[] = $this->l('File is invalid.');
			}
		}elseif(Tools::isSubmit('submitDeleteFont')){
			$filename = urldecode(Tools::getValue('font_load'));
			@unlink(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/' . $filename);
			$this->_html .= '<div class="conf confirm">'.$this->l('Delete file.').' '.$filename.' '.$this->l('successfull').'</div>';
		}elseif(Tools::isSubmit('submitsample')){
                    include_once(_PS_MODULE_DIR_ . 'leotempcp/DataSample.php');
                    $dataSample = new Datasample();
                    
                    $action = Tools::getValue('action');
                    if($action == "themesql"){
                        $result = $dataSample->exportThemeSql();
                        if($result=="OK"){
                            $this->_html .= '<div class="conf confirm">'.$this->l('Export theme.sql was successful').'</div>';
                        }else if($result=="ERROR_WRITE_FILE"){
                            $this->_html .= '<div class="error">'.$this->l('Unable to create backup file of. Please set permission 755 for module/leotempcp/sample').'</div>';
                        }else{
                            $this->_html .= '<div class="error">'.$this->l('An error occurred while backing up. Unable to obtain the schema of'). ' ' . $result .'</div>';
                        }
                    }else if($action == "dbstruct"){
                        $result = $dataSample->exportDBStruct();
                        if($result=="OK"){
                            $this->_html .= '<div class="conf confirm">'.$this->l('Export dbstruct was successful').'</div>';
                        }else if($result=="ERROR_WRITE_FILE"){
                            $this->_html .= '<div class="error">'.$this->l('Unable to create backup file of. Please set permission 755 for module/leotempcp/sample').'</div>';
                        }else if($result=="NO_VALID"){
                            $this->_html .= '<div class="error">'.$this->l('No valid tables were found to backup').'</div>';
                        }
                        else{
                            $this->_html .= '<div class="error">'.$this->l('An error occurred while backing up. Unable to obtain the schema of'). ' ' . $result .'</div>';
                        }
                    }else if($action === "dbrestore"){
                        $listFile = Tools::getValue('modfiles');
                        $listFile = explode(",", $listFile);
                        if($listFile){
                            foreach ($listFile as $value){
                                if(!$dataSample->installSampleModule(_PS_ALL_THEMES_DIR_.$this->context->shop->theme_directory."/backup/".$value))
                                    $this->_html .= '<div class="error">'.$this->l('Can not restore file'). ' "' .$value. ' "' .'</div>';
                                else
                                    $this->_html .= '<div class="conf confirm">'.$this->l('Restore data for'). ' "' .$value. ' "' .$this->l('was successful').'</div>';
                            }
                        }
                    }else{
                        $modules = Tools::getValue('val');
                        $modules = explode(",", $modules);
                        $moduleList = $this->getModuleList();

                        foreach($moduleList->children() as $module){
                            if(in_array($module->name, $modules)){
                                $isCreateData = "";
                                //export database
                                if(isset($module->table_prefix) && $module->table_prefix){
                                    $table_prefix = explode(",", $module->table_prefix);
                                    $installFolder = "";
                                    if($action=="dbbackup")
                                            $installFolder = _PS_ALL_THEMES_DIR_.$this->context->shop->theme_directory."/backup/";
                                    foreach ($table_prefix as $value){
                                        $isCreateData = $dataSample->createTableSample($module->name, $value, $installFolder);
                                        if($isCreateData == "OK")
                                            $this->_html .= '<div class="conf confirm">'.$this->l('Create sample database sample for'). ' "' .$module->name. ' "' .$this->l('was successful').'</div>';
                                        else if($isCreateData=="ERROR_WRITE_FILE"){
                                            if($action!="dbbackup")
                                                $this->_html .= '<div class="error">'.$this->l('Unable to create backup file of'). ' "' .$module->name. ' "' .$this->l('Please create folder install in this module and set permission 755').'</div>';
                                            else
                                                $this->_html .= '<div class="error">'.$this->l('Unable to create backup file of'). ' "' .$module->name. ' "' .$this->l('Please create folder backup in template folder and set permission 755').'</div>';
                                        }else if($isCreateData=="ERROR_BACKING_UP")
                                            $this->_html .= '<div class="error">'.$this->l('An error occurred while backing up. Unable to obtain the schema'). ' "' .$module->name. ' "' .'</div>';
                                        else
                                            $this->_html .= '<div class="error">'.$this->l('Can not create database sample for'). ' "' .$module->name. ' "' .'</div>';
                                    }

                                }
                                //export config
                                if(isset($module->config_prefix) && $module->config_prefix){
                                    $isCreateConfig = $dataSample->createConfigSample($module->name, $module->config_prefix, $isCreateData);

                                    if($isCreateConfig == "OK")
                                        $this->_html .= '<div class="conf confirm">'.$this->l('Create sample config for'). ' "' .$module->name. ' "' .$this->l('was successful').'</div>'; 
                                    else if($isCreateConfig=="ERROR_DATA_NULL")
                                        $this->_html .= '<div class="error">'.$this->l('Can not find any config of '). ' "' .$module->name. ' "' .'</div>';
                                    else if($isCreateConfig=="ERROR_WRITE_FILE")
                                        $this->_html .= '<div class="error">'.$this->l('Unable to create backup file of'). ' "' .$module->name. ' "' .$this->l('Please create folder install in this module and set permission 755').'</div>';
                                    else
                                       $this->_html .= '<div class="error">'.$this->l('Can not create sample config for'). ' "' .$module->name. ' "' .'</div>';

                                }
                            }
                        }
                    }
                }
                
		if (sizeof($errors)){
			foreach ($errors AS $err){
				$this->_html .= '<div class="alert error">'.$err.'</div>';
			}
		}
		$this->_displayForm();
		
		return $this->_html;
	}
	
	private function _getFontData(){
		$font_name = Tools::getValue('font_name','');
		$results = array();
		if($font_name){
			$font_name = urldecode($font_name);
			$filePath = _PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/'.$font_name;
			if(file_exists($filePath))
				$results = @file_get_contents($filePath);
			$results = json_decode($results, true);
		}else{
			for($i = 1; $i <= $this->amounts; $i++){
				$results[$i] = array(
					'font_type' => Configuration::get($this->prefix.'font_type'.$i),
					'standard_font' => Configuration::get($this->prefix.'standard_font'.$i),
					'google_link' => Configuration::get($this->prefix.'google_link'.$i),
					'google_font' => Configuration::get($this->prefix.'google_font'.$i),
					'selector' => Configuration::get($this->prefix.'selector'.$i),
				);
			}
		}
		return $results;
	}
	/**
    * Get list of sub folder's name 
    */
	private function _getFileList( $path ) {
		$items = array();
		$handle = opendir($path);
		if (! $handle) {
			return $items;
		}
		while (false !== ($file = readdir($handle))) {
			if (is_file($path . $file)){
				$file_info = pathinfo($path . $file);
				if($file_info['extension'] == 'txt')
					$items[$file] = $file;
			}
		}
		unset($items['.'], $items['..'], $items['.svn']);
		
		return $items;
	}
	private function _displayForm()
	{
		global $cookie;
		$font_name =  Tools::getValue('font_name','');
	 
		if( empty($this->themeInfo) ){
			$this->_html .= '	<fieldset style="width: 900px;"><legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>'.
				$this->l("The Theme Configuration is not avariable, because may be you forgot set a theme from LeoTheme.Com as default theme of front-office, Please try to check again")
			.'</fieldset';
			
			return ;
		}
		$fontValues = $this->_getFontData();
		 
		$fontFiles = $this->_getFileList(_PS_ALL_THEMES_DIR_.Context::getContext()->shop->getTheme().'/fonts/');
		
		$skins = $this->themeInfo["skins"];
		$layouts = $this->themeInfo["layouts"];
		$dskins = Configuration::get('leoskin');
		$fontsizes = array(9,10,11,12,13,14,15);
		$fontsize = Configuration::get('leofontsize');
		$fontsize = $fontsize ? $fontsize:13;
		
		$dlayout = Configuration::get('leolayout');
		
		$this->_html .= '<br />
		<form method="post" action="'.$this->base_config_url.'" enctype="multipart/form-data">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
				<ul>
				<li>+ <a href="index.php?controller=AdminLeotempcpPanel&token='.Tools::getAdminTokenLite('AdminLeotempcpPanel').'"><b>'.$this->l("Configure Theme Positions").'</b></a></li>
				<li>+ '. $this->l( "Configuration For <b>" . Context::getContext()->shop->getTheme() . "</b> Theme " ) .'</li>
				</ul>
				<script type="text/javascript">
					var iddiv = "'.(Tools::getValue('iddiv') ? Tools::getValue('iddiv') : 'base_setting').'";
					var base_url = "'.$this->base_config_url.'";
				</script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/assets/admin/form.js"></script>
				<link rel="stylesheet" href="'.__PS_BASE_URI__.'modules/'.$this->name.'/assets/admin/jquery-ui.css" type="text/css" media="screen" charset="utf-8" />
				<div class="lof_config_wrrapper clearfix ui-tabs ui-widget ui-widget-content ui-corner-all" id="lof-pdf-tab">
					<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
						<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a class="lof-tab" href="javascript:void(0)" rel="#base_setting"><span>'.$this->l("Basic settings").'</span></a></li>
						<li class="ui-state-default ui-corner-top"><a class="lof-tab" href="javascript:void(0)" rel="#fonts_setting"><span>'.$this->l("Fonts").'</span></a></li>
                                                <li class="ui-state-default ui-corner-top"><a class="lof-tab" href="javascript:void(0)" rel="#data_sample"><span>'.$this->l("Data Sample").'</span></a></li>
					</ul>
					<div id="base_setting" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
						<label>'.$this->l('Template Width').'</label>
						<div class="margin-form">
							<input name="templatewidth" value="'.(Configuration::get('templatewidth') ? Configuration::get('templatewidth') : 'auto').'"/>
							<div class="clear clr"></div><sub>'.$this->l("put value <b>auto</b> as default of theme, or Set Template Width in number(px) or number(%), for example 980px, 99% ").'</sub>
						</div>
						<label>'.$this->l('Columns In Product List Page').'</label>
						<div class="margin-form">
							<input name="productlistcols" value="'.(Configuration::get('productlistcols') ? Configuration::get('productlistcols') : '3').'"/>
							<div class="clear clr"></div><sub>'.$this->l("put value <b>auto</b> as default of theme, or Set Template Width in number(px) or number(%), for example 980px, 99% ").'</sub>
						</div>
						<label>'.$this->l('Copyright').'</label>
						<div class="margin-form">
							<textarea name="leocopyright" style="width:320px; height:90px" />'. Configuration::get('leocopyright'). '</textarea>
							<div class="clear clr"></div><sub>'.$this->l(" put your site copyright here").'</sub>
						</div>
						<label>'.$this->l('Layout').'</label>
						<div class="margin-form">
							<select name="leolayout">';
								foreach( $layouts as $layout ){
									$this->_html .= '<option '.($layout==$dlayout?'selected="selected"':"").' value="'.$layout.'">'.$this->l($layout).'</option>';
								}
							$this->_html .=	'</select>
						</div>	
						<label>'.$this->l('Default Skin').'</label>
						<div class="margin-form">
							<select name="leoskin">';
							if(is_array($skins))
								foreach( $skins as $skin ){
									$this->_html .= '<option '.($skin==$dskins?'selected="selected"':"").' value="'.$skin.'">'.$this->l($skin).'</option>';
								}
							$this->_html .=	'</select>
						</div>	
						
						<label>'.$this->l('Font Size').'</label>
						<div class="margin-form">
							<select name="leofontsize">';
								foreach( $fontsizes  as $fs ){
									$this->_html .= '<option '.($fs==$fontsize?'selected="selected"':"").' value="'.$fs.'">'.$this->l($fs).'</option>';
								}
							$this->_html .=	'</select>
						</div>	
						
						<label>'.$this->l('Panel Toool').'</label>	
						<div class="margin-form">
							<input type="radio" name="leopntool" id="leopntool_on" value="1" '.(Tools::getValue('leopntool', Configuration::get('leopntool')) ? 'checked="checked" ' : '').'/>
							<label class="t" for="leopntool_on"> <img src="../img/admin/enabled.gif" /></label>
							<input type="radio" name="leopntool" id="leopntool_off" value="0" '.(!Tools::getValue('leopntool', Configuration::get('leopntool')) ? 'checked="checked" ' : '').'/>
							<label class="t" for="leopntool_off"> <img src="../img/admin/disabled.gif" /></label>
						</div>	
						<label>'.$this->l('Responsive feature').'</label>	
						<div class="margin-form">
							<input type="radio" name="leorespon" id="leorespon_on" value="1" '.(Tools::getValue('leorespon', Configuration::get('leorespon')) ? 'checked="checked" ' : '').'/>
							<label class="t" for="leorespon_on"> <img src="../img/admin/enabled.gif" /></label>
							<input type="radio" name="leorespon" id="leorespon_off" value="0" '.(!Tools::getValue('leorespon', Configuration::get('leorespon')) ? 'checked="checked" ' : '').'/>
							<label class="t" for="leorespon_off"> <img src="../img/admin/disabled.gif" /></label>
						</div>
						
						';
					$this->_html = LeoThemeInfo::onRenderForm( $this->_html, $this );	
					$this->_html .= '<div class="clear pspace"></div>
						<div class="margin-form clear"><input type="submit" name="submitUpdate" value="'.$this->l('    Save    ').'" class="button" /></div>
					</div>
					<div id="fonts_setting" class="ui-tabs-panel ui-widget-content ui-corner-bottom"  style="display:none;" >
						<label>'.$this->l('Font Feature:').'</label>
						<div class="margin-form">
							<select name="enable_font">
								<option value="1"'.(Configuration::get($this->prefix.'enable_font') ? ' selected="selected"' : '').'>'.$this->l('Enable').'</option>
								<option value="0"'.(!Configuration::get($this->prefix.'enable_font') ? ' selected="selected"' : '').'>'.$this->l('Disable').'</option>
							</select>
							<p>'.$this->l('Enable to use this function.').'</p>
						</div>
						<div class="clear space"></div>
						<label>'.$this->l('Import font:').'</label>
						<div class="margin-form">
							<input type="button" value="'.$this->l('Click here').'" name="ImportFont" id="ImportFont" class="button"/>
							<p>'.$this->l('Click here to import font.').'</p>
							<div class="ImportFont" style="display:none;">
								<input type="file" name="font_file" size="30"/>
								<p>'.$this->l('Font file .txt').'</p>
								<input type="submit" value="'.$this->l('Import').'" name="submitImport" class="button"/>
							</div>
							<script type="text/javascript">
								jQuery(document).ready(function($){
									$("#ImportFont").click(function(){
										$(".ImportFont").toggle(400);
									});
									var val = $("#font_apply").val();
									if(val != "0")
										$(".load_font").css("display","block");
									else
										$(".load_font").css("display","none");
										
									$("#font_apply").change(function(){
										var val = $(this).val();
										if(val != "0")
											$(".load_font").css("display","block");
										else
											$(".load_font").css("display","none");
									});
									$("#load_font").click(function(){
										var val = $("#font_apply").val();
										if(val != "0")
											window.location = base_url+"&font_name="+val+"&iddiv=fonts_setting";
									});
									$("#submitReset").click(function(){
										window.location = base_url;
									});
                                                                        $("#selectAll").click(function(){
                                                                            $(".chkmodule").attr("checked", $(this).is(":checked"))
                                                                        });
                                                                        $("#submitsample").click(function(){
                                                                                var allVals = [];
                                                                                $(".chkmodule").each(function() {
                                                                                    if($(this).is(":checked"))
                                                                                        allVals.push($(this).val());
                                                                                });
                                                                                if(!allVals.length){
                                                                                    alert("'.$this->l('Please select one or more modules').'");
                                                                                    return false
                                                                                }
										window.location = base_url+"&submitsample=1&action=dbsample&val="+allVals;
									});
                                                                        $("#submitbackup").click(function(){
                                                                                var allVals = [];
                                                                                $(".chkmodule").each(function() {
                                                                                    if($(this).is(":checked"))
                                                                                        allVals.push($(this).val());
                                                                                });
                                                                                if(!allVals.length){
                                                                                    alert("'.$this->l('Please select one or more modules').'");
                                                                                    return false
                                                                                }
										window.location = base_url+"&submitsample=1&action=dbbackup&val="+allVals;
									});
                                                                        $("#submitrestore").click(function(){
                                                                                if(confirm("'.$this->l('Do you realy to restore?').'")){
                                                                                    var allFiles = [];
                                                                                    $(".selectfile").each(function() {
                                                                                        if($(this).parent().find(".chkmodule").is(":checked"))
                                                                                            allFiles.push($(this).val());
                                                                                    });
                                                                                    
                                                                                    if(!allFiles.length){
                                                                                        alert("'.$this->l('Please select one or more modules').'");
                                                                                        return false
                                                                                    }
                                                                                    window.location = base_url+"&submitsample=1&action=dbrestore&modfiles="+allFiles;
                                                                                }
									});
                                                                        $("#submitdbstructure").click(function(){
										window.location = base_url+"&submitsample=1&action=dbstruct";
									});
                                                                        $("#submitthemesql").click(function(){
										window.location = base_url+"&submitsample=1&action=themesql";
									});
								});
							</script>
						</div>
						
						<div class="separation"></div>
						<div class="clear"></div>
						<label>'.$this->l('Load font:').'</label>
						<div class="margin-form">
							<select name="font_load" id="font_apply">';
							$this->_html .= '<option value="0">'.$this->l('---------').'</option>';
								global $currentIndex;
								if($fontFiles)
									foreach($fontFiles as $f){
										$this->_html .= '<option value="'.urlencode($f).'"'.(urlencode($f) == urlencode(Tools::getValue('font_name')) ? ' selected="selected"' : '').'>'.$f.'</option>';
									}
					$this->_html .= '
							</select>
							<div class="load_font" style="display:none;">
								<div class="clear space"></div>
								<input type="button" value="'.$this->l('   Load   ').'" class="button" id="load_font"/>
								<input type="submit" value="'.$this->l('   Download   ').'" class="button" name="submitDownload"/>
								<input type="submit" value="'.$this->l('   Delete   ').'" class="button" name="submitDeleteFont"/>
							</div>
						</div>
						<div class="separation"></div>
						<label>'.$this->l('Google Font Directory:').'</label>
						<div class="margin-form">
							<a href="//code.google.com/webfonts" target="_blank" title="'.$this->l('Google Fonts').'" style="color: #4285F4; display: block; padding-top: 3px;">'.$this->l('Click here').'</a>
						</div>
						<label>'.$this->l('Body - font:').'</label>
						<div class="margin-form">
							<select name="font_type1" id="font_type1" class="font_type">
								<option value="standard"'.($fontValues[1]['font_type'] == 'standard' ? ' selected="selected"' : '').'>'.$this->l('Standard').'</option>
								<option value="google"'.($fontValues[1]['font_type'] == 'google' ? ' selected="selected"' : '').'>'.$this->l('Google Fonts').'</option>
							</select>';
						$this->_html .= ' 
							<select name="standard_font1" class="font_type1 font_type1_standard">';
								foreach($this->fonts as $key=>$row){
									$this->_html .= '<option value="'.$key.'"'.($key == $fontValues[1]['standard_font'] ? ' selected="selected"' : '').'>'.$row.'</option>';
								}
					$this->_html .= '
							</select>
						</div>
						<div class="clear"></div>
						<div class="font_type1 font_type1_google">
							<label>'.$this->l('Font url:').'</label>
							<div class="margin-form">
								<input type="text" name="google_link1" value="'.$fontValues[1]['google_link'].'" size="40"/>
								<p>'.$this->l('Example: //fonts.googleapis.com/css?family=Petit+Formal+Script').'</p>
							</div>
							<label>'.$this->l('Font family:').'</label>
							<div class="margin-form">
								<input type="text" name="google_font1" value="'.$fontValues[1]['google_font'].'" size="40"/>
								<p>'.$this->l('Example: Petit Formal Script').'</p>
							</div>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Body - selectors').'</label>
						<div class="margin-form">
							<textarea cols="50" rows="5" name="selector1">'.$fontValues[1]['selector'].'</textarea>
							<p>'.$this->l('Example: h1,h2,#lof-title h3').'</p>
						</div>
						<div class="clear"></div>
						<div class="separation"></div>
						<label>'.$this->l('Headers - font:').'</label>
						<div class="margin-form">
							<select name="font_type2" id="font_type2" class="font_type">
								<option value="standard"'.($fontValues[2]['font_type'] == 'standard' ? ' selected="selected"' : '').'>'.$this->l('Standard').'</option>
								<option value="google"'.($fontValues[2]['font_type'] == 'google' ? ' selected="selected"' : '').'>'.$this->l('Google Fonts').'</option>
							</select>';
							$this->_html .= ' 
							<select name="standard_font2" class="font_type2 font_type2_standard">';
								foreach($this->fonts as $key=>$row){
									$this->_html .= '<option value="'.$key.'"'.($key == $fontValues[2]['standard_font'] ? ' selected="selected"' : '').'>'.$row.'</option>';
								}
					$this->_html .= '
							</select>
						</div>
						<div class="clear"></div>
						<div class="font_type2 font_type2_google">
							<label>'.$this->l('Font url:').'</label>
							<div class="margin-form">
								<input type="text" name="google_link2" value="'.$fontValues[2]['google_link'].'" size="40"/>
								<p>'.$this->l('Example: //fonts.googleapis.com/css?family=Petit+Formal+Script').'</p>
							</div>
							<label>'.$this->l('Font family:').'</label>
							<div class="margin-form">
								<input type="text" name="google_font2" value="'.$fontValues[2]['google_font'].'" size="40"/>
								<p>'.$this->l('Example: Petit Formal Script').'</p>
							</div>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Headers - selectors:').'</label>
						<div class="margin-form">
							<textarea cols="50" rows="5" name="selector2">'.$fontValues[2]['selector'].'</textarea>
							<p>'.$this->l('Example: h1,h2,#lof-title h3').'</p>
						</div>
						<div class="clear"></div>
						<div class="separation"></div>
						<label>'.$this->l('Other I - font:').'</label>
						<div class="margin-form">
							<select name="font_type3" id="font_type3" class="font_type">
								<option value="standard"'.($fontValues[3]['font_type'] == 'standard' ? ' selected="selected"' : '').'>'.$this->l('Standard').'</option>
								<option value="google"'.($fontValues[3]['font_type'] == 'google' ? ' selected="selected"' : '').'>'.$this->l('Google Fonts').'</option>
							</select>';
							$this->_html .= ' 
							<select name="standard_font3" class="font_type3 font_type3_standard">';
								foreach($this->fonts as $key=>$row){
									$this->_html .= '<option value="'.$key.'"'.($key == $fontValues[3]['standard_font'] ? ' selected="selected"' : '').'>'.$row.'</option>';
								}
					$this->_html .= '
							</select>
						</div>
						<div class="clear"></div>
						<div class="font_type3 font_type3_google">
							<label>'.$this->l('Font url:').'</label>
							<div class="margin-form">
								<input type="text" name="google_link3" value="'.$fontValues[3]['google_link'].'" size="40"/>
								<p>'.$this->l('Example: //fonts.googleapis.com/css?family=Petit+Formal+Script').'</p>
							</div>
							<label>'.$this->l('Font family:').'</label>
							<div class="margin-form">
								<input type="text" name="google_font3" value="'.$fontValues[3]['google_font'].'" size="40"/>
								<p>'.$this->l('Example: Petit Formal Script').'</p>
							</div>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Other I - selectors:').'</label>
						<div class="margin-form">
							<textarea cols="50" rows="5" name="selector3">'.$fontValues[3]['selector'].'</textarea>
							<p>'.$this->l('Example: h1,h2,#lof-title h3').'</p>
						</div>
						<div class="separation"></div>
						<label>'.$this->l('Other II - font:').'</label>
						<div class="margin-form">
							<select name="font_type4" id="font_type4" class="font_type">
								<option value="standard"'.($fontValues[4]['font_type'] == 'standard' ? ' selected="selected"' : '').'>'.$this->l('Standard').'</option>
								<option value="google"'.($fontValues[4]['font_type'] == 'google' ? ' selected="selected"' : '').'>'.$this->l('Google Fonts').'</option>
							</select>';
							$this->_html .= ' 
							<select name="standard_font4" class="font_type4 font_type4_standard">';
								foreach($this->fonts as $key=>$row){
									$this->_html .= '<option value="'.$key.'"'.($key == $fontValues[4]['standard_font'] ? ' selected="selected"' : '').'>'.$row.'</option>';
								}
					$this->_html .= '
							</select>
						</div>
						<div class="clear"></div>
						<div class="font_type4 font_type4_google">
							<label>'.$this->l('Font url:').'</label>
							<div class="margin-form">
								<input type="text" name="google_link4" value="'.$fontValues[4]['google_link'].'" size="40"/>
								<p>'.$this->l('Example: //fonts.googleapis.com/css?family=Petit+Formal+Script').'</p>
							</div>
							<label>'.$this->l('Font family:').'</label>
							<div class="margin-form">
								<input type="text" name="google_font4" value="'.$fontValues[4]['google_font'].'" size="40"/>
								<p>'.$this->l('Example: Petit Formal Script').'</p>
							</div>
						</div>
						<div class="clear "></div>
						<label>'.$this->l('Other I - selectors:').'</label>
						<div class="margin-form">
							<textarea cols="50" rows="5" name="selector4">'.$fontValues[4]['selector'].'</textarea>
							<p>'.$this->l('Example: h1,h2,#lof-title h3').'</p>
						</div>
						
						<div class="clear space"></div>
						<div class="margin-form clear">
							<input type="submit" name="submitUpdate" value="'.$this->l('    Save    ').'" class="button" />
							<input type="button" name="submitReset" value="'.$this->l('    Reset    ').'" class="button" id="submitReset" />
							<input type="submit" name="submitExport" value="'.$this->l('    Export    ').'" class="button" />
						</div>
					</div>';
                                 $moduleList = $this->getModuleList(_PS_ALL_THEMES_DIR_.$this->context->shop->theme_directory."/backup/");
                                 
                                 if($moduleList){
                                 $this->_html .='<div id="data_sample" class="ui-tabs-panel ui-widget-content ui-corner-bottom"  style="display:none;" >';
                                 $this->_html .='<ul>
                                                    <li>
                                                        <h3>'.$this->l('Export db_structure.sql and theme.sql for quickstart').'</h3>
                                                        <div class="separation"></div><div class="clear space"></div>
                                                    </li>
                                                    <li>
                                                        <input type="checkbox" value="all" id="selectAll"/>
                                                        <label for="selectAll">'.$this->l('Check all').'</label>
                                                        <div class="separation"></div><div class="clear space"></div>
                                                   </li>';
                                 
                                 foreach($moduleList as $moduleName => $moduleFile){
                                     $this->_html .='<li><input type="checkbox" class="chkmodule" name="chkmodule" value="'.$moduleName.'" id="chk_'.$moduleName.'"/>
                                                         <label for="chk_'.$moduleName.'">'.$moduleName.'</label>';
                                                   if($moduleFile){
                                                       $this->_html .= '<select name="" class="selectfile">';
                                                       foreach ($moduleFile as $value) {
                                                           $this->_html .= '<option value="'.$value.'">'.$value.'</option>';
                                                       }
                                                       $this->_html .= '</select>';
                                                   }      
                                     $this->_html .='<div class="clear space"></div> 
                                                     </li>';
                                     //$this->_html .='<li><div class="clear space"></div></li>';
                                 }
                                 
                                 $this->_html .='<li>
                                                    <div class="separation"></div>
                                                    
                                                    <input type="button" id="submitsample" class="button" value="'.$this->l("Create Sample").'" name="submitsample">
                                                    <input type="button" id="submitbackup" class="button" value="'.$this->l("Create Backup").'" name="submitbackup">
                                                    <input type="button" id="submitrestore" class="button" value="'.$this->l("Restore").'" name="submitrestore">
                                                </li>';
                                 $this->_html .='</ul>';
                                 }
                                 $this->_html .='<ul>
                                                    <li>
                                                        <div class="clear space"></div>
                                                        <h3>'.$this->l('Export db_structure.sql and theme.sql for quickstart').'</h3>
                                                        <div class="separation"></div><div class="clear space"></div>
                                                    </li>';
                                 
                                 $this->_html .='<li>
                                                    <input type="button" id="submitdbstructure" class="button" value="'.$this->l("Export db_structure.sql").'" name="submitsample">
                                                    <input type="button" id="submitthemesql" class="button" value="'.$this->l("Export Theme.sql").'" name="submitsample">
                                                </li>';
                                 $this->_html .='</ul>';
                                 
                                 
                                 $this->_html .='</div>';
                $this->_html .='
                            </div>
			</fieldset>
		</form>';
	}
        
        
    public function getModuleList($path = ''){
        $theme_dir = Context::getContext()->shop->getTheme();
        if( !file_exists( _PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml') )return ;

        $data = simplexml_load_file( _PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml' );

        if( !$data || !isset($data->modules))return null;

        $result = array();
        if(!$path)
        foreach ($data->modules as $modules) {
            return $modules;
        }
        $fileList = array();
        if(is_dir($path)){
            $fileList = @Tools::scandir($path);
            if($fileList) arsort($fileList);
        }
        $moduleList = array();
        //get file match with module
        foreach ($data->modules as $modules) {
            foreach ($modules as $module) {
                if(!isset($moduleList[$module->name.""])) $moduleList[$module->name.""] =  array();
                foreach ($fileList as $file){
                    if(strpos($file,$module->name."") !== false)
                        $moduleList[$module->name.""][] = $file;
                }
            }
        }
        //echo "<pre>";print_r($moduleList);die;
        return $moduleList;
    }
        
	public function getInfo(){
	
		$theme_dir = Context::getContext()->shop->getTheme();
		if( !file_exists( _PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml') ){
			return ;
		}
				
		$info = simplexml_load_file( _PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml' );
		if( !$info || !isset($info->name)|| !isset($info->positions) ){
			return null;
		}

		if( isset($info->author) && strtolower($info->author) == 'leotheme' ){
 
			$p = (array)$info->positions;
			$output = array("skins"=>"", 'layouts' => '',"positions"=>$p["position"],"name"=>(string)$info->name );
			if( isset($info->skins) ){
				$tmp =  (array)$info->skins;
				$output["skins"] = $tmp["skin"];
			}
			if( isset($info->layouts) ){
				$tmp =  (array)$info->layouts; 
				$output["layouts"] = $tmp["layout"];
			}
		
			$output = LeoThemeInfo::onGetInfo( $output );
			return $output;
		}
	}
	
	function hooktop($params)
	{			
		
		return false;
	}
	
	/**
	 * Execute modules for specified hook
	 *
	 * @param string $hook_name Hook Name
	 * @param array $hook_args Parameters for the functions
	 * @param int $id_module Execute hook for this module only
	 * @return string modules output
	 */
	public  function exec($hook_name, $hook_args = array(), $id_module = null)
	{	
		
		// Check arguments validity
		if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name))
			throw new PrestaShopException('Invalid id_module or hook_name');

		// If no modules associated to hook_name or recompatible hook name, we stop the function
	
		if (!$module_list = Hook::getHookModuleExecList($hook_name))
			return '';
		
		// Check if hook exists
		if (!$id_hook = Hook::getIdByName($hook_name))
			return false;
	
		// Store list of executed hooks on this page
		Hook::$executed_hooks[$id_hook] = $hook_name;
			
		$live_edit = false;
		$context = Context::getContext();
		if (!isset($hook_args['cookie']) || !$hook_args['cookie'])
			$hook_args['cookie'] = $context->cookie;
		if (!isset($hook_args['cart']) || !$hook_args['cart'])
			$hook_args['cart'] = $context->cart;

		$retro_hook_name = Hook::getRetroHookName($hook_name);

		// Look on modules list
		$altern = 0;
		$output = '';
		foreach ($module_list as $array)
		{
			
			
			// Check errors
			if ($id_module && $id_module != $array['id_module'])
				continue;
			if (!($moduleInstance = Module::getInstanceByName($array['module'])))
				continue;
			
			
			// echo '<pre>'.print_r( $this->overrideHooks, 1 ); die;
			// Check permissions
			$exceptions = $moduleInstance->getExceptions($array['id_hook']);
			if (in_array(Dispatcher::getInstance()->getController(), $exceptions))
				continue;
			if (Validate::isLoadedObject($context->employee) && !$moduleInstance->getPermission('view', $context->employee))
				continue;

			// Check which / if method is callable
			
			$hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
			$ohook=$orhook="";
			$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
			if( array_key_exists($moduleInstance->id,$this->overrideHooks) ){
				$ohook = Hook::getRetroHookName($this->overrideHooks[$moduleInstance->id]);
				$orhook = ($this->overrideHooks[$moduleInstance->id]);
				$hook_callable = is_callable(array($moduleInstance, 'hook'.$orhook));
				$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$ohook));
			}
					
			if (($hook_callable || $hook_retro_callable) && Module::preCall($moduleInstance->name))
			{
				$hook_args['altern'] = ++$altern;
				if( array_key_exists($moduleInstance->id,$this->overrideHooks) ){
					if ($hook_callable)
						$display = $moduleInstance->{'hook'.$orhook}($hook_args);
					else if ($hook_retro_callable)
						$display = $moduleInstance->{'hook'.$ohook}($hook_args);
				}else {
					// Call hook method
					if ($hook_callable)
						$display = $moduleInstance->{'hook'.$hook_name}($hook_args);
					else if ($hook_retro_callable)
						$display = $moduleInstance->{'hook'.$retro_hook_name}($hook_args);
				}
				// Live edit
				if ($array['live_edit'] && Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getValue('liveToken') == Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee')))
				{
					$live_edit = true;
					$output .= self::wrapLiveEdit($display, $moduleInstance, $array['id_hook']);
				}
				else
					$output .= $display;
			}

		}

		// Return html string
		return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\'); </script>
				<div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
	}
	
	public static function wrapLiveEdit($display, $moduleInstance, $id_hook)
	{
		return '<script type="text/javascript"> modules_list.push(\''.Tools::safeOutput($moduleInstance->name).'\');</script>
				<div id="hook_'.(int)$id_hook.'_module_'.(int)$moduleInstance->id.'_moduleName_'.str_replace('_', '-', Tools::safeOutput($moduleInstance->name)).'"
				class="dndModule" style="border: 1px dotted red;'.(!strlen($display) ? 'height:50px;' : '').'">
				<span style="font-family: Georgia;font-size:13px;font-style:italic;">
				<img style="padding-right:5px;" src="'._MODULE_DIR_.Tools::safeOutput($moduleInstance->name).'/logo.gif">'
			 	.Tools::safeOutput($moduleInstance->displayName).'<span style="float:right">
			 	<a href="#" id="'.(int)$id_hook.'_'.(int)$moduleInstance->id.'" class="moveModule">
			 		<img src="'._PS_ADMIN_IMG_.'arrow_out.png"></a>
			 	<a href="#" id="'.(int)$id_hook.'_'.(int)$moduleInstance->id.'" class="unregisterHook">
			 		<img src="'._PS_ADMIN_IMG_.'delete.gif"></span></a>
			 	</span>'.$display.'</div>';
	}

	
	function hookHeader(){
	global $cookie, $smarty;
		$leorespon =  Tools::getValue('leorespon', Configuration::get('leorespon'));
		$output = '';
		if(Configuration::get($this->prefix.'enable_font'))
			for($i = 1; $i <= $this->amounts; $i++){
				$font_type = Configuration::get($this->prefix.'font_type'.$i);
				if(Configuration::get($this->prefix.'selector'.$i)){
					if($font_type == 'standard'){
						$output .= '<style type="text/css">
							'.Configuration::get($this->prefix.'selector'.$i).'{font-family: ' . Configuration::get($this->prefix.'standard_font'.$i) . '; }
						</style>';
					}elseif($font_type == 'google') {
                        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                        $font_link = Configuration::get($this->prefix.'google_link'.$i);
                        $font_link = str_replace('http://',$protocol, $font_link);
						$font_family =Configuration::get($this->prefix.'google_font'.$i);
						$output .= '<link rel="stylesheet" type="text/css" href="'.$font_link.'" />';
						$output .= '<style type="text/css">
							'.Configuration::get($this->prefix.'selector'.$i).'{font-family: \''.$font_family. '\'; }
						</style>';
					}
				}
			}
		
	 
		$this->context->controller->addJS(($this->_path).'bootstrap/js/bootstrap.min.js');
		//add jquery cookie
		$this->context->controller->addJqueryPlugin('cooki-plugin');		
				
		if( $this->themeInfo ){
			$skin = Configuration::get('leoskin');
			$bgpattern = Configuration::get('leobgpattern');
			$layout = Configuration::get('leolayout');
			
			if(!$layout)
				$layout = 'default';
			$paneltool =  Tools::getValue('leopntool', Configuration::get('leopntool'));
			/* if enable user custom setting, the theme will use those configuration*/
			if( $paneltool ){
				$vars = array("skin"=>$skin,"layout"=>$layout,"bgpattern"=>$bgpattern);
				if( Tools::getIsset('usercustom') && strtolower( Tools::getValue('usercustom') ) == "apply" ){
					foreach( $vars as $key => $val ){
                                            if(Tools::getIsset($key)){
                                                $cookie->__set( "leou_".$key, Tools::getValue($key) );
                                                $val =  Tools::getValue($key);
                                            }
					}
					Tools::redirect( "index.php" );
				}
                                if( Tools::getIsset('leoaction') && strtolower( Tools::getValue('leoaction') ) == "reset" ){
					foreach( $vars as $key => $val ){
						$cookie->__set("leou_".$key, Configuration::get("leo"+$key));
					}
					Tools::redirect( "index.php" );	
				}
				//echo "<pre>".print_r($cookie,1); die;
				if($vars){
					foreach( $vars as $key => $val ){
						if( $cookie->__get(  "leou_".$key ) ){
							$$key = $cookie->__get(  "leou_".$key );	
						}else {
							$$key = $val;
						}
					}
				}
				
				$this->context->controller->addJS(  __PS_BASE_URI__.'themes/'.Context::getContext()->shop->getTheme()."/info/assets/form.js" );
				$this->context->controller->addCss(  __PS_BASE_URI__.'themes/'.Context::getContext()->shop->getTheme()."/info/assets/form.css" );
			}
			//echo $cookie->__get(  "leou_bgpattern" ); die;
			$bootstrapCss = ($this->_path).'bootstrap/css/bootstrap.min.css'; 
			//$bootstrapResponsive = ($this->_path).'bootstrap/css/bootstrap-responsive.css';
		 
			if( file_exists(_PS_ALL_THEMES_DIR_. Context::getContext()->shop->getTheme()."/css/bootstrap.css") ){
				$bootstrapCss = __PS_BASE_URI__.'themes/'.Context::getContext()->shop->getTheme()."/css/bootstrap.css";
			}
			//if( file_exists(_PS_ALL_THEMES_DIR_. Context::getContext()->shop->getTheme()."/css/bootstrap-responsive.css") ){
			//	$bootstrapResponsive = __PS_BASE_URI__.'themes/'.Context::getContext()->shop->getTheme()."/css/bootstrap-responsive.css";
			//}
			
			//
			$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'leohook` WHERE theme="'.Context::getContext()->shop->getTheme().'" AND id_shop='.(int)($this->context->shop->id);
			$result = Db::getInstance()->executeS($sql);
			if($result)
			foreach( $result as $row ){
				$this->overrideHooks[$row['id_module']] = $row['name_hook'];
			}
			$customWidth = '';
			$twidth = Tools::getValue('templatewidth', Configuration::get('templatewidth',"auto"));
			if( $twidth != 'auto' && !empty( $twidth ) ) {
				$customWidth .= ' <style type="text/css">
					.container{ width:'.$twidth.'}
					</style>';
			}
			$fontsize = Configuration::get('leofontsize');
			$fontsize = $fontsize ? $fontsize:13;
			$productlistcols = (int)(Configuration::get('productlistcols'));
			if( $productlistcols <=0 ){
				Configuration::updateValue('productlistcols',3);
			}
			$defaultLayout = 'default';
			if( is_dir(_PS_ALL_THEMES_DIR_. Context::getContext()->shop->getTheme()."/layout/".trim($layout)) &&  $layout ){
				$defaultLayout=trim($layout);
			}
			$page_name = Dispatcher::getInstance()->getController();
			$page_name = (preg_match('/^[0-9]/', $page_name)) ? 'page_'.$page_name : $page_name;
			$ps = array(	
				'LEO_CUSTOMFONT' =>  Configuration::get('enable_font'),
				'LEO_COPYRIGHT'       => Configuration::get('leocopyright'),
				'PRODUCTSLIST_COLUMNS'    => $productlistcols,
				'BOOTSTRAP_CSS_URI'     => $bootstrapCss,
				'LEO_SKIN_DEFAULT' => $skin,
				'LEO_CUSTOMWIDTH'  => $customWidth,
				'this_path' 	   => $this->_path,
				'LEO_RESPONSIVE'   =>  Configuration::get('leorespon'),
				'LEO_PANELTOOL'	   => $paneltool,
				'LEO_THEMEINFO'    => $this->themeInfo,
				'LEO_THEMENAME'	   => Context::getContext()->shop->getTheme(),
				'LEO_LAYOUT_DIRECTION' => $defaultLayout,
				'LEO_PATTERN' => $bgpattern.'.png',
				'LEO_BGPATTERN' => $bgpattern,
				'FONT_SIZE' => $fontsize,
			 	'HOOK_SLIDESHOW' =>  in_array($page_name,array('index')) ?  $this->exec('displaySlideshow'):"",
			 	'HOOK_TOPNAVIGATION'  => $this->exec('topNavigation'),
				'HOOK_PROMOTETOP' => $this->exec( 'displayPromoteTop' ),
			 	'HOOK_HEADERRIGHT'  => $this->exec('displayHeaderRight'),
				'HOOK_BOTTOM'		=> $this->exec( 'displayBottom' ),
				'HOOK_CONTENTBOTTOM' => $this->exec( 'displayContentBottom' ),
				'HOOK_FOOTNAV' => $this->exec( 'displayFootNav' ),
				'LEO_WISHLIST_ENABLE' => Module::isInstalled('blockwishlist') 
			);
		 
			//$ps = LeoThemeInfo::onProcessHookTop( $ps );
			
			$smarty->assign( $ps );

			
		}
		
		return $output;		
	}
	public function hookActionShopDataDuplication($params)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'leohook` WHERE id_shop='.(int)$params['old_id_shop'];
                
                $result = Db::getInstance()->executeS($sql);
                
		foreach($result as $val)
		{       
                        //(int)$params['new_id_shop']
                        $sql = ' INSERT INTO `'._DB_PREFIX_.'leohook` (id_hook,id_module,id_shop,theme, name_hook) 
						VALUES('.$val["id_hook"].','.$val["id_module"].','.(int)$params['new_id_shop'].',"'.$val["theme"].'","'.$val["name_hook"].'")';

			Db::getInstance()->execute($sql);
		}
		
	}
}