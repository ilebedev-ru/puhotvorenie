<?php
/**
 * Base YML elements class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

class ymlElement
{
//	protected $collectionName = false;
//	protected $element = '';
	protected $tagContent = '';
	protected $generalProperties = array();
	protected $generalAttributes = array();
	private static $from_char = array('&nbsp;', '"', '&', '>', '<', '\'', '`');
	private static $to_char = array(' ', '&quot;', '&amp;', '&gt;', '&lt;', '&apos;', '&apos;');

	/**
	 * Установка значений
	 * @param $name
	 * @param $value
	 * @return bool
	 */
	public function __set($name, $value)
	{
		if (isset($this->generalProperties[$name]))
			$this->generalProperties[$name] = $value;
		else if (isset($this->generalAttributes[$name]))
			$this->generalAttributes[$name] = $value;
		else
			return false;

	}

	/**
	 * Получение значений
	 * @param $name
	 * @return bool
	 */
	public function __get($name)
	{
		if (isset($this->generalProperties[$name]))
			return $this->generalProperties[$name];
		else if (isset($this->generalAttributes[$name]))
			return $this->generalAttributes[$name];
		else
			return false;
	}

	/**
	 * Подготовка текстового поля в соответствии с требованиями Яндекса
	 * @param $s
	 * @return string
	 */
	public static function PrepareString($str)
	{
		//Удаляем html теги
		$str = preg_replace('!<[^>]*?>!', ' ', $str);
		//Преобразуем символы в html сущности
		$str = str_replace(self::$from_char, self::$to_char, $str);
		//Удаляем запрещенные символы
		$str = preg_replace('#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', $str);
		//Конвертируем в кодировку windows-1251
		//$str = iconv("UTF-8", "CP1251//IGNORE//TRANSLIT", $str);
		return trim($str);
	}

	/**
	 * Преобрзование свойства в строку
	 * @param $data
	 * @return string
	 */
	protected function getProp($data)
	{
		$tmp = '';
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_array($value)){
					foreach ($value as $k => $v) {
						$tmp .= "<".$key.($k?" name=\"".self::PrepareString($k)."\"":'').">" . self::PrepareString($v) . "</".$key.">\r\n";
					}
					//$tmp .= $this->getProp($value);
				} else if (Tools::strlen(trim($value)) > 0) {
					$tmp .= "<".$key.">" . self::PrepareString($value) . "</".$key.">\r\n";
				}
			}
		}

		return $tmp;
	}


	public function add(ymlElement $comp)
	{
		$this->tagContent.=$comp->generate();
	}

	public function addString($string)
	{
		$this->tagContent.=$string;
	}

	public function startTag($tag)
	{
		$this->tagContent.='<'.$tag.'>';
	}

	public function endTag($tag)
	{
		$this->tagContent.='</'.$tag.'>';
	}

	/**
	 * Генерация строки из объекта
	 * @return string
	 */
	public function generate($close=true)
	{
		$tmp = '<'.$this->element;
		//Добавляем атрибуты

		foreach ($this->generalAttributes as $key => $value) {
			if (Tools::strlen(trim($value)) > 0) {
				$tmp .= ' '.$key.'="' . self::PrepareString($value) . '"';
			}
		}
		if ($this->generalProperties OR $this->tagContent){
			$tmp .= ">\r\n";

			//Добавляем своства
			$tmp .= $this->getProp($this->generalProperties);

			$tmp .= $this->tagContent;
			if ($close)
				$tmp .= '</'.$this->element.'>';
		} else {
			$tmp .= "/>\r\n";
		}

		return $tmp;
	}

}