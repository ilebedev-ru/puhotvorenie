<?php

class recentOrders extends Module
{
	private $_html = '';
	private $_postErrors = array();
	protected $_xml;

	function __construct()
	{
		$this->name = 'recentorders';
		$this->tab = 'Iccessory';
		$this->version = '1.0';

		parent::__construct();
		
		$this->displayName = $this->l('Recent orders');
		$this->description = $this->l('Displays recent orders - made by <a target=_blank href=http://www.phpskill.com>PHPSkill.com</a>');
		$this->_xml = $this->_getXml();
	}

	function install()
	{
		if (!parent::install() OR !$this->registerHook('home') OR !Configuration::updateValue('RECENT_ORDER_TYPE', 'auto') OR !Configuration::updateValue('RECENT_ORDER_NUMBER', '10') OR !Configuration::updateValue('RECENT_ORDER_NUMBER_DISPLAY', '3'))
			return false;
		return true;
	}
	
	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_html .= $this->_postProcess();
		$this->_html .= $this->_displayForm();
		return $this->_html;
	}
	
	function replaceNB($obj, $id, $newid)
	{
		$obj->img[0] = str_replace($id, $newid, $obj->img[0]);
		$obj->thumbnail[0] = str_replace($id, $newid, $obj->thumbnail[0]);
		return $obj;
	}
	
	protected function putContent($xml_data, $key, $field)
	{
		$field = htmlspecialchars($field);
		if (!$field)
			return 0;
		return ("\n\t\t<".$key.">".$field."</".$key.">");
	}

	private function _postProcess()
	{
		$newXml = '<'.'?'.'xml version="1.0" encoding="utf-8" '.'?'.'>';
		$newXml .= "\n<items>";
		if (Tools::isSubmit('submitUpdate'))
		{
			Configuration::updateValue('RECENT_ORDER_TYPE', $_POST['type']);
			Configuration::updateValue('RECENT_ORDER_NUMBER', (int)$_POST['number']);
			Configuration::updateValue('RECENT_ORDER_NUMBER_DISPLAY', (int)$_POST['numberdisplay']);
			$i = 0;
			foreach ($_POST['item'] as $item) 
			{
				$newXml .= "\n\t<item>";
				foreach ($item AS $key => $field)
				{
					if ($line = $this->putContent($newXml, $key, htmlspecialchars($field)))
						$newXml .= $line;
				}
				$newXml .= "\n\t</item>\n";
				$i++;
			}
			$newXml .= "\n</items>\n";

			if ($fd = @fopen(dirname(__FILE__).'/'.$this->getXmlFilename(), 'w'))
			{
				if (!@fwrite($fd, $newXml))
					return $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					return $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				return $this->displayError($this->l('Unable to update the editor file.<br />Please check the editor file\'s writing permissions.'));

			/* refresh XML */
			$this->_xml = $this->_getXml();
			return $this->displayConfirmation($this->l('Items updated.'));
		}else if($_GET['op'] && isset($_GET['item']))
		{
			$newId = $_GET['op'] == 'up' ? $_GET['item'] - 1 : $_GET['item'] + 1; 
			$file = file_get_contents(dirname(__FILE__).'/'.$this->getXmlFilename());
			$xml = simplexml_load_file(dirname(__FILE__).'/'.$this->getXmlFilename());
			$xmlPart = $xml->xpath('/items/item['.$_GET['item'].']');//var_Dump($xml->xpath('/items'));
			$i = 0;
			foreach($xml->item as $key => $item)
			{
				if($i == $newId){
					$newXml .= $this->replaceNB($xml->item[(int)$_GET['item']][0], $_GET['item'], $newId)->asXML();
				}elseif($i == $_GET['item'])
					$newXml .= $this->replaceNB($xml->item[(int)$newId], $newId, $_GET['item'])->asXML();
				else{
					$newXml .= $item->asXML();
					//var_dump($item->asXML());
				}
				$i++;
			}
			$newXml .= "\n</items>\n";
			
			//die($newXml);
			if ($fd = @fopen(dirname(__FILE__).'/'.$this->getXmlFilename(), 'w'))
			{
				if (!@fwrite($fd, $newXml))
					 $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					 $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				 $this->displayError($this->l('Unable to update the editor file.<br />Please check the editor file\'s writing permissions.'));
			parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY ), $url);
			unset($url['op']);
			unset($url['item']);
			//exit(str_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY ), '', $_SERVER['REQUEST_URI']).http_build_query($url));
			header('Location:'.str_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY ), '', $_SERVER['REQUEST_URI']).http_build_query($url));
		}
	}

	static private function getXmlFilename()
	{
		return 'data.xml';
	}
	
	private function _getXml()
	{
		if (file_exists(dirname(__FILE__).'/'.$this->getXmlFilename()))
		{
			if ($xml = @simplexml_load_file(dirname(__FILE__).'/'.$this->getXmlFilename()))
				return $xml;
		}
		return false;
	}

	public function _getFormItem($i, $last)
	{
		$divLangName = '';
		$output = '
			<div class="item" id="item'.$i.'">
				<h3>'.$this->l('Item #').($i+1).'</h3>
				<input type="hidden" name="item['.$i.'][item]" value="" />';
		$output .= '
				<label>'.$this->l('Product ID').'</label>
				<div class="margin-form">
					<br /><input type="text" style="width:430px" name="item['.$i.'][id_product]" value="'.$this->_xml->item[$i]->id_product.'" />
					<p style="clear: both"></p>
				</div>';
		$output .= '
				<label>'.$this->l('Address').'</label>
				<div class="margin-form" style="padding-left:0">
					<br /><input type="text" style="width:430px" name="item['.$i.'][address]" value="'.$this->_xml->item[$i]->address.'" />
					<p style="clear: both">Salt Lake City, USA</p>
				</div>';
		$output .= '
				<div class="clear pspace"></div>
				'.($i >= 0 ? '<a href="javascript:{}" onclick="removeDiv(\'item'.$i.'\')" style="color:#EA2E30"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('delete').'" />'.$this->l('Delete this item').'</a>' : '').'
				'.($i > 0 ? '<a href="'.$_SERVER['REQUEST_URI'].'&item='.$i.'&op=up" style="color:#000">'.$this->l('Up').'</a>' : '').'
				'.(!$last ? '<a href="'.$_SERVER['REQUEST_URI'].'&item='.$i.'&op=down" style="color:#000">'.$this->l('Down').'</a>' : '').'
			<hr/></div>';
		return $output;
	}

	public function _displayForm()
	{

		$output = '';

		$xml = false;
		if (!$xml = $this->_xml)
			$output .= $this->displayError($this->l('Your data file is empty.'));

		$output .= '
		<script type="text/javascript">
		function removeDiv(id)
		{
			$("#"+id).fadeOut("slow");
			$("#"+id).remove();
		}
		function cloneIt(cloneId) {
			var currentDiv = $(".item:last");
			var id = ($(currentDiv).size()) ? $(currentDiv).attr("id").match(/[0-9]/gi) : -1;
			var nextId = parseInt(id) + 1;
			$.get("'._MODULE_DIR_.$this->name.'/ajax.php?id="+nextId, function(data) {
				$("#items").append(data);
			});
			$("#"+cloneId).remove();
		}
		</script>
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">
			<fieldset style="width: 900px;">
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->displayName.'</legend>
				<div>
				<label>'.$this->l('Display mode').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="radio" name="type" value="auto" '.((Configuration::get('RECENT_ORDER_TYPE') == 'auto') ? 'checked="checked"' : '').' />&nbsp;Auto&nbsp;&nbsp;
					<input type="radio" name="type" value="fake" '.((Configuration::get('RECENT_ORDER_TYPE') == 'fake') ? 'checked="checked"' : '').' />&nbsp;Fake data&nbsp;&nbsp;
					<input type="radio" name="type" value="mixed" '.((Configuration::get('RECENT_ORDER_TYPE') == 'mixed') ? 'checked="checked"' : '').' />&nbsp;Mixed&nbsp;&nbsp;
				</div>
				<label>'.$this->l('Maximum orders').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="number" value="'.Configuration::get('RECENT_ORDER_NUMBER').'" />
				</div>
				<label>'.$this->l('Maximum orders display').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="numberdisplay" value="'.Configuration::get('RECENT_ORDER_NUMBER_DISPLAY').'" />
				</div>
				</div>
				<div id="items">';

		$i = 0;
		foreach ($xml->item as $item)
		{
			$last = ($i == (count($xml->item)-1) ? true : false);
			$output .= $this->_getFormItem($i, $last);
			$i++;
		}
		$output .= '
				</div>
				<a id="clone'.$i.'" href="javascript:cloneIt(\'clone'.$i.'\')" style="color:#488E41"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="'.$this->l('add').'" /><b>'.$this->l('Add a new item').'</b></a>
				<div class="margin-form clear">
					<input type="submit" name="submitUpdate" value="'.$this->l('Save').'" class="button" />
				</div>
			</fieldset>
		</form>
		';
		return $output;
	}
	
	function hookHome($params){
		return $this->hookLeftColumn($params);
	}

	function hookLeftColumn($params)
	{
		global $smarty, $cookie;
		
		if(Configuration::get('RECENT_ORDER_TYPE') == 'fake' || Configuration::get('RECENT_ORDER_TYPE') == 'mixed')
		{
			
			if ($xml = $this->_xml)
			{
				$i = 0;
				foreach($xml as $k => $v){
					if($i > Configuration::get('RECENT_ORDER_NUMBER'))
						break;
					$product = new Product($v->id_product);
					if(!$product)
						continue;

					$result[] = array(
						'product_name' => $product->name[$cookie->id_lang],
						'product_link' => $product->getLink(),
						'address' => $v->address,
						'dateadd' => $dateadd
					);
					$i++;
				}
			}
		}
		if(Configuration::get('RECENT_ORDER_TYPE') == 'auto' || Configuration::get('RECENT_ORDER_TYPE') == 'mixed'){
			$this->lang = intval($cookie->id_lang);
			$this->identifier = 'id_order';
			$this->table = 'order';
	
			$select = '
				a.id_order,
				CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
				osl.`name` AS `osname`,
				os.`color`,
				IF((SELECT COUNT(so.id_order) FROM `ps_orders` so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new,
				(SELECT COUNT(od.`id_order`) FROM `ps_order_detail` od WHERE od.`id_order` = a.`id_order` GROUP BY `id_order`) AS product_number';
			$join = 'LEFT JOIN `ps_customer` c ON (c.`id_customer` = a.`id_customer`)
			LEFT JOIN `ps_order_history` oh ON (oh.`id_order` = a.`id_order`)
			LEFT JOIN `ps_order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
			LEFT JOIN `ps_order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.intval($cookie->id_lang).')';
			$where = 'AND oh.`id_order_history` = (SELECT MAX(`id_order_history`) FROM `ps_order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)';
	
			$sql = 'SELECT SQL_CALC_FOUND_ROWS
				a.*, '.$select.'
				FROM `ps_orders` a
				'.$join.'
				WHERE 1 '.$where.'
				ORDER BY a.`'.pSQL('date_add').'` '.pSQL('DESC');
			
			
			$list = Db::getInstance()->ExecuteS($sql);
			foreach($list as $item)
			{
				$order = new Order($item['id_order']);
				$addressDelivery = new Address($order->id_address_delivery, intval($cookie->id_lang));
				$dateadd = $order->date_add;
				$products = $order->getProducts();
				$products = array_slice($products, 0, 1);
				$product = new Product($products[0]['product_id'],true,$cookie->id_lang);
				
				$dt = $dateadd; // Присвоение переменной $dt значения поля datetime из базы blogg
				$yy = substr($dt,0,4); // Год
				$mm = substr($dt,5,2); // Месяц
				$dd = substr($dt,8,2); // День


				// Переназначаем переменные
				if ($mm == "01") $mm1="января";
				if ($mm == "02") $mm1="февраля";
				if ($mm == "03") $mm1="марта";
				if ($mm == "04") $mm1="апреля";
				if ($mm == "05") $mm1="мая";
				if ($mm == "06") $mm1="июня";
				if ($mm == "07") $mm1="июля";
				if ($mm == "08") $mm1="августа";
				if ($mm == "09") $mm1="сентября";
				if ($mm == "10") $mm1="октября";
				if ($mm == "11") $mm1="ноября";
				if ($mm == "12") $mm1="декабря";

				$hours = substr($dt,11,5); // Время 
				$ddtt = $dd." ".$mm1." ".$yy.", ".$hours; // Конечный вид строки

				$stid = $addressDelivery->id_state;
				$state = State::getNameById($stid);		
				$id_image = Product::getCover($product->id);		
				$id_image = $product->id."-".$id_image['id_image'];
				$result[] = array(
				'id_product' =>$product->id,
				'date' => $ddtt,
				'link_rewrite' => $product->link_rewrite,
				'id_image' => $id_image,
				'product_name' => $products[0]['product_name'],
				'product_link' => $product->getLink(),
				'address' => $state,
				);
								
			}
		}

		$smarty->assign(array(
			'result' => array_slice($result, 0, Configuration::get('RECENT_ORDER_NUMBER')),
			'RECENT_ORDER_NUMBER_DISPLAY' => (intval(Configuration::get('RECENT_ORDER_NUMBER_DISPLAY')) == 0 ) ? '1' : intval(Configuration::get('RECENT_ORDER_NUMBER_DISPLAY'))
		));
		return $this->display(__FILE__, 'recentorders.tpl');
	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
}
