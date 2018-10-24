<?php
/**
 * Product offer class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

class ymlOffer extends ymlElement
{
	public static $collectionName = 'offers';
	protected $element = 'offer';
	protected $generalAttributes = array('id'=>'', 'type'=>'', 'available'=>'true', 'bid'=>'');
	protected $generalProperties = array('url'=>'','price'=>'','currencyId'=>'RUB','categoryId'=>'1','picture'=>array(),'store'=>'false','pickup'=>'false','delivery'=>'false','local_delivery_cost'=>'','name'=>'','vendor'=>'','vendorCode'=>'','description'=>'','sales_notes'=>'','country_of_origin'=>'','adult'=>'','barcode'=>'','param'=>array());

	public function __construct($id, $type, $available='true', $bid=false)
	{
		$this->id = $id;
		$this->type = $type;
		$this->available = $available;
		$this->bid = $bid;
	}
}