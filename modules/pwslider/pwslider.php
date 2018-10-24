<?php
class PWSlider extends Module
{
	/** @var max image size */
 	protected $maxImageSize = 10000000;
	protected $_xml;
 	function __construct()
 	{
 	 	$this->name = 'pwslider';
		$this->tab = 'other';
 	 	$this->version = '0.2';
	 	parent::__construct();
		$this->page = basename(__FILE__, '.php');
	 	$this->displayName = $this->l('Слайдер главной');
	 	$this->description = $this->l('Показывает слайдер на главной странице');
		$this->_xml = $this->_getXml();
 	}
    function install()
    {
        if (!parent::install() OR !$this->registerHook('header') OR !$this->registerHook('top'))
            return false;
        return true;
    }
	function putContent($xml_data, $key, $field)
	{
		$field = htmlspecialchars($field);
		if (!$field)
			return 0;
		return ("\n".'		<'.$key.'>'.$field.'</'.$key.'>');
	}
 	function getContent()
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
       	 	
 	 	/* display the module name */
 	 	$this->_html = '<h2>'.$this->displayName.' '.$this->version.'</h2>';
 	 	/* update the editorial xml */
		
 	 	if (isset($_POST['submitUpdate']))
 	 	{
			$display = (int)Tools::getValue('display');
			if ($display <= 0)
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('PS_SLIDER_DISPLAY', (int)$display);
			// Generate new XML data
 	 	 	$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n";
			$newXml .= '<links>'."\n";
			$i = 1;
			foreach ($_POST['link'] as $link)
			{
				if($link['id']) $i = (int)$link['id'];
				$newXml .= '<link>';
				foreach ($link AS $key => $field)
				{
					if ($line = $this->putContent($newXml, $key, $field))
						$newXml .= $line;
				}
				/* upload the image */
				if (isset($_FILES['link_'.$i.'_img']) AND isset($_FILES['link_'.$i.'_img']['tmp_name']) AND !empty($_FILES['link_'.$i.'_img']['tmp_name']))
				{
					Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
					if ($error = checkImage($_FILES['link_'.$i.'_img'], $this->maxImageSize))
						$this->_html .= $error;
					$exts = explode(".", $_FILES['link_'.$i.'_img']['name']); $ext = strtolower(end($exts));
					if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['link_'.$i.'_img']['tmp_name'], $tmpName))
						return false;
					if (!imageResize($tmpName, dirname(__FILE__).'/slides/slide_'.$i.'.'.$ext, NULL, NULL, $ext))
						$this->_html .= $this->displayError($this->l('An error occurred during the image upload.'));
						unlink($tmpName);
				}
				if(!empty($_FILES['link_'.$i.'_img']['tmp_name'])){
					if ($line = $this->putContent($newXml, 'img', 'slides/slide_'.$i.'.'.$ext)) $newXml .= $line;
				} elseif($this->_xml->link[$i]->img) if ($line = $this->putContent($newXml, 'img', $this->_xml->link[$i]->img)) $newXml .= $line;
				$newXml .= "\n".'	</link>'."\n";
				
				$i++;
			}
			$newXml .= '</links>'."\n";
			/* write it into the editorial xml file */
			if ($fd = @fopen(dirname(__FILE__).'/links.xml', 'w'))
			{
				if (!@fwrite($fd, $newXml))
					$this->_html .= $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					$this->_html .= $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				$this->_html .= $this->displayError($this->l('Unable to update the editor file.<br />Please check the editor file\'s writing permissions.'));
 	 	}
		if (Tools::isSubmit('submitUpdate'))
		{
            $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
 		/* display the editorial's form */
 	 	$this->_displayForm();
 	 	return $this->_html;
 	}
	
	static private function getXmlFilename()
	{
		return 'links.xml';
	}
	
	private function _getXml()
	{
	
		if (file_exists(dirname(__FILE__).'/'.$this->getXmlFilename()))
		{
			if ($xml = @simplexml_load_file(dirname(__FILE__).'/'.$this->getXmlFilename()))
				
			
			foreach($xml->link as $row){
				$xml2->link[(int)$row->id] = $row;
			}
			
			return $xml2;
		}
		return false;
	}
	
	public function _getFormItem($i, $last)
	{
		global $cookie;
		$this->_xml = $this->_getXml();
		$i = (int)$i;
		//echo "<pre>";print_r($this->_xml);echo "</pre>";
		$isoUser = Language::getIsoById(intval($cookie->id_lang));
		$output = '
			<div class="item" id="item'.$i.'">
				<h3>'.$this->l('Item #').$i.'</h3>
				<input type="hidden" name="link['.$i.'][id]" value="'.$i.'" />';
		$output .= '
				<label>'.$this->l('Название').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field1]" size="64" value="'.@$this->_xml->link[$i]->field1.'" />
					<p style="clear: both"></p>
				</div>';
		$output .= '
				<label>'.$this->l('Описание').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field2]" size="64" value="'.@$this->_xml->link[$i]->field2.'" />
					<p style="clear: both"></p>
				</div>';
        $output .= '
				<label>'.$this->l('Старая цена').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field3]" size="64" value="'.@$this->_xml->link[$i]->field3.'" />
					<p style="clear: both"></p>
				</div>';
        $output .= '
				<label>'.$this->l('Новая цена').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field4]" size="64" value="'.@$this->_xml->link[$i]->field4.'" />
					<p style="clear: both"></p>
				</div>';
		$output .= '
				<label>'.$this->l('Отключить').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="checkbox" name="link['.$i.'][turnoff]" size="64" value="1" '.(@$this->_xml->link[$i]->turnoff ? 'checked="checked"' : '').' />
					<p style="clear: both"></p>
				</div>';
        $output .= '
				<label>'.$this->l('Вставить звездочку').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="checkbox" name="link['.$i.'][vip]" size="64" value="1" '.(@$this->_xml->link[$i]->vip ? 'checked="checked"' : '').' />
					<p style="clear: both"></p>
				</div>';
        $output .= '
				<label>'.$this->l('Изображение').'</label>
				<div class="margin-form">
					<div><a target="_blank" href="'.$this->_path.@$this->_xml->link[$i]->img.'?time='.time().'"><img src="'.$this->_path.@$this->_xml->link[$i]->img.'?time='.time().'" alt="" style="max-width:600px;" title=""/></a></div>
					<input type="file" name="link_'.$i.'_img" />
					<p style="clear: both"></p>
				</div>';
		$output .= '
				<label>'.$this->l('Куда должна вести ссылка?').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][url]" size="64" value="'.@$this->_xml->link[$i]->url.'" />
					<p style="clear: both"></p>
				</div>';
		$output .= '
				<div class="clear pspace"></div>
				'.($i >= 0 ? '<a href="javascript:{}" onclick="removeDiv(\'item'.$i.'\')" style="color:#EA2E30"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('delete').'" />'.$this->l('Удалить').'</a>' : '').'
			<hr/></div>';
		return $output;
	}
	
 	private function _displayForm()
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
 	 	/* xml loading */
 	 	$xml = false;
 	 	if (file_exists(dirname(__FILE__).'/links.xml'))
		  	if (!$xml = @simplexml_load_file(dirname(__FILE__).'/links.xml'))
		  		$this->_html .= $this->displayError($this->l('Your links file is empty.'));
		        $this->_html .= '
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
			<fieldset style="width: 800px;">
        		<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
					<div id="items">';
						$i = 1;
						foreach ($xml->link as $link)
						{
							$last = ($i == (count($xml->link)-1) ? true : false);
							$i = (empty($link->id) ? $i : $link->id);
							$this->_html .= $this->_getFormItem($i, $last);
							$i++;
						}
						$this->_html .= '
				</div>
				<a id="clone'.$i.'" href="javascript:cloneIt(\'clone'.$i.'\')" style="color:#488E41"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="'.$this->l('add').'" /><b>'.$this->l('Добавить новый слайд').'</b></a>';
		
		$this->_html .= '
				<div class="margin-form clear">
					<div class="clear pspace"></div>
					<div class="margin-form">
						 <input type="submit" name="submitUpdate" value="'.$this->l('Сохранить').'" class="button" />
					</div>
				</div>
					
				</fieldset>
			</form>';
 	}
	
 	function hookTop($params)
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
 	 	if (file_exists(dirname(__FILE__).'/links.xml'))
 	 		if ($xml = simplexml_load_file(dirname(__FILE__).'/links.xml'))
 	 		{
 	 		 	global $cookie, $smarty;
				//var_dump($xml);
				$arr = Array();

				foreach($xml->link as $key=>$value){
					if($value->turnoff) continue;
					$arr[] = $value;
				}
				//print_r($xml->link);
				$smarty->assign(array(
					'xml' => $arr,
					'this_path' => $this->_path
				));
				return $this->display(__FILE__, 'pwslider.tpl');
			}
		return false;
 	}

	function hookheader($params)
 	{
		Tools::addCSS($this->_path.'pwslider.css', 'all');
		Tools::addJS(array($this->_path.'jquery.fractionslider.min.js'));
	}
	
}
?>