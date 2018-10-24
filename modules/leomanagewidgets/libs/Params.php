<?php 
/**
 * $ModDesc
 * 
 * @version   $Id: file.php $Revision
 * @package   modules
 * @subpackage  $Subpackage.
 * @copyright Copyright (C) November 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */
if( !class_exists('LeoHomeParams', false) ){
class LeoHomeParams{
  
    /**
    * @var string name ;
    *
    * @access public;
    */
    public  $name   = '';
    /**
    * @var arry name ;
    *
    * @protected public;
    */
    protected $_categories= array();
    
	
	protected $currentMod = null;
	private $used_texteditor = false;
	/**
	 * Constructor
    */
	public function LeoHomeParams( $current, $name ){
		global $cookie;
		$this->currentMod = $current;
		$this->name = $name;
	}
    
	public function l( $lang ){
		return $this->currentMod->l( $lang );
	}
	
	public function inputTag( $label, $name, $value, $note="", $attrs='size="5"' ){
		$html = '
		<label>'.$this->l( $label ).'</label>
		<div class="margin-form">
			<input type="text" name="'. $this->getFieldName($name) .'" id="'.$name.'" '.$attrs.' value="'.$value.'" /> '.$note.'
		</div>';
		
		return $html;
	}
	
	public function inputTagLang( $label, $name, $values, $keysLang, $note="", $attrs = 'size="40"' ){
        $languages = Language::getLanguages(false);
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $html = '<label>'.$label.'</label>';
        $html .= '<div class="margin-form">';
        foreach ($languages as $language){
            $html .= '<div id="'.$name.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;width: 230px;">
					<input size="40" type="text" id="c'.$name.'_'.$language['id_lang'].'" name="'.$this->getFieldName($name.'_'.$language['id_lang']).'" value="'.(isset($values[$language['id_lang']]) ? $values[$language['id_lang']] : '').'" '.$attrs.'/>
				</div>';
        }
        $html .= $this->currentMod->displayFlags($languages, $defaultLanguage, $keysLang, $name, true);
        $html .= $note;
        $html .= '</div><div class="clear space"></div>';
        return $html;
    }
	
	public function statusTag( $label, $name, $value, $id ){
		$name = $this->getFieldName($name);
		$html = '
			<label for="'.$name.'_on">'.$this->l($label ).'</label>
			<div class="margin-form">
				<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Yes" title="Yes" />
				<input type="radio" name="'.$name.'" id="'.$id.'_on" '.( $value == 1 ? 'checked="checked"' : '').' value="1" />
				<label class="t" for="'.$name.'_on">'.$this->l('Yes').'</label>
				<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="No" title="No" style="margin-left: 10px;" />
				<input type="radio" name="'.$name.'" id="'.$id.'_off" '.( $value == 0 ? 'checked="checked" ' : '').' value="0" />
				<label class="t" for="loop_off">'.$this->l('No').'</label>
			</div>';
		return $html;	
	}
	
	/**
	 *
	 */
	public function getSourceDataTag( $current ){

		$path = (dirname(__FILE__)).'/sources/';
		
		if( !is_dir($path) ){
			return $this->l( "Could not found any themes in 'themes' folder" );
		}
		
		$sources = $this->getFolderList( $path );
		
		$html = '<label for="source">'.$this->l( 'Source:' ).'</label>
			<div class="margin-form">';
		$html .='<select name="'.$this->getFieldName('source').'" id="source">';
		foreach( $sources as $source ) {
			$selected = ($source == $current ) ? 'selected="selected"' : '';
			$html .= '<option value="'.$source.'" '. $selected .'>'.$source.'</option>';
		}
		
		$html .='</select>';
		$html .= '</div>'; 
		
		$html .='<div class="group_configs" id="groupconfigs">';
				foreach( $sources as $source ){
					$html .= '<div class="source-group source'.$source.'">';
						$html .= LeoBaseSource::getSource( $source )->renderForm( $this );
					$html .= '</div>';
				}
		$html .='</div>';
		
		$html .= '<script>';
			$html .= '
				$(document).ready( function(){
					$(".source-group").hide();
					$(".source"+$("#source").val() ).show();
					$("#source").change(function(){
					$(".source-group").hide();
					$(".source"+$(this).val() ).show();
				} );	
			} )';
		$html .= '</script>';
		return $html;
		
	}
	
	private function getFieldName( $name ){
		return $name;
	}
	
	/**
	 *
	 */
	public function getThemesTag( $current ){
		$path = dirname(dirname(__FILE__)).'/themes/';
 
		if( !is_dir($path) ){
			return $this->l( "Could not found any themes in tmpl folder" );
		}
		
		$themes = $this->getFolderList( $path );
		$name = $this->getFieldName('theme');
		$html = '<label for="'.$name.'">'.$this->l( 'Theme:' ).'</label>
			<div class="margin-form">';
		$html .='<select name="'.$this->getFieldName('theme').'" id="'.$name.'">';
		foreach( $themes as $theme ) {
			$selected = ($theme == $current ) ? 'selected="selected"' : '';
			$html .= '<option value="'.$theme.'" '. $selected .'>'.$theme.'</option>';
		}
		
		$html .='</select>';
		$html .= '</div>'; 
		return $html;
		
	}
	/**
	 *
	 */
	public function getBackgroundList( $pathFile, $attrs, $id = 'tabBackground', $button = true, $name = 'theme', $value = '' ){
		$path = $pathFile;
		if( !is_dir($path) ){
			return $this->l( "Could not found any file in this folder" );
		}
		
		$themes = $this->getFileInFolderList( $path );
		$html = '<label for="'.$id.'">'.$this->l( 'Images list' ).'</label>
			<div class="margin-form">';
		$html .='<select name="'.$this->getFieldName($name).'" id="'.$id.'" '.$attrs.'>';
		$html .= '<option value="0">'.$this->currentMod->l('------ None ------').'</option>';
		foreach( $themes as $theme ) {
			$html .= '<option value="'.$theme.'"'.($value == $theme ? ' selected="selected"' : '').'>'.$theme.'</option>';
		}
		$html .='</select>';
		if($button)
			$html .= '<br/><br/><button onclick="return addBackGround();">'.$this->l('Add background').'</button>';
		
		$html .= '</div>'; 
		return $html;
	}	
	
	public function selectTag( $data, $label, $name, $value, $note='', $attrs='' ){
		
		$html = '<label for="'.$name.'">'.$this->l( $label ).'</label>
			<div class="margin-form">';
		$html .='<select name="'.$this->getFieldName($name).'" id="'.$name.'" '.$attrs.'>';
		foreach( $data as $key => $item ) {
			$selected = ($key == $value ) ? 'selected="selected"' : '';
			$html .= '<option value="'.$key.'" '. $selected .'>'.$item.'</option>';
		}
		$html .='</select>'.$note;
		$html .= '</div>';
		
		return $html;
	}
 
	/**
    * Get list of sub folder's name 
    */
	public function getFolderList( $path ) {
		$items = array();
		$handle = opendir($path);
		if (! $handle) {
			return $items;
		}
		while (false !== ($file = readdir($handle))) {
			if (is_dir($path . $file))
				$items[$file] = $file;
		}
		unset($items['.'], $items['..'], $items['.svn']);
		return $items;
	}
	
	/**
	* Get list of file in folder 
	*/
	public function getFileInFolderList( $path ) {
		$items = array();
		$handle = opendir($path);
		if (! $handle) {
			return $items;
		}
		
		while (false !== ($file = readdir($handle))) {
			if (is_file($path . $file) && $this->checkImage($path . $file))
				$items[$file] = $file;
		}
		unset($items['.'], $items['..'], $items['.svn']);
		return $items;
	}
	
	function checkImage($image) {
		//checks if the file is a browser compatible image
		
		$mimes = array('image/gif','image/jpeg','image/pjpeg','image/png');
		//get mime type
		$mime = getimagesize($image);
		$mime = $mime['mime'];
		
		$extensions = array('jpg','png','gif','jpeg');
		$extension = strtolower( pathinfo( $image, PATHINFO_EXTENSION ) );
		
		if ( in_array( $extension , $extensions ) AND in_array( $mime, $mimes ) ) return TRUE; 
		else return FALSE; 
	
	}
	
	/**
    * Get List Categories Tree source
	* 
	* @access public
	* @static method
	* return array contain list of categories source 
    */ 
	public function getIndexedCategories(){		
		global $cookie;
		$id_lang = intval($cookie->id_lang);
        if(version_compare(_PS_VERSION_,"1.5","<"))
            $join = '';
        else
            $join = 'JOIN `'._DB_PREFIX_.'category_shop` cs ON(c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)(Context::getContext()->shop->id).')';

		$allCat = Db::getInstance()->ExecuteS('
		SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
		FROM `'._DB_PREFIX_.'category` c
		'.$join.'
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.intval($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
		WHERE `active` = 1		
		GROUP BY c.`id_category`
		ORDER BY `name` ASC');		
		$children = array();
		if ( $allCat )
		{
			foreach ( $allCat as $v )
			{
				$this->_categories[$v["id_category"]] = $v["name"];
				$pt 	= $v["id_parent"];
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
			return $children;
		}		
		return array();
	}
	/**
    * Build category tree list
    */
	public static function treeCategory($id, &$list, $children, $tree=""){		
		if (isset($children[$id])){			
			if($id != 0){
				$tree = $tree." - ";
			}
			foreach ($children[$id] as $v)
			{	
				$v["tree"] = $tree;				
				$list[] = $v;							
				self::treeCategory( $v["id_category"], $list, $children,$tree);
			}
		}		
	}
	
	
	public function listCategoryImage($value='',$imgPath=''){
		
		$html = '';
		if($value){
			if(!is_array($value))
				$list = explode("#,#",$value);
			else
				$list = $value;
			
			foreach($list as $val){
				$cat   = explode('#@#',$val);
				
				if($this->_categories[$cat[0]]) $catname = $this->_categories[$cat[0]];
				$html .= '<tr id="row-'.$cat[0].'">';
				$html .= '<td>'.$cat[0].'</td><td><input type="hidden" value="'.$cat[0].'#@#'.$cat[1].'" name="leocblist[]"/>'.$catname.'</td><td><img src='.$imgPath.$cat[1].' alt=""/></td><td><a href="javascript:deleteCBG('.$cat[0].');"><img src="../img/admin/delete.gif"></a></td>';
				$html .= '</tr>';
			}
			
		}
		
		return $html;
	}
	
	 /**
	 * render textarea html tag.
	 */
	 public function textAreaTag( $label, $name, $values, $use_texteditor = true, $lang = false, $keysLang = '', $note='', $attrs='' ){
		global $adminfolder;
        $html = '';
        if($use_texteditor){
			if(!$this->used_texteditor){
				//Context::getContext()->controller->addJS(__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js');
				//Context::getContext()->controller->addJS(__PS_BASE_URI__.'js/tinymce.inc.js');
				$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.Context::getContext()->language->iso_code.'.js') ? Context::getContext()->language->iso_code : 'en');
				$ad = dirname($_SERVER["PHP_SELF"]);
				//$html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js'.'"></script>';
				//$html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js'.'"></script>';
				$html .= '<script type="text/javascript">
				  var iso = \''.$isoTinyMCE.'\' ;
				  var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				  var ad = \''.__PS_BASE_URI__.$adminfolder.'\' ;
				  tinySetup();
				</script>';
				$this->used_texteditor = true;
			}
        }
        $html .= '<label for="'.$name.'">'.$this->l( $label ).'</label>
			<div class="margin-form">';
		if($lang){
			$languages = Language::getLanguages(false);
			$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
			foreach($languages as $language){
			  $html .= '
				<div id="'.$name.'_'.$language['id_lang'].'" style="display: ' . ($language['id_lang'] == $defaultLanguage ? 'block' : 'none') . '; float: left;">
				  <textarea '.($use_texteditor ? 'class="rte"' : '').' cols="100" rows="20" name="'.$this->getFieldName($name).'_'.$language['id_lang'].'" '.$attrs.'>'.htmlentities($values[$language['id_lang']], ENT_COMPAT, 'UTF-8').'</textarea>
				</div>';
			}
			$html .= $this->currentMod->displayFlags($languages, $defaultLanguage, $keysLang, $name, true);
		}else{
			$html .= '<textarea '.($use_texteditor ? 'class="rte"' : '').' cols="100" rows="20" name="'.$this->getFieldName($name).'" '.$attrs.'>'.htmlentities($values, ENT_COMPAT, 'UTF-8').'</textarea>';
		}
        $html .= '<div class="clear"></div>';
        $html .= $note;
        $html .= '</div>';
        return $html;
    }
	
	public function categoryTag( $name, $value, $title, $attr='', $liAtrr="", $ulAttr = "", $tooltip="", $textAllCat = ""){
	//	echo '<pre>'.print_r($value,1 ); die;
        $children  = $this->getIndexedCategories();
        $list = array();			
        $context = Context::getContext();
        $id_category = $context->shop->getCategory();
        $this->treeCategory( $id_category, $list , $children );  
		$catArray = $value;
		if(!is_array($catArray))
			$catArray  = explode(",",$catArray);
        
        $id = "params_".$name;
        $id = str_replace("[]","",$id);
        
        $isSelected = (in_array("",$catArray))?'selected="selected"':"";        
        $options  = '';        
        foreach($list as $cat){
            $isSelected = (in_array($cat["id_category"],$catArray) || in_array("",$catArray))?'selected="selected"':"";
            $options  .= '<option value="'.$cat["id_category"].'" '.$isSelected.'>---| '.$cat["tree"].$cat["name"].'</option>';                                       
        }
         $html = '<label for="theme">'.$this->l( $title ).'</label><div class="margin-form">';
			   $html .= ' <select '.$attr.' id="'.$id.'" name="'.$this->getFieldName($name).'[]">'.$options.'</select>';
		$html .= '</div>';
        return $html;               		
	}
 }
}
?>