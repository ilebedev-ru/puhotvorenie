<?php
/**
 * Currrency class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

class ymlCurrency extends ymlElement
{
	public static $collectionName = 'currencies';
	protected $element = 'currency';
	protected $generalAttributes = array('id'=>'RUB', 'rate'=>'1', 'plus'=>'');

	public function __construct($id='RUB', $rate='1')
	{
		if (!in_array($id, array('RUR', 'USD', 'BYR', 'KZT', 'EUR', 'UAH')))
			return false;
		$this->id = $id;
		$this->rate = $rate;
	}
}