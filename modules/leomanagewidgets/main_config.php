<?php
  /**
   * Lof Modal Cart Module
   *
   * @version   $Id: file.php $Revision
   * @package   modules
   * @subpackage  $Subpackage.
   * @copyright Copyright (C) June 2013 LeoTheme.Com <@emai:leotheme@gmail.com>.All rights reserved.
   * @license   GNU General Public License version 2
   */

  /**
   * @since 1.5.0
   * @version 1.0 (2013-06-20)
   */

  if (!defined('_PS_VERSION_'))
    exit;
	
	$this->context->controller->addjQueryPlugin(array(
		'fancybox'
	));
	
	$only_custom = array('custom'=>$this->l('Custom Html'));
	$only_tab_carousel = array('tab'=>$this->l('Tab Products'), 'carousel' => $this->l('Carousel Products'));
	$all = array('tab'=>$this->l('Tab Products'), 'carousel' => $this->l('Carousel Products'), 'custom' => $this->l('Custom Html'));
	
	$file_list = LeoManageWidget::getAllExceptions($this->context->shop->id);
	if (!is_array($file_list))
			$file_list = ($file_list) ? array($file_list) : array();
	$content = '
			<select id="em_list_filter" style="width:237px">
				<option value="'.$this->base_config_url.'"'.(!$exception ? ' selected="selected"' : '').'>'.$this->l('------ None -------').'</option>
				<option disabled="disabled">'.'___________ CUSTOM ___________'.'</option>';
	// @todo do something better with controllers
	$controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
	ksort($controllers);
	foreach ($file_list as $k => $v)
		if ( ! array_key_exists ($v, $controllers))
			$content .= '
				<option value="'.$this->base_config_url.'&exception='.$v.'"'.($exception == $v ? ' selected="selected"' : '').'>'.$v.'</option>';
	$content .= '
				<option disabled="disabled">'.'____________ CORE ____________'.'</option>';
	foreach ($controllers as $k => $v)
		$content .= '
				<option value="'.$this->base_config_url.'&exception='.$k.'"'.($exception == $k ? ' selected="selected"' : '').'>'.$k.'</option>';
	$content .= '
		</select>';
	
    $this->_html .= '<div id="leo-page" class="clearfix">
	<div class="leotheme-layout">
		<label>'.$this->l('Filter By Exception Page:').'</label>
		<div class="margin-form">'.$content.'</div>
		<div id="leo-header" >
			<div class="topbar leo-container" data-position="displayTop">
				<div class="pos">HOOK_TOP</div>'.$this->renderLink('displayTop', $only_custom);
				if( isset($hookModules['displayTop']) && count($hookModules['displayTop']) > 0){
					foreach ($hookModules['displayTop'] as $position => $module){
						
						$this->_html .= $this->renderItem($module);
						
					}
				}
			$this->_html .= '
			</div>
			<div class="leoheader clearfix">
				<div id="leologo"><div class="pos">LOGO</div></div>
				<div id="leo-hheaderright" class="leo-container overridehook" data-position="displayHeaderRight"><div class="pos">HOOK_HEADERRIGHT</div>'.$this->renderLink('displayHeaderRight', $only_custom);
				
					if(isset($hookModules['displayHeaderRight']) && count($hookModules['displayHeaderRight']) > 0){
						foreach($hookModules['displayHeaderRight'] as $position => $module){
							
								$this->_html .= $this->renderItem($module);
							
						}
					}
			$this->_html .= '
				</div>
			</div>
		</div>
		
		
		<div id="leo-menu" class="leo-container overridehook" data-position="topNavigation"><div class="pos">HOOK_TOPNAVIATION</div>'.$this->renderLink('topNavigation', $only_custom);
			if(isset($hookModules['topNavigation']) && count($hookModules['topNavigation']) > 0){
				foreach($hookModules['topNavigation'] as $position => $module){
					$this->_html .= $this->renderItem($module);
				}
			}
		$this->_html .= '
		</div>
		
		
		<div id="leo-slideshow" class="leo-container overridehook" data-position="displaySlideshow"><div class="pos">HOOK_SLIDESHOW</div>'.$this->renderLink('displaySlideshow', $all);
			if(isset($hookModules['displaySlideshow']) && count($hookModules['displaySlideshow']) > 0){
				foreach($hookModules['displaySlideshow'] as $position => $module){
					$this->_html .= $this->renderItem($module);
				}
			}
		$this->_html .= '
		</div>
		<div id="leo-promotetop"  class="leo-container overridehook" data-position="displayPromoteTop"><div class="pos">HOOK_PROMOTETOP</div>'.$this->renderLink('displayPromoteTop', $all);
			if(isset($hookModules['displayPromoteTop']) && count($hookModules['displayPromoteTop']) > 0){
				foreach($hookModules['displayPromoteTop'] as $position => $module){
					$this->_html .= $this->renderItem($module);
				}
			}
		$this->_html .= '
		</div>

		<div id="leo-content" class="clearfix"  >
			<div id="leo-left" class="leo-container" data-position="displayLeftColumn"><div class="pos">HOOK_LEFT</div>'.$this->renderLink('displayLeftColumn', $only_custom);
				if(isset($hookModules['displayLeftColumn']) && count($hookModules['displayLeftColumn']) > 0){
					foreach( $hookModules['displayLeftColumn'] as $position => $module){
						$this->_html .= $this->renderItem($module);
					}
				}
			$this->_html .= '
			</div>
			<div id="leo-center">
				<div  class="leo-container inner" data-position="displayHome" style="min-height:250px"><div class="pos">HOOK_HOME</div>'.$this->renderLink('displayHome', $all);
				if( isset($hookModules['displayHome']) && count($hookModules['displayHome']) > 0){
					foreach( $hookModules['displayHome'] as $position => $module){
						$this->_html .= $this->renderItem($module);
					}
				}
				$this->_html .= '
				</div>
				
				<div  class="leo-container overridehook inner" data-position="displayContentBottom" style="min-height:50px">
					<div class="pos">HOOK_CONTENTBOTTOM</div>'.$this->renderLink('displayContentBottom', $all);
					if( isset($hookModules['displayContentBottom']) && count($hookModules['displayContentBottom']) > 0){
						foreach( $hookModules['displayContentBottom'] as $position => $module){
							$this->_html .= $this->renderItem($module);
						}
					}
				$this->_html .= '
				</div>
			</div>
			
			<div id="leo-right" class="leo-container" data-position="displayRightColumn"><div class="pos">HOOK_RIGHT</div>'.$this->renderLink('displayRightColumn', $only_custom);
				if( isset($hookModules['displayRightColumn']) && count($hookModules['displayRightColumn']) > 0){
					foreach( $hookModules['displayRightColumn'] as $position => $module){
						$this->_html .= $this->renderItem($module);
					}
				}
			$this->_html .= '
			</div>
		</div>
		<div id="leo-bottom" class="leo-container overridehook clearfix sortable_container" data-position="displayBottom">
			<div class="pos">HOOK_BOTTOM</div>'.$this->renderLink('displayBottom', $all);
			if( isset($hookModules['displayBottom']) && count($hookModules['displayBottom']) > 0){
				foreach( $hookModules['displayBottom'] as $position => $module){
					$this->_html .= $this->renderItem($module);
				}
			}
		$this->_html .= '
		</div>
		<div id="leo-footer" class="clearfix sortable_container">
			<div id="leo-hfooter" class="leo-container clearfix" data-position="displayFooter">
				<div class="pos">HOOK_FOOTER</div>'.$this->renderLink('displayFooter', $only_custom);
				if( isset($hookModules['displayFooter']) && count($hookModules['displayFooter']) > 0){
					foreach( $hookModules['displayFooter'] as $position => $module){
						$this->_html .= $this->renderItem($module);
					}
				}
			$this->_html .= '
			</div>
		</div>
		
		<div class="clearfix sortable_container"  id="page-footer">
				<div id="leo-copyright" class="clearfix"><div class="pos">HOOK_COPYRIGHT</div></div>
		
				<div id="leo-footnav" class="leo-container overridehook clearfix" data-position="displayFootNav">				
					<div class="pos">HOOK_FOOTNAV</div>'.$this->renderLink('displayFootNav', $only_custom);
						if( isset($hookModules['displayFootNav']) && count($hookModules['displayFootNav']) > 0){
							foreach( $hookModules['displayFootNav'] as $position => $module){
								$this->_html .= $this->renderItem($module);
							}
						}
					$this->_html .= '
					</div>
				</div>
	</div>
</div>
<script type="text/javascript">
	var id_shop = '.$this->context->shop->id.';
	var secure_key = "'.$this->secure_key.'";
	$("#em_list_filter").change(function(){
		window.location.href = $(this).val();
	});
</script>
';
