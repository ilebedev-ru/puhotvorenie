<?php
  /*
  **************************************
  **        PrestaShop V1.5.4.x        *
  **            ModalCart              *
  **    http://www.brainos.com         *
  **             V 1.0                 *
  **    Author-team: Land of coder     *
  **************************************
  */

class LeoManageWidget extends ObjectModel{
	
	public $title;
	public $configs;
	public $position;
	public $hook;
	public $task;
	public $active;
	public $file_names = array();
	
	public $cache_configs = array();

	public static $definition = array(
		'table' => 'leomanagewidgets',
		'primary' => 'id_leomanagewidgets',
		'fields' => array(
			'hook' => 				array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 25),
			'task' => 				array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 25),
			'active' => 			array('type' => self::TYPE_INT, 'validate' => 'isBool'),
		)
	);
	
	public function __construct($id = NULL, $id_lang = NULL, $id_shop = NULL){
		parent::__construct($id, $id_lang, $id_shop);
		if($id){
			$this->getShopData();
			$this->title = unserialize(base64_decode($this->title));
			$this->configs = unserialize(base64_decode($this->configs));
			if(!$id_shop)
				$id_shop = Context::getContext()->shop->id;
			$this->file_names = $this->getExceptions($this->hook, $id_shop);
		}
	}

	public function add($autodate = true, $null_values = false, $id_shop = false)
	{
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$this->configs =  base64_encode(serialize( $this->configs ));
		$this->title =  base64_encode(serialize( $this->title ));
		$this->position =  self::getLastPosition( $this->hook, $id_shop );
		$res = parent::add($autodate, $null_values);
		if($res){
			$res &= $this->addShopData();
			if($this->file_names && count($this->file_names))
				$res &= $this->addExcepData();
		}
		return $res;
	}
	
	public function update($nullValues = false, $id_shop = false)
	{
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$this->configs =  base64_encode(serialize( $this->configs ));
		$this->title =  base64_encode(serialize( $this->title ));
		$res = parent::update(true);
		if($res){
			$res &= $this->addShopData();
			if($this->file_names && count($this->file_names))
				$res &= $this->addExcepData();
		}
	 	return $res;
	}
	
	public function addShopData($id_shop = false){
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$res = true;
		$res &= Db::getInstance()->insert('leomanagewidgets_shop', array('id_leomanagewidgets'=>$this->id, 'id_shop' => $id_shop, 'position' => $this->position, 'title'=>$this->title, 'configs' => $this->configs), false, true, Db::REPLACE);
		return $res;
	}
	
	public function addExcepData($id_shop = false){
		$this->deleteExcepData();
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$res = true;
		foreach($this->file_names as $file_name){
			$res &= Db::getInstance()->insert('leomanagewidgets_exceptions', array('id_leomanagewidgets' => $this->id, 'id_shop' => $id_shop, 'hook' => $this->hook, 'file_name' => trim($file_name)), false, true, Db::REPLACE);
		}
		return $res;
	}
	
	public function delete() {
		$res = parent::delete();
		if($res){
			$res &= $this->deleteShopData();
			$res &= $this->deleteExcepData();
		}
		return $res;
	}
	
	public function deleteShopData(){
		return (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'leomanagewidgets_shop` WHERE id_leomanagewidgets = '.(int)($this->id) ));
	}
	
	public function deleteExcepData(){
		return (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'leomanagewidgets_exceptions` WHERE id_leomanagewidgets = '.(int)($this->id) ));
	}
	
    public static function getLastPosition($hook, $id_shop)
	{
		return (int)(Db::getInstance()->getValue('
		SELECT MAX(cs.`position`)
		FROM `'._DB_PREFIX_.'leomanagewidgets` c
		LEFT JOIN `'._DB_PREFIX_.'leomanagewidgets_shop` cs ON (c.`id_leomanagewidgets` = cs.`id_leomanagewidgets` AND cs.`id_shop` = '.(int)$id_shop.')
		WHERE c.`hook` = \''.pSQL($hook).'\'' ) + 1);
	}
	
	public function getShopData($id_shop = false){
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$res = Db::getInstance()->getRow(
			'SELECT * FROM `'._DB_PREFIX_.'leomanagewidgets_shop` WHERE id_shop = '.(int)($id_shop).' AND id_leomanagewidgets = '.(int)($this->id)
		);
		if(!$res)
			return;
		foreach($res as $key => $val)
			$this->{$key} = $val;
	}
	
	public function getConfig( $key, $pre = '', $id_leomanagewidgets = 0, $default = '', $id_shop = false ){
		if(!$id_leomanagewidgets)
			$id_leomanagewidgets = $this->id;
		$cache_key = md5($key.'-'.(int)($id_leomanagewidgets).'-'.$pre);
		if(!isset($this->cache_configs[$cache_key])){
			$configs = $this->getConfigs($id_leomanagewidgets, $id_shop);
			if(!$configs){
				return ($this->cache_configs[$cache_key] = $default);
			}
			$configs = unserialize(base64_decode($configs['configs']));
			
			if($pre){
				foreach(explode(',', $pre) as $row){
					if(isset($configs[trim($row)]))
						$configs = $configs[trim($row)];
					else
						return ($this->cache_configs[$cache_key] = $default);
				}
			}
			$this->cache_configs[$cache_key] = (isset($configs[$key]) ? $configs[$key] : $default);
		}
		
		return $this->cache_configs[$cache_key];
	}
	
	public function getConfigs($id_leomanagewidgets = 0, $id_shop = false){
		if(!$id_leomanagewidgets)
			$id_leomanagewidgets = $this->id;
		
		$cache_key = md5($id_leomanagewidgets);
		if(!isset($this->cache_configs[$cache_key])){
			$res = self::getStaticConfigs($id_shop);
			$this->cache_configs[$cache_key] = array();
			if(isset($res[$id_leomanagewidgets]))
				$this->cache_configs[$cache_key] = $res[$id_leomanagewidgets];
		}
		
		return $this->cache_configs[$cache_key];
	}
	
	Public static function getStaticConfigs( $id_shop = false){
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$res = Db::getInstance()->executeS( 'SELECT `configs`, `id_leomanagewidgets` FROM `'._DB_PREFIX_.'leomanagewidgets_shop` WHERE `id_shop` = '.(int)($id_shop));
		$return = array();
		if($res)
			foreach($res as $row){
				$return[$row['id_leomanagewidgets']] = $row;
			}
		return $return;
	}
	
	public static function gets($exception = false){
		$res = Db::getInstance()->executeS(
			'SELECT DISTINCT a.id_leomanagewidgets, a.*, b.position, b.title, b.configs 
			FROM `'._DB_PREFIX_.'leomanagewidgets` a
			JOIN `'._DB_PREFIX_.'leomanagewidgets_shop` b ON (a.id_leomanagewidgets = b.id_leomanagewidgets AND b.id_shop = '.(int)(Context::getContext()->shop->id).')'.
			($exception ? '
			JOIN `'._DB_PREFIX_.'leomanagewidgets_exceptions` c ON (a.id_leomanagewidgets = c.id_leomanagewidgets AND b.id_shop = '.(int)(Context::getContext()->shop->id).' AND c.file_name = \''.pSQL($exception).'\')' : '')
		);
		return $res;
	}
	
	public static function getsHook($exception = false){
		$modules = self::gets($exception);
		$return = array();
		$position = array();
		foreach($modules as &$mod){
			$mod['title'] = unserialize(base64_decode( $mod['title'] ));
			$mod['configs'] = unserialize(base64_decode( $mod['configs'] ));
			$return[$mod['hook']][] = $mod;
			$position[$mod['hook']][] = $mod['position'];
		}
		
		if($return){
			foreach($return as $key=>&$row){
				array_multisort( $position[$key], $row);
			}
		}
		return $return;
	}
	
	public function getExceptions($hook, $id_shop = false, $id_leomanagewidgets = false){
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		if(!$id_leomanagewidgets)
			$id_leomanagewidgets = $this->id;
		$res = Db::getInstance()->executeS(
			'SELECT a.file_name
			FROM `'._DB_PREFIX_.'leomanagewidgets_exceptions` a
			WHERE a.id_leomanagewidgets = '.(int)($id_leomanagewidgets).' AND a.id_shop = '.(int)($id_shop).' AND a.hook = \''.pSQL($hook).'\''
		);
		$return = array();
		if($res)
			foreach($res as $row){
				$return[] = $row['file_name'];
			}
		return $return;
	}
	
	public static function getAllExceptions($id_shop = false){
		if(!$id_shop)
			$id_shop = Context::getContext()->shop->id;
		$res = Db::getInstance()->executeS(
			'SELECT a.file_name
			FROM `'._DB_PREFIX_.'leomanagewidgets_exceptions` a
			WHERE a.id_shop = '.(int)($id_shop)
		);
		$return = array();
		if($res)
			foreach($res as $row){
				$return[] = $row['file_name'];
			}
		return $return;
	}
	
	public function displayModuleExceptionList($file_list)
	{
		if (!is_array($file_list))
			$file_list = ($file_list) ? array($file_list) : array();

		$content = '<input type="text" name="exceptions" size="40" value="'.implode(', ', $file_list).'" id="em_text" />';
		
		$content .= '
				<br />
				<select id="em_list" size="30" multiple="multiple" style="width:237px">
					<option disabled="disabled">'.'___________ CUSTOM ___________'.'</option>';
		
		// @todo do something better with controllers
		$controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
		ksort($controllers);
		
		foreach ($file_list as $k => $v)
			if ( ! array_key_exists ($v, $controllers))
				$content .= '
					<option value="'.$v.'">'.$v.'</option>';

		$content .= '
					<option disabled="disabled">'.'____________ CORE ____________'.'</option>';
		foreach ($controllers as $k => $v)
			$content .= '
					<option value="'.$k.'">'.$k.'</option>';
		
		$content .= '
			</select>
			';

		return $content;
	}
	
	
	
	
	
	
	
	
}