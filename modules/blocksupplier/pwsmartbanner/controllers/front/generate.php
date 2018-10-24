<?php
/**
* yamarket module generator controller.
*
* @author    0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 2.0
*/

class yamarketgenerateModuleFrontController extends ModuleFrontController
{
	public $display_header = false;
	public $display_column_left = false;
	public $display_column_right = false;
	public $display_footer = false;
	public $ssl = false;

	public function postProcess()
	{
		parent::postProcess();

		$this->module->generate(Tools::getValue('cron'));
	}
}