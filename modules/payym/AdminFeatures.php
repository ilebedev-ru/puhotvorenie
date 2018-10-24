<?php

include_once(PS_ADMIN_DIR.'/tabs/AdminFeaturesValues.php');

class AdminFeatures extends AdminTab
{
	public function __construct()
	{
		$this->adminFeaturesValues = new AdminFeaturesValues();
		$this->table = 'feature';
		$this->className = 'Feature';
		$this->lang = true;
		$this->edit = true;
		$this->delete = true;
		$this->_defaultOrderBy = 'name';

		$this->fieldsDisplay = array(
			'name' => array('title' => $this->l('Name'), 'width' => 128),
			'value' => array('title' => $this->l('Values'), 'width' => 255, 'orderby' => false, 'search' => false));

		parent::__construct();
	}

	public function display()
	{
		global $currentIndex;

		if ((isset($_POST['submitAddfeature_value']) AND sizeof($this->adminFeaturesValues->_errors))
			OR isset($_GET['updatefeature_value']) OR isset($_GET['addfeature_value']))
		{
			$this->adminFeaturesValues->displayForm($this->token);
			echo '<br /><br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" alt="" /> '.$this->l('Back to the features list').'</a><br />';
		}
		else
			parent::display();
	}

	/* Report to AdminTab::displayList() for more details */
	public function displayList()
	{
		global $currentIndex;

		echo '<br />
			<a href="'.$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> <b>'.$this->l('Add a new feature').'</b></a><br />
			<a href="'.$currentIndex.'&addfeature_value&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add a new feature value').'</a><br /><br />
		'.$this->l('Click on a feature name to view its values and then click again if you want to hide them.').'<br /><br />';

		$this->displayListHeader();
		echo '<input type="hidden" name="groupid" value="0">';

		if (!sizeof($this->_list))
			echo '<tr><td class="center" colspan="'.sizeof($this->_list).'">'.$this->l('No features found.').'</td></tr>';

		$irow = 0;
		foreach ($this->_list AS $tr)
		{
			$id = (int)($tr['id_'.$this->table]);
			echo '
			<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" /></td>
				<td style="width: 140px; vertical-align: top; padding: 4px 0 4px 0; cursor: pointer" onclick="$(\'#features_values_'.$id.'\').slideToggle();">'.$tr['name'].'</td>
				<td style="vertical-align: top; padding: 4px 0 4px 0; width: 340px">
					<div id="features_values_'.$id.'" style="display: none">
					<table class="table" cellpadding="0" cellspacing="0">
						<tr>
							<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'feature_value'.$id.'Box[]\', this.checked)" /></th>
							<th width="100%">'.$this->l('Value').'</th>
							<th>'.$this->l('Actions').'</th>
						</tr>';
			$features = FeatureValue::getFeatureValuesWithLang((int)(Configuration::get('PS_LANG_DEFAULT')), $id);
			foreach ($features AS $feature)
			{
				echo '
				<tr>
					<td class="center"><input type="checkbox" name="feature_value'.$id.'Box[]" value="'.$feature['id_feature_value'].'" class="noborder" /></td>
					<td>'.$feature['value'].'</td>
					<td class="center">
						<a href="'.$currentIndex.'&id_feature_value='.$feature['id_feature_value'].'&updatefeature_value&token='.$this->token.'">
						<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
						<a href="'.$currentIndex.'&id_feature_value='.$feature['id_feature_value'].'&deletefeature_value&token='.$this->token.'"
						onclick="return confirm(\''.$this->l('Delete value', __CLASS__, true, false).' #'.$feature['id_feature_value'].'?\');">
						<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
					</td>
				</tr>';
			}
			if (!sizeof($features))
				echo '
						<tr><td colspan="3" style="text-align:center">'.$this->l('No values defined').'</td></tr>';
			echo '
					</table>
					<p><input type="Submit" class="button" name="submitDelfeature_value" value="'.$this->l('Delete selection').'"
					onclick="changeFormParam(this.form, \'?tab=AdminFeatures\', '.$id.'); return confirm(\''.$this->l('Delete selected items?', __CLASS__, true, false).'\');" /></p>
					</div>
					</td>';

			echo '
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center">
					<a href="'.$currentIndex.'&id_'.$this->table.'='.$id.'&update'.$this->table.'&token='.$this->token.'">
					<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
					<a href="'.$currentIndex.'&id_'.$this->table.'='.$id.'&delete'.$this->table.'&token='.$this->token.'" onclick="return confirm(\''.$this->l('Delete item', __CLASS__, true, false).' #'.$id.'?\');">
					<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
				</td>
			</tr>';
		}

		$this->displayListFooter();

	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		if (!($obj = $this->loadObject(true)))
			return;
		//print_r($categories);
		if($obj->id){
			$fgroups = Category::getFGroups();
			$fgroups_feature = Category::getFGroups($obj->id);
		}
		echo '
		<h2>'.$this->l('Add a new feature').'</h2>
		<form action="'.$currentIndex.'&token='.$this->token.'"" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset class="width2">
				<legend><img src="../img/t/AdminFeatures.gif" />'.$this->l('Add a new feature').'</legend>
				<label>'.$this->l('Name:').'</label>
				<div class="margin-form">';
		foreach ($this->_languages AS $language)
			echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>
				<script type="text/javascript">
					var flag_fields = \'name\';
				</script>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'flag_fields', 'name', false, true);
		echo '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Отображается в следующих группах:').'</label>';
				/*echo '<div class="margin-form">';
				if($fgroups){
					echo '<div class="fgroup">';
					foreach($fgroups as $fgroup){
						echo '<input type="checkbox" name="fgroup['.$fgroup['id_category'].']" '.($fgroups_feature[$fgroup['id_category']] ? 'checked="checked"' : '').'><span>'.$fgroup['name'].'</span><br />';
					}
					echo '</div>';
				}
				echo '</div>';*/
				// Translations are not automatic for the moment ;)
					$trads = array(
						 'Home' => $this->l('Главная'),
						 'selected' => $this->l('Выбрано'),
						 'Collapse All' => $this->l('Collapse All'),
						 'Expand All' => $this->l('Expand All'),
						 'Check All' => $this->l('Check All'),
						 'Uncheck All'  => $this->l('Uncheck All')
					);
				echo Helper::renderAdminCategorieTree($trads, $fgroups_feature);
				echo Module::hookExec('featureForm', array('id_feature' => $obj->id)).'
					<div class="clear"></div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	public function displayErrors()
	{
		$this->adminFeaturesValues->displayErrors();
		parent::displayErrors();
	}

	public function postProcess()
	{
		global $cookie, $currentIndex;
		$this->adminFeaturesValues->tabAccess = Profile::getProfileAccess($cookie->profile, $this->id);
		$this->adminFeaturesValues->postProcess($this->token);
		Module::hookExec('postProcessFeature',
		array('errors' => &$this->_errors)); // send _errors as reference to allow postProcessFeature to stop saving process
		if(isset($_POST['categoryBox']) and $_POST['id_feature']){
			$id_feature = (int)$_POST['id_feature'];
			Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM '._DB_PREFIX_.'features_fgroup WHERE id_feature = '.$id_feature);
			foreach($_POST['categoryBox'] as $key=>$value){
				if((int)$value>0){
					Category::setFGroupForFeature($id_feature, $value);
				}
			}
		}
		if (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$object = new $this->className();
					if ($object->deleteSelection($_POST[$this->table.'Box']))
						Tools::redirectAdmin($currentIndex.'&conf=2'.'&token='.$this->token);
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');
				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
			parent::postProcess();
	}
}
