<?php
/**
 * Product parameter class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

class ymlParam
{
	protected $element = 'currency';
	protected $generalAttributes = array('name'=>'');

	public function __construct($name, $value)
	{
		$this->name = self::PrepareString($name);
		$this->tagContent = self::PrepareString($value);
	}
}
