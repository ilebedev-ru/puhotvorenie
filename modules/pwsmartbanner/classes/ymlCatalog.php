<?php
/**
 * Catalog class
 *
 * @author    0RS <admin@prestalab.ru>
 * @link http://prestalab.ru/
 * @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
 * @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 2.0
 */

require_once 'ymlElement.php';
require_once 'ymlShop.php';
require_once 'ymlCurrency.php';
require_once 'ymlCategory.php';
require_once 'ymlOffer.php';

class ymlCatalog extends ymlElement
{
	protected $element = 'yml_catalog';
	protected $generalAttributes = array('date'=>'');
	public $gzip=false;

	public function __construct()
	{
		$this->date = date("Y-m-d H:i");
	}

	public function generate($close=true)
	{
		$tmp='<?xml version="1.0" encoding="utf-8"?>
		<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
		$tmp.=parent::generate($close);
		if($this->gzip&&function_exists('gzencode')){
			$tmp = gzencode($tmp, 9);
		}
		return $tmp;
	}
}
