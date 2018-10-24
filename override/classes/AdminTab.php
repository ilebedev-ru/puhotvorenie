<?php  
  
class AdminTab extends AdminTabCore  
{  
	public function displayListContent($token = NULL)  
	{  
		/* Display results in a table  
		 *  
		 * align  : determine value alignment  
		 * prefix : displayed before value  
		 * suffix : displayed after value  
		 * image  : object image  
		 * icon   : icon determined by values  
		 * active : allow to toggle status  
		 */  
  
		global $currentIndex, $cookie;  
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));  
  
		$id_category = 1; // default categ  
  
		$irow = 0;  
		if ($this->_list AND isset($this->fieldsDisplay['position']))  
		{  
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);  
			sort($positions);  
		}  
		if ($this->_list)  
		{  
			$isCms = false;  
			if (preg_match('/cms/Ui', $this->identifier))  
				$isCms = true;  
			$keyToGet = 'id_'.($isCms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');  
			foreach ($this->_list AS $tr)  
			{  
				$id = $tr[$this->identifier];  
				echo '<tr'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="tr_'.(($id_category = (int)(Tools::getValue('id_'.($isCms ? 'cms_' : '').'category', '1'))) ? $id_category : '').'_'.$id.'_'.$tr['position'].'"' : '').($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>  
							<td class="center">';  
				if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))  
					echo '<input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" />';  
				echo '</td>';  
				foreach ($this->fieldsDisplay AS $key => $params)  
				{  
					$tmp = explode('!', $key);  
					$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];  
					echo '  
					<td '.(isset($params['position']) ? ' id="td_'.(isset($id_category) AND $id_category ? $id_category : 0).'_'.$id.'"' : '').' class="'.((!isset($this->noLink) OR !$this->noLink) ? 'pointer' : '').((isset($params['position']) AND $this->_orderBy == 'position')? ' dragHandle' : ''). (isset($params['align']) ? ' '.$params['align'] : '').'" ';  
					if (!isset($params['position']) AND (!isset($this->noLink) OR !$this->noLink))  
						echo ' onclick="document.location = \''.$currentIndex.'&'.$this->identifier.'='.$id.($this->view? '&view' : '&update').$this->table.'&token='.($token!=NULL ? $token : $this->token).'\'">'.(isset($params['prefix']) ? $params['prefix'] : '');  
					else  
						echo '>';  
					if (isset($params['active']) AND isset($tr[$key]))  
						$this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));  
					elseif (isset($params['activeVisu']) AND isset($tr[$key]))  
						echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"  
						alt="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" />';  
					elseif (isset($params['position']))  
					{  
						if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC')  
						{  
							echo '<a'.(!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.$currentIndex.  
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'  
									&way=1&position='.(int)($tr['position'] + 1).'&token='.($token!=NULL ? $token : $this->token).'">  
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"  
									alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';  
  
							echo '<a'.(!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '').' href="'.$currentIndex.  
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'  
									&way=0&position='.(int)($tr['position'] - 1).'&token='.($token!=NULL ? $token : $this->token).'">  
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"  
									alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';                     }  
						else  
							echo (int)($tr[$key] + 1);  
					}  
					elseif (isset($params['image']))  
					{  
						// item_id is the product id in a product image context, else it is the image id.  
						$item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;  
						// If it's a product image  
						if (isset($tr['id_image']))  
						{  
							$image = new Image((int)$tr['id_image']);  
							$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$image->getExistingImgPath().'.'.$this->imageType;  
						}else  
							$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType;  
  
						echo cacheImage($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);  
					}  
					elseif (isset($params['icon']) AND (isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))  
						echo '<img src="../img/admin/'.(isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'].'" alt="'.$tr[$key]).'" title="'.$tr[$key].'" />';  
					elseif (isset($params['price']))  
						echo Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance((int)($tr['id_currency'])) : $currency), false);  
					elseif (isset($params['float']))  
						echo rtrim(rtrim($tr[$key], '0'), '.');  
					elseif (isset($params['type']) AND $params['type'] == 'date')  
						echo Tools::displayDate($tr[$key], (int)$cookie->id_lang);  
					elseif (isset($params['type']) AND $params['type'] == 'datetime')  
						echo Tools::displayDate($tr[$key], (int)$cookie->id_lang, true);  
					elseif (isset($tr[$key]))  
					{  
						$echo = ($key == 'price' ? round($tr[$key], 2) : isset($params['maxlength']) ? Tools::substr(Tools::htmlentitiesUTF8($tr[$key]), 0, $params['maxlength']).'...' : Tools::htmlentitiesUTF8($tr[$key]));  
						echo isset($params['callback']) ? call_user_func_array(array($this->className, $params['callback']), array($echo, $tr)) : $echo;  
					}  
					else  
						echo '--';  
  
					echo (isset($params['suffix']) ? $params['suffix'] : '').  
					'</td>';  
				}  
  
				if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))  
				{  
					echo '<td class="center" style="white-space: nowrap;">';  
					if ($this->view)  
						$this->_displayViewLink($token, $id);  
					if ($this->edit)  
						$this->_displayEditLink($token, $id);  
					if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))  
						$this->_displayDeleteLink($token, $id);  
					if ($this->duplicate)  
						$this->_displayDuplicate($token, $id);  
					echo '</td>';  
				}  
				echo '</tr>';  
			}  
		}  
	}  
  
}