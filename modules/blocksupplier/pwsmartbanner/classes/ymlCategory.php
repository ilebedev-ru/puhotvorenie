<?php
/**
 * Product category class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

class ymlCategory extends ymlElement
{
	public static $collectionName = 'categories';
	protected $element = 'category';
	protected $generalAttributes = array('id'=>'1', 'parentId'=>'');

	public function __construct($id='1', $name='', $parentId='')
	{
		$this->id = $id;
		$this->tagContent = self::PrepareString($name);
		$this->parentId = $parentId;
	}
}